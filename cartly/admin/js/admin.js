/**
 * codelitix — Cartly Admin JS v12
 * Polished: toast notifications, preset glow, dark-preset text visibility.
 *
 * Third-party credits:
 *   SVG Icons — Feather Icons by Cole Bemis (MIT License) https://feathericons.com
 *   jQuery    — jQuery Foundation (MIT License) https://jquery.com — bundled by WordPress
 *
 * @package Cartly
 * @author  codelitix
 */
(function ($) {
	'use strict';
	if (typeof CartlyAdmin === 'undefined') {
		return;
	}

	var C     = CartlyAdmin;
	var dirty = false;

	/* ── PRESET CONFIG ─────────────────────────────────── */
	var PREVIEW = {
		modern:     { p:'#6c63ff', bg:'#ffffff', btn:'#6c63ff', bar:'#6c63ff', text:'#111827', muted:'#6b7280', dark:false },
		minimal:    { p:'#18181b', bg:'#ffffff', btn:'#18181b', bar:'#18181b', text:'#111827', muted:'#6b7280', dark:false },
		dark:       { p:'#818cf8', bg:'#0f0c29', btn:'#818cf8', bar:'#818cf8', text:'#e0e0ff', muted:'rgba(224,224,255,.45)', dark:true  },
		glass:      { p:'#667eea', bg:'#f0f4ff', btn:'#667eea', bar:'#667eea', text:'#1e1b4b', muted:'#6b7280', dark:false },
		gold:       { p:'#c9a84c', bg:'#1c1712', btn:'#c9a84c', bar:'#c9a84c', text:'#f5e6c8', muted:'rgba(245,230,200,.45)', dark:true  },
		conversion: { p:'#059669', bg:'#ffffff', btn:'#059669', bar:'#059669', text:'#111827', muted:'#6b7280', dark:false },
	};
	var LABELS  = { modern:'Modern', minimal:'Minimal', dark:'Dark', glass:'Glass', gold:'Luxury', conversion:'Convert' };

	/* ── TABS ────────────────────────────────────────────── */
	$( document ).on(
		'click',
		'.cw-tn',
		function () {
			var t = $( this ).data( 'tab' );
			$( '.cw-tn' ).removeClass( 'on' );
			$( this ).addClass( 'on' );
			$( '.cw-pane' ).removeClass( 'on' );
			$( '[data-pane="' + t + '"]' ).addClass( 'on' );
		}
	);

	/* ── PRESETS ─────────────────────────────────────────── */
	$( document ).on(
		'click',
		'.cw-ps',
		function () {
			var k = $( this ).data( 'preset' );
			$( '.cw-ps' ).removeClass( 'on' );
			$( this ).addClass( 'on' );
			$( 'input[name="preset"]' ).val( k );
			$( '#cw-pname' ).text( LABELS[k] || k );
			previewPreset( k );
			markDirty();
		}
	);

	function previewPreset(k){
		var p = PREVIEW[k]; if ( ! p) {
			return;
		}
		updateMock( p );
		var colors = C.presets && C.presets[k];
		if (colors) {
			['primary_color','bg_color','button_color','text_color','secondary_color','shipping_bar_color','button_text_color'].forEach(
				function (f) {
					if (colors[f]) {
						updateColorPicker( f, colors[f] );
					}
				}
			);
		}
	}

	function updateColorPicker(name, color){
		var $inp = $( '[name="' + name + '"]' );
		if ( ! $inp.length || ! color) {
			return;
		}
		$inp.val( color );
		try {
			$inp.wpColorPicker( 'color', color ); } catch (e) {
			}
	}

	function updateMock(p){
		if (typeof p === 'string') {
			p = { p: p, bg: null, btn: p, bar: p, text: '#111827', muted: '#6b7280', dark: false };
		}
		var mock = document.getElementById( 'cw-mock' ); if ( ! mock) {
			return;
		}
		var $mock = $( mock );

		var primary = p.p || '#6c63ff';
		var bg      = p.bg || '#ffffff';
		var btn     = p.btn || primary;
		var bar     = p.bar || primary;
		var text    = p.text || '#111827';
		var muted   = p.muted || '#6b7280';
		var isDark  = p.dark || false;

		mock.style.background = bg;
		$mock.toggleClass( 'is-dark', isDark );

		var $hd = $mock.find( '.cw-m-hd' );
		$hd.css( { background: bg, borderBottomColor: isDark ? 'rgba(255,255,255,.08)' : 'rgba(0,0,0,.06)' } );

		var ico = document.getElementById( 'm-ico' );
		if (ico) {
			ico.style.background = 'linear-gradient(135deg,' + primary + ',' + primary + 'bb)';
		}

		var bdg = document.getElementById( 'm-bdg' );
		if (bdg) {
			bdg.style.background = primary;
		}

		var barEl = document.getElementById( 'm-bar' );
		if (barEl) {
			barEl.style.background = 'linear-gradient(90deg,' + (bar || primary) + ',' + (bar || primary) + 'bb)';
		}

		var chk = document.getElementById( 'm-chk' );
		if (chk) {
			chk.style.background = 'linear-gradient(135deg,' + (btn || primary) + ',' + (btn || primary) + 'bb)';
		}

		$mock.find( '.cw-m-ship' ).css(
			{
				background: isDark ? 'rgba(255,255,255,.04)' : 'rgba(108,92,231,.03)'
			}
		);
		$mock.find( '.cw-m-ship-msg' ).css( 'color', muted );
		$mock.find( '.cw-m-ship-track' ).css( 'background', isDark ? 'rgba(255,255,255,.1)' : '#e5e7eb' );
		$mock.find( '.cw-m-iname' ).css( 'color', text );
		$mock.find( '.cw-m-imeta' ).css( 'color', muted );
		$mock.find( '.cw-m-qty' ).css(
			{
				borderColor: isDark ? 'rgba(255,255,255,.12)' : '#e5e7eb',
				color: isDark ? 'rgba(255,255,255,.75)' : '#374151'
			}
		);
		$mock.find( '.cw-m-price, .cw-m-sub' ).css( 'color', primary );
		$mock.find( '.cw-m-ft' ).css( { background: bg, borderTopColor: isDark ? 'rgba(255,255,255,.08)' : 'rgba(0,0,0,.06)' } );
		$mock.find( '.cw-m-subrow' ).css( 'color', isDark ? 'rgba(255,255,255,.7)' : '#374151' );
		$mock.find( '.cw-m-title' ).css( 'color', text );
		$mock.find( '.cw-m-trust' ).css( 'color', muted );
		$mock.find( '.cw-m-cls' ).css(
			{
				background:   isDark ? 'rgba(255,255,255,.06)' : '#f9fafb',
				borderColor:  isDark ? 'rgba(255,255,255,.12)' : 'rgba(0,0,0,.1)',
				color:        isDark ? 'rgba(255,255,255,.4)' : '#9ca3af'
			}
		);
		mock.style.border = '1px solid ' + (isDark ? 'rgba(255,255,255,.08)' : 'rgba(0,0,0,.08)');
	}

	/* ── DEVICE SWITCHER ───────────────────────────────── */
	$( document ).on(
		'click',
		'.cw-dv',
		function () {
			var d = $( this ).data( 'dev' );
			$( '.cw-dv' ).removeClass( 'on' );
			$( this ).addClass( 'on' );
			var $mock = $( '#cw-mock' );
			$mock.removeClass( 'cw-mock--tab cw-mock--mob' );
			if (d === 'tab') {
				$mock.addClass( 'cw-mock--tab' );
			}
			if (d === 'mob') {
				$mock.addClass( 'cw-mock--mob' );
			}
		}
	);

	/* ── RANGE SLIDERS ──────────────────────────────────── */
	$( document ).on(
		'input',
		'.cw-range',
		function () {
			var vid = $( this ).data( 'vid' );
			var u   = $( this ).data( 'unit' ) || '';
			if (vid) {
				$( '#' + vid ).text( $( this ).val() + u );
			}
			setRangeFill( this );
			var name = $( this ).attr( 'name' );
			if (name === 'border_radius') {
				var r = $( this ).val() + 'px';
				$( '#cw-mock' ).css( 'border-radius', r );
				$( '#m-chk' ).css( 'border-radius', Math.min( parseInt( r ), 14 ) + 'px' );
			}
			markDirty();
		}
	);

	function setRangeFill(el){
		var min = parseFloat( el.min ) || 0, max = parseFloat( el.max ) || 100, val = parseFloat( el.value ) || 0;
		var pct = ((val - min) / (max - min) * 100).toFixed( 1 ) + '%';
		el.style.setProperty( '--pct', pct );
	}
	function initRanges(){
		document.querySelectorAll( '.cw-range' ).forEach( setRangeFill );
	}

	/* ── SEGMENTED CONTROLS ─────────────────────────────── */
	$( document ).on(
		'change',
		'.cw-seg__i input',
		function () {
			var name = $( this ).attr( 'name' );
			$( '[name="' + name + '"]' ).closest( '.cw-seg__i' ).removeClass( 'on' );
			$( this ).closest( '.cw-seg__i' ).addClass( 'on' );
			markDirty();
		}
	);

	/* ── COLOR PICKERS ──────────────────────────────────── */
	function initColorPickers(){
		$( '.cw-color-picker' ).each(
			function () {
				var $inp = $( this );
				$inp.wpColorPicker(
					{
						change: function (e, ui) {
							var name     = $inp.attr( 'name' );
							var color    = ui.color.toString();
							var cur      = PREVIEW[$( '.cw-ps.on' ).data( 'preset' ) || 'modern'] || PREVIEW.modern;
							var override = $.extend( {}, cur );
							if (name === 'primary_color') {
								override.p = color; }
							if (name === 'button_color') {
								override.btn = color; }
							if (name === 'shipping_bar_color') {
								override.bar = color; }
							if (name === 'bg_color') {
								override.bg = color; }
							if (name === 'text_color') {
								override.text = color; }
							updateMock( override );
							markDirty();
						},
						clear: function () {
							markDirty(); },
						hide: true,
						palettes: ['#6c63ff','#ec4899','#18181b','#667eea','#c9a84c','#10b981','#ef4444','#059669'],
					}
				);
			}
		);
	}

	/* ── MASTER TOGGLE ──────────────────────────────────── */
	$( document ).on(
		'change',
		'#cw-master',
		function () {
			var on = $( this ).is( ':checked' );
			$( '#cw-master-lbl' ).html(
				on
				? '<span class="cw-badge cw-badge--live"><span class="cw-badge__dot"></span>Auto Frontend Active</span>'
				: '<span class="cw-badge">Cart Disabled</span>'
			);
			markDirty();
		}
	);

	/* ── DIRTY TRACKING ─────────────────────────────────── */
	$( document ).on( 'change input', '#cw input:not(.cw-range):not(.cw-color-picker), #cw select, #cw textarea', markDirty );
	function markDirty(){
		dirty = true; }

	/* ── COLLECT FORM DATA ──────────────────────────────── */
	function collect(){
		var d  = {};
		var ps = $( '.cw-ps.on' ).data( 'preset' );
		if (ps) {
			d.preset = ps;
		}
		if ( ! d.preset) {
			d.preset = $( 'input[name="preset"]' ).val() || 'modern';
		}

		$( '#cw input[name], #cw select[name], #cw textarea[name]' ).each(
			function () {
				var $el = $( this ), n = $el.attr( 'name' ), t = $el.attr( 'type' );
				if ( ! n || n === 'preset') {
					return;
				}
				if (t === 'checkbox') {
					d[n] = $el.is( ':checked' ) ? '1' : '';
				} else if (t === 'radio') {
					if ($el.is( ':checked' )) {
						d[n] = $el.val();
					} } else if ( ! (n in d)) {
					d[n] = $el.val();
					}
			}
		);
		return d;
	}

	/* ── TOAST ──────────────────────────────────────────── */
	function showToast(msg, type){
		var $t = $( '#cw-toast' );
		if ( ! $t.length) {
			$t = $( '<div id="cw-toast" class="cw-toast"></div>' ).appendTo( '#cw' );
		}
		$t.text( msg ).removeClass( 'cw-toast--success cw-toast--error' );
		if (type === 'success') {
			$t.addClass( 'cw-toast--success' );
		}
		$t.addClass( 'show' );
		setTimeout(
			function () {
				$t.removeClass( 'show' ); },
			2800
		);
	}

	/* ── SAVE ───────────────────────────────────────────── */
	$( '#cw-save' ).on(
		'click',
		function () {
			var $b = $( this ).addClass( 'saving' );
			$b.find( 'span.txt' ).text( 'Saving…' );
			$.ajax(
				{
					url: C.ajax_url, method: 'POST',
					data: { action:'cartly_save_settings', nonce:C.nonce, settings:collect() },
					success: function (r) {
						$b.removeClass( 'saving' );
						$b.find( 'span.txt' ).text( 'Save Settings' );
						if (r.success) {
							showStatus( '✓ Saved!', false );
							showToast( '✓ Settings saved successfully', 'success' );
							dirty = false;
						} else {
							showStatus( '⚠ Error saving', true );
							showToast( '⚠ Error saving settings', '' );
						}
					},
					error: function () {
						$b.removeClass( 'saving' );
						$b.find( 'span.txt' ).text( 'Save Settings' );
						showStatus( '⚠ Connection error', true );
						showToast( '⚠ Connection error', '' );
					}
				}
			);
		}
	);

	/* ── RESET ──────────────────────────────────────────── */
	$( '#cw-reset' ).on(
		'click',
		function () {
			if ( ! confirm( 'Reset all Cartly settings to defaults?' )) {
				return;
			}
			$.ajax(
				{
					url: C.ajax_url, method: 'POST',
					data: { action:'cartly_reset_settings', nonce:C.nonce },
					success: function (r) {
						if (r.success) {
							showStatus( '↺ Reset to defaults', false );
							showToast( '↺ Settings reset to defaults', 'success' );
							dirty = false;
							setTimeout(
								function () {
									location.reload(); },
								600
							);
						}
					}
				}
			);
		}
	);

	/* ── STATUS ─────────────────────────────────────────── */
	function showStatus(msg, isErr){
		var $s = $( '#cw-status' ).text( msg ).removeClass( 'err' ).addClass( 'show' );
		if (isErr) {
			$s.addClass( 'err' );
		}
		setTimeout(
			function () {
				$s.removeClass( 'show' ); },
			3000
		);
	}

	/* ── UNSAVED WARNING ────────────────────────────────── */
	$( window ).on(
		'beforeunload',
		function () {
			if (dirty) {
				return C.i18n && C.i18n.unsaved ? C.i18n.unsaved : 'You have unsaved changes.';
			}
		}
	);

	/* ── INIT ───────────────────────────────────────────── */
	$(
		function () {
			initRanges();
			initColorPickers();
			var cur = (C.settings && C.settings.preset) || 'modern';
			$( '.cw-ps' ).removeClass( 'on' );
			$( '.cw-ps[data-preset="' + cur + '"]' ).addClass( 'on' );
			previewPreset( cur );
			$( '#cw-pname' ).text( LABELS[cur] || cur );
		}
	);

})( jQuery );
