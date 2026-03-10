<?php
/**
 * Cartly – Uninstall
 *
 * Fires when the plugin is fully deleted via WordPress Admin → Plugins → Delete.
 * Does NOT run on deactivation — only on hard deletion.
 *
 * Removes:
 *   - cartly_settings  (all plugin configuration)
 *   - cartly_activated (post-activation redirect transient)
 *
 * @package Cartly
 * @author  codelitix
 */

// Only run when WordPress triggers an uninstall.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// ── Single site ──────────────────────────────────────────────────────────────
delete_option( 'cartly_settings' );
delete_option( 'cartly_version' );
delete_transient( 'cartly_activated' );

// ── Multisite: clean every sub-site ─────────────────────────────────────────
if ( is_multisite() ) {
	$sites = get_sites(
		array(
			'fields' => 'ids',
			'number' => 0,
		)
	);
	foreach ( $sites as $site_id ) {
		switch_to_blog( $site_id );
		delete_option( 'cartly_settings' );
		delete_option( 'cartly_version' );
		delete_transient( 'cartly_activated' );
		restore_current_blog();
	}
}
