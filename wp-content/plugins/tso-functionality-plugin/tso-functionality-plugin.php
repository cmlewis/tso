<?php
/**
 * Plugin Name: The Sapphire Owl Functionality Plugin
 * Plugin URI: http://www.sapphireowl.com
 * Description: Functionality for The Sapphire Owl blog, separate from any theme customizations.
 * Author: Christy Lewis
 * Author URI: http://www.christyml.com
 * Version: 0.1.0
 */


/*===================================================================================
 * Custom Global Variables
 * =================================================================================*/
function init_tso_global_vars() {

    global $tso;
    $tso = array();

    // Map social network names to their icon file
    $tso['icon_img_dir'] = get_stylesheet_directory_uri() . '/images/icons/';
}


/*===================================================================================
 * Add Author Links
 * =================================================================================*/
function add_to_author_profile( $contactmethods ) {

    $contactmethods['user_twitter'] = 'Twitter';
    $contactmethods['user_facebook'] = 'Facebook';
    $contactmethods['user_linkedin'] = 'LinkedIn';
    $contactmethods['user_youtube'] = 'YouTube';
    $contactmethods['user_spotify'] = 'Spotify';
    $contactmethods['user_github'] = 'GitHub';
    $contactmethods['user_instagram'] = 'Instagram';
    $contactmethods['user_pinterest'] = 'Pinterest';
    $contactmethods['user_foursquare'] = 'Foursquare';
    $contactmethods['user_rss'] = 'RSS Feed';
    $contactmethods['user_bloglovin'] = 'Blog Lovin';
    $contactmethods['user_feedly'] = 'Feedly';

    return $contactmethods;
}
add_filter( 'user_contactmethods', 'add_to_author_profile', 10, 1);



/*===================================================================================
 * Disable Jetpack Modules
 * =================================================================================*/
/**
 * Disable all non-whitelisted jetpack modules.
 *
 * This will allow all of the currently available Jetpack modules to work
 * normally. If there's a module you'd like to disable, simply comment it out
 * or remove it from the whitelist and it will no longer load.
 *
 * @author FAT Media, LLC
 * @link http://wpbacon.com/tutorials/disable-jetpack-modules/
 */
add_filter( 'jetpack_get_available_modules', 'prefix_kill_all_the_jetpacks' );
function prefix_kill_all_the_jetpacks( $modules ) {
// A list of Jetpack modules which are allowed to activate.
    $whitelist = array(
        'after-the-deadline',
        'carousel',
        'comments',
        'contact-form',
//        'custom-css',
        'enhanced-distribution',
        'gplus-authorship',
//        'gravatar-hovercards',
//        'infinite-scroll',
        'json-api',
//        'latex',
        'likes',
        'minileven',
//        'mobile-push',
//        'monitor',
        'notes',
//        'omnisearch',
        'photon',
//        'post-by-email',
        'publicize',
        'sharedaddy',
        'shortcodes',
        'shortlinks',
        'sso',
        'stats',
        'subscriptions',
        'tiled-gallery',
        'vaultpress',
        'videopress',
        'widget-visibility',
        'widgets',
    );
// Deactivate all non-whitelisted modules.
    $modules = array_intersect_key( $modules, array_flip( $whitelist ) );

    return $modules;
}



/*===================================================================================
 * Add class to body based on browser
 * =================================================================================*/
function tso_browser_body_class($classes) {

    global $is_gecko, $is_IE, $is_opera, $is_safari, $is_chrome;

    if($is_gecko)      $classes[] = 'firefox';
    elseif($is_opera)  $classes[] = 'opera';
    elseif($is_safari) $classes[] = 'safari';
    elseif($is_chrome) $classes[] = 'chrome';
    elseif($is_IE)     $classes[] = 'ie';
    else               $classes[] = '';

    // Add a standard TSO class to the body as well
    $classes[] = 'tso';

    return $classes;

}
add_filter('body_class','tso_browser_body_class');



/*===================================================================================
 * Custom category shortcode that excludes any categories passed in from displaying
 * =================================================================================*/
add_shortcode( 'post_categories_exclude_cats', 'post_categories_exclude_cats_shortcode' );
/**
 * Produces the category links list without the 'Uncategorized' shortcode
 *
 * Supported shortcode attributes are:
 *   after (output after link, default is empty string),
 *   before (output before link, default is 'Tagged With: '),
 *   sep (separator string between tags, default is ', ').
 *
 * Output passes through 'genesis_post_categories_shortcode' filter before returning.
 *
 * @since 1.1.0
 *
 * @see genesis_post_categories_shortcode()
 *
 * @param array|string $atts Shortcode attributes. Empty string if no attributes.
 * @return string Shortcode output
 */
function post_categories_exclude_cats_shortcode( $atts ) {

    $defaults = array(
        'sep'    => ', ',
        'before' => __( 'Filed Under: ', 'genesis' ),
        'after'  => '',
        'excluded_categories' => array()
    );

    $atts = shortcode_atts( $defaults, $atts, 'post_categories' );

    // Convert excluded_categories to an array.
    $atts['excluded_categories'] = explode( ',', $atts['excluded_categories'] );

    $cats = '';
    $sep = trim( $atts['sep'] ) . ' ';

    foreach((get_the_category()) as $category) {
        // Skip displaying the category if it's in the excluded array.
        if (!in_array($category->name, $atts['excluded_categories'])) {
            $cats .= '<a rel="category tag" title="' . sprintf( __( "View all posts in %s" ), $category->name ) . '" href="' . get_category_link( $category->term_id ) . '">' . $category->name . '</a>' . $sep;
        }
    }

    // Remove the last separator
    $cats = substr($cats, 0, strlen($sep) * -1);

    if ( genesis_html5() )
        $output = sprintf( '<span %s>', genesis_attr( 'entry-categories' ) ) . $atts['before'] . $cats . $atts['after'] . '</span>';
    else
        $output = '<span class="categories">' . $atts['before'] . $cats . $atts['after'] . '</span>';

    return apply_filters( 'genesis_post_categories_shortcode', $output, $atts );

}