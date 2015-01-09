<?php
/**
 * Functions
 *
 * @package      BE_Genesis_Child
 * @since        1.0.0
 * @link         https://github.com/billerickson/BE-Genesis-Child
 * @author       Bill Erickson <bill@billerickson.net>
 * @copyright    Copyright (c) 2011, Bill Erickson
 * @license      http://opensource.org/licenses/gpl-2.0.php GNU Public License
 *
 */

/**
 * Theme Setup
 * @since 1.0.0
 *
 * This setup function attaches all of the site-wide functions 
 * to the correct hooks and filters. All the functions themselves
 * are defined below this setup function.
 *
 */
// include custom post types
include_once(ABSPATH . 'wp-content/themes/genesis-child/posttypes.php');



// include venues custom screen options


add_filter( 'manage_edit-venues_columns', 'set_custom_edit_venues_columns' );
add_action( 'manage_venues_posts_custom_column' , 'custom_columns', 10, 2 );

function set_custom_edit_venues_columns($columns) {
    return $columns 
         + array('provinces/territories' => __('Province'), 
                 'cities' => __('City'));
}

function custom_columns( $column, $post_id ) {
	switch ( $column ) {
	case 'provinces/territories':
		$terms = get_the_term_list( $post->ID , 'provinces/territories' , '' , ',' , '' );
		if ( is_string( $terms ) ) {
			echo $terms;
		} else {
			echo 'Unable to get province/territory';
		}
		break;

	case 'cities':
		$terms = get_the_term_list( $post->ID , array('AB_cities','BC_cities','MB_cities','NB_cities','NL_cities','NS_cities','NT_cities','NU_cities','ON_cities','PE_cities','QC_cities','SK_cities','YT_cities') , '' , ',' , '' );
		if ( is_string( $terms ) ) {
			echo $terms;
		} else {
			echo 'Unable to get city';
		}
		break;

	}
}

// end venues custom screen options

//Feature image functionality
add_theme_support( 'post-thumbnails' );

/** Add new image sizes **/
add_image_size('grid-thumbnail', 100, 100, TRUE);



add_action('genesis_setup','child_theme_setup', 15);
function child_theme_setup() {
	
	// ** Backend **	
	
	// Image Sizes
	// add_image_size( 'be_featured', 400, 100, true );
	
	// Menus
	add_theme_support( 'genesis-menus', array( 'primary' => 'Primary Navigation Menu' ) );
	
	// Sidebars
	//unregister_sidebar( 'sidebar-alt' );
	//genesis_register_sidebar( array( 'name' => 'Blog Sidebar', 'id' => 'blog-sidebar' ) );
	add_theme_support( 'genesis-footer-widgets', 4 );

	// Remove Unused Page Layouts
	//genesis_unregister_layout( 'full-width-content' );
	//genesis_unregister_layout( 'content-sidebar' );	
	//genesis_unregister_layout( 'sidebar-content' );
	//genesis_unregister_layout( 'content-sidebar-sidebar' );
	//genesis_unregister_layout( 'sidebar-sidebar-content' );
	//genesis_unregister_layout( 'sidebar-content-sidebar' );
	
	// Remove Unused User Settings
	add_filter( 'user_contactmethods', 'be_contactmethods' );
	remove_action( 'show_user_profile', 'genesis_user_options_fields' );
	remove_action( 'edit_user_profile', 'genesis_user_options_fields' );
	remove_action( 'show_user_profile', 'genesis_user_archive_fields' );
	remove_action( 'edit_user_profile', 'genesis_user_archive_fields' );
	remove_action( 'show_user_profile', 'genesis_user_seo_fields' );
	remove_action( 'edit_user_profile', 'genesis_user_seo_fields' );
	remove_action( 'show_user_profile', 'genesis_user_layout_fields' );
	remove_action( 'edit_user_profile', 'genesis_user_layout_fields' );

	// Editor Styles
	add_editor_style( 'editor-style.css' );
		
	// Setup Theme Settings
	include_once( CHILD_DIR . '/lib/functions/child-theme-settings.php' );
	
	// Don't update theme
	add_filter( 'http_request_args', 'be_dont_update_theme', 5, 2 );
		
	// ** Frontend **		
	
	// Remove Edit link
	add_filter( 'genesis_edit_post_link', '__return_false' );
	
	// Responsive Meta Tag
	add_action( 'genesis_meta', 'be_viewport_meta_tag' );
	
	// Footer
	remove_action( 'genesis_footer', 'genesis_do_footer' );
	add_action( 'genesis_footer', 'sb_footer' );
}

// ** Backend Functions ** //

/**
 * Customize Contact Methods
 * @since 1.0.0
 *
 * @author Bill Erickson
 * @link http://sillybean.net/2010/01/creating-a-user-directory-part-1-changing-user-contact-fields/
 *
 * @param array $contactmethods
 * @return array
 */
function be_contactmethods( $contactmethods ) {
	unset( $contactmethods['aim'] );
	unset( $contactmethods['yim'] );
	unset( $contactmethods['jabber'] );
	
	return $contactmethods;
}

/**
 * Don't Update Theme
 * @since 1.0.0
 *
 * If there is a theme in the repo with the same name, 
 * this prevents WP from prompting an update.
 *
 * @author Mark Jaquith
 * @link http://markjaquith.wordpress.com/2009/12/14/excluding-your-plugin-or-theme-from-update-checks/
 *
 * @param array $r, request arguments
 * @param string $url, request url
 * @return array request arguments
 */

function be_dont_update_theme( $r, $url ) {
	if ( 0 !== strpos( $url, 'http://api.wordpress.org/themes/update-check' ) )
		return $r; // Not a theme update request. Bail immediately.
	$themes = unserialize( $r['body']['themes'] );
	unset( $themes[ get_option( 'template' ) ] );
	unset( $themes[ get_option( 'stylesheet' ) ] );
	$r['body']['themes'] = serialize( $themes );
	return $r;
}

// ** Frontend Functions ** //

/**
 * Viewport Meta Tag for Mobile Browsers
 *
 * @author Bill Erickson
 * @link http://www.billerickson.net/code/responsive-meta-tag
 */
function be_viewport_meta_tag() {
	echo '<meta name="viewport" content="width=device-width, initial-scale=1.0"/>';
}

/**
 * Footer 
 *
 */

// add footer menu
// register_nav_menus( array( 'footer' => 'footernav' ) );

function sb_footer() {
	$creds = '&copy; Copyright '. date('Y') . ' ' . get_bloginfo('name') . '. All rights reserved.';
    ?>
  <p class="creds"><?php echo $creds; ?></p>
<?php }

/**
 * Comments
 *
 */

/** Modify the speak your mind text */
add_filter( 'genesis_comment_form_args', 'custom_comment_form_args' );
function custom_comment_form_args($args) {
    $args['title_reply'] = 'Leave a Comment';
    return $args;
}
/** Add a comment policy box */
add_action( 'genesis_after_comments', 'single_post_comment_policy' );
function single_post_comment_policy() {
    if ( is_single() && !is_user_logged_in() && comments_open() ) {
    ?>
    <div class="comment-policy-box">
        <p class="comment-policy"><small><strong>Comment Policy:</strong>Your words are your own, so be nice and helpful if you can. Consider posting helpful information about venue sound systems, promotion and door deals.  Opinions about this kind of information can vary from group to group, but will be helpful for other bands.  Please limit the amount of links submitted in your comment.</small></p>   
    </div>   
    <?php
    }
}

// function my_qmt_base_url() {
// 	return get_page_link( 21 );
// }
// add_filter( 'qmt_base_url', 'my_qmt_base_url' );

///automatically populate photos custom fields


   
add_action('publish_venues', 'add_Photos_custom_field_automatically');
function add_Photos_custom_field_automatically($post_ID) {
	global $wp_query;
    global $wpdb;
    if(!wp_is_post_revision($post_ID)) {
        add_post_meta($post_ID, 'Photos', '[nggallery id="' . $post_ID . '"]', true);
    }
}
add_action('publish_venues', 'add_photos_upload_custom_field_automatically');
function add_photos_upload_custom_field_automatically($post_ID) {
	global $wp_query;
    global $wpdb;
    if(!wp_is_post_revision($post_ID)) {
        add_post_meta($post_ID, 'photos_upload', '[ngg_uploader id="' . $post_ID . '"]', true);
    }
}

// Replace header hook to include logo 
remove_action( 'genesis_header', 'genesis_do_header' ); 
add_action( 'genesis_header', 'genesis_do_new_header' ); 
function genesis_do_new_header() { 
    echo '<a href="' . get_site_url() .'"><div id="title-area"><img src="' .  get_stylesheet_directory_uri() . '/images/StageBanter_Logo.png" alt="Site Logo" /></a>'; 
    do_action( 'genesis_site_title' ); 
    do_action( 'genesis_site_description' ); 
    echo '</div><!-- end #title-area -->'; 
    
}  

function feature_image_header() {
if( is_singular('venues') && has_post_thumbnail()) {
 
    genesis_image(
    array(
        'size' => 'single-post',
        'attr' => array( 'class' => 'venueimg' )
        ) );
        }
}
 
add_action('genesis_after_post_title', 'feature_image_header');





