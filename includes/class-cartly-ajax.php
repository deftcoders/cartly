<?php
/**
 * Cartly AJAX Handler
 *
 * Registers and processes all front-end AJAX endpoints for the cart drawer.
 * Every endpoint verifies a nonce inline (PHPCS WordPress.Security.NonceVerification
 * requires check_ajax_referer() / wp_verify_nonce() to be called in the same
 * function scope where $_POST / $_GET is read — delegating to a helper method
 * is not detected by the static sniff).
 *
 * Cart operations are intentionally open to guests (wp_ajax_nopriv_*) because
 * WooCommerce supports guest checkout natively.
 * Admin save/reset endpoints live in class-cartly-admin.php.
 *
 * Endpoint map (action → method):
 *   cartly_get_cart      → get_cart()
 *   cartly_add_to_cart   → add_to_cart()
 *   cartly_update_cart   → update_cart()
 *   cartly_remove_item   → remove_item()
 *   cartly_apply_coupon  → apply_coupon()
 *   cartly_remove_coupon → remove_coupon()
 *   cartly_get_upsells   → get_upsells()
 *
 * JS parameter names (must match cartly.js exactly):
 *   product_id, variation_id, qty, key, coupon
 *
 * @package Cartly
 * @author  DeftCoders
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Cartly_Ajax
 *
 * Singleton that wires all wp_ajax_* hooks and processes cart requests.
 *
 * @since 1.0.0
 */
class Cartly_Ajax {

	// ── Singleton ────────────────────────────────────────────────────────────

	/**
	 * Single class instance.
	 *
	 * @since 1.0.0
	 * @var Cartly_Ajax|null
	 */
	private static $instance = null;

	/**
	 * Return (or create) the single class instance.
	 *
	 * @since  1.0.0
	 * @return Cartly_Ajax
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	// ── Boot ─────────────────────────────────────────────────────────────────

	/**
	 * Register all wp_ajax_* hooks.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		$endpoints = array(
			'cartly_get_cart'      => 'get_cart',
			'cartly_add_to_cart'   => 'add_to_cart',
			'cartly_update_cart'   => 'update_cart',
			'cartly_remove_item'   => 'remove_item',
			'cartly_apply_coupon'  => 'apply_coupon',
			'cartly_remove_coupon' => 'remove_coupon',
			'cartly_get_upsells'   => 'get_upsells',
		);

		foreach ( $endpoints as $action => $method ) {
			add_action( 'wp_ajax_' . $action, array( $this, $method ) );
			add_action( 'wp_ajax_nopriv_' . $action, array( $this, $method ) );
		}
	}

	// ── Public AJAX Endpoints ─────────────────────────────────────────────────

	/**
	 * Return the current cart state to the JS layer.
	 *
	 * @since 1.0.0
	 * @return void — dies via wp_send_json_*
	 */
	public function get_cart() {
		check_ajax_referer( 'cartly_nonce', 'nonce' );
		$this->require_cart();

		WC()->cart->calculate_totals();

		wp_send_json_success( $this->build_cart_payload() );
	}

	/**
	 * Add a product to the cart.
	 *
	 * Expected POST params:
	 *   int product_id   — Required. WooCommerce product ID.
	 *   int qty          — Optional. Quantity to add (default 1).
	 *   int variation_id — Optional. Variation ID for variable products.
	 *
	 * @since 1.0.0
	 * @return void — dies via wp_send_json_*
	 */
	public function add_to_cart() {
		check_ajax_referer( 'cartly_nonce', 'nonce' );
		$this->require_cart();

		$product_id   = absint( wp_unslash( $_POST['product_id'] ?? 0 ) );
		$quantity     = max( 1, absint( wp_unslash( $_POST['qty'] ?? 1 ) ) );
		$variation_id = absint( wp_unslash( $_POST['variation_id'] ?? 0 ) );

		if ( ! $product_id ) {
			wp_send_json_error(
				array( 'message' => __( 'Invalid product ID.', 'cartly' ) ),
				400
			);
		}

		$product = wc_get_product( $product_id );
		if ( ! $product || ! $product->is_purchasable() ) {
			wp_send_json_error(
				array( 'message' => __( 'This product is not available for purchase.', 'cartly' ) ),
				400
			);
		}

		if ( $product->is_type( 'variable' ) && ! $variation_id ) {
			wp_send_json_error(
				array( 'message' => __( 'Please select product options before adding to cart.', 'cartly' ) ),
				400
			);
		}

		wc_clear_notices();

		$cart_item_key = WC()->cart->add_to_cart( $product_id, $quantity, $variation_id );

		if ( $cart_item_key ) {
			WC()->cart->calculate_totals();
			wp_send_json_success( $this->build_cart_payload() );
		}

		$error_notices = wc_get_notices( 'error' );
		wc_clear_notices();

		$message = ! empty( $error_notices )
			? wp_strip_all_tags( $error_notices[0]['notice'] )
			: __( 'Could not add the product to cart. Please try again.', 'cartly' );

		wp_send_json_error( array( 'message' => $message ), 422 );
	}

	/**
	 * Update quantity of an existing cart item.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function update_cart() {
		check_ajax_referer( 'cartly_nonce', 'nonce' );
		$this->require_cart();

		$cart_item_key = sanitize_text_field( wp_unslash( $_POST['key'] ?? '' ) );
		$quantity      = absint( wp_unslash( $_POST['qty'] ?? 0 ) );

		if ( ! $cart_item_key ) {
			wp_send_json_error(
				array( 'message' => __( 'Invalid cart item key.', 'cartly' ) ),
				400
			);
		}

		if ( ! WC()->cart->get_cart_item( $cart_item_key ) ) {
			wp_send_json_error(
				array( 'message' => __( 'Cart item not found.', 'cartly' ) ),
				404
			);
		}

		if ( 0 === $quantity ) {
			WC()->cart->remove_cart_item( $cart_item_key );
		} else {
			WC()->cart->set_quantity( $cart_item_key, $quantity );
		}

		WC()->cart->calculate_totals();

		wp_send_json_success( $this->build_cart_payload() );
	}

	/**
	 * Remove an item from the cart.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function remove_item() {
		check_ajax_referer( 'cartly_nonce', 'nonce' );
		$this->require_cart();

		$cart_item_key = sanitize_text_field( wp_unslash( $_POST['key'] ?? '' ) );

		if ( ! $cart_item_key ) {
			wp_send_json_error(
				array( 'message' => __( 'Invalid cart item key.', 'cartly' ) ),
				400
			);
		}

		if ( ! WC()->cart->get_cart_item( $cart_item_key ) ) {
			wp_send_json_error(
				array( 'message' => __( 'Cart item not found.', 'cartly' ) ),
				404
			);
		}

		WC()->cart->remove_cart_item( $cart_item_key );
		WC()->cart->calculate_totals();

		wp_send_json_success( $this->build_cart_payload() );
	}

	/**
	 * Apply a coupon code.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function apply_coupon() {
		check_ajax_referer( 'cartly_nonce', 'nonce' );
		$this->require_cart();

		$coupon_code = sanitize_text_field( wp_unslash( $_POST['coupon'] ?? '' ) );

		if ( ! $coupon_code ) {
			wp_send_json_error(
				array( 'message' => __( 'Please enter a coupon code.', 'cartly' ) )
			);
		}

		wc_clear_notices();
		WC()->cart->apply_coupon( $coupon_code );

		$all_notices = wc_get_notices();
		wc_clear_notices();

		$applied = ! empty( $all_notices['success'] );
		$message = '';

		if ( ! empty( $all_notices['error'] ) ) {
			$message = wp_strip_all_tags( $all_notices['error'][0]['notice'] );
		} elseif ( $applied ) {
			$message = wp_strip_all_tags( $all_notices['success'][0]['notice'] );
		}

		if ( $applied ) {
			WC()->cart->calculate_totals();
			wp_send_json_success(
				array_merge( $this->build_cart_payload(), array( 'message' => $message ) )
			);
		}

		wp_send_json_error(
			array(
				'message' => $message ? $message : __( 'Invalid or expired coupon code.', 'cartly' ),
			)
		);
	}

	/**
	 * Remove an applied coupon.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function remove_coupon() {
		check_ajax_referer( 'cartly_nonce', 'nonce' );
		$this->require_cart();

		$coupon_code = sanitize_text_field( wp_unslash( $_POST['coupon'] ?? '' ) );

		if ( ! $coupon_code ) {
			wp_send_json_error(
				array( 'message' => __( 'Invalid coupon code.', 'cartly' ) ),
				400
			);
		}

		WC()->cart->remove_coupon( $coupon_code );
		WC()->cart->calculate_totals();

		wp_send_json_success( $this->build_cart_payload() );
	}

	/**
	 * Return upsell products.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function get_upsells() {
		check_ajax_referer( 'cartly_nonce', 'nonce' );

		wp_send_json_success( Cartly_Upsells::get_products() );
	}

	// ── Private Helpers ───────────────────────────────────────────────────────

	/**
	 * Confirm that WooCommerce and the cart session are fully available.
	 *
	 * This can legitimately fail in REST API or WP-CLI contexts, or
	 * when WooCommerce is deactivated mid-session.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function require_cart() {
		if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
			wp_send_json_error(
				array( 'message' => __( 'The cart is not available. Please refresh the page and try again.', 'cartly' ) ),
				503
			);
		}
	}

	/**
	 * Build the full cart payload for cartly.js.
	 *
	 * Generates the full structured cart data including items, totals, coupons,
	 * goal progress, and all other display-related metadata required by the
	 * Cartly front-end.
	 *
	 * @since  1.0.0
	 * @return array<string, mixed> Full cart data payload.
	 */
	private function build_cart_payload() {
		$cart     = WC()->cart;
		$settings = Cartly_Settings::get_all();

		// ── Line items ───────────────────────────────────────────────────────
		$items = array();

		foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
			/* @var WC_Product $product WooCommerce product object. */
			$product = $cart_item['data'];

			if ( ! $product instanceof WC_Product ) {
				continue;
			}

			$thumbnail_url = wp_get_attachment_image_url( (int) $product->get_image_id(), 'woocommerce_thumbnail' );
			if ( ! $thumbnail_url ) {
				$thumbnail_url = wc_placeholder_img_src();
			}

			$items[] = array(
				'key'             => $cart_item_key,
				'product_id'      => absint( $cart_item['product_id'] ),
				'name'            => $product->get_name(),
				'thumb'           => esc_url( $thumbnail_url ),
				'permalink'       => esc_url( get_permalink( $cart_item['product_id'] ) ),
				'qty'             => absint( $cart_item['quantity'] ),
				'variation'       => $this->format_variation( $cart_item['variation'] ?? array() ),
				'line_total_html' => wc_price( $cart_item['line_total'] ),
				'line_total_raw'  => floatval( $cart_item['line_total'] ),
				'price_raw'       => floatval( $product->get_price() ),
				'low_stock'       => $product->managing_stock() && (int) $product->get_stock_quantity() <= 3,
			);
		}

		// ── Applied coupons ──────────────────────────────────────────────────
		$coupons = array();

		foreach ( $cart->get_applied_coupons() as $coupon_code ) {
			$coupons[] = array(
				'code'     => $coupon_code,
				'discount' => wc_price( $cart->get_coupon_discount_amount( $coupon_code ) ),
			);
		}

		// ── Multi-tier cart goals ─────────────────────────────────────────────
		$goals = array();

		if ( ! empty( $settings['goals_enabled'] ) ) {
			for ( $tier = 1; $tier <= 3; $tier++ ) {
				$goals[] = array(
					'amount' => floatval( $settings[ "goal_{$tier}_amount" ] ?? 0 ),
					'icon'   => $settings[ "goal_{$tier}_icon" ] ?? '',
					'label'  => $settings[ "goal_{$tier}_label" ] ?? '',
				);
			}
		}

		// ── Final payload ─────────────────────────────────────────────────────
		return array(
			'items'            => $items,
			'count'            => absint( $cart->get_cart_contents_count() ),
			'subtotal_html'    => $cart->get_cart_subtotal(),
			'subtotal_raw'     => floatval( $cart->get_subtotal() ),
			'shipping_goal'    => floatval( $settings['shipping_goal'] ?? 0 ),
			'shipping_msg'     => wp_kses_post( $settings['shipping_message'] ?? '' ),
			'shipping_success' => wp_kses_post( $settings['shipping_success_message'] ?? '' ),
			'reward_goal'      => floatval( $settings['reward_goal'] ?? 50 ),
			'reward_text'      => wp_kses_post( $settings['reward_text'] ?? '' ),
			'goals'            => $goals,
			'coupons'          => $coupons,
			'is_empty'         => $cart->is_empty(),
		);
	}

	/**
	 * Convert variation attributes into readable text.
	 *
	 * @since  1.0.0
	 * @param  array<string, string> $variation Raw variation attributes.
	 * @return string
	 */
	private function format_variation( array $variation ) {
		if ( empty( $variation ) ) {
			return '';
		}

		$parts = array();

		foreach ( $variation as $attribute_key => $attribute_value ) {
			$label = ucfirst(
				str_replace(
					array( 'attribute_', '-', '_' ),
					array( '', ' ', ' ' ),
					$attribute_key
				)
			);

			$parts[] = sanitize_text_field( $label ) . ': ' . sanitize_text_field( $attribute_value );
		}

		return implode( ' · ', $parts );
	}
}
