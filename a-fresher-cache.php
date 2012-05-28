<?php
/*
	Plugin Name: A Fresher Cache
	Plugin URI: http://github.com/tollmanz/a-fresher-cache
	Description: Get a fresher cache with a dash of "A Fresher Cache" for absolutely no cash.
	Author: tollmanz
	Version: 0.1
	Author URI: http://tollmanz.com/
*/

/**
 * Make plugin 3.3+ only.
 */
if ( ! function_exists( 'is_main_query' ) )
	exit( __( 'WordPress 3.3 or greater is required for "A Fresher Cache" to function properly. Please uninstall', 'a-fresher-cache' ) );

/**
 * Define constants.
 */
define( 'A_FRESHER_CACHE_VERSION' , '0.1' );
define( 'A_FRESHER_CACHE_ROOT' , dirname( __FILE__ ) );
define( 'A_FRESHER_CACHE_FILE_PATH' , A_FRESHER_CACHE_ROOT . '/' . basename(__FILE__) );

/**
 * Main class for the A Fresher Cache plugin.
 *
 * This class is for namespacing of the AFC plugin functions. It adds functionality as needed.
 * The primary purpose for this function is to route requests and hold the registered refresh
 * buttons.
 */
class afcFresherCache {

	/**
	 * Holds all of the refresh buttons.
	 *
	 * @var array
	 */
	private $_fresher_cache_items;

	/**
	 * Registers the main actions.
	 */
	public function __construct() {
		// Including required files
		require_once( A_FRESHER_CACHE_ROOT . '/classes/class-afc-fresher-cache-item.php' );
		require_once( A_FRESHER_CACHE_ROOT . '/public-functions.php' );

		// l18n support
		load_plugin_textdomain( 'a-fresher-cache', null, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		// Generate the menu items
		add_action( 'admin_bar_menu', array( $this, 'do_items' ), 500 );

		// Run the desired function
		add_action( 'init', array( $this, 'do_function' ), 1000 );

		// Add the default menu items
		add_action( 'init', array( $this, 'add_defaults' ) );
	}

	/**
	 * Generates the menu items.
	 *
	 * The function loops through all of the registeres buttons and creates menu items out of them.
	 * The "registered buttons" are simply wrappers for admin bar menu items. The only difference, is
	 * that the registered buttons take two additional arguments that are used to route the links to
	 * the correct functions with the correct parameters.
	 *
	 * @return bool
	 */
	public function do_items() {
		$items = $this->get_items();

		if ( empty( $items ) )
			return false;

		// Add the parent item
		$this->_add_main_menu();

		// Add each registered button as a menu item
		foreach ( $items as $key => $button ) {
			// Create the final URL if there is a URL set
			if ( $button['href'] ) {
				// Create the final URL by appending the item key and a nonce, but do it only if the user has not provided a custom url
				if ( home_url() == $button['href'] ) {
					$params = array(
						'afc-key' => $button['id'],
					);

					$href = add_query_arg( $params, $button['href'] );
					$href = wp_nonce_url( $href, 'afc-refresh' );
					$href = esc_url( $href );

					$button['href'] = $href;
				} else {
					$button['href'] = esc_url( $button['href'] );
				}
			}

			$button = apply_filters( 'afc_button_args_' . $key, $button );

			// The function and args parameters are not needed for the add_menu method
			unset( $button['function'] );
			unset( $button['args'] );

			global $wp_admin_bar;
			$wp_admin_bar->add_menu( $button );
		}
	}

	/**
	 * Adds the top most menu item.
	 *
	 * This plugin adds the main menu item which other others use as a parent. It is important not
	 * to change the id for this items as it may break functionality in other themes or plugins. The
	 * title and parent attributes are filterable, but be careful about using these filters as they
	 * may cause unexpected changes for users not expecting to see the item labelled differently or
	 * in a different location.
	 */
	private function _add_main_menu() {
		global $wp_admin_bar;
		$wp_admin_bar->add_menu( array(
			'id' => 'afc-main-menu-item',
			'title' => apply_filters( 'afc_main_item_title', 'Freshen' ),
			'parent' => apply_filters( 'afc_main_item_parent', 'top-secondary' ),
			'capability' => apply_filters( 'afc_main_item_capability', 'edit_theme_options' )
		) );
	}

	/**
	 * Registers a new cache refresh item.
	 *
	 * @param $args
	 * @return array|bool
	 */
	public function add_item( $args ) {
		if ( ! isset( $args['id'] ) )
			return false;

		$fresher_cache_items = $this->get_items();
		$fresher_cache_items[$args['id']] = $args;

		$this->set_items( $fresher_cache_items );

		return $this->get_items();
	}

	/**
	 * Gets an a single registered item by key.
	 *
	 * @param $key
	 * @return array|bool
	 */
	public function retrieve_item( $key ) {
		$fresher_cache_items = $this->get_items();

		if ( isset( $fresher_cache_items[$key] ) )
			return $fresher_cache_items[$key];
		else
			return false;
	}

	/**
	 * Remove a registered fresher cache item.
	 *
	 * @param $key
	 * @return array|WP_Error
	 */
	public function remove_item( $key ) {
		$fresher_cache_items = $this->get_items();

		if ( ! isset( $fresher_cache_items[$key] ) )
			return new WP_Error( 'afc-key-not-registered', __( 'The item you attempted to remove is not registered.', 'a-fresher-cache' ) );

		unset( $fresher_cache_items[$key] );

		$this->set_items( $fresher_cache_items );
		return $this->get_items();
	}

	/**
	 * Sets all items.
	 *
	 * @param $buttons
	 * @return void
	 */
	public function set_items( $buttons ) {
		$this->_fresher_cache_items = $buttons;
	}

	/**
	 * Gets all items.
	 *
	 * @return array
	 */
	public function get_items() {
		return $this->_fresher_cache_items;
	}

	/**
	 * Runs a function.
	 *
	 * When a refresher cache item link is clicked, this item is initiated. It checks user permissions,
	 * verifies the nonce, verifies that the item is registered and then runs the function. After running
	 * the function, the user is redirected to the previous page.
	 *
	 * @return void
	 */
	public function do_function() {
		// Nothing should happen if the admin bar isn't showing
		if ( ! is_admin_bar_showing() )
			return;

		// Check for existence of key
		if ( ! isset( $_GET['afc-key'] ) )
			return;

		$key = sanitize_key( $_GET['afc-key'] );

		// Verify the nonce
		if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'afc-refresh' ) )
			return;

		// Make sure user has permission
		$capability = $this->get_capability( $key );

		if ( ! current_user_can( $capability ) )
			return;

		// Verify the function
		$function = $this->get_function( $key );

		if ( false === $function )
			return;

		// Call the function
		$args = $this->get_args( $key );

		if ( empty( $args ) )
			call_user_func( $function );
		else
			call_user_func_array( $function, $args );

		wp_safe_redirect( wp_get_referer() );
		die();
	}

	/**
	 * Get the function associated with the item id.
	 *
	 * @param $id
	 * @return bool|string
	 */
	public function get_function( $id ) {
		return $this->_get_value( $id, 'function' );
	}

	/**
	 * Get the args associated with the item id.
	 *
	 * @param $id
	 * @return bool|string
	 */
	public function get_args( $id ) {
		return $this->_get_value( $id, 'args' );
	}

	/**
	 * Get the capability associated with the item id.
	 *
	 * @param $id
	 * @return bool|string
	 */
	public function get_capability( $id ) {
		return $this->_get_value( $id, 'capability' );
	}

	/**
	 * Returns a function name based on the refresher cache item key.
	 *
	 * Given the key that was used to register the refresher cache item, the corresponding
	 * array key is returned. If the given key/id is not found, false is returned. This is an important
	 * security feature in that it allows only registered functions and values are executed.
	 *
	 * @param $id
	 * @param $value
	 * @return bool|string
	 */
	private function _get_value( $id, $value ) {
		$buttons = $this->get_items();

		if ( ! isset( $buttons[$id][$value] ) )
			return false;

		return $buttons[$id][$value];
	}

	/**
	 * Adds the default core items to the admin bar.
	 *
	 * @return void
	 */
	public function add_defaults() {
		$default_items = array(
			array(
				'id' => 'afc-core-items',
				'title' => __( 'Core', 'a-fresher-cache' ),
				'no_href' => true
			),
			array(
				'id' => 'afc-flush-rewrite-rules',
				'title' => __( 'Flush Rewrite Rules', 'a-fresher-cache' ),
				'function' => 'flush_rewrite_rules',
				'parent' => 'afc-core-items',
				'args' => array( true )
			)
		);

		$default_items = apply_filters( 'afc_default_items', $default_items );

		foreach ( $default_items as $key => $value )
			afc_add_item( $value );
	}
}

global $afcFresherCache;
$afcFresherCache = new afcFresherCache();