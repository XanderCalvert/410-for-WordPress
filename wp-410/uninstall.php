<?php
/**
 * Cleanup tasks when uninstalling 410 for WordPress.
 *
 * @package WP_410
 */

if ( ! defined( 'ABSPATH' ) && ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/**
 * Drop the plugin's custom table during uninstall.
 *
 * @return void
 */
function remove_wp_410_table() {
	global $wpdb;

	$table = esc_sql( $wpdb->prefix . '410_links' );

	// Schema change is intentional during uninstall, caching does not apply here.
	$wpdb->query( "DROP TABLE IF EXISTS `$table`" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.SchemaChange,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.InterpolatedNotPrepared
}

remove_wp_410_table();
delete_option( 'wp_410_options_version' );
delete_option( 'wp_410_max_404s' );
