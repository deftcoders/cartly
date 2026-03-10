<?php
/**
 * Codelitix - Cartly Admin Class v7.
 *
 * @package Cartly
 * @author  codelitix
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Cartly Admin class.
 */
class Cartly_Admin {

	/**
	 * Singleton instance.
	 *
	 * @var Cartly_Admin|null
	 */
	private static $instance = null;

	/**
	 * Get or create the singleton instance.
	 *
	 * @return Cartly_Admin
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor - registers admin hooks.
	 */
	private function __construct() {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'wp_ajax_cartly_save_settings', array( $this, 'save_settings' ) );
		add_action( 'wp_ajax_cartly_reset_settings', array( $this, 'reset_settings' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( CARTLY_FILE ), array( $this, 'action_links' ) );
	}

	/**
	 * Add Settings link to the plugin row on the Plugins screen.
	 *
	 * @param array $links Existing action links.
	 * @return array
	 */
	public function action_links( $links ) {
		$settings_link = '<a href="' . esc_url( admin_url( 'admin.php?page=cartly-settings' ) ) . '">'
			. esc_html__( 'Settings', 'cartly' )
			. '</a>';

		array_unshift( $links, $settings_link );

		return $links;
	}

	/**
	 * Register admin menu pages.
	 */
	public function register_menu() {
		// Pre-encoded SVG for admin menu icon — avoids runtime base64_encode().
		$icon = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgc3Ryb2tlPSIjYTdhYWFkIiBzdHJva2Utd2lkdGg9IjIiIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIgc3Ryb2tlLWxpbmVqb2luPSJyb3VuZCI+PGNpcmNsZSBjeD0iOSIgY3k9IjIxIiByPSIxIi8+PGNpcmNsZSBjeD0iMjAiIGN5PSIyMSIgcj0iMSIvPjxwYXRoIGQ9Ik0xIDFoNGwyLjY4IDEzLjM5YTIgMiAwIDAgMCAyIDEuNjFoOS43MmEyIDIgMCAwIDAgMi0xLjYxTDIzIDZINiIvPjwvc3ZnPg==';
		add_menu_page(
			__( 'Cartly Settings', 'cartly' ),
			__( 'Cartly', 'cartly' ),
			'manage_options',
			'cartly-settings',
			array( $this, 'render_page' ),
			$icon,
			56
		);
		add_submenu_page( 'cartly-settings', __( 'Settings', 'cartly' ), __( 'Settings', 'cartly' ), 'manage_options', 'cartly-settings', array( $this, 'render_page' ) );
		add_submenu_page( 'cartly-settings', __( 'Support', 'cartly' ), __( '⭐ Support', 'cartly' ), 'manage_options', 'cartly-support', array( $this, 'render_support' ) );
	}

	/**
	 * Enqueue admin scripts and styles.
	 *
	 * @param string $hook Current admin page hook.
	 */
	public function enqueue_assets( $hook ) {
		if ( false === strpos( $hook, 'cartly-settings' ) ) {
			return;
		}

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( 'cartly-admin', CARTLY_URL . 'admin/css/admin.css', array( 'wp-color-picker' ), CARTLY_VERSION );
		wp_enqueue_script( 'cartly-admin', CARTLY_URL . 'admin/js/admin.js', array( 'jquery', 'wp-color-picker' ), CARTLY_VERSION, true );

		wp_localize_script(
			'cartly-admin',
			'CartlyAdmin',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'cartly_admin_nonce' ),
				'settings' => Cartly_Settings::get_all(),
				'presets'  => Cartly_Settings::preset_colors(),
				'i18n'     => array(
					'saved'        => __( 'Saved!', 'cartly' ),
					'active'       => __( 'Cart Active', 'cartly' ),
					'disabled'     => __( 'Cart Disabled', 'cartly' ),
					'unsaved'      => __( 'You have unsaved changes.', 'cartly' ),
					'confirmReset' => __( 'Reset all settings to defaults? This cannot be undone.', 'cartly' ),
				),
			)
		);
	}

	/**
	 * Render the main settings page.
	 */
	public function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Access denied.', 'cartly' ) );
		}
		$settings = Cartly_Settings::get_all();
		// Codelitix - wrap required for WP admin page positioning.
		echo '<div class="wrap cartly-admin-wrap">';
		include CARTLY_PATH . 'admin/views/admin-panel.php';
		echo '</div>';
	}

	/**
	 * Render the support page.
	 */
	public function render_support() {
		?>
		<div style="max-width:500px;padding:48px 0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;">
			<h2 style="font-size:22px;margin:0 0 8px;"><?php esc_html_e( 'Cartly Support', 'cartly' ); ?></h2>
			<p style="color:#6b7280;margin:0 0 24px;"><?php esc_html_e( 'Need help? Contact codelitix through CodeCanyon.', 'cartly' ); ?></p>
			<a href="https://codecanyon.net/user/codelitix" class="button button-primary" target="_blank"><?php esc_html_e( 'Open Support', 'cartly' ); ?></a>
		</div>
		<?php
	}

	/**
	 * AJAX handler to save settings.
	 */
	public function save_settings() {
		check_ajax_referer( 'cartly_admin_nonce', 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Unauthorized.', 'cartly' ) ), 403 );
		}
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized field-by-field in sanitize().
		$posted = isset( $_POST['settings'] ) ? (array) wp_unslash( $_POST['settings'] ) : array();
		$data   = $this->sanitize( $posted );
		Cartly_Settings::save( $data );
		wp_send_json_success( array( 'message' => __( 'Saved!', 'cartly' ) ) );
	}

	/**
	 * AJAX handler to reset settings to defaults.
	 */
	public function reset_settings() {
		check_ajax_referer( 'cartly_admin_nonce', 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Unauthorized.', 'cartly' ) ), 403 );
		}
		$defaults = cartly_defaults();
		Cartly_Settings::save( $defaults );
		wp_send_json_success(
			array(
				'message'  => __( 'Reset!', 'cartly' ),
				'settings' => $defaults,
			)
		);
	}

	/**
	 * Sanitize raw settings data from POST.
	 *
	 * @param array $data Raw unsanitized settings array.
	 * @return array Sanitized settings.
	 */
	private function sanitize( $data ) {
		$defaults = cartly_defaults();
		$clean    = array();

		// All boolean/checkbox fields.
		$bools = array(
			'enabled',
			'auto_open_atc',
			'auto_open',
			'button_show',
			'button_mobile_bottom',
			'shipping_bar_enabled',
			'upsells_enabled',
			'coupon_enabled',
			'trust_badges_enabled',
			'notice_enabled',
			'sticky_atc_enabled',
			'sticky_atc_show_qty',
			'countdown_enabled',
			'reward_enabled',
			'goals_enabled',
			'trigger_exit_intent',
			'load_only_woo_pages',
			'load_only_when_not_empty',
			'disable_animations',
			// Fix: mobile_cart_enabled and mobile_enabled must be saved.
			'mobile_enabled',
			'mobile_cart_enabled',
			'mobile_swipe_close',
			'mobile_auto_open',
			'upsells_fbt_enabled',
			'smart_recs_enabled',
			'upsells_show_badge',
			'upsells_show_rating',
			'upsells_show_add_btn',
			'upsell_smart_enabled',
			'upsell_show_badge',
			'upsell_show_rating',
			'upsell_show_add_btn',
			'sticky_bar_enabled',
			'wc_fragments_compat',
			'ajax_refresh',
		);
		foreach ( $bools as $f ) {
			// Store explicit booleans so core can check === false safely.
			$clean[ $f ] = ! empty( $data[ $f ] ) ? true : false;
		}

		// Text/select/HTML-safe fields.
		$texts = array(
			'position',
			'animation_style',
			'preset',
			'button_animation',
			'upsell_source',
			'upsells_title',
			'upsells_fbt_title',
			'trust_text',
			'notice_text',
			'sticky_atc_text',
			'countdown_mode',
			'countdown_label',
			'goal_1_label',
			'goal_2_label',
			'goal_3_label',
			'goal_1_icon',
			'goal_2_icon',
			'goal_3_icon',
			'upsells_rule_product_ids',
			'upsells_product_ids',
			'reward_text',
			'reward_bar_style',
			'shipping_message',
			'shipping_success_message',
			'shipping_bar_style',
			'bg_style',
			'shadow_style',
			'font_weight',
			'checkout_btn_style',
			'checkout_btn_hover',
			'hover_effect',
			'upsell_smart_strategy',
			'smart_recs_strategy',
			'upsells_layout',
			'upsell_layout',
			'upsell_display_location',
			'upsells_display_loc',
			'mobile_layout',
			'coupon_label',
			'show_on_pages',
			'hide_on_pages',
			'hide_on_page_ids',
			// Fix: standardise to 'exit_intent_devices' (plural) as canonical key.
			'exit_intent_devices',
			'exit_intent_device',
			'shadow_intensity',
			'user_roles',
			'trigger_event',
		);
		foreach ( $texts as $f ) {
			$clean[ $f ] = isset( $data[ $f ] ) ? wp_kses_post( $data[ $f ] ) : ( $defaults[ $f ] ?? '' );
		}

		// Custom CSS - strip scripts but allow CSS.
		$clean['custom_css'] = isset( $data['custom_css'] ) ? wp_strip_all_tags( $data['custom_css'] ) : '';

		// Apply preset colors first, then individual overrides.
		$preset     = sanitize_text_field( $data['preset'] ?? 'modern' );
		$preset_map = Cartly_Settings::preset_colors();
		if ( isset( $preset_map[ $preset ] ) ) {
			foreach ( $preset_map[ $preset ] as $k => $v ) {
				$clean[ $k ] = $v;
			}
		}

		$color_fields = array(
			'primary_color',
			'secondary_color',
			'bg_color',
			'text_color',
			'button_color',
			'button_text_color',
			'shipping_bar_color',
			'sticky_atc_bg',
			'notice_bg',
		);
		foreach ( $color_fields as $f ) {
			if ( isset( $data[ $f ] ) && ! empty( $data[ $f ] ) ) {
				$val = sanitize_hex_color( $data[ $f ] );
				if ( $val ) {
					$clean[ $f ] = $val;
				}
			}
			if ( empty( $clean[ $f ] ) ) {
				$clean[ $f ] = $defaults[ $f ] ?? '#6c63ff';
			}
		}

		// Integer fields.
		$ints = array(
			'trigger_scroll_percent',
			'trigger_delay_seconds',
			'trigger_exit_delay',
			'drawer_width',
			'goal_1_amount',
			'goal_2_amount',
			'goal_3_amount',
			'upsells_rule_min_cart',
			'border_radius',
			'font_size',
			'upsells_limit',
			'countdown_minutes',
			'reward_goal',
			'reward_threshold',
			'shipping_goal',
			'price_font_size',
			'checkout_btn_radius',
			'button_radius',
		);
		foreach ( $ints as $f ) {
			$clean[ $f ] = isset( $data[ $f ] ) ? absint( $data[ $f ] ) : ( $defaults[ $f ] ?? 0 );
		}

		return $clean;
	}
}
