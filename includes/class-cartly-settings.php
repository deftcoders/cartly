<?php
/**
 * Cartly Settings Helper.
 *
 * Provides easy access to saved settings and generates
 * correct frontend CSS variables.
 *
 * @package Cartly
 * @author  codelitix
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Cartly_Settings.
 */
class Cartly_Settings {

	/**
	 * Cached settings array.
	 *
	 * @var array|null
	 */
	private static $settings = null;

	/**
	 * Get preset color maps.
	 *
	 * Must match admin JavaScript presets.
	 *
	 * @return array
	 */
	public static function preset_colors() {
		return array(
			'modern'     => array(
				'primary_color'      => '#6c63ff',
				'secondary_color'    => '#a78bfa',
				'bg_color'           => '#ffffff',
				'text_color'         => '#111827',
				'button_color'       => '#6c63ff',
				'button_text_color'  => '#ffffff',
				'shipping_bar_color' => '#6c63ff',
			),
			'minimal'    => array(
				'primary_color'      => '#18181b',
				'secondary_color'    => '#52525b',
				'bg_color'           => '#ffffff',
				'text_color'         => '#18181b',
				'button_color'       => '#18181b',
				'button_text_color'  => '#ffffff',
				'shipping_bar_color' => '#18181b',
			),
			'dark'       => array(
				'primary_color'      => '#818cf8',
				'secondary_color'    => '#a78bfa',
				'bg_color'           => '#0f0c29',
				'text_color'         => '#e0e0ff',
				'button_color'       => '#818cf8',
				'button_text_color'  => '#ffffff',
				'shipping_bar_color' => '#818cf8',
			),
			'glass'      => array(
				'primary_color'      => '#667eea',
				'secondary_color'    => '#764ba2',
				'bg_color'           => '#f0f4ff',
				'text_color'         => '#1e1b4b',
				'button_color'       => '#667eea',
				'button_text_color'  => '#ffffff',
				'shipping_bar_color' => '#667eea',
			),
			'gold'       => array(
				'primary_color'      => '#c9a84c',
				'secondary_color'    => '#8b6914',
				'bg_color'           => '#1c1712',
				'text_color'         => '#f5e6c8',
				'button_color'       => '#c9a84c',
				'button_text_color'  => '#1c1712',
				'shipping_bar_color' => '#c9a84c',
			),
			'conversion' => array(
				'primary_color'      => '#059669',
				'secondary_color'    => '#0D9488',
				'bg_color'           => '#FFFFFF',
				'text_color'         => '#111827',
				'button_color'       => '#059669',
				'button_text_color'  => '#FFFFFF',
				'shipping_bar_color' => '#059669',
			),
		);
	}

	/**
	 * Get all settings.
	 *
	 * @return array
	 */
	public static function get_all() {

		if ( null === self::$settings ) {
			$saved          = (array) get_option( 'cartly_settings', array() );
			self::$settings = wp_parse_args( $saved, cartly_defaults() );
		}

		return self::$settings;
	}

	/**
	 * Get a single setting value.
	 *
	 * @param string $key           Setting key.
	 * @param mixed  $default_value Default value if not set.
	 *
	 * @return mixed
	 */
	public static function get( $key, $default_value = null ) {
		$all = self::get_all();

		return isset( $all[ $key ] ) ? $all[ $key ] : $default_value;
	}

	/**
	 * Save settings.
	 *
	 * @param array $data Settings array.
	 *
	 * @return void
	 */
	public static function save( $data ) {
		self::$settings = null;
		update_option( 'cartly_settings', $data );
	}

	/**
	 * Generate inline CSS variables.
	 *
	 * Variable names must match cartly.css :root variables.
	 *
	 * @return string
	 */
	public static function generate_css_vars() {

		$s      = self::get_all();
		$preset = isset( $s['preset'] ) ? $s['preset'] : 'modern';

		// If individual colors are empty, fill from preset map.
		$preset_map = self::preset_colors();

		if ( isset( $preset_map[ $preset ] ) ) {
			foreach ( $preset_map[ $preset ] as $k => $v ) {
				if ( empty( $s[ $k ] ) ) {
					$s[ $k ] = $v;
				}
			}
		}

		$shadow_map = array(
			'none'     => 'none',
			'soft'     => '0 4px 24px rgba(0,0,0,.08)',
			'medium'   => '0 8px 40px rgba(0,0,0,.16)',
			'dramatic' => '0 16px 64px rgba(0,0,0,.28)',
			'light'    => '0 4px 24px rgba(0,0,0,.08)',
			'heavy'    => '0 16px 64px rgba(0,0,0,.28)',
		);

		// Use shadow_style first, fall back to shadow_intensity.
		$shadow_key = ! empty( $s['shadow_style'] )
			? $s['shadow_style']
			: ( isset( $s['shadow_intensity'] ) ? $s['shadow_intensity'] : 'medium' );

		$shadow = isset( $shadow_map[ $shadow_key ] )
			? $shadow_map[ $shadow_key ]
			: $shadow_map['medium'];

		$css  = ':root{';
		$css .= '--cly-pri:' . esc_attr( $s['primary_color'] ) . ';';
		$css .= '--cly-sec:' . esc_attr( $s['secondary_color'] ) . ';';
		$css .= '--cly-bg:' . esc_attr( $s['bg_color'] ) . ';';
		$css .= '--cly-text:' . esc_attr( $s['text_color'] ) . ';';
		$css .= '--cly-btn:' . esc_attr( $s['button_color'] ) . ';';
		$css .= '--cly-btn-tx:' . esc_attr( $s['button_text_color'] ) . ';';
		$css .= '--cly-r:' . intval( $s['border_radius'] ) . 'px;';
		$css .= '--cly-fs:' . intval( $s['font_size'] ) . 'px;';
		$css .= '--cly-w:' . intval( $s['drawer_width'] ) . 'px;';
		$css .= '--cly-shadow:' . wp_strip_all_tags( $shadow ) . ';';
		$css .= '--cly-bar:' . esc_attr( $s['shipping_bar_color'] ) . ';';
		$css .= '}';

		return $css;
	}
}
