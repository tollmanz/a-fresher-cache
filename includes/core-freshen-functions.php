<?php

if ( ! function_exists( 'afc_update_term_cache_all' ) ) :
/**
 * Update all term caches.
 *
 * @return  void
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
 * @param   string  $taxonomy   Taxonomy name.
 * @return  void
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
 * @author  Thanks to Rarst for inspiring this function.
 * @link    http://wordpress.stackexchange.com/questions/6602/are-transients-garbage-collected
 *
 * @param   string  $starting_with  Value to append to "_transient_".
 * @return  void
 */
function afc_delete_all_transients( $starting_with = '' ) {
	global $wpdb;

	// Get the transient timeout keys
	$key = '_transient_' . $starting_with;
	$statement = $wpdb->prepare( "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s", like_escape( $key ) . '%' );
	$transient_names = $wpdb->get_col( $statement );

	// Cycle through transient timeout keys, convert to transient keys and delete
	foreach ( $transient_names as $transient ) {
		$key = str_replace( '_transient_', '', $transient );
		delete_transient( $key );
	}
}
endif;