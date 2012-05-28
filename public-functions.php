<?php

if ( ! function_exists( 'afc_add_refresh_item' ) ) :
/**
 * Wrapper function to add an item to the refresher menu.
 *
 * @param $args array These values are the same as the add_menu args
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
 * @return afcFresherCacheItem
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
 * @param $id
 * @return array|WP_Error
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
 * @return void
 */
function afc_remove_default_actions() {
	global $afcFresherCache;
	remove_action( 'init', array( $afcFresherCache, 'add_defaults' ) );
}
endif;