<?php

if ( ! function_exists( 'afc_update_term_cache_all' ) ) :
/**
 * Update all term caches.
 *
 * @return void
 */
function afc_update_term_cache_all() {
	$taxonomies = get_taxonomies();

	if ( ! is_array( $taxonomies ) )
		return;

	foreach ( $taxonomies as $taxonomy )
		afc_update_taxonomy_term_cache( $taxonomy );
}
endif;

if ( ! function_exists( 'afc_update_term_cache' ) ) :
/**
 * Update all terms caches within a specified taxonomy.
 *
 * @param $taxonomy
 * @return void
 */
function afc_update_taxonomy_term_cache( $taxonomy ) {
	if ( ! taxonomy_exists( $taxonomy ) )
		return;

	$terms = get_terms( $taxonomy, array( 'hide_empty' => false ) );

	if ( is_wp_error( $terms ) )
		return;

	update_term_cache( $terms );
}
endif;

if ( ! function_exists( 'afc_delete_all_transients' ) ) :
/**
 * Removes all single site transients from the database.
 *
 * @return void
 */
function afc_delete_all_transients() {
	global $wpdb, $_wp_using_ext_object_cache;

	if ( $_wp_using_ext_object_cache )
		return;

	// Get the transient timeout keys
	$timeout_name = $wpdb->get_col( "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout%'" );

	// Cycle through transient timeout keys, convert to transient keys and delete
	foreach ( $timeout_name as $transient ) {
		$key = str_replace( '_transient_timeout_', '', $transient );
		delete_transient( $key );
	}
}
endif;