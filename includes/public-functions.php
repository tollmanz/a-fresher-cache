<?php

if ( ! function_exists( 'afc_add_refresh_item' ) ) :
/**
 * Wrapper function to add an item to the refresher menu.
 *
 * @param   array   $args   These values are the same as the add_menu args
 * 		'id'        => false
 *		'title'     => false
 *		'parent'    => false
 *		'href'      => false
 *		'group'     => false
 *		'meta'      => array()
 *		'function'  => false
 *		'args'      => array()
 *      'capability'=> 'edit_theme_options'
 *      'no_href'   => false
 * @return afcFresherCacheItem  Returns the newly created instance.
 */
function afc_add_item( $args ) {
	$refresh_item = new afcFresherCacheItem( $args );
	return $refresh_item;
}
endif;

if ( ! function_exists( 'afc_remove_refresh_item' ) ) :
/**
 * Remove a fresher cache item.
 *
 * @param   string      $id ID of the item to remove.
 * @return  array|WP_Error  True on success, WP_Error on failure.
 */
function afc_remove_item( $id ) {
	global $afcFresherCache;
	return $afcFresherCache->remove_item( $id );
}
endif;

if ( ! function_exists( 'afc_remove_default_actions' ) ) :
/**
 * Removes all of the menu items automatically added by the plugin.
 *
 * @return  void
 */
function afc_remove_default_actions() {
	global $afcFresherCache;
	remove_action( 'init', array( $afcFresherCache, 'add_defaults' ) );
}
endif;

if ( ! function_exists( 'afc_delete_transient_group' ) ) :
/**
 * Remove all transients that start with $starting_with.
 *
 * The intent of this function is to remove all transients that are grouped with
 * the same prefix. This can easily target all transients related to a plugin/theme
 * with a prefix or grouped function with a specific prefix.
 *
 * @param   string  $starting_with  Value to append to "_transient_".
 * @return  void
 */
function afc_delete_transient_group ( $starting_with ) {
	if ( ! isset( $starting_with ) )
		return;

	afc_delete_all_transients( $starting_with );
}
endif;