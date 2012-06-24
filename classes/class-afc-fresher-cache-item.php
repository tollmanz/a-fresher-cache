<?php

if ( ! class_exists( 'afcFresherCacheItem' ) ) :
/**
 * Defines a Fresher Cache Item.
 *
 * A Fresher Cache Item is a menu item that will be added to the admin bar for
 * purposes of refreshing the cache. The item will be a link to URL that will
 * initiate the execution of a function that presumably refreshes a cache item.
 */
class afcFresherCacheItem {

	/**
	 * The item's id.
	 *
	 * @var string|bool
	 */
	private $_id = false;

	/**
	 * The item's label in the menu.
	 *
	 * @var string|bool
	 */
	private $_title = false;

	/**
	 * The item's parent in the menu.
	 *
	 * @var string|bool
	 */
	private $_parent = false;

	/**
	 * The item's URL.
	 *
	 * @var string|bool
	 */
	private $_href = false;

	/**
	 * The 'group' setting for the menu item.
	 *
	 * @var bool
	 */
	private $_group = false;

	/**
	 * Meta attributes for the menu item.
	 *
	 * @var array
	 */
	private $_meta = array();

	/**
	 * The name of the function to execute.
	 *
	 * @var string|bool
	 */
	private $_function = false;

	/**
	 * The args to pass to the function.
	 *
	 * @var array
	 */
	private $_args = array();

	/**
	 * Determines whether an "href" value of false will produce the default href or not.
	 *
	 * If "href" is set to false and "no_href" is also false, the actual href for the button will be
	 * home_url() plus the necessary query args to fire the function. If the "href" value is false and
	 * "no_hre" is true, the item will not have a URL. If the "href" value has a URL value, it will use
	 * that value for the URL. In this scenario, the "no_href" value will be ignored.
	 *
	 * @var bool
	 */
	private $_no_href = false;

	/**
	 * Holds the most recent error.
	 *
	 * @var bool|WP_Error
	 */
	private $_error = false;

	/**
	 * Constructs the class.
	 *
	 * @param   array   $args   See "parse_args" for accepted args.
	 */
	public function __construct( $args = array() ) {
		if ( ! is_array( $args ) )
			$this->_error = new WP_Error( 'afc_args_array', __( 'The "args" parameter must be an array', 'a-fresher-cache' ) );

		if ( ! empty( $args ) )
			$this->add_refresh_item( $args );
	}

	/**
	 * Parses the arguments set to the instance of the class.
	 *
	 * Handles adding defaults, sanitizes values, and setting the class variables.
	 *
	 * @param   array   $args   These values are the same as the add_menu args
	 * 	'id'        => false
	 *	'title'     => false
	 *	'parent'    => false
	 *	'href'      => false
	 *	'group'     => false
	 *	'meta'      => array()
	 *	'function'  => false
	 *	'args'      => array()
	 *      'capability'=> 'edit_theme_options'
	 *      'no_href'   => false
	 * @return  array           The validated/sanitized values.
	 */
	public function parse_args( $args ) {
		$args = wp_parse_args( $args, array(
			'id' => false,
			'title' => false,
			'parent' => false,
			'href' => false,
			'group' => false,
			'meta' => array(),
			'function' => false,
			'args' => array(),
			'capability' => 'edit_theme_options',
			'no_href' => false
		) );

		// Figure out the "href" value as it is more involved
		if ( false === $args['href'] && false === $args['no_href'] )
			$args['href'] = home_url();
		elseif ( false === $args['href'] && true === $args['no_href'] )
			$args['href'] = false;
		else
			$args['href'] = esc_url( $args['href'] );

		// Validate/sanitize
		$cleaned_args = array(
			'id' => sanitize_key( $args['id'] ),
			'title' => wp_strip_all_tags( $args['title'] ),
			'parent' => false !== $args['parent'] ? sanitize_key( $args['parent'] ) : 'afc-main-menu-item',
			'href' => $args['href'],
			'group' => (bool) $args['group'],
			'function' => false !== $args['function'] && is_callable( $args['function'] ) ? $args['function'] : false,
			'args' => $args['args'],
			'capability' => sanitize_key( $args['capability'] )
		);

		// Set the instance variables
		foreach ( $cleaned_args as $key => $value )
			$this->set_arg( '_' . $key, $value );

		return $cleaned_args;
	}

	/**
	 * Add an item to the main global variable to hold the refresh item.
	 *
	 * @param   array   $args   Arrays of args defined in "parse_args".
	 * @return  bool            True on success, false on failure.
	 */
	public function add_refresh_item( $args ) {
		if ( ! is_admin_bar_showing() )
			return false;

		if ( ! is_array( $args ) || empty( $args ) )
			return false;

		$parsed_args = $this->parse_args( $args );

		if ( ! current_user_can( $parsed_args['capability'] ) )
			return false;

		global $afcFresherCache;

		if ( $afcFresherCache->retrieve_item( $parsed_args['id'] ) )
			return false;

		return $afcFresherCache->add_item( $parsed_args );
	}

	/**
	 * Set a class variable.
	 *
	 * @param   string  $arg_name   Name of the class variable to set.
	 * @param   mixed   $value      Value to set the variable to.
	 * @return  void
	 */
	public function set_arg( $arg_name, $value ) {
		if ( ! isset( $this->$arg_name ) )
			return;

		$this->$arg_name = $value;
	}

	/**
	 * Get a class variable.
	 *
	 * @param   string  $arg_name   Name of variable to get.
	 * @return  mixed|WP_Error      Value on success, WP_Error on failure.
	 */
	public function get_arg( $arg_name ) {
		if ( ! isset( $this->$arg_name ) )
			return new WP_Error( 'afc_unknown_arg', __( 'Argument does not exist', 'a-fresher-cache' ) );

		return $this->$arg_name;
	}
}
endif;