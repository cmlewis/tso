<?php
//* start the engine
include_once( get_template_directory() . '/lib/init.php' );

//* child theme (do not remove)
define( 'child_theme_name', 'sapphire owl theme' );
define( 'child_theme_url', 'http://www.sapphireowl.com/' );
define( 'child_theme_version', '1.0' );

//* add html5 markup structure
add_theme_support( 'html5' );

//* add viewport meta tag for mobile browsers
add_theme_support( 'genesis-responsive-viewport' );

//* add support for 3-column footer widgets
add_theme_support( 'genesis-footer-widgets', 3 );


// ************************* TSO CUSTOMIZATIONS ************************* //
// Unregister any unwanted Genesis widgets
add_action( 'widgets_init', 'unregister_genesis_widgets', 20 );
function unregister_genesis_widgets() {
    unregister_widget( 'Genesis_User_Profile_Widget' );
}


// Add/register TSO widgets
include_once( CHILD_DIR . '/lib/widgets/user-profile-widget.php' );
include_once( CHILD_DIR . '/lib/widgets/tso-social-icon-widget.php' );
register_widget( 'TSO_User_Profile_Widget' );
register_widget( 'TSO_Social_Icon_Widget' );

// enqueue google fonts
add_action( 'wp_enqueue_scripts', 'genesis_sample_google_fonts' );
function genesis_sample_google_fonts() {
    wp_enqueue_style( 'google-font-sanchez-and-signika', '//fonts.googleapis.com/css?family=Sanchez:400,400italic|Signika:700', array(), child_theme_version );
}

// *********** UNREGISTER WIDGETS, REMOVE ACTIONS, ETC. **********
remove_theme_support( 'genesis-menus' ); // remove all the genesis menus (primary and secondary)
unregister_sidebar( 'header-right' ); // Remove the header right widget area

add_action( 'wp_print_styles', 'tso_dequeue_styles', 100 );
function tso_dequeue_styles() {
    wp_deregister_style('jetpack-subscriptions'); // remove jetpack subscriptions CSS
}

// *********** TSO - END UNREGISTER/REMOVE **********

// Include the firefox stylesheet if is_gecko
if($is_gecko) {
    add_action( 'wp_enqueue_scripts', 'tso_enqueue_firefox_style' );
    function tso_enqueue_firefox_style() {
        wp_enqueue_style( 'child-theme-firefox', get_stylesheet_directory_uri() . '/style-firefox.css', false );
    }
}

// TSO - override header
remove_action( 'genesis_header', 'genesis_do_header' ); // Remove the header
add_action( 'genesis_header', 'tso_do_header', 10, 2 );
/**
 * Echo the default header, including the #title-area div, along with #title and #description, as well as the .widget-area.
 *
 * Does the `genesis_site_title`, `genesis_site_description` and `genesis_header_right` actions.
 *
 * @since 1.0.2
 *
 * @global $wp_registered_sidebars Holds all of the registered sidebars.
 *
 * @uses genesis_markup() Apply contextual markup.
 */
function tso_do_header() {

    // TSO - hijacking the header and navigation so the logo can go in the middle.
    $nav = '<ul id="menu-primary-navigation-1" class="menu genesis-nav-menu menu-primary">
                <li class="menu-item menu-item-type-post_type menu-item-object-page">
                    <a href="/">home</a>
                </li>
                <li class="menu-item menu-item-type-custom menu-item-object-custom">
                    <a href="#">about</a>
                </li>
                <li class="menu-item menu-item-type-custom menu-item-object-custom">
                    <a href="#">projects</a>
                </li>
                <li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-logo">
                    <a href="/"></a>
                </li>
                <li class="menu-item menu-item-type-custom menu-item-object-custom">
                    <a href="#">recipes</a>
                </li>
                <li class="menu-item menu-item-type-custom menu-item-object-custom">
                    <a href="#">resources</a>
                </li>
                <li class="last menu-item menu-item-type-post_type menu-item-object-page">
                    <a href="/contact/">contact</a>
                </li>
            </ul>';

    $nav_markup_open = genesis_markup( array(
        'html5'   => '<nav %s>',
        'xhtml'   => '<div id="nav">',
        'context' => 'tso-nav-primary',
        'echo'    => false,
    ) );

    $nav_markup_close = genesis_html5() ? '</nav>' : '</div>';

    $nav_output =  $nav_markup_open . $nav . $nav_markup_close;

    echo $nav_output;
}


// Add the clearfix class to the entry header.
remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_open', 5 ); // Remove the header opening tag
add_action( 'genesis_entry_header', 'tso_entry_header_markup_open', 5 );
function tso_entry_header_markup_open() {
    printf( '<header class="entry-header clearfix">');
}

// TSO - Add the date before the post header
// TODO: CONSIDER CHANGING TO genesis_before_post_content SO TITLE DOESN'T HAVE TO BE MANIPULATED
add_filter( 'genesis_post_title_output', 'tso_post_title_output' );
add_filter( 'genesis_post_title_output', 'do_shortcode', 20 );

function tso_post_title_output( $title ) {

    $isCurrentYear = get_the_time( 'Y' ) === date('Y');

    // Add the date div before the title
    $date = '<div class="tso-date';
    if (!$isCurrentYear)
        $date .= ' previous-year';
    $date .= '">';
    $date .= '<div class="tso-month">';
    $date .= ( strlen(get_the_time( 'F' )) > 5 ) ? '[post_date format="M"]' : '[post_date format="F"]';
    $date .= '</div>';
    $date .= '<div class="tso-day">[post_date format="d"]</div>';

    // Only display the year if it's not the current year.
    if ( !$isCurrentYear ) {
        $date .= '<div class="tso-year">[post_date format="Y"]</div>';
    }

    $date .= '</div>';

    return $date . $title;
}
// TSO - Customize post info
//* Customize the post info function
add_filter( 'genesis_post_info', 'tso_post_info_filter' );
function tso_post_info_filter($post_info) {
    if ( !is_page() ) {
        // Default - for reference
//        $post_info = '[post_date] by [post_author_posts_link] [post_comments] [post_edit]';
        $post_info = '[post_categories_no_uncategorized before=""]';
        return $post_info;
    }
}

// TSO - Customize the entry footer
add_filter( 'genesis_post_meta', 'sp_post_meta_filter' );
function sp_post_meta_filter($post_meta) {
    // Default - for reference
    // $post_meta = '[post_categories] [post_tags]';
     $post_meta = '[post_comments] [post_edit]';

    return $post_meta;
}