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
// Init the TSO global variables for use within this plugin.
init_tso_global_vars();

// Unregister any unwanted Genesis widgets
add_action( 'widgets_init', 'unregister_genesis_widgets', 20 );
function unregister_genesis_widgets() {
    unregister_widget( 'Genesis_User_Profile_Widget' );
}


// Add/register TSO widgets
include_once( CHILD_DIR . '/lib/widgets/user-profile-widget.php' );
include_once( CHILD_DIR . '/lib/widgets/tso-social-icon-widget.php' );
include_once( CHILD_DIR . '/lib/widgets/tso-blog-follow-widget.php' );
register_widget( 'TSO_User_Profile_Widget' );
register_widget( 'TSO_Social_Icon_Widget' );
register_widget( 'TSO_Blog_Follow_Widget' );

// enqueue google fonts
add_action( 'wp_enqueue_scripts', 'genesis_sample_google_fonts' );
function genesis_sample_google_fonts() {
    wp_enqueue_style( 'google-font-sanchez-and-signika', '//fonts.googleapis.com/css?family=Sanchez:400,400italic|Signika:700', array(), child_theme_version );
}

// enqueue any custom js libs at bottom of page
/** Load scripts before closing the body tag */
add_action('genesis_after_footer', 'tso_footer_js_scripts');
function tso_footer_js_scripts() {
//    wp_register_script( 'pinterest', '//assets.pinterest.com/js/pinit.js' );
//    wp_enqueue_script( 'pinterest', '//assets.pinterest.com/js/pinit.js');
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
                    <a href="/about/">about</a>
                </li>
                <li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-logo">
                    <a href="/"></a>
                </li>
                <li class="menu-item menu-item-type-custom menu-item-object-custom">
                    <a href="/projects/">projects</a>
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
add_filter( 'genesis_post_title_output', 'tso_post_title_output' );
add_filter( 'genesis_post_title_output', 'do_shortcode', 20 );

function tso_post_title_output( $title ) {

    $date = '';

    // Only add the date if it's a post
    if ( 'post' === get_post_type() ) {

        $isCurrentYear = get_the_time( 'Y' ) === date('Y');

        // Add the date div before the title
        $date .= '<div class="tso-date';
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
    }

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

// TSO - customize entry footer open to add sharing links
remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_open', 5 );
add_action( 'genesis_entry_footer', 'tso_entry_footer_markup_open', 5 );
function tso_entry_footer_markup_open() {

    if ( 'post' === get_post_type() ) {
        printf( '<footer %s>', genesis_attr( 'entry-footer' ) );

        // Add sharing icons
        $post_footer_sharing_icons = '<div class="tso-entry-footer-sharing">
                    <ul>
                        <li><a title="Share via Pinterest" href="javascript:void((function(){var%20e=document.createElement(\'script\');e.setAttribute(\'type\',\'text/javascript\');e.setAttribute(\'charset\',\'UTF-8\');e.setAttribute(\'src\',\'http://assets.pinterest.com/js/pinmarklet.js?r=\'+Math.random()*99999999);document.body.appendChild(e)})());""><div class="tso-social-icon tso-icon-pinterest"></div></a></li>
                        <li><a title="Share via Facebook" target="_blank" href="http://www.facebook.com/sharer.php?u='. get_permalink() . '&amp;t=' . get_the_title() . '"><div class="tso-social-icon tso-icon-facebook"></div></a></li>
                        <li><a title="Share via Twitter" target="_blank" href="http://twitter.com/share?text=' . str_replace(" ", "%20", get_the_title()) . '%20%23sapphireowl&url='. get_permalink() . '"><div class="tso-social-icon tso-icon-twitter"></div></a></li>
                        <li><a title="Share via Email" href="mailto:?subject=Check%20out%20this%20post%20from%20The%20Sapphire%20Owl: ' . str_replace(" ", "%20", get_the_title()) . '&body=I%20just%20read%20this%20post%20on%20The%20Sapphire%20Owl%20blog%20and%20thought%20you%20might%20be%20interested.%20Check%20it%20out! '. get_permalink() . '"><div class="tso-social-icon tso-icon-email"></div></a></li>
                    </ul>
                </div>';

        echo $post_footer_sharing_icons;
    }
}

// TSO - Customize the entry footer meta
add_filter( 'genesis_post_meta', 'tso_post_meta_filter' );
function tso_post_meta_filter($post_meta) {
    // Default - for reference
    // $post_meta = '[post_categories] [post_tags]';
    $post_meta = '[post_comments] [post_edit]';

    return $post_meta;
}

// TSO Breadcrumbs
add_filter('genesis_breadcrumb_args', 'tso_breadcrumb_args');
function tso_breadcrumb_args( $args ){
    $args['sep'] = ' &gt; '; // Separator
    $args['labels']['prefix'] = ''; // Remove 'You are here'

    return $args;
}

// TSO - Customize the footer text
add_filter('genesis_footer_creds_text', 'tso_footer_creds_filter');
function tso_footer_creds_filter( $creds ) {
    $creds = '<div class="tso-footer-wrapper"><ul class="tso-footer-menu">';
    $creds .= '<li class="menu-item">Copyright [footer_copyright] &middot; The Sapphire Owl </li>';
    $creds .= '<li class="menu-item tso-footer-logo"></li>';
    $creds .= '<li class="menu-item"> Design by Christy Lewis &middot; Built on <a href="http://www.studiopress.com/themes/genesis" target="_blank" title="Genesis Framework">Genesis</a></li>';
    $creds .= '</ul></div>';
    return $creds;
}