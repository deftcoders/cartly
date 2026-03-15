<?php
/**
 * Plugin Name:       DeftCoders – Cartly Ajax Side Cart for WooCommerce
 * Description:       Boost WooCommerce conversions with a modern AJAX side cart drawer, floating cart, sticky add to cart bar, smart upsells, free shipping progress, and behavior-based triggers — all customizable with a live visual builder.
 * Version:           1.0.0
 * Author:            DeftCoders
 * Author URI:        https://github.com/deftcoders
 * Text Domain:       cartly
 * Domain Path:       /languages
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * WC requires at least: 7.0
 * WC tested up to:   9.6
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package Cartly
 */

defined( 'ABSPATH' ) || exit;

// ──────────────────────────────────────────────────────────
// Constants.
// ──────────────────────────────────────────────────────────
define( 'CARTLY_VERSION', '1.0.0' );
define( 'CARTLY_FILE', __FILE__ );
define( 'CARTLY_PATH', plugin_dir_path( __FILE__ ) );
define( 'CARTLY_URL', plugin_dir_url( __FILE__ ) );
define( 'CARTLY_ASSETS', CARTLY_URL . 'assets/' );
define( 'CARTLY_TEMPLATES', CARTLY_PATH . 'templates/' );
define( 'CARTLY_INC', CARTLY_PATH . 'includes/' );



// ──────────────────────────────────────────────────────────
// Disable WordPress Emoji.
// Prevents loading the emoji CDN.
// ──────────────────────────────────────────────────────────
add_action( 'init', 'cartly_disable_wp_emoji', 1 );

/**
 * Disable WordPress emoji scripts and styles.
 *
 * Prevents loading the emoji CDN and related scripts
 * on both the front end and admin area.
 *
 * @since 1.0.0
 * @return void
 */
function cartly_disable_wp_emoji() {

	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );

	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );

	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

	add_filter(
		'tiny_mce_plugins',
		function ( $plugins ) {
			if ( is_array( $plugins ) ) {
				return array_diff( $plugins, array( 'wpemoji' ) );
			}
			return array();
		}
	);
}


// ──────────────────────────────────────────────────────────
// Load Text Domain.
// ──────────────────────────────────────────────────────────
/**
 * Load plugin text domain.
 */
function cartly_load_textdomain() {
	load_plugin_textdomain(
		'cartly',
		false,
		dirname( plugin_basename( __FILE__ ) ) . '/languages'
	);
}
add_action( 'plugins_loaded', 'cartly_load_textdomain', 5 );


// ──────────────────────────────────────────────────────────
// WooCommerce HPOS and Blocks Compatibility.
// ──────────────────────────────────────────────────────────
/**
 * Declare WooCommerce HPOS and Blocks compatibility.
 */
function cartly_declare_wc_compatibility() {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
			'custom_order_tables',
			__FILE__,
			true
		);
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
			'cart_checkout_blocks',
			__FILE__,
			true
		);
	}
}
add_action( 'before_woocommerce_init', 'cartly_declare_wc_compatibility' );


// ──────────────────────────────────────────────────────────
// Boot Plugin.
// ──────────────────────────────────────────────────────────
/**
 * Bootstrap the Cartly plugin.
 */
function cartly_init() {

	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'cartly_wc_missing_notice' );
		return;
	}

	require_once CARTLY_INC . 'class-cartly-settings.php';
	require_once CARTLY_INC . 'class-cartly-core.php';
	require_once CARTLY_INC . 'class-cartly-ajax.php';
	require_once CARTLY_INC . 'class-cartly-upsells.php';
	require_once CARTLY_INC . 'class-cartly-sticky-atc.php';

	if ( is_admin() ) {

		require_once CARTLY_INC . 'class-cartly-admin.php';
		require_once CARTLY_INC . 'class-cartly-welcome.php';

		Cartly_Admin::instance();
		Cartly_Welcome::instance();
	}

	Cartly_Core::instance();
	Cartly_Ajax::instance();
	Cartly_Sticky_Atc::instance();
}

add_action( 'plugins_loaded', 'cartly_init', 20 );


// ──────────────────────────────────────────────────────────
// WooCommerce Missing Notice.
// ──────────────────────────────────────────────────────────
/**
 * Display admin notice when WooCommerce is not active.
 */
function cartly_wc_missing_notice() {
	?>
	<div class="notice notice-warning is-dismissible">
		<p>
			<strong><?php esc_html_e( 'Cartly', 'cartly' ); ?></strong>
			<?php
			echo wp_kses_post(
				sprintf(
					/* translators: 1: HTML link to the WooCommerce plugin. */
					__( 'requires %1$s to be installed and active to unlock all features. Please install and activate WooCommerce to continue.', 'cartly' ),
					'<a href="https://wordpress.org/plugins/woocommerce/" target="_blank" rel="noopener noreferrer"><strong>WooCommerce</strong></a>'
				)
			);
			?>
		</p>
	</div>
	<?php
}

// ──────────────────────────────────────────────────────────
// Activation.
// ──────────────────────────────────────────────────────────
register_activation_hook( __FILE__, 'cartly_activate' );

/**
 * Plugin activation handler.
 */
function cartly_activate() {

	// PHP version check.
	if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
		deactivate_plugins( plugin_basename( __FILE__ ) );

		wp_die(
			esc_html(
				sprintf(
					/* translators: 1: Current PHP version number running on the server. */
					__( 'Cartly requires PHP 7.4 or higher. Your server is running PHP %1$s. The plugin has not been activated.', 'cartly' ),
					PHP_VERSION
				)
			),
			esc_html__( 'Plugin Activation Error', 'cartly' ),
			array( 'back_link' => true )
		);
	}

	// WooCommerce dependency check.
	if ( ! class_exists( 'WooCommerce' ) ) {
		deactivate_plugins( plugin_basename( __FILE__ ) );

		wp_die(
			wp_kses_post(
				sprintf(
					/* translators: 1: HTML link to the WooCommerce plugin. */
					__( '<strong>Cartly</strong> requires %1$s to be installed and active. The plugin has not been activated.', 'cartly' ),
					'<a href="https://woocommerce.com" target="_blank" rel="noopener noreferrer">WooCommerce</a>'
				)
			),
			esc_html__( 'Plugin Activation Error', 'cartly' ),
			array( 'back_link' => true )
		);
	}

	if ( ! get_option( 'cartly_settings' ) ) {
		update_option( 'cartly_settings', cartly_defaults(), false );
	}

	set_transient( 'cartly_activated', 1, 30 );
	flush_rewrite_rules();
}

/**
 * Plugin deactivation handler.
 */
function cartly_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'cartly_deactivate' );


// ──────────────────────────────────────────────────────────
// Defaults.
// ──────────────────────────────────────────────────────────
/**
 * Return the default settings array.
 *
 * @return array
 */
function cartly_defaults() {

	return array(

		// Core.
		'enabled'                  => true,
		'load_only_woo_pages'      => false,

		// Cart Setup.
		'position'                 => 'right',
		'animation_style'          => 'slide',
		'auto_open_atc'            => true,

		'button_show'              => true,
		'button_animation'         => 'bounce',

		'shipping_bar_enabled'     => true,
		'shipping_goal'            => 50,
		'shipping_message'         => 'Spend <strong>{amount}</strong> more for <strong>Free Shipping</strong> 🚚',
		'shipping_success_message' => '🎉 You\'ve unlocked Free Shipping!',
		'shipping_bar_style'       => 'rounded',

		'coupon_enabled'           => true,
		'coupon_label'             => 'Apply coupon code',
		'trust_badges_enabled'     => true,
		'trust_text'               => '🔒 Secure Checkout · Free Returns · 24/7 Support',

		'notice_enabled'           => false,
		'notice_text'              => '⚡ Limited stock — order now!',
		'notice_bg'                => '#fff3cd',

		// Appearance.
		'preset'                   => 'modern',
		'drawer_width'             => 420,
		'border_radius'            => 16,
		'shadow_style'             => 'soft',
		'shadow_intensity'         => 'medium',
		'bg_style'                 => 'solid',

		'font_size'                => 14,
		'font_weight'              => '400',
		'price_font_size'          => 15,

		'primary_color'            => '#6c63ff',
		'secondary_color'          => '#a78bfa',
		'bg_color'                 => '#ffffff',
		'text_color'               => '#111827',
		'button_color'             => '#6c63ff',
		'button_text_color'        => '#ffffff',
		'shipping_bar_color'       => '#6c63ff',
		'sticky_atc_bg'            => '#ffffff',

		'checkout_btn_style'       => 'pill',
		'button_radius'            => 12,
		'checkout_btn_hover'       => 'lift',

		// Boost Revenue.
		'upsells_enabled'          => false,
		'upsell_source'            => 'related',
		'upsells_product_ids'      => '',
		'upsells_display_loc'      => 'inside',
		'upsells_title'            => 'You might also like',
		'upsells_limit'            => 3,
		'upsells_fbt_enabled'      => false,
		'upsells_fbt_title'        => 'Frequently Bought Together',

		'smart_recs_enabled'       => false,
		'smart_recs_strategy'      => 'related',

		'upsells_show_badge'       => true,
		'upsells_show_rating'      => false,
		'upsells_show_add_btn'     => true,
		'upsells_layout'           => 'card',

		// Rewards.
		'goals_enabled'            => false,
		'reward_goal'              => 50,
		'reward_bar_style'         => 'rounded',

		'goal_1_amount'            => 50,
		'goal_1_icon'              => '🚚',
		'goal_1_label'             => 'Free Shipping',
		'goal_2_amount'            => 80,
		'goal_2_icon'              => '🎁',
		'goal_2_label'             => 'Free Gift',
		'goal_3_amount'            => 120,
		'goal_3_icon'              => '💰',
		'goal_3_label'             => '10% Off',

		'reward_enabled'           => false,
		'reward_threshold'         => 100,
		'reward_text'              => 'Add {amount} more to unlock 10% off!',

		// Behavior.
		'trigger_event'            => 'auto_open',
		'trigger_scroll_percent'   => 30,
		'trigger_delay_seconds'    => 3,

		'trigger_exit_intent'      => false,
		'exit_intent_devices'      => 'desktop',
		'trigger_exit_delay'       => 500,

		'countdown_enabled'        => false,
		'countdown_mode'           => 'reserve',
		'countdown_minutes'        => 10,

		// Mobile.
		'mobile_enabled'           => true,
		'mobile_layout'            => 'bottom_sheet',
		'mobile_auto_open'         => true,
		'mobile_swipe_close'       => true,
		'button_mobile_bottom'     => true,

		'sticky_atc_enabled'       => true,
		'sticky_atc_text'          => 'Add to Cart',
		'sticky_atc_show_qty'      => true,

		// Advanced.
		'show_on_pages'            => 'all',
		'hide_on_page_ids'         => '',
		'user_roles'               => 'all',
		'ajax_refresh'             => true,
		'wc_fragments_compat'      => true,
		'disable_animations'       => false,
		'sticky_bar_enabled'       => false,
		'custom_css'               => '',
	);
}