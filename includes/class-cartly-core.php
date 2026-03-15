<?php
/**
 * DeftCoders - Cartly Core.
 * Loads minified assets, renders drawer and float button, passes config to JS.
 * Excludes cart/checkout pages. HPOS compatible.
 *
 * @package Cartly
 * @author  DeftCoders
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Cartly Core class.
 */
class Cartly_Core {

	/**
	 * Singleton instance.
	 *
	 * @var Cartly_Core|null
	 */
	private static $instance = null;

	/**
	 * Cached plugin settings.
	 *
	 * @var array
	 */
	private $s;

	/**
	 * Get or create the singleton instance.
	 *
	 * @return Cartly_Core
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor - registers frontend hooks.
	 */
	private function __construct() {
		$this->s = Cartly_Settings::get_all();
		// Master on/off check - explicit false only, default is always on.
		if ( isset( $this->s['enabled'] ) && false === $this->s['enabled'] ) {
			return;
		}
		// Render drawer HTML at priority 1, BEFORE wp_print_footer_scripts (priority 20).
		// This ensures the DOM elements exist when the script executes.
		// The JS also uses $(document).ready() as a belt-and-suspenders fallback.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
		add_action( 'wp_footer', array( $this, 'render_drawer' ), 1 );
		add_action( 'wp_footer', array( $this, 'render_float_btn' ), 1 );
	}

	/**
	 * Decide whether Cartly should load on the current page.
	 * Never loads on checkout (avoids conflicts with payment gateways).
	 *
	 * Fixes applied:
	 *   #3 – Honour hide_on_pages (page IDs) and user_roles display conditions.
	 *   #4 – Honour mobile_cart_enabled toggle (uses wp_is_mobile()).
	 */
	private function should_load() {

		// Always skip checkout.
		if ( is_checkout() ) {
			return false;
		}

		// Respect mobile cart enable toggle.
		if ( wp_is_mobile() && isset( $this->s['mobile_cart_enabled'] ) && false === $this->s['mobile_cart_enabled'] ) {
			return false;
		}

		// WooCommerce-pages-only restriction.
		if ( ! empty( $this->s['load_only_woo_pages'] ) ) {
			if ( ! is_woocommerce() && ! is_cart() && ! is_shop() ) {
				return false;
			}
		}

		// Hide on specific page IDs.
		$hide_raw = trim( $this->s['hide_on_pages'] ?? $this->s['hide_on_page_ids'] ?? '' );
		if ( $hide_raw ) {
			$hide_ids = array_filter( array_map( 'absint', explode( ',', $hide_raw ) ) );
			if ( ! empty( $hide_ids ) && in_array( (int) get_the_ID(), $hide_ids, true ) ) {
				return false;
			}
		}

		// User-role restriction.
		$role_setting = $this->s['user_roles'] ?? 'all';
		if ( 'all' !== $role_setting ) {
			$current_user = wp_get_current_user();
			$user_roles   = (array) $current_user->roles;

			if ( 'logged_in' === $role_setting && ! is_user_logged_in() ) {
				return false;
			}

			if ( 'logged_out' === $role_setting && is_user_logged_in() ) {
				return false;
			}

			// Support comma-separated role slugs e.g. "administrator,editor".
			if ( 'logged_in' !== $role_setting && 'logged_out' !== $role_setting ) {
				$allowed = array_filter( array_map( 'trim', explode( ',', $role_setting ) ) );
				if ( ! empty( $allowed ) && empty( array_intersect( $user_roles, $allowed ) ) ) {
					return false;
				}
			}
		}

		if ( is_cart() && ! empty( $this->s['load_only_woo_pages'] ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Enqueue frontend scripts and styles.
	 */
	public function enqueue() {
		if ( ! $this->should_load() ) {
			return;
		}
		if ( ! empty( $this->s['load_only_when_not_empty'] )
			&& WC()->cart && WC()->cart->is_empty() ) {
			return;
		}

		// Use minified files when not in debug mode.
		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		wp_enqueue_style(
			'cartly',
			CARTLY_ASSETS . 'css/cartly' . $suffix . '.css',
			array(),
			CARTLY_VERSION
		);
		wp_add_inline_style( 'cartly', Cartly_Settings::generate_css_vars() );

		// Custom CSS from admin.
		$custom_css = trim( $this->s['custom_css'] ?? '' );
		if ( $custom_css ) {
			wp_add_inline_style( 'cartly', $custom_css );
		}

		// wc-cart-fragments is optional; register it if WC has not yet.
		$deps = array( 'jquery' );
		if ( wp_script_is( 'wc-cart-fragments', 'registered' ) ) {
			$deps[] = 'wc-cart-fragments';
		}

		wp_enqueue_script(
			'cartly',
			CARTLY_ASSETS . 'js/cartly' . $suffix . '.js',
			$deps,
			CARTLY_VERSION,
			true
		);

		$s = $this->s;

		// The 'Auto Open on Add to Cart' checkbox saves under 'auto_open'.
		// Check both 'auto_open' and 'auto_open_atc' so either key disables auto-open.
		$trigger_event = sanitize_key( $s['trigger_event'] ?? 'auto_open' );
		$auto_open_on  = $s['auto_open'] ?? $s['auto_open_atc'] ?? true;
		if ( 'auto_open' === $trigger_event && false === $auto_open_on ) {
			$trigger_event = 'manual';
		}

		// Resolve single canonical exit_intent_devices key.
		$exit_devices = sanitize_key( $s['exit_intent_devices'] ?? $s['exit_intent_device'] ?? 'desktop' );

		wp_localize_script(
			'cartly',
			'CartlyConfig',
			array(
				'ajax_url'            => admin_url( 'admin-ajax.php' ),
				'nonce'               => wp_create_nonce( 'cartly_nonce' ),
				'preset'              => sanitize_key( $s['preset'] ?? 'modern' ),
				'position'            => sanitize_key( $s['position'] ?? 'right' ),
				'trigger_event'       => $trigger_event,
				'trigger_scroll'      => intval( $s['trigger_scroll_percent'] ?? 30 ),
				'trigger_delay'       => intval( $s['trigger_delay_seconds'] ?? 3 ),
				'trigger_exit_intent' => ! empty( $s['trigger_exit_intent'] ),
				'trigger_exit_delay'  => ! empty( $s['trigger_exit_delay'] ) ? intval( $s['trigger_exit_delay'] ) : 500,
				'exit_devices'        => $exit_devices,
				'mobile_enabled'      => isset( $s['mobile_cart_enabled'] ) ? (bool) $s['mobile_cart_enabled'] : true,
				'button_animation'    => sanitize_key( $s['button_animation'] ?? 'bounce' ),
				'disable_anim'        => ! empty( $s['disable_animations'] ),
				'countdown_enabled'   => ! empty( $s['countdown_enabled'] ),
				'countdown_mins'      => intval( $s['countdown_minutes'] ?? 10 ),
				'countdown_mode'      => sanitize_key( $s['countdown_mode'] ?? 'reserve' ),
				'sticky_atc_enabled'  => ! empty( $s['sticky_atc_enabled'] ),
				'satc_text'           => esc_attr( $s['sticky_atc_text'] ?? __( 'Add to Cart', 'cartly' ) ),
				'sticky_bar'          => ! empty( $s['sticky_bar_enabled'] ),
				'shipping_goal'       => floatval( $s['shipping_goal'] ?? 50 ),
				'shipping_msg'        => wp_kses_post( $s['shipping_message'] ?? '' ),
				'shipping_success'    => wp_kses_post( $s['shipping_success_message'] ?? '' ),
				'reward_goal'         => floatval( $s['reward_goal'] ?? 50 ),
				'reward_text'         => wp_kses_post( $s['reward_text'] ?? '' ),
				'goals_enabled'       => ! empty( $s['goals_enabled'] ),
				'upsells_fbt_enabled' => ! empty( $s['upsells_fbt_enabled'] ),
				'upsells_fbt_title'   => esc_js( $s['upsells_fbt_title'] ?? __( 'Frequently Bought Together', 'cartly' ) ),
				'currency_symbol'     => get_woocommerce_currency_symbol(),
				'cart_url'            => wc_get_cart_url(),
				'checkout_url'        => wc_get_checkout_url(),
			)
		);
	}

	/**
	 * Render the cart drawer HTML in the footer.
	 */
	public function render_drawer() {
		if ( ! $this->should_load() ) {
			return;
		}
		$settings = $this->s;
		$tpl      = CARTLY_TEMPLATES . 'cart-drawer.php';
		if ( file_exists( $tpl ) ) {
			include $tpl;
		}
	}

	/**
	 * Render the floating cart button in the footer.
	 */
	public function render_float_btn() {
		if ( ! $this->should_load() ) {
			return;
		}
		if ( isset( $this->s['button_show'] ) && false === $this->s['button_show'] ) {
			return;
		}

		$pos   = ( isset( $this->s['position'] ) && 'left' === $this->s['position'] ) ? 'cly-float-btn--left' : 'cly-float-btn--right';
		$mob   = ! empty( $this->s['button_mobile_bottom'] ) ? 'cly-float-btn--mob-bottom' : '';
		$count = WC()->cart ? intval( WC()->cart->get_cart_contents_count() ) : 0;
		?>
		<button id="cly-float-btn"
				class="cly-float-btn <?php echo esc_attr( "$pos $mob" ); ?>"
				aria-label="<?php esc_attr_e( 'Open Cart', 'cartly' ); ?>">
			<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
			<?php
			/* translators: %d: number of items in the cart */
			$cartly_count_label = sprintf( _n( '%d item in cart', '%d items in cart', $count, 'cartly' ), $count );
			?>
			<span class="cly-btn-count" data-count="<?php echo esc_attr( (string) $count ); ?>" aria-label="<?php echo esc_attr( $cartly_count_label ); ?>"><?php echo esc_html( (string) $count ); ?></span>
		</button>
		<?php
		// Note: cly-sticky-bar is rendered inside templates/cart-drawer.php.
		// It must NOT be duplicated here - duplicate IDs break jQuery selectors in cartly.js.
	}
}
