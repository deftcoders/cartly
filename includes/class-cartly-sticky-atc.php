<?php
/**
 * Cartly Sticky Add-to-Cart Bar.
 *
 * Renders a sticky bar at the bottom of single product pages.
 * The bar appears (via JS) when the native WooCommerce add to cart
 * button scrolls above the viewport. Clicking adds to cart and opens the drawer.
 *
 * @package Cartly
 * @author  codelitix
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Cartly_Sticky_Atc
 *
 * Handles the sticky add to cart functionality.
 */
class Cartly_Sticky_Atc {

	/**
	 * Singleton instance.
	 *
	 * @var Cartly_Sticky_Atc|null
	 */
	private static $instance = null;

	/**
	 * Get class instance.
	 *
	 * @return Cartly_Sticky_Atc
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {

		// Only render on single product pages.
		add_action( 'wp_footer', array( $this, 'render' ), 98 );
	}

	/**
	 * Render sticky add to cart bar.
	 *
	 * @return void
	 */
	public function render() {

		$s = Cartly_Settings::get_all();

		// Respect master enable toggle and sticky ATC setting.
		if ( empty( $s['enabled'] ) || empty( $s['sticky_atc_enabled'] ) ) {
			return;
		}

		if ( ! function_exists( 'is_product' ) || ! is_product() ) {
			return;
		}

		global $product;

		if ( ! $product instanceof WC_Product ) {
			$product = wc_get_product( get_the_ID() );
		}

		if ( ! $product ) {
			return;
		}

		$pid        = $product->get_id();
		$is_var     = $product->is_type( 'variable' );
		$thumb      = wp_get_attachment_image_url( (int) $product->get_image_id(), 'thumbnail' );
		$thumb      = $thumb ? $thumb : wc_placeholder_img_src( 'thumbnail' );
		$price_html = $product->get_price_html();
		$btn_text   = ! empty( $s['sticky_atc_text'] ) ? $s['sticky_atc_text'] : __( 'Add to Cart', 'cartly' );
		$bar_bg     = ! empty( $s['sticky_atc_bg'] ) ? $s['sticky_atc_bg'] : '#ffffff';
		$show_qty   = ! empty( $s['sticky_atc_show_qty'] );
		$nonce      = wp_create_nonce( 'cartly_nonce' );
		?>
		<div id="cly-satc" class="cly-satc" style="--satc-bg:<?php echo esc_attr( $bar_bg ); ?>;" aria-label="<?php esc_attr_e( 'Quick add to cart', 'cartly' ); ?>">
			<div class="cly-satc__inner">

				<img src="<?php echo esc_url( $thumb ); ?>"
					alt="<?php echo esc_attr( $product->get_name() ); ?>"
					class="cly-satc__img"
					width="50"
					height="50"
					loading="lazy">

				<div class="cly-satc__info">
					<span class="cly-satc__name"><?php echo esc_html( $product->get_name() ); ?></span>
					<span class="cly-satc__price"><?php echo wp_kses_post( $price_html ); ?></span>
				</div>

				<?php if ( $show_qty && ! $is_var ) : ?>
					<div class="cly-satc__qty" role="group" aria-label="<?php esc_attr_e( 'Quantity', 'cartly' ); ?>">
						<button class="cly-satc__qty-btn" data-dir="-1" aria-label="<?php esc_attr_e( 'Decrease quantity', 'cartly' ); ?>">−</button>
						<span class="cly-satc__qty-val" id="cly-satc-qty" aria-live="polite">1</span>
						<button class="cly-satc__qty-btn" data-dir="1" aria-label="<?php esc_attr_e( 'Increase quantity', 'cartly' ); ?>">+</button>
					</div>
				<?php endif; ?>

				<?php if ( $is_var ) : ?>
					<a href="<?php echo esc_url( $product->get_permalink() ); ?>#cly-options"
						class="cly-satc__btn cly-satc__btn--ghost"
						id="cly-satc-btn"
						data-pid="<?php echo esc_attr( (string) $pid ); ?>"
						data-var="1">
						<?php esc_html_e( 'Select Options', 'cartly' ); ?> →
					</a>
				<?php else : ?>
					<button class="cly-satc__btn"
						id="cly-satc-btn"
						data-pid="<?php echo esc_attr( (string) $pid ); ?>"
						data-nonce="<?php echo esc_attr( $nonce ); ?>"
						data-var="0"
						aria-label="<?php echo esc_attr( $btn_text . ' — ' . $product->get_name() ); ?>">
						<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true">
							<circle cx="9" cy="21" r="1"/>
							<circle cx="20" cy="21" r="1"/>
							<path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
						</svg>
						<?php echo esc_html( $btn_text ); ?>
					</button>
				<?php endif; ?>

			</div>
		</div>
		<?php
	}
}