<?php
/**
 * Codelitix - Cartly Admin Panel v9 - Ultra Premium.
 * Preview: uses OLD working mock with CSS device classes.
 * Tabs: Cart Setup, Appearance, Upsells, Rewards, Behavior, Mobile, Advanced.
 *
 * @package Cartly
 * @author  codelitix
 * @var array<string, mixed> $settings Settings passed into the template.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


$cartly_s = $settings;
$sym      = get_woocommerce_currency_symbol();

$presets = array(
	'modern'     => array(
		'l'  => __( 'Modern', 'cartly' ),
		'bg' => 'linear-gradient(135deg,#6c63ff,#a78bfa)',
	),
	'minimal'    => array(
		'l'  => __( 'Minimal', 'cartly' ),
		'bg' => '#18181b',
	),
	'dark'       => array(
		'l'  => __( 'Dark', 'cartly' ),
		'bg' => 'linear-gradient(135deg,#0f0c29,#302b63)',
	),
	'glass'      => array(
		'l'  => __( 'Glass', 'cartly' ),
		'bg' => 'linear-gradient(135deg,#667eea,#764ba2)',
	),
	'gold'       => array(
		'l'  => __( 'Luxury', 'cartly' ),
		'bg' => 'linear-gradient(135deg,#c9a84c,#6b3a1f)',
	),
	'conversion' => array(
		'l'  => __( 'Convert', 'cartly' ),
		'bg' => 'linear-gradient(135deg,#059669,#0d9488)',
	),
);

if ( ! function_exists( 'cartly_checked_field' ) ) {
	/**
	 * Output a checked attribute for a checkbox field.
	 *
	 * @param mixed $v Field value.
	 */
	function cartly_checked_field( $v ): void {
		checked( $v ); }
}
if ( ! function_exists( 'cartly_selected_field' ) ) {
	/**
	 * Output a selected attribute for a select field.
	 *
	 * @param mixed $a Current value.
	 * @param mixed $b Option value.
	 */
	function cartly_selected_field( $a, $b ): void {
		selected( $a, $b ); }
}

/* ── helpers ── */
if ( ! function_exists( 'cartly_row' ) ) :
	/**
	 * Render a toggle row with label and checkbox.
	 *
	 * @param string $name    Input name attribute.
	 * @param string $label   Row label text.
	 * @param string $sub     Optional sub-label text.
	 * @param string $tip     Optional tooltip text.
	 * @param bool   $checked Whether the checkbox is checked.
	 */
	function cartly_row( $name, $label, $sub = '', $tip = '', $checked = false ): void {
		echo '<div class="cw-row">';
		echo '<div class="cw-row__left"><span class="cw-lbl">' . esc_html( $label ) . '</span>';
		if ( $sub ) {
			echo '<span class="cw-sub">' . esc_html( $sub ) . '</span>';
		}
		echo '</div>';
		echo '<label class="cw-sw"><input type="checkbox" name="' . esc_attr( $name ) . '" ' . ( $checked ? 'checked' : '' ) . '><span class="cw-sw__t"></span></label>';
		echo '</div>';
	}
endif;
if ( ! function_exists( 'cartly_field' ) ) :
	/**
	 * Render a text/number input field.
	 *
	 * @param string $name  Input name attribute.
	 * @param string $label Field label text.
	 * @param mixed  $value Current field value.
	 * @param string $type  Input type attribute.
	 * @param array  $opts  Optional extra attributes (sub, ph, min, max).
	 */
	function cartly_field( $name, $label, $value, $type = 'text', array $opts = array() ): void {
		$sub = $opts['sub'] ?? '';
		$ph  = $opts['ph'] ?? '';
		$min = $opts['min'] ?? '';
		$max = $opts['max'] ?? '';
		echo '<div class="cw-f">';
		echo '<span class="cw-lbl">' . esc_html( $label ) . '</span>';
		if ( $sub ) {
			echo '<span class="cw-sub">' . esc_html( $sub ) . '</span>';
		}
		echo '<input type="' . esc_attr( $type ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( $value ) . '"';
		if ( $ph ) {
			echo ' placeholder="' . esc_attr( $ph ) . '"';
		}
		if ( '' !== $min ) {
			echo ' min="' . esc_attr( $min ) . '"';
		}
		if ( '' !== $max ) {
			echo ' max="' . esc_attr( $max ) . '"';
		}
		echo ' class="cw-inp">';
		echo '</div>';
	}
endif;
if ( ! function_exists( 'cartly_textarea' ) ) :
	/**
	 * Render a textarea field.
	 *
	 * @param string $name  Input name attribute.
	 * @param string $label Field label text.
	 * @param mixed  $value Current field value.
	 * @param string $ph    Placeholder text.
	 */
	function cartly_textarea( $name, $label, $value, $ph = '' ): void {
		echo '<div class="cw-f">';
		echo '<span class="cw-lbl">' . esc_html( $label ) . '</span>';
		echo '<textarea name="' . esc_attr( $name ) . '" class="cw-inp" rows="4" style="height:auto;resize:vertical;" placeholder="' . esc_attr( $ph ) . '">' . esc_textarea( $value ) . '</textarea>';
		echo '</div>';
	}
endif;
if ( ! function_exists( 'cartly_select' ) ) :
	/**
	 * Render a select/dropdown field.
	 *
	 * @param string $name    Input name attribute.
	 * @param string $label   Field label text.
	 * @param mixed  $current Currently selected value.
	 * @param array  $opts    Array of value => label options.
	 * @param string $sub     Optional sub-label text.
	 */
	function cartly_select( $name, $label, $current, array $opts, $sub = '' ): void {
		echo '<div class="cw-f"><span class="cw-lbl">' . esc_html( $label ) . '</span>';
		if ( $sub ) {
			echo '<span class="cw-sub">' . esc_html( $sub ) . '</span>';
		}
		echo '<select name="' . esc_attr( $name ) . '" class="cw-sel">';
		foreach ( $opts as $v => $l ) {
			echo '<option value="' . esc_attr( $v ) . '" ' . selected( $current, $v, false ) . '>' . esc_html( $l ) . '</option>';
		}
		echo '</select></div>';
	}
endif;
if ( ! function_exists( 'cartly_range' ) ) :
	/**
	 * Render a range slider field.
	 *
	 * @param string $name  Input name attribute.
	 * @param string $label Field label text.
	 * @param mixed  $value Current value.
	 * @param mixed  $min   Minimum value.
	 * @param mixed  $max   Maximum value.
	 * @param string $unit  Unit suffix (e.g. px).
	 * @param int    $step  Step increment.
	 * @param string $vid   ID for the value display element.
	 */
	function cartly_range( $name, $label, $value, $min, $max, $unit = 'px', $step = 1, $vid = '' ): void {
		if ( ! $vid ) {
			$vid = 'rv-' . $name;
		}
		echo '<div class="cw-f">';
		echo '<div class="cw-range-row"><span class="cw-lbl">' . esc_html( $label ) . '</span>';
		echo '<span class="cw-range-v" id="' . esc_attr( $vid ) . '">' . esc_attr( $value ) . esc_attr( $unit ) . '</span></div>';
		echo '<input type="range" name="' . esc_attr( $name ) . '" value="' . esc_attr( $value ) . '" min="' . esc_attr( $min ) . '" max="' . esc_attr( $max ) . '" step="' . esc_attr( (string) $step ) . '"';
		echo ' class="cw-range" data-unit="' . esc_attr( $unit ) . '" data-vid="' . esc_attr( $vid ) . '">';
		echo '</div>';
	}
endif;
if ( ! function_exists( 'cartly_sep' ) ) :
	/**
	 * Render a section separator with label.
	 *
	 * @param string $label Section label text.
	 * @param string $sub   Optional sub-label text.
	 */
	function cartly_sep( $label, $sub = '' ): void {
		echo '<div class="cw-sep">' . esc_html( $label ) . '</div>';
		if ( $sub ) {
			echo '<p style="font-size:11px;color:#9ca3af;margin:-6px 0 8px;">' . esc_html( $sub ) . '</p>';
		}
	}
endif;
if ( ! function_exists( 'cartly_info' ) ) :
	/**
	 * Render an info/notice box.
	 *
	 * @param string $ico   Icon character or emoji.
	 * @param string $title Info box title.
	 * @param string $text  Info box body text.
	 * @param string $cls   Optional extra CSS class.
	 */
	function cartly_info( $ico, $title, $text, $cls = '' ): void {
		echo '<div class="cw-info ' . esc_attr( $cls ) . '"><span class="cw-info__ico">' . esc_html( $ico ) . '</span>';
		echo '<div><b>' . esc_html( $title ) . '</b><p>' . esc_html( $text ) . '</p></div></div>';
	}
endif;
?>
<div id="cw">
<input type="hidden" name="preset" id="cw-preset-hidden" value="<?php echo esc_attr( $cartly_s['preset'] ?? 'modern' ); ?>">

<!-- ══════════ TOPBAR ══════════ -->
<div id="cw-top">
	<div class="cw-brand">
	<div class="cw-brand__icon">
		<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
	</div>
	<span class="cw-brand__name">Cartly</span>
	<span class="cw-brand__ver">v<?php echo esc_html( CARTLY_VERSION ); ?></span>
	</div>

	<div class="cw-top-mid">
	<div style="display:flex;align-items:center;gap:8px;">
		<label class="cw-sw"><input type="checkbox" name="enabled" id="cw-master" <?php cartly_checked_field( $cartly_s['enabled'] ?? true ); ?>><span class="cw-sw__t"></span></label>
		<span id="cw-master-lbl">
		<?php if ( ! empty( $cartly_s['enabled'] ?? true ) ) : ?>
		<span class="cw-badge cw-badge--live"><span class="cw-badge__dot"></span><?php esc_html_e( 'Auto Frontend Active', 'cartly' ); ?></span>
		<?php else : ?>
		<span class="cw-badge"><?php esc_html_e( 'Cart Disabled', 'cartly' ); ?></span>
		<?php endif; ?>
		</span>
	</div>
	<span class="cw-badge" id="cw-preset-badge"><?php esc_html_e( 'Preset:', 'cartly' ); ?> <b id="cw-pname"><?php echo esc_html( ucfirst( $cartly_s['preset'] ?? 'Modern' ) ); ?></b></span>
	</div>

	<div class="cw-top-right">
	<span id="cw-status"></span>
	<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" target="_blank" class="cw-btn cw-btn--ghost" style="font-size:11.5px;padding:6px 12px;">
		<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
		<?php esc_html_e( 'View Store', 'cartly' ); ?>
	</a>
	<button class="cw-btn cw-btn--ghost" id="cw-reset">
		<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.51"/></svg>
		<?php esc_html_e( 'Reset', 'cartly' ); ?>
	</button>
	<button class="cw-btn cw-btn--save" id="cw-save">
		<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
		<span class="txt"><?php esc_html_e( 'Save Settings', 'cartly' ); ?></span>
	</button>
	</div>
</div>

<!-- ══════════ BODY ══════════ -->
<div id="cw-body">

<!-- ──── LEFT PANEL ──── -->
<div id="cw-left">

	<!-- PRESETS -->
	<div id="cw-presets">
	<div class="cw-sec-hd">
		<span class="cw-sec-hd__spark">✦</span>
		<?php esc_html_e( 'Style Presets', 'cartly' ); ?>
		<span class="cw-chip"><?php esc_html_e( 'HOT', 'cartly' ); ?></span>
	</div>
	<div class="cw-presets">
		<?php
		foreach ( $presets as $k => $p ) :
			$on = ( ( $cartly_s['preset'] ?? 'modern' ) === $k ) ? 'on' : '';
			?>
		<button class="cw-ps <?php echo esc_attr( $on ); ?>" data-preset="<?php echo esc_attr( $k ); ?>" type="button">
		<span class="cw-ps__dot" style="background:<?php echo esc_attr( $p['bg'] ); ?>"></span>
		<span class="cw-ps__lbl"><?php echo esc_html( $p['l'] ); ?></span>
		</button>
		<?php endforeach; ?>
	</div>
	</div>

	<!-- TABS -->
	<div id="cw-tabs">
	<div class="cw-tab-nav" role="tablist">
		<button class="cw-tn on" data-tab="cart"     role="tab"><span class="cw-tn__ico">🛒</span><?php esc_html_e( 'Cart', 'cartly' ); ?></button>
		<button class="cw-tn"    data-tab="design"   role="tab"><span class="cw-tn__ico">🎨</span><?php esc_html_e( 'Design', 'cartly' ); ?></button>
		<button class="cw-tn"    data-tab="upsells"  role="tab"><span class="cw-tn__ico">🎯</span><?php esc_html_e( 'Upsells', 'cartly' ); ?></button>
		<button class="cw-tn"    data-tab="rewards"  role="tab"><span class="cw-tn__ico">🏆</span><?php esc_html_e( 'Rewards', 'cartly' ); ?></button>
		<button class="cw-tn"    data-tab="triggers" role="tab"><span class="cw-tn__ico">⚡</span><?php esc_html_e( 'Behavior', 'cartly' ); ?></button>
		<button class="cw-tn"    data-tab="mobile"   role="tab"><span class="cw-tn__ico">📱</span><?php esc_html_e( 'Mobile', 'cartly' ); ?></button>
		<button class="cw-tn"    data-tab="advanced" role="tab"><span class="cw-tn__ico">⚙️</span><?php esc_html_e( 'Advanced', 'cartly' ); ?></button>
	</div>

	<!-- ─── CART SETUP ─── -->
	<div class="cw-pane on" data-pane="cart">
		<?php cartly_sep( __( 'Drawer Settings', 'cartly' ) ); ?>
		<div class="cw-f">
		<span class="cw-lbl"><?php esc_html_e( 'Drawer Position', 'cartly' ); ?></span>
		<div class="cw-seg">
			<label class="cw-seg__i <?php echo esc_attr( 'right' === ( $cartly_s['position'] ?? 'right' ) ? 'on' : '' ); ?>">
			<input type="radio" name="position" value="right" <?php cartly_selected_field( $cartly_s['position'] ?? 'right', 'right' ); ?>>
			<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="15" y1="3" x2="15" y2="21"/></svg>
			<?php esc_html_e( 'Right', 'cartly' ); ?>
			</label>
			<label class="cw-seg__i <?php echo esc_attr( 'left' === ( $cartly_s['position'] ?? 'right' ) ? 'on' : '' ); ?>">
			<input type="radio" name="position" value="left" <?php cartly_selected_field( $cartly_s['position'] ?? 'right', 'left' ); ?>>
			<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="9" y1="3" x2="9" y2="21"/></svg>
			<?php esc_html_e( 'Left', 'cartly' ); ?>
			</label>
		</div>
		</div>
		<?php
		cartly_select(
			'animation_style',
			__( 'Open Animation', 'cartly' ),
			$cartly_s['animation_style'] ?? 'slide',
			array(
				'slide'  => __( 'Slide', 'cartly' ),
				'fade'   => __( 'Fade', 'cartly' ),
				'smooth' => __( 'Smooth Ease', 'cartly' ),
				'bounce' => __( 'Bounce', 'cartly' ),
			)
		);
		?>
		<?php cartly_row( 'auto_open', __( 'Auto Open on Add to Cart', 'cartly' ), __( 'Opens drawer when a product is added', 'cartly' ), '', ! empty( $cartly_s['auto_open'] ?? true ) ); ?>

		<?php cartly_sep( __( 'Free Shipping Bar', 'cartly' ), __( 'Proven to increase average order value', 'cartly' ) ); ?>
		<?php cartly_row( 'shipping_bar_enabled', __( 'Enable Shipping Bar', 'cartly' ), '', '', ! empty( $cartly_s['shipping_bar_enabled'] ) ); ?>
		<?php
		/* translators: %s: currency symbol e.g. $ */
		cartly_range( 'shipping_goal', sprintf( __( 'Goal Amount (%s)', 'cartly' ), $sym ), $cartly_s['shipping_goal'] ?? 50, 5, 500, get_woocommerce_currency_symbol(), 5, 'rv-ship-goal' );
		?>
		<?php cartly_field( 'shipping_message', __( 'Progress Message', 'cartly' ), $cartly_s['shipping_message'] ?? '', 'text', array( 'ph' => __( 'Spend {amount} more for Free Shipping 🚚', 'cartly' ) ) ); ?>
		<?php cartly_field( 'shipping_success_message', __( 'Success Message', 'cartly' ), $cartly_s['shipping_success_message'] ?? '', 'text', array( 'ph' => __( "🎉 You've unlocked Free Shipping!", 'cartly' ) ) ); ?>
		<?php
		cartly_select(
			'shipping_bar_style',
			__( 'Bar Style', 'cartly' ),
			$cartly_s['shipping_bar_style'] ?? 'rounded',
			array(
				'rounded' => __( 'Rounded (capsule)', 'cartly' ),
				'line'    => __( 'Thin Line', 'cartly' ),
			)
		);
		?>

		<?php cartly_sep( __( 'Coupon & Trust', 'cartly' ) ); ?>
		<?php cartly_row( 'coupon_enabled', __( 'Coupon Field', 'cartly' ), __( 'Show inside drawer', 'cartly' ), '', ! empty( $cartly_s['coupon_enabled'] ) ); ?>
		<?php cartly_row( 'trust_badges_enabled', __( 'Trust Badges', 'cartly' ), __( 'Secure checkout message at footer', 'cartly' ), '', ! empty( $cartly_s['trust_badges_enabled'] ) ); ?>
		<?php cartly_field( 'trust_text', __( 'Badge Text', 'cartly' ), $cartly_s['trust_text'] ?? '' ); ?>

		<?php cartly_sep( __( 'Cart Notice', 'cartly' ) ); ?>
		<?php cartly_row( 'notice_enabled', __( 'Enable Notice Banner', 'cartly' ), __( 'Announcement bar inside cart', 'cartly' ), '', ! empty( $cartly_s['notice_enabled'] ) ); ?>
		<?php cartly_field( 'notice_text', __( 'Notice Text', 'cartly' ), $cartly_s['notice_text'] ?? '' ); ?>
		<div class="cw-f">
		<span class="cw-lbl"><?php esc_html_e( 'Notice Background', 'cartly' ); ?></span>
		<input type="text" name="notice_bg" value="<?php echo esc_attr( $cartly_s['notice_bg'] ?? '#fff3cd' ); ?>" class="cw-color-picker">
		</div>
	</div>

	<!-- ─── APPEARANCE / DESIGN ─── -->
	<div class="cw-pane" data-pane="design">
		<div class="cw-color-grid">
		<?php
		$colors = array(
			'primary_color'      => __( 'Primary / Accent', 'cartly' ),
			'secondary_color'    => __( 'Secondary', 'cartly' ),
			'bg_color'           => __( 'Cart Background', 'cartly' ),
			'text_color'         => __( 'Text Color', 'cartly' ),
			'button_color'       => __( 'Checkout Button', 'cartly' ),
			'button_text_color'  => __( 'Button Text', 'cartly' ),
			'shipping_bar_color' => __( 'Shipping Bar Fill', 'cartly' ),
		);
		?>
		<?php foreach ( $colors as $n => $l ) : ?>
		<div class="cw-color-f">
			<span class="cw-lbl"><?php echo esc_html( $l ); ?></span>
			<input type="text" name="<?php echo esc_attr( $n ); ?>" value="<?php echo esc_attr( $cartly_s[ $n ] ?? '#6c63ff' ); ?>" class="cw-color-picker">
		</div>
		<?php endforeach; ?>
		</div>

		<?php cartly_sep( __( 'Layout', 'cartly' ) ); ?>
		<?php cartly_range( 'drawer_width', __( 'Cart Width', 'cartly' ), $cartly_s['drawer_width'] ?? 420, 300, 600, 'px', 10, 'rv-width' ); ?>
		<?php cartly_range( 'border_radius', __( 'Border Radius', 'cartly' ), $cartly_s['border_radius'] ?? 16, 0, 32, 'px', 2, 'rv-radius' ); ?>
		<?php
		cartly_select(
			'shadow_style',
			__( 'Shadow Style', 'cartly' ),
			$cartly_s['shadow_style'] ?? 'medium',
			array(
				'none'   => __( 'None', 'cartly' ),
				'light'  => __( 'Light', 'cartly' ),
				'medium' => __( 'Medium', 'cartly' ),
				'heavy'  => __( 'Heavy', 'cartly' ),
			)
		);
		?>
		<?php
		cartly_select(
			'bg_style',
			__( 'Background Style', 'cartly' ),
			$cartly_s['bg_style'] ?? 'solid',
			array(
				'solid'  => __( 'Solid', 'cartly' ),
				'glass'  => __( 'Glass (frosted)', 'cartly' ),
				'subtle' => __( 'Subtle Gradient', 'cartly' ),
			)
		);
		?>

		<?php cartly_sep( __( 'Typography', 'cartly' ) ); ?>
		<?php cartly_range( 'font_size', __( 'Font Size', 'cartly' ), $cartly_s['font_size'] ?? 14, 12, 20, 'px', 1, 'rv-font' ); ?>
		<?php cartly_range( 'price_font_size', __( 'Price Font Size', 'cartly' ), $cartly_s['price_font_size'] ?? 15, 12, 24, 'px', 1, 'rv-pfont' ); ?>
		<?php
		cartly_select(
			'font_weight',
			__( 'Font Weight', 'cartly' ),
			$cartly_s['font_weight'] ?? '400',
			array(
				'300' => __( 'Light (300)', 'cartly' ),
				'400' => __( 'Regular (400)', 'cartly' ),
				'500' => __( 'Medium (500)', 'cartly' ),
				'600' => __( 'Semi-Bold (600)', 'cartly' ),
			)
		);
		?>

		<?php cartly_sep( __( 'Checkout Button', 'cartly' ) ); ?>
		<?php
		cartly_select(
			'checkout_btn_style',
			__( 'Button Style', 'cartly' ),
			$cartly_s['checkout_btn_style'] ?? 'filled',
			array(
				'filled'   => __( 'Filled', 'cartly' ),
				'outline'  => __( 'Outline', 'cartly' ),
				'gradient' => __( 'Gradient', 'cartly' ),
			)
		);
		?>
		<?php cartly_range( 'checkout_btn_radius', __( 'Button Radius', 'cartly' ), $cartly_s['checkout_btn_radius'] ?? 10, 0, 32, 'px', 2, 'rv-btn-r' ); ?>
		<?php
		cartly_select(
			'hover_effect',
			__( 'Hover Effect', 'cartly' ),
			$cartly_s['hover_effect'] ?? 'lift',
			array(
				'none'  => __( 'None', 'cartly' ),
				'lift'  => __( 'Lift', 'cartly' ),
				'glow'  => __( 'Glow', 'cartly' ),
				'scale' => __( 'Scale', 'cartly' ),
			)
		);
		?>
	</div>

	<!-- ─── UPSELLS ─── -->
	<div class="cw-pane" data-pane="upsells">
		<?php cartly_info( '🎯', __( 'Smart Upsell Engine', 'cartly' ), __( 'Auto-suggests related products to boost average order value. Zero config required.', 'cartly' ) ); ?>
		<?php cartly_row( 'upsells_enabled', __( 'Enable Upsells', 'cartly' ), '', '', ! empty( $cartly_s['upsells_enabled'] ) ); ?>
		<?php
		cartly_select(
			'upsell_source',
			__( 'Upsell Source', 'cartly' ),
			$cartly_s['upsell_source'] ?? 'related',
			array(
				'related'   => __( '🔗 Related Products (automatic)', 'cartly' ),
				'crosssell' => __( '🔄 Cross-sells (per product)', 'cartly' ),
				'category'  => __( '📂 Same Category', 'cartly' ),
			),
			__( 'Related: auto-picked by WooCommerce', 'cartly' )
		);
		?>
		<?php cartly_range( 'upsells_limit', __( 'Max Products Shown', 'cartly' ), $cartly_s['upsells_limit'] ?? 3, 1, 8, '', 1, 'rv-upsell-limit' ); ?>
		<?php
		cartly_select(
			'upsell_display_location',
			__( 'Display Location', 'cartly' ),
			$cartly_s['upsell_display_location'] ?? 'inside',
			array(
				'inside' => __( 'Inside Cart (scrollable)', 'cartly' ),
				'below'  => __( 'Below Cart Items', 'cartly' ),
			)
		);
		?>
		<?php cartly_field( 'upsells_title', __( 'Section Title', 'cartly' ), $cartly_s['upsells_title'] ?? 'You might also like' ); ?>

		<?php cartly_sep( __( '⭐ Smart Recommendations', 'cartly' ), __( 'AI-style product strategy', 'cartly' ) ); ?>
		<?php cartly_row( 'upsell_smart_enabled', __( 'Enable Smart Recommendations', 'cartly' ), '', '', ! empty( $cartly_s['upsell_smart_enabled'] ) ); ?>
		<?php
		cartly_select(
			'upsell_smart_strategy',
			__( 'Strategy', 'cartly' ),
			$cartly_s['upsell_smart_strategy'] ?? 'related',
			array(
				'best_sellers'  => __( 'Best Sellers', 'cartly' ),
				'highest_price' => __( 'Highest Price', 'cartly' ),
				'related'       => __( 'Related Products', 'cartly' ),
				'category'      => __( 'Category Match', 'cartly' ),
			)
		);
		?>

		<?php cartly_sep( __( 'Display Options', 'cartly' ) ); ?>
		<?php cartly_row( 'upsell_show_badge', __( 'Show Discount Badge', 'cartly' ), '', '', ! empty( $cartly_s['upsell_show_badge'] ?? true ) ); ?>
		<?php cartly_row( 'upsell_show_rating', __( 'Show Star Ratings', 'cartly' ), '', '', ! empty( $cartly_s['upsell_show_rating'] ?? false ) ); ?>
		<?php cartly_row( 'upsell_show_add_btn', __( 'Show Add Button', 'cartly' ), '', '', ! empty( $cartly_s['upsell_show_add_btn'] ?? true ) ); ?>
		<?php
		cartly_select(
			'upsell_layout',
			__( 'Layout Style', 'cartly' ),
			$cartly_s['upsell_layout'] ?? 'card',
			array(
				'card'    => __( 'Card (image + details)', 'cartly' ),
				'compact' => __( 'Compact', 'cartly' ),
			)
		);
		?>
	</div>

	<!-- ─── REWARDS ─── -->
	<div class="cw-pane" data-pane="rewards">
		<?php cartly_sep( __( 'Free Shipping Goals', 'cartly' ), __( 'Multi-tier milestone progress bar', 'cartly' ) ); ?>
		<?php cartly_row( 'goals_enabled', __( 'Enable Goal Bar', 'cartly' ), __( '3-tier milestone track inside cart', 'cartly' ), '', ! empty( $cartly_s['goals_enabled'] ) ); ?>
		<?php for ( $i = 1;$i <= 3;$i++ ) : ?>
		<div style="display:flex;gap:8px;align-items:flex-end;">
		<div style="flex:0 0 80px;">
			<?php
			/* translators: 1: goal number (1, 2, 3), 2: currency symbol e.g. $ */
			cartly_field( "goal_{$i}_amount", sprintf( __( 'Goal %1$d (%2$s)', 'cartly' ), $i, $sym ), $cartly_s[ "goal_{$i}_amount" ] ?? ( $i * 40 ), 'number', array( 'min' => '1' ) );
			?>
		</div>
		<div style="flex:0 0 50px;"><?php cartly_field( "goal_{$i}_icon", __( 'Icon', 'cartly' ), $cartly_s[ "goal_{$i}_icon" ] ?? '', 'text', array( 'ph' => '🎁' ) ); ?></div>
		<div style="flex:1;"><?php cartly_field( "goal_{$i}_label", __( 'Label', 'cartly' ), $cartly_s[ "goal_{$i}_label" ] ?? '', 'text', array( 'ph' => __( 'Free Shipping', 'cartly' ) ) ); ?></div>
		</div>
		<?php endfor; ?>

		<?php cartly_sep( __( 'Cart Reward Message', 'cartly' ), __( 'Single-goal motivational bar', 'cartly' ) ); ?>
		<?php cartly_row( 'reward_enabled', __( 'Enable Reward Message', 'cartly' ), '', '', ! empty( $cartly_s['reward_enabled'] ) ); ?>
		<?php
		/* translators: %s: currency symbol e.g. $ */
		cartly_range( 'reward_goal', sprintf( __( 'Threshold Amount (%s)', 'cartly' ), $sym ), $cartly_s['reward_goal'] ?? 100, 10, 500, get_woocommerce_currency_symbol(), 5, 'rv-reward-goal' );
		?>
		<?php cartly_field( 'reward_text', __( 'Reward Text', 'cartly' ), $cartly_s['reward_text'] ?? '', 'text', array( 'ph' => __( 'Add {amount} more for 10% off!', 'cartly' ) ) ); ?>

		<?php cartly_sep( __( '⏰ Countdown Timer', 'cartly' ), __( 'Add urgency at checkout', 'cartly' ) ); ?>
		<?php cartly_row( 'countdown_enabled', __( 'Enable Countdown Timer', 'cartly' ), __( 'Appears inside open cart drawer', 'cartly' ), '', ! empty( $cartly_s['countdown_enabled'] ) ); ?>
		<?php
		cartly_select(
			'countdown_mode',
			__( 'Timer Mode', 'cartly' ),
			$cartly_s['countdown_mode'] ?? 'reserve',
			array(
				'reserve'  => __( '🛒 Reserve Cart – holds for X minutes', 'cartly' ),
				'discount' => __( '⚡ Urgency Only – discount expires', 'cartly' ),
				'freeship' => __( '🚚 Free Shipping Offer Ends', 'cartly' ),
			)
		);
		?>
		<?php cartly_range( 'countdown_minutes', __( 'Duration (minutes)', 'cartly' ), $cartly_s['countdown_minutes'] ?? 10, 1, 60, 'min', 1, 'rv-cd-mins' ); ?>
	</div>

	<!-- ─── BEHAVIOR / TRIGGERS ─── -->
	<div class="cw-pane" data-pane="triggers">
		<?php cartly_sep( __( 'Auto Open Behavior', 'cartly' ) ); ?>
		<?php cartly_row( 'auto_open', __( 'Auto Open on Add to Cart', 'cartly' ), __( 'Opens drawer the moment a product is added', 'cartly' ), '', ! empty( $cartly_s['auto_open'] ?? true ) ); ?>
		<?php
		cartly_select(
			'trigger_event',
			__( 'Open Trigger', 'cartly' ),
			$cartly_s['trigger_event'] ?? 'auto_open',
			array(
				'auto_open' => __( '🛒 Auto-open on Add to Cart', 'cartly' ),
				'scroll'    => __( '📜 After Scroll %', 'cartly' ),
				'delay'     => __( '⏱ After Delay (seconds)', 'cartly' ),
				'manual'    => __( '🖱 Manual Only', 'cartly' ),
			)
		);
		?>

		<div class="cw-f">
		<div class="cw-range-row">
			<span class="cw-lbl"><?php esc_html_e( 'Scroll Trigger (%)', 'cartly' ); ?></span>
			<span class="cw-range-v" id="rv-scroll"><?php echo esc_attr( $cartly_s['trigger_scroll_percent'] ?? 30 ); ?>%</span>
		</div>
		<input type="range" name="trigger_scroll_percent" value="<?php echo esc_attr( $cartly_s['trigger_scroll_percent'] ?? 30 ); ?>" min="10" max="90" step="5" class="cw-range" data-unit="%" data-vid="rv-scroll">
		<span class="cw-sub"><?php esc_html_e( 'Cart opens after scrolling this % of page. Used when trigger is "After Scroll".', 'cartly' ); ?></span>
		</div>
		<div class="cw-f">
		<div class="cw-range-row">
			<span class="cw-lbl"><?php esc_html_e( 'Delay Trigger (seconds)', 'cartly' ); ?></span>
			<span class="cw-range-v" id="rv-delay"><?php echo esc_attr( $cartly_s['trigger_delay_seconds'] ?? 3 ); ?>s</span>
		</div>
		<input type="range" name="trigger_delay_seconds" value="<?php echo esc_attr( $cartly_s['trigger_delay_seconds'] ?? 3 ); ?>" min="1" max="30" step="1" class="cw-range" data-unit="s" data-vid="rv-delay">
		</div>

		<?php cartly_sep( __( 'Exit Intent', 'cartly' ) ); ?>
		<?php cartly_row( 'trigger_exit_intent', __( 'Enable Exit Intent', 'cartly' ), __( 'Opens cart when cursor leaves window', 'cartly' ), '', ! empty( $cartly_s['trigger_exit_intent'] ) ); ?>
		<?php
		cartly_select(
			'exit_intent_device',
			__( 'Devices', 'cartly' ),
			$cartly_s['exit_intent_device'] ?? 'desktop',
			array(
				'desktop' => __( 'Desktop Only', 'cartly' ),
				'all'     => __( 'All Devices', 'cartly' ),
			)
		);
		?>

		<?php cartly_sep( __( 'Sticky Add-to-Cart Bar', 'cartly' ), __( 'Product pages only ⭐', 'cartly' ) ); ?>
		<?php cartly_row( 'sticky_atc_enabled', __( 'Enable Sticky ATC Bar', 'cartly' ), __( 'Appears when native ATC button scrolls out of view', 'cartly' ), '', ! empty( $cartly_s['sticky_atc_enabled'] ) ); ?>
		<?php cartly_field( 'sticky_atc_text', __( 'Button Label', 'cartly' ), $cartly_s['sticky_atc_text'] ?? 'Add to Cart' ); ?>
		<?php cartly_row( 'sticky_atc_show_qty', __( 'Show Quantity Selector', 'cartly' ), '', '', ! empty( $cartly_s['sticky_atc_show_qty'] ?? true ) ); ?>
	</div>

	<!-- ─── MOBILE ─── -->
	<div class="cw-pane" data-pane="mobile">
		<?php cartly_info( '📱', __( 'Mobile-First Design', 'cartly' ), __( 'Bottom sheet drawer, swipe-to-close gesture, sticky bottom cart button. All built-in.', 'cartly' ), 'cw-info--green' ); ?>
		<?php cartly_row( 'mobile_cart_enabled', __( 'Enable Mobile Cart', 'cartly' ), __( 'Cart on mobile devices', 'cartly' ), '', ! empty( $cartly_s['mobile_cart_enabled'] ?? true ) ); ?>
		<div class="cw-f">
		<span class="cw-lbl"><?php esc_html_e( 'Layout Style', 'cartly' ); ?></span>
		<div class="cw-seg">
			<?php $ml = $cartly_s['mobile_layout'] ?? 'bottom_sheet'; ?>
			<label class="cw-seg__i <?php echo esc_attr( 'bottom_sheet' === $ml ? 'on' : '' ); ?>">
			<input type="radio" name="mobile_layout" value="bottom_sheet" <?php cartly_selected_field( $ml, 'bottom_sheet' ); ?>>
			<?php esc_html_e( 'Bottom Sheet', 'cartly' ); ?>
			</label>
			<label class="cw-seg__i <?php echo esc_attr( 'fullscreen' === $ml ? 'on' : '' ); ?>">
			<input type="radio" name="mobile_layout" value="fullscreen" <?php cartly_selected_field( $ml, 'fullscreen' ); ?>>
			<?php esc_html_e( 'Full Screen', 'cartly' ); ?>
			</label>
		</div>
		</div>
		<?php cartly_row( 'mobile_auto_open', __( 'Auto Open on Mobile', 'cartly' ), __( 'Opens cart on mobile when product added', 'cartly' ), '', ! empty( $cartly_s['mobile_auto_open'] ?? true ) ); ?>
		<?php cartly_row( 'mobile_swipe_close', __( 'Swipe to Close', 'cartly' ), __( 'Swipe-down gesture closes bottom sheet', 'cartly' ), '', ! empty( $cartly_s['mobile_swipe_close'] ?? true ) ); ?>

		<?php cartly_sep( __( 'Floating Button', 'cartly' ) ); ?>
		<?php cartly_row( 'button_show', __( 'Show Floating Cart Button', 'cartly' ), '', '', ! empty( $cartly_s['button_show'] ?? true ) ); ?>
		<?php cartly_row( 'button_mobile_bottom', __( 'Move Button to Bottom on Mobile', 'cartly' ), '', '', ! empty( $cartly_s['button_mobile_bottom'] ?? true ) ); ?>
		<?php
		cartly_select(
			'button_animation',
			__( 'Button Animation', 'cartly' ),
			$cartly_s['button_animation'] ?? 'bounce',
			array(
				'bounce' => __( 'Bounce', 'cartly' ),
				'shake'  => __( 'Shake', 'cartly' ),
				'none'   => __( 'None', 'cartly' ),
			)
		);
		?>

		<?php cartly_sep( __( 'Sticky Summary Bar', 'cartly' ) ); ?>
		<?php cartly_row( 'sticky_bar_enabled', __( 'Enable Sticky Bar', 'cartly' ), __( 'Shows "3 items · $89 · View Cart" bar', 'cartly' ), '', ! empty( $cartly_s['sticky_bar_enabled'] ) ); ?>
	</div>

	<!-- ─── ADVANCED ─── -->
	<div class="cw-pane" data-pane="advanced">
		<?php cartly_sep( __( 'Display Conditions', 'cartly' ) ); ?>
		<?php
		cartly_select(
			'show_on_pages',
			__( 'Show On Pages', 'cartly' ),
			$cartly_s['show_on_pages'] ?? 'all',
			array(
				'all'  => __( 'All Pages', 'cartly' ),
				'woo'  => __( 'WooCommerce Pages Only', 'cartly' ),
				'shop' => __( 'Shop + Product Pages Only', 'cartly' ),
			),
			__( 'Restrict which pages load the cart.', 'cartly' )
		);
		?>
		<?php cartly_field( 'hide_on_pages', __( 'Hide On Pages (Page IDs)', 'cartly' ), $cartly_s['hide_on_pages'] ?? '', 'text', array( 'ph' => __( '42, 87, 114 (comma separated)', 'cartly' ) ) ); ?>

		<?php cartly_sep( __( 'Performance', 'cartly' ) ); ?>
		<?php cartly_row( 'load_only_woo_pages', __( 'WooCommerce Pages Only', 'cartly' ), __( 'Off = load everywhere (default)', 'cartly' ), '', ! empty( $cartly_s['load_only_woo_pages'] ) ); ?>
		<?php cartly_row( 'load_only_when_not_empty', __( 'Skip When Cart Empty', 'cartly' ), '', '', ! empty( $cartly_s['load_only_when_not_empty'] ) ); ?>
		<?php cartly_row( 'wc_fragments_compat', __( 'WooCommerce Fragments Compatibility', 'cartly' ), __( 'Real-time updates via WC fragments', 'cartly' ), '', ! empty( $cartly_s['wc_fragments_compat'] ?? true ) ); ?>
		<?php cartly_row( 'disable_animations', __( 'Disable All Animations', 'cartly' ), __( 'Performance / accessibility mode', 'cartly' ), '', ! empty( $cartly_s['disable_animations'] ) ); ?>

		<?php cartly_sep( __( 'Custom CSS', 'cartly' ), __( 'Override without editing plugin files', 'cartly' ) ); ?>
		<?php cartly_textarea( 'custom_css', __( 'Custom CSS', 'cartly' ), $cartly_s['custom_css'] ?? '', '/* .cly-drawer { } */' ); ?>

		<?php cartly_sep( __( 'Compatibility', 'cartly' ) ); ?>
		<div class="cw-compat">
		<span><?php esc_html_e( '✅ HPOS / Custom Order Tables', 'cartly' ); ?></span>
		<span><?php esc_html_e( '✅ WooCommerce Blocks', 'cartly' ); ?></span>
		<span><?php esc_html_e( '✅ Simple & Variable Products', 'cartly' ); ?></span>
		<span><?php esc_html_e( '✅ Guest Checkout', 'cartly' ); ?></span>
		<span><?php esc_html_e( '✅ PHP 8.2+', 'cartly' ); ?></span>
		<span><?php esc_html_e( '✅ WC 7.0+', 'cartly' ); ?></span>
		</div>

	</div>

	</div><!-- /#cw-tabs -->
</div><!-- /#cw-left -->

<!-- ──── RIGHT PREVIEW ──── -->
<div id="cw-right">

	<div id="cw-preview-wrap">

	<div id="cw-prev-bar">
	<div class="cw-prev-bar__l">
		<span class="cw-dot cw-dot--r"></span>
		<span class="cw-dot cw-dot--y"></span>
		<span class="cw-dot cw-dot--g"></span>
		<span class="cw-prev-bar__lbl"><?php esc_html_e( 'Live Cart Preview', 'cartly' ); ?></span>
	</div>
	<div class="cw-devs">
		<button class="cw-dv on" data-dev="desk" title="<?php esc_attr_e( 'Desktop', 'cartly' ); ?>">
		<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
		<span style="font-size:10px;font-weight:700;"><?php esc_html_e( 'Desktop', 'cartly' ); ?></span>
		</button>
		<button class="cw-dv" data-dev="tab" title="<?php esc_attr_e( 'Tablet', 'cartly' ); ?>">
		<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="4" y="2" width="16" height="20" rx="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
		<span style="font-size:10px;font-weight:700;"><?php esc_html_e( 'Tablet', 'cartly' ); ?></span>
		</button>
		<button class="cw-dv" data-dev="mob" title="<?php esc_attr_e( 'Mobile', 'cartly' ); ?>">
		<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="5" y="2" width="14" height="20" rx="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
		<span style="font-size:10px;font-weight:700;"><?php esc_html_e( 'Mobile', 'cartly' ); ?></span>
		</button>
	</div>
	</div>

	<div id="cw-stage">
	<div class="cw-mock" id="cw-mock">
		<!-- HEADER -->
		<div class="cw-m-hd">
		<div class="cw-m-hd__l">
			<div class="cw-m-ico" id="m-ico">
			<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
			</div>
			<span class="cw-m-title"><?php esc_html_e( 'Your Cart', 'cartly' ); ?></span>
			<span class="cw-m-bdg" id="m-bdg">3</span>
		</div>
		<button class="cw-m-cls">&#x2715;</button>
		</div>
		<!-- SHIPPING BAR -->
		<div class="cw-m-ship">
		<p class="cw-m-ship-msg"><?php esc_html_e( 'Spend $12 more for Free Shipping 🚚', 'cartly' ); ?></p>
		<div class="cw-m-ship-track"><div class="cw-m-ship-fill" id="m-bar" style="width:76%"></div></div>
		</div>
		<!-- ITEMS -->
		<div class="cw-m-body">
		<div class="cw-m-item">
			<div class="cw-m-thumb cw-m-thumb--a"></div>
			<div style="flex:1;min-width:0;">
			<div class="cw-m-iname"><?php esc_html_e( 'Premium Wireless Headphones', 'cartly' ); ?></div>
			<div class="cw-m-imeta"><?php esc_html_e( 'Color: Black · Size: M', 'cartly' ); ?></div>
			<div class="cw-m-irow">
				<div class="cw-m-qty">&#8722; &nbsp;1&nbsp; +</div>
				<b class="cw-m-price" id="m-p1">$49.00</b>
			</div>
			</div>
		</div>
		<div class="cw-m-item">
			<div class="cw-m-thumb cw-m-thumb--b"></div>
			<div style="flex:1;min-width:0;">
			<div class="cw-m-iname"><?php esc_html_e( 'Leather Bifold Wallet', 'cartly' ); ?></div>
			<div class="cw-m-imeta"><?php esc_html_e( 'Brown · Premium', 'cartly' ); ?></div>
			<div class="cw-m-irow">
				<div class="cw-m-qty">&#8722; &nbsp;2&nbsp; +</div>
				<b class="cw-m-price" id="m-p2">$58.00</b>
			</div>
			</div>
		</div>
		</div>
		<!-- FOOTER -->
		<div class="cw-m-ft">
		<div class="cw-m-subrow">
			<span><?php esc_html_e( 'Subtotal', 'cartly' ); ?></span>
			<b class="cw-m-sub" id="m-sub">$107.00</b>
		</div>
		<button class="cw-m-checkout" id="m-chk">
			<?php esc_html_e( 'Checkout · $107.00', 'cartly' ); ?>
			<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
		</button>
		<div class="cw-m-trust"><?php esc_html_e( '🔒 Secure Checkout · Free Returns · 24/7 Support', 'cartly' ); ?></div>
		</div>
	</div>
	</div>

	</div><!-- /#cw-preview-wrap -->

	<!-- FEATURE PILLS -->
	<div id="cw-prev-status">
	<span class="cw-badge cw-badge--live"><span class="cw-badge__dot"></span><?php esc_html_e( 'Auto Frontend', 'cartly' ); ?></span>
	<span class="cw-fp cw-fp--new"><?php esc_html_e( '✦ Smart Upsells', 'cartly' ); ?></span>
	<span class="cw-fp cw-fp--hot"><?php esc_html_e( '🔥 Free Shipping Bar', 'cartly' ); ?></span>
	<span class="cw-fp cw-fp--pro"><?php esc_html_e( '⭐ 6 Presets', 'cartly' ); ?></span>
	<span class="cw-fp cw-fp--new"><?php esc_html_e( '⏰ Countdown Timer', 'cartly' ); ?></span>
	<span class="cw-fp cw-fp--hot"><?php esc_html_e( '🏆 Reward Goals', 'cartly' ); ?></span>
	<span class="cw-fp cw-fp--pro"><?php esc_html_e( '📱 Mobile Sheet', 'cartly' ); ?></span>
	<span class="cw-fp cw-fp--new"><?php esc_html_e( '⚡ Sticky ATC', 'cartly' ); ?></span>
	</div>

</div><!-- /#cw-right -->
</div><!-- /#cw-body -->
</div><!-- /#cw -->
