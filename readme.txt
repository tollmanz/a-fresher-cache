=== Plugin Name ===
Contributors: tollmanz
Donate link: http://tollmanz.com/
Tags: cache, admin bar
Requires at least: 3.3
Tested up to: 3.9.1
Stable tag: 0.2.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Get a fresher cache with a dash of "A Fresher Cache" for absolutely no cash.

== Description ==

A Fresher Cache adds an admin bar menu that gives easy access to functions that refresh (i.e, regenerate or invalidate)
cached items. Out of the box, the plugin provides easy access to refresh items that WordPress Core caches; however, the true
power of the plugin is its API that allows developers to easily add items to the menu that initiate custom functions that
refresh cached items.

For instance, imagine that you wrote a function called "my_update_all_term_caches" that loops through all of the terms
in your WordPress install and updates the cache for each item. You can add this function to the "Freshen" admin bar
menu with the following API call:

<pre><code>
function my_update_all_term_caches_menu_item() {
    $args = array(
        'id' => 'my-update-all-term-caches',
        'title' => 'Update Term Cache',
        'function' => 'my_update_all_term_caches'
    );

    afc_add_item( $args );
}
add_action( 'init', 'my_update_all_term_caches_menu_item' );
</code></pre>

This code will generate a menu item labelled "Update Term Cache" that creates a link that will run the function defined
in the "function" key of the $args array (in this case, my_update_all_term_caches()).

The primary purpose of this plugin is to provide an easy tool for developers to refresh cached items when developing new
features. Additionally, it can serve as a convenient tool for users to be able to update cached items when needed.

Note that this is not merely a wrapper for the admin bar API. Rather, it allows you to add items to the admin bar and provides
all of the necessary coding that will link a callback function to a menu item. You can think of it as an extension of
the admin bar that adds a very specific functionality. The plugin also uses the admin bar as intended and only adds a
few extra arguments that support the functionality of this plugin.

A more complete tutorial that describes the full functionality of the plugin can be read at
[tollmanz.com](http://tollmanz.com/a-fresher-cache-announcement/ "A Fresher Cache Announcement").

== Installation ==

1. Upload `a-fresher-cache` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the "Plugins" menu in WordPress
3. See above to start adding custom items to the "Freshen" admin menu item

== Frequently Asked Questions ==

= I installed the plugin and it really doesn't do much =

Indeed. The plugin, out of the box does very little. It is intended for assisting a developer's work. The provided API
 needs to be utilized for this to be truly useful plugin.

= If I need to do extra work with this, why haven't you documented it? =

Documentation will soon be posted on [tollmanz.com](http://tollmanz.com/ "Author Homepage") that will show you how to use
the plugin, as well as give an example of the true power of the plugin.

== Screenshots ==

1. After installing the plugin, a new admin bar menu called "Freshen" is added to the admin bar.
2. Upon hovering over the "Freshen" item, the registered items are displayed. The plugin supports parent/child relationships.
3. You can add your own special, grouped cache refresh items. This screenshot shows links to items that update cached
tweets from two different Twitter accounts.
4. WordPress cache refresh items are built into the plugin and stored under a menu item called "Core".

== Changelog ==

= 0.2.0 =
* Added methods for flushing update transients

= 0.1.2 =
* Class methods are now valid callbacks
* Transients can be removed even if using an object cache

= 0.1.1 =
* Functions for removing transients
* Documentation updates
* Verified compatibility with 3.4

= 0.1 =
* Initial release

== Upgrade Notice ==

= 0.2.0 =
* Adds method for flushing update transients

= 0.1.2 =
* Class methods are now valid callbacks

= 0.1.1 =
* More public functions

= 0.1 =
* Initial release
