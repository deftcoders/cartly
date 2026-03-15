<?php
/**
 * Cartly Smart Upsell Engine v2.
 *
 * Supports: related, crosssell, category, rule-based (cart total threshold),
 * and frequently bought together.
 *
 * @package Cartly
 * @author  DeftCoders
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Cartly_Upsells
 *
 * Handles smart upsell product generation logic.
 */
class Cartly_Upsells {

	/**
	 * Get upsell products.
	 *
	 * Rule-based logic takes priority.
	 *
	 * @return array
	 */
	public static function get_products() {

		$s     = Cartly_Settings::get_all();
		$limit = ! empty( $s['upsells_limit'] ) ? absint( $s['upsells_limit'] ) : 3;

		// Collect cart product IDs to exclude.
		$cart_ids   = array();
		$cart_cats  = array();
		$cart_total = 0.0;

		if ( WC()->cart ) {
			$cart_total = (float) WC()->cart->get_subtotal();

			foreach ( WC()->cart->get_cart() as $item ) {
				$cart_ids[] = $item['product_id'];

				$cats = wp_get_post_terms(
					$item['product_id'],
					'product_cat',
					array( 'fields' => 'ids' )
				);

				if ( ! is_wp_error( $cats ) ) {
					$cart_cats = array_merge( $cart_cats, $cats );
				}
			}
		}

		$cart_cats = array_unique( $cart_cats );

		// Rule-based: cart total threshold overrides everything.
		if ( ! empty( $s['upsells_rule_enabled'] ) && ! empty( $s['upsells_rule_product_ids'] ) ) {

			$min = (float) $s['upsells_rule_min_cart'];

			if ( $cart_total >= $min ) {

				$manual_ids = array_filter(
					array_map(
						'absint',
						explode( ',', $s['upsells_rule_product_ids'] )
					)
				);

				$manual_ids = array_diff( $manual_ids, $cart_ids );

				if ( ! empty( $manual_ids ) ) {
					return self::format( array_slice( $manual_ids, 0, $limit ) );
				}
			}
		}

		// Frequently bought together.
		if ( ! empty( $s['upsells_fbt_enabled'] ) && ! empty( $cart_ids ) ) {
			$fbt_ids = self::get_frequently_bought( $cart_ids, $limit, $cart_ids );

			if ( ! empty( $fbt_ids ) ) {
				return self::format( $fbt_ids );
			}
		}

		// Standard strategies.
		$product_ids = array();
		$source      = isset( $s['upsell_source'] ) ? $s['upsell_source'] : 'related';

		switch ( $source ) {

			case 'crosssell':
				foreach ( $cart_ids as $pid ) {
					$product = wc_get_product( $pid );

					if ( $product ) {
						$product_ids = array_merge(
							$product_ids,
							$product->get_cross_sell_ids()
						);
					}
				}
				break;

			case 'category':
				if ( ! empty( $cart_cats ) ) {

					$query = new WP_Query(
						array(
							'post_type'              => 'product',
							'posts_per_page'         => $limit * 3,
							// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- Required for category-based upsell logic.
							'tax_query'              => array(
								array(
									'taxonomy' => 'product_cat',
									'field'    => 'term_id',
									'terms'    => $cart_cats,
								),
							),
							'fields'                 => 'ids',
							'post__not_in'           => $cart_ids,
							'orderby'                => 'rand',
							'no_found_rows'          => true,
							'update_post_meta_cache' => false,
							'update_post_term_cache' => false,
						)
					);

					$product_ids = $query->posts;
					wp_reset_postdata();
				}
				break;

			default: // Related.
				foreach ( $cart_ids as $pid ) {
					$product_ids = array_merge(
						$product_ids,
						wc_get_related_products( $pid, $limit * 2 )
					);
				}
				break;
		}

		$product_ids = array_slice(
			array_unique( array_diff( $product_ids, $cart_ids ) ),
			0,
			$limit
		);

		shuffle( $product_ids );

		return self::format( array_slice( $product_ids, 0, $limit ) );
	}

	/**
	 * Get frequently bought together products.
	 *
	 * Uses cross-sells as proxy and falls back to related products.
	 *
	 * @param array<int, int> $cart_ids Cart product IDs.
	 * @param int             $limit    Number of products to return.
	 * @param array<int, int> $exclude  Product IDs to exclude.
	 *
	 * @return array<int, int>
	 */
	private static function get_frequently_bought( array $cart_ids, $limit, array $exclude ): array {

		// Use cross-sells as proxy for FBT.
		$ids = array();

		foreach ( $cart_ids as $pid ) {
			$product = wc_get_product( $pid );

			if ( $product ) {
				$ids = array_merge( $ids, $product->get_cross_sell_ids() );
			}
		}

		if ( empty( $ids ) ) {
			// Fallback to related products.
			foreach ( $cart_ids as $pid ) {
				$ids = array_merge(
					$ids,
					wc_get_related_products( $pid, $limit )
				);
			}
		}

		return array_slice(
			array_unique( array_diff( $ids, $exclude ) ),
			0,
			$limit
		);
	}

	/**
	 * Format product IDs into structured array for JS rendering.
	 *
	 * @param array<int, int> $product_ids Product IDs.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	private static function format( array $product_ids ): array {

		$output = array();

		foreach ( $product_ids as $pid ) {

			$product = wc_get_product( $pid );

			if ( ! $product || ! $product->is_purchasable() || ! $product->is_in_stock() ) {
				continue;
			}

			$thumb = wp_get_attachment_image_url(
				(int) $product->get_image_id(),
				'woocommerce_thumbnail'
			);

			if ( ! $thumb ) {
				$thumb = wc_placeholder_img_src();
			}

			$output[] = array(
				'id'           => $pid,
				'name'         => $product->get_name(),
				'price'        => $product->get_price_html(),
				'price_raw'    => (float) $product->get_price(),
				'thumb'        => esc_url( $thumb ),
				'permalink'    => esc_url( get_permalink( $pid ) ),
				'rating'       => $product->get_average_rating(),
				'review_count' => $product->get_review_count(),
				'on_sale'      => $product->is_on_sale(),
				'badge'        => $product->is_on_sale()
					? __( 'Sale', 'cartly' )
					: ( $product->is_featured()
						? __( 'Featured', 'cartly' )
						: '' ),
				'type'         => $product->get_type(),
			);
		}

		return $output;
	}
}
