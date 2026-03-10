<?php
/**
 * Cartly Welcome / Onboarding Page.
 *
 * Full-screen onboarding experience after activation.
 * Includes 3-step setup wizard and live preview links.
 *
 * @package Cartly
 * @author  Codelitix
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Cartly_Welcome
 *
 * Handles the plugin welcome / onboarding screen.
 */
class Cartly_Welcome {

	/**
	 * Singleton instance.
	 *
	 * @var Cartly_Welcome|null
	 */
	private static $instance = null;

	/**
	 * Get class instance.
	 *
	 * @return Cartly_Welcome
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
		add_action( 'admin_init', array( $this, 'maybe_redirect' ) );
		add_action( 'admin_menu', array( $this, 'register_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	/**
	 * Register hidden welcome page.
	 *
	 * @return void
	 */
	public function register_page() {
		add_submenu_page(
			'',
			__( 'Welcome to Cartly', 'cartly' ),
			__( 'Welcome', 'cartly' ),
			'manage_options',
			'cartly-welcome',
			array( $this, 'render' )
		);
	}

	/**
	 * Redirect to welcome page after activation.
	 *
	 * @return void
	 */
	public function maybe_redirect() {

		if ( ! get_transient( 'cartly_activated' ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		delete_transient( 'cartly_activated' );

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['activate-multi'] ) ) {
			return;
		}

		wp_safe_redirect( admin_url( 'admin.php?page=cartly-welcome' ) );
		exit;
	}

	/**
	 * Enqueue scripts and styles.
	 *
	 * @param string $hook Current admin page hook.
	 * @return void
	 */
	public function enqueue( $hook ) {

		if ( false === strpos( $hook, 'cartly-welcome' ) ) {
			return;
		}

		wp_enqueue_style(
			'cartly-welcome',
			CARTLY_URL . 'admin/css/welcome.css',
			array(),
			CARTLY_VERSION
		);

		wp_enqueue_script(
			'cartly-welcome',
			CARTLY_URL . 'admin/js/welcome.js',
			array( 'jquery' ),
			CARTLY_VERSION,
			true
		);
	}

	/**
	 * Render welcome page.
	 *
	 * @return void
	 */
	public function render() {

		$settings_url = admin_url( 'admin.php?page=cartly-settings' );
		$product_url  = wc_get_page_permalink( 'shop' );

		$products = wc_get_products(
			array(
				'limit'  => 1,
				'status' => 'publish',
			)
		);

		if ( ! empty( $products ) ) {
			$product_url = get_permalink( $products[0]->get_id() );
		}
		?>

		<div class="cartly-welcome-wrap">
		<div class="cw-onboard">

			<div class="cw-onboard__orb cw-onboard__orb--1"></div>
			<div class="cw-onboard__orb cw-onboard__orb--2"></div>
			<div class="cw-onboard__orb cw-onboard__orb--3"></div>

			<div class="cw-onboard__header">
				<div class="cw-onboard__logo">
					<div class="cw-onboard__logo-icon">
						<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
							<circle cx="9" cy="21" r="1"/>
							<circle cx="20" cy="21" r="1"/>
							<path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
						</svg>
					</div>
					<span>Cartly</span>
				</div>

				<a href="<?php echo esc_url( $settings_url ); ?>" class="cw-onboard__skip">
					<?php esc_html_e( 'Skip Setup →', 'cartly' ); ?>
				</a>
			</div>

			<div class="cw-onboard__hero">

				<div class="cw-onboard__badge">
					<span class="cw-onboard__badge-dot"></span>
					<?php esc_html_e( 'Successfully Installed 🎉', 'cartly' ); ?>
				</div>

				<h1 class="cw-onboard__title">
					<?php esc_html_e( 'Welcome to Cartly 🚀', 'cartly' ); ?>
				</h1>

				<p class="cw-onboard__subtitle">
					<?php
					esc_html_e(
						'Boost conversions and average order value with a modern WooCommerce cart drawer, smart upsells, and free shipping goals. Your cart is already live — customize it in seconds.',
						'cartly'
					);
					?>
				</p>

				<p class="cw-onboard__trust">
					<?php esc_html_e( '✅ Works instantly after activation — no coding required.', 'cartly' ); ?>
				</p>

				<div class="cw-onboard__features">
					<?php
					$feats = array(
						array( '🛒', __( 'Floating Cart Drawer', 'cartly' ) ),
						array( '🎯', __( 'Smart Upsells', 'cartly' ) ),
						array( '🚚', __( 'Free Shipping Goals', 'cartly' ) ),
						array( '⚡', __( 'Conversion Triggers', 'cartly' ) ),
					);

					foreach ( $feats as $f ) {
						echo '<div class="cw-onboard__feat">
								<span class="cw-onboard__feat-ico">' . esc_html( $f[0] ) . '</span>
								<span>' . esc_html( $f[1] ) . '</span>
							  </div>';
					}
					?>
				</div>

			</div>

			<div class="cw-onboard__steps">
				<div class="cw-onboard__step cw-onboard__step--active">
					<div class="cw-onboard__step-num">✓</div>
					<div class="cw-onboard__step-body">
						<strong><?php esc_html_e( 'Cart Enabled', 'cartly' ); ?></strong>
						<span><?php esc_html_e( 'Your cart is active on the store', 'cartly' ); ?></span>
					</div>
				</div>

				<div class="cw-onboard__step-line"></div>

				<div class="cw-onboard__step">
					<div class="cw-onboard__step-num">2</div>
					<div class="cw-onboard__step-body">
						<strong><?php esc_html_e( 'Choose Style', 'cartly' ); ?></strong>
						<span><?php esc_html_e( 'Pick your visual preset', 'cartly' ); ?></span>
					</div>
				</div>

				<div class="cw-onboard__step-line"></div>

				<div class="cw-onboard__step">
					<div class="cw-onboard__step-num">3</div>
					<div class="cw-onboard__step-body">
						<strong><?php esc_html_e( 'View Live Cart', 'cartly' ); ?></strong>
						<span><?php esc_html_e( 'See it on your website frontend', 'cartly' ); ?></span>
					</div>
				</div>
			</div>

			<div class="cw-onboard__cta">
				<a href="<?php echo esc_url( $settings_url ); ?>" class="cw-onboard__btn cw-onboard__btn--primary">
					<?php esc_html_e( 'Customize Cart', 'cartly' ); ?>
				</a>

				<a href="<?php echo esc_url( $product_url ); ?>" target="_blank" class="cw-onboard__btn cw-onboard__btn--secondary">
					<?php esc_html_e( 'View Live Demo', 'cartly' ); ?>
				</a>
			</div>

				<div class="cw-onboard__footer">
					<p>
						<?php
						/* translators: %s: HTML link to developer profile on CodeCanyon */
						$footer_text = __(
							'Developed by %s · Premium WooCommerce Conversion Plugin',
							'cartly'
						);

						printf(
							wp_kses(
								$footer_text,
								array(
									'a' => array(
										'href'   => array(),
										'target' => array(),
									),
								)
							),
							'<a href="https://codecanyon.net/user/codelitix" target="_blank">Codelitix</a>'
						);
						?>
					</p>
				</div>

		</div>
		</div>

		<?php
	}
}

Cartly_Welcome::instance();