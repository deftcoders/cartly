<?php
/**
 * Cart drawer template.
 *
 * Features: Smart triggers, multi-tier cart goals, enhanced countdown, and FBT upsells.
 *
 * @package Cartly
 * @author  Codelitix
 * @var array<string, mixed> $settings Settings passed into the template.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$cartly_s = $settings;
$pos      = ( isset( $cartly_s['position'] ) && 'left' === $cartly_s['position'] ) ? 'cly-drawer--left' : 'cly-drawer--right';
$pre      = 'cly-preset-' . sanitize_html_class( $cartly_s['preset'] ?? 'modern' );
$ani      = 'cly-anim-' . sanitize_html_class( $cartly_s['animation_style'] ?? 'slide' );
?>
<div id="cly-overlay" class="cly-overlay" aria-hidden="true"></div>

<div id="cly-drawer"
	class="cly-drawer <?php echo esc_attr( "$pos $pre $ani" ); ?>"
	role="dialog" aria-modal="true"
	aria-label="<?php esc_attr_e( 'Shopping Cart', 'cartly' ); ?>"
	tabindex="-1">

	<!-- ── HEADER ─────────────────────────────────────── -->
	<div class="cly-drawer__hd">
	<div class="cly-drawer__hd-l">
		<span class="cly-drawer__ico">
		<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
		</span>
		<h2 class="cly-drawer__title"><?php esc_html_e( 'Your Cart', 'cartly' ); ?></h2>
		<span class="cly-drawer__cnt" id="cly-count">0</span>
	</div>
	<button class="cly-drawer__close" id="cly-close" aria-label="<?php esc_attr_e( 'Close cart', 'cartly' ); ?>">
		<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
	</button>
	</div>

	<!-- ── NOTICE BANNER ──────────────────────────────── -->
	<?php if ( ! empty( $cartly_s['notice_enabled'] ) && ! empty( $cartly_s['notice_text'] ) ) : ?>
	<div class="cly-notice" style="background:<?php echo esc_attr( $cartly_s['notice_bg'] ?? '#fff3cd' ); ?>;" role="alert">
		<?php echo wp_kses_post( $cartly_s['notice_text'] ?? '' ); ?>
	</div>
	<?php endif; ?>

	<!-- ── FEATURE 3: ENHANCED COUNTDOWN TIMER ────────── -->
	<?php
	if ( ! empty( $cartly_s['countdown_enabled'] ) ) :
		$cartly_mode = $cartly_s['countdown_mode'] ?? 'reserve';
		$label       = 'discount' === $cartly_mode ? ( $cartly_s['countdown_label_discount'] ?? __( '⚡ Discount expires in', 'cartly' ) )
			: ( 'freeship' === $cartly_mode ? ( $cartly_s['countdown_label_freeship'] ?? __( '🚚 Free shipping ends in', 'cartly' ) )
											: ( $cartly_s['countdown_label'] ?? __( 'Cart reserved for', 'cartly' ) ) );
		?>
	<div class="cly-countdown cly-countdown--<?php echo esc_attr( $cartly_mode ); ?>" id="cly-countdown" style="display:none;" role="timer" aria-live="polite">
	<span class="cly-countdown__lbl"><?php echo wp_kses_post( $label ); ?></span>
	<span class="cly-countdown__tmr" id="cly-timer">
		<span class="cly-countdown__digits" id="cly-timer-min">10</span>
		<span class="cly-countdown__colon">:</span>
		<span class="cly-countdown__digits" id="cly-timer-sec">00</span>
	</span>
	</div>
		<?php endif; ?>

	<!-- ── FREE SHIPPING BAR ───────────────────────────── -->
	<?php if ( ! empty( $cartly_s['shipping_bar_enabled'] ) ) : ?>
	<div class="cly-ship" id="cly-ship" aria-live="polite">
	<p class="cly-ship__msg" id="cly-ship-msg"><?php echo wp_kses_post( $cartly_s['shipping_message'] ?? '' ); ?></p>
	<div class="cly-ship__track">
		<div class="cly-ship__fill" id="cly-ship-fill" style="width:0%" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
	</div>
	</div>
	<?php endif; ?>

	<!-- ── FEATURE 5: MULTI-TIER CART GOALS ───────────── -->
	<?php
	if ( ! empty( $cartly_s['goals_enabled'] ) ) :
		$goals = array(
			1 => array(
				'amount' => $cartly_s['goal_1_amount'] ?? 0,
				'icon'   => $cartly_s['goal_1_icon'] ?? '',
				'label'  => $cartly_s['goal_1_label'] ?? '',
			),
			2 => array(
				'amount' => $cartly_s['goal_2_amount'] ?? 0,
				'icon'   => $cartly_s['goal_2_icon'] ?? '',
				'label'  => $cartly_s['goal_2_label'] ?? '',
			),
			3 => array(
				'amount' => $cartly_s['goal_3_amount'] ?? 0,
				'icon'   => $cartly_s['goal_3_icon'] ?? '',
				'label'  => $cartly_s['goal_3_label'] ?? '',
			),
		);
		?>
	<div class="cly-goals" id="cly-goals" aria-live="polite">
	<div class="cly-goals__track">
		<div class="cly-goals__fill" id="cly-goals-fill" style="width:0%"></div>
		<?php foreach ( $goals as $i => $g ) : ?>
		<div class="cly-goals__marker cly-goals__marker--<?php echo esc_attr( (string) $i ); ?>"
			style="left:<?php echo esc_attr( (string) ( $i * 33.33 ) ); ?>%"
			data-amount="<?php echo esc_attr( (string) $g['amount'] ); ?>"
			data-label="<?php echo esc_attr( $g['label'] ); ?>"
			id="cly-goal-<?php echo esc_attr( (string) $i ); ?>">
		<span class="cly-goals__ico"><?php echo esc_html( $g['icon'] ); ?></span>
		<span class="cly-goals__lbl"><?php echo esc_html( $g['label'] ); ?></span>
		</div>
		<?php endforeach; ?>
	</div>
	<p class="cly-goals__msg" id="cly-goals-msg"></p>
	</div>
		<?php endif; ?>

	<!-- ── SCROLLABLE BODY ─────────────────────────────── -->
	<div class="cly-drawer__body" id="cly-body">
	<div class="cly-skeleton" id="cly-skeleton">
		<div class="cly-skeleton__row"></div>
		<div class="cly-skeleton__row"></div>
	</div>
	<div class="cly-empty" id="cly-empty" style="display:none;">
		<div class="cly-empty__ico">
		<svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
		</div>
		<p class="cly-empty__title"><?php esc_html_e( 'Your cart is empty', 'cartly' ); ?></p>
		<p class="cly-empty__sub"><?php esc_html_e( 'Add something you love', 'cartly' ); ?></p>
		<button class="cly-btn-ghost" id="cly-continue-empty"><?php esc_html_e( 'Start Shopping', 'cartly' ); ?></button>
	</div>
	<ul class="cly-items" id="cly-items" aria-label="<?php esc_attr_e( 'Cart items', 'cartly' ); ?>"></ul>

	<!-- UPSELLS (also used for FBT) -->
	<?php
	if ( ! empty( $cartly_s['upsells_enabled'] ) ) :
		$cartly_title = ! empty( $cartly_s['upsells_title'] ) ? $cartly_s['upsells_title'] : __( 'You might also like', 'cartly' );
		?>
	<div class="cly-upsells" id="cly-upsells" style="display:none;" aria-label="<?php esc_attr_e( 'Product suggestions', 'cartly' ); ?>">
		<div class="cly-upsells__title" id="cly-upsells-title"><?php echo esc_html( $cartly_title ); ?></div>
		<div class="cly-upsells__list" id="cly-upsells-list"></div>
	</div>
	<?php endif; ?>
	</div>

	<!-- ── FOOTER ──────────────────────────────────────── -->
	<div class="cly-drawer__ft" id="cly-ft" style="display:none;">

	<?php if ( ! empty( $cartly_s['reward_enabled'] ) ) : ?>
	<div class="cly-reward" id="cly-reward" style="display:none;">
		<span class="cly-reward__ico">🎁</span>
		<div class="cly-reward__wrap">
		<p class="cly-reward__msg" id="cly-reward-msg"></p>
		<div class="cly-reward__track"><div class="cly-reward__fill" id="cly-reward-fill" style="width:0%"></div></div>
		</div>
	</div>
	<?php endif; ?>

	<?php if ( ! empty( $cartly_s['coupon_enabled'] ) ) : ?>
	<div class="cly-coupon" id="cly-coupon">
		<button class="cly-coupon__tog" id="cly-coup-tog" aria-expanded="false">
		<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 12 20 22 4 22 4 12"/><rect x="2" y="7" width="20" height="5"/><path d="M12 22V7M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7zM12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/></svg>
		<?php echo esc_html( ! empty( $cartly_s['coupon_label'] ) ? $cartly_s['coupon_label'] : __( 'Apply coupon code', 'cartly' ) ); ?>
		<svg class="cly-coupon__arr" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
		</button>
		<div class="cly-coupon__body" id="cly-coup-body" style="display:none;">
		<div class="cly-coupon__row">
			<input type="text" id="cly-coup-inp" class="cly-coupon__inp" placeholder="<?php esc_attr_e( 'Enter coupon code', 'cartly' ); ?>" autocomplete="off">
			<button id="cly-coup-apply" class="cly-coupon__apply"><?php esc_html_e( 'Apply', 'cartly' ); ?></button>
		</div>
		<p class="cly-coupon__msg" id="cly-coup-msg" aria-live="polite"></p>
		</div>
		<div class="cly-coupon__tags" id="cly-coup-tags"></div>
	</div>
	<?php endif; ?>

	<div class="cly-summary">
		<div class="cly-summary__row">
		<span><?php esc_html_e( 'Subtotal', 'cartly' ); ?></span>
		<strong class="cly-summary__val" id="cly-subtotal"></strong>
		</div>
	</div>

	<?php if ( ! empty( $cartly_s['trust_badges_enabled'] ) && ! empty( $cartly_s['trust_text'] ) ) : ?>
	<div class="cly-trust"><p><?php echo wp_kses_post( $cartly_s['trust_text'] ?? '' ); ?></p></div>
	<?php endif; ?>

	<div class="cly-actions">
		<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="cly-btn-primary" id="cly-checkout">
		<?php esc_html_e( 'Checkout', 'cartly' ); ?> &mdash;
		<span id="cly-checkout-sub"></span>
		<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
		</a>
		<div class="cly-actions__row">
		<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="cly-btn-ghost"><?php esc_html_e( 'View Cart', 'cartly' ); ?></a>
		<button class="cly-btn-ghost" id="cly-continue"><?php esc_html_e( 'Continue Shopping', 'cartly' ); ?></button>
		</div>
	</div>
	</div>

	<!-- STICKY SUMMARY BAR (floating when drawer closed) -->
	<?php if ( ! empty( $cartly_s['sticky_bar_enabled'] ) ) : ?>
	<div id="cly-sticky-bar" class="cly-sticky-bar" style="display:none;" aria-live="polite">
	<span class="cly-sticky-bar__summary" id="cly-sticky-summary"></span>
	<button class="cly-sticky-bar__cta" id="cly-sticky-open"><?php esc_html_e( 'View Cart', 'cartly' ); ?></button>
	</div>
	<?php endif; ?>

</div>
