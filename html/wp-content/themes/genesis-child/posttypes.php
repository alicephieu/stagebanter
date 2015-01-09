<?php

// Add new post type for Venues funky git test
add_action('init', 'venues_init');
function venues_init() 
{
	$venues_labels = array(
		'name' => _x('Venues', 'post type general name'),
		'singular_name' => _x('Venue', 'post type singular name'),
		'all_items' => __('All Venues'),
		'add_new' => _x('Add new venue', 'venues'),
		'add_new_item' => __('Add new venue'),
		'edit_item' => __('Edit venue'),
		'new_item' => __('New venue'),
		'view_item' => __('View venue'),
		'search_items' => __('Search in venues'),
		'not_found' =>  __('No venues found'),
		'not_found_in_trash' => __('No venues found in trash'), 
		'parent_item_colon' => ''
	);
	$args = array(
		'labels' => $venues_labels,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true, 
		'query_var' => true,
		'rewrite' => true,
		'capability_type' => 'post',
		'hierarchical' => false,
		'menu_position' => 5,
		'supports' => array('title','editor','author','thumbnail','excerpt','comments','custom-fields')
	); 
	register_post_type('venues',$args);
}

// Add new Custom Post Type icons
add_action( 'admin_head', 'venue_icons' );
function venue_icons() {
?>
	<style type="text/css" media="screen">
		#menu-posts-venues .wp-menu-image {
			background: url(<?php bloginfo('url') ?>/wp-content/themes/genesis-child/images/musicplus.png) no-repeat 6px !important;
		}
		.icon32-posts-venues {
			background: url(<?php bloginfo('url') ?>/wp-content/themes/genesis-child/images/musicplusbig.png) no-repeat !important;
		}
		

    </style>
<?php } 

// Add custom taxonomies
add_action( 'init', 'venues_create_taxonomies', 0 );

function venues_create_taxonomies() 
{
	// Provinces/Territories
	$province_labels = array(
		'name' => _x( 'Provinces/Territories', 'taxonomy general name' ),
		'singular_name' => _x( 'Province/Territory', 'taxonomy singular name' ),
		'search_items' =>  __( 'Search in provinces/territories' ),
		'all_items' => __( 'All provinces/territories' ),
		'most_used_items' => null,
		'parent_item' => null,
		'parent_item_colon' => null,
		'edit_item' => __( 'Edit province/territory' ), 
		'update_item' => __( 'Update province/territory' ),
		'add_new_item' => __( 'Add new province/territory' ),
		'new_item_name' => __( 'New province/territory' ),
		'menu_name' => __( 'Provinces/Territories' ),
	);
	register_taxonomy('provinces/territories','venues',array(
		'hierarchical' => true,
		'labels' => $province_labels,
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => array('slug' => 'province' )
	));

	// Alberta Cities
	$ABcity_labels = array(
		'name' => _x( 'Cities in Alberta', 'taxonomy general name' ),
		'singular_name' => _x( 'Alberta', 'taxonomy singular name' ),
		'search_items' =>  __( 'Search in Alberta' ),
		'all_items' => __( 'All Alberta' ),
		'most_used_items' => null,
		'parent_item' => null,
		'parent_item_colon' => null,
		'edit_item' => __( 'Edit Alberta' ), 
		'update_item' => __( 'Update Alberta' ),
		'add_new_item' => __( 'Add new Alberta' ),
		'new_item_name' => __( 'New Alberta' ),
		'menu_name' => __( 'Alberta' ),
	);
	register_taxonomy('AB_cities','venues',array(
		'hierarchical' => true,
		'labels' => $ABcity_labels,
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => array('slug' => 'Alberta' )
	));

	// British Columbia Cities
	$BCcity_labels = array(
		'name' => _x( 'Cities in British Columbia', 'taxonomy general name' ),
		'singular_name' => _x( 'BC_City', 'taxonomy singular name' ),
		'search_items' =>  __( 'Search in BC_city' ),
		'all_items' => __( 'All BC_cities' ),
		'most_used_items' => null,
		'parent_item' => null,
		'parent_item_colon' => null,
		'edit_item' => __( 'Edit BC_city' ), 
		'update_item' => __( 'Update BC_city' ),
		'add_new_item' => __( 'Add new BC_city' ),
		'new_item_name' => __( 'New BC_city' ),
		'menu_name' => __( 'BC_Cities' ),
	);
	register_taxonomy('BC_cities','venues',array(
		'hierarchical' => true,
		'labels' => $BCcity_labels,
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => array('slug' => 'BCcity' )
	));

// Manitoba Cities
	$MBcity_labels = array(
		'name' => _x( 'Cities in Manitoba', 'taxonomy general name' ),
		'singular_name' => _x( 'MB_City', 'taxonomy singular name' ),
		'search_items' =>  __( 'Search in MB_city' ),
		'all_items' => __( 'All MB_cities' ),
		'most_used_items' => null,
		'parent_item' => null,
		'parent_item_colon' => null,
		'edit_item' => __( 'Edit MB_city' ), 
		'update_item' => __( 'Update MB_city' ),
		'add_new_item' => __( 'Add new MB_city' ),
		'new_item_name' => __( 'New MB_city' ),
		'menu_name' => __( 'MB_Cities' ),
	);
	register_taxonomy('MB_cities','venues',array(
		'hierarchical' => true,
		'labels' => $MBcity_labels,
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => array('slug' => 'MBcity' )
	));
	
	// New Brunswick Cities
	$NBcity_labels = array(
		'name' => _x( 'Cities in New Brunswick', 'taxonomy general name' ),
		'singular_name' => _x( 'NB_City', 'taxonomy singular name' ),
		'search_items' =>  __( 'Search in NB_city' ),
		'all_items' => __( 'All NB_cities' ),
		'most_used_items' => null,
		'parent_item' => null,
		'parent_item_colon' => null,
		'edit_item' => __( 'Edit NB_city' ), 
		'update_item' => __( 'Update NB_city' ),
		'add_new_item' => __( 'Add new NB_city' ),
		'new_item_name' => __( 'New NB_city' ),
		'menu_name' => __( 'NB_Cities' ),
	);
	register_taxonomy('NB_cities','venues',array(
		'hierarchical' => true,
		'labels' => $NBcity_labels,
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => array('slug' => 'NBcity' )
	));

	// Newfoundland and Labrador Cities
	$NLcity_labels = array(
		'name' => _x( 'Cities in Newfoundland and Labrador', 'taxonomy general name' ),
		'singular_name' => _x( 'NL_City', 'taxonomy singular name' ),
		'search_items' =>  __( 'Search in NL_city' ),
		'all_items' => __( 'All NL_cities' ),
		'most_used_items' => null,
		'parent_item' => null,
		'parent_item_colon' => null,
		'edit_item' => __( 'Edit NL_city' ), 
		'update_item' => __( 'Update NL_city' ),
		'add_new_item' => __( 'Add new NL_city' ),
		'new_item_name' => __( 'New NL_city' ),
		'menu_name' => __( 'NL_Cities' ),
	);
	register_taxonomy('NL_cities','venues',array(
		'hierarchical' => true,
		'labels' => $NLcity_labels,
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => array('slug' => 'NLcity' )
	));

	// Nova Scotia Cities
	$NScity_labels = array(
		'name' => _x( 'Cities in Nova Scotia', 'taxonomy general name' ),
		'singular_name' => _x( 'NS_City', 'taxonomy singular name' ),
		'search_items' =>  __( 'Search in NS_city' ),
		'all_items' => __( 'All NS_cities' ),
		'most_used_items' => null,
		'parent_item' => null,
		'parent_item_colon' => null,
		'edit_item' => __( 'Edit NS_city' ), 
		'update_item' => __( 'Update NS_city' ),
		'add_new_item' => __( 'Add new NS_city' ),
		'new_item_name' => __( 'New NS_city' ),
		'menu_name' => __( 'NS_Cities' ),
	);
	register_taxonomy('NS_cities','venues',array(
		'hierarchical' => true,
		'labels' => $NScity_labels,
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => array('slug' => 'NScity' )
	));

	// Northwest Territories Cities
	$NTcity_labels = array(
		'name' => _x( 'Cities in Northwest Territories', 'taxonomy general name' ),
		'singular_name' => _x( 'NT_City', 'taxonomy singular name' ),
		'search_items' =>  __( 'Search in NT_city' ),
		'all_items' => __( 'All NT_cities' ),
		'most_used_items' => null,
		'parent_item' => null,
		'parent_item_colon' => null,
		'edit_item' => __( 'Edit NT_city' ), 
		'update_item' => __( 'Update NT_city' ),
		'add_new_item' => __( 'Add new NT_city' ),
		'new_item_name' => __( 'New NT_city' ),
		'menu_name' => __( 'NT_Cities' ),
	);
	register_taxonomy('NT_cities','venues',array(
		'hierarchical' => true,
		'labels' => $NTcity_labels,
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => array('slug' => 'NTcity' )
	));

	// Nunavut Cities
	$NUcity_labels = array(
		'name' => _x( 'Cities in Nunavut', 'taxonomy general name' ),
		'singular_name' => _x( 'NU_City', 'taxonomy singular name' ),
		'search_items' =>  __( 'Search in NU_city' ),
		'all_items' => __( 'All NU_cities' ),
		'most_used_items' => null,
		'parent_item' => null,
		'parent_item_colon' => null,
		'edit_item' => __( 'Edit NU_city' ), 
		'update_item' => __( 'Update NU_city' ),
		'add_new_item' => __( 'Add new NU_city' ),
		'new_item_name' => __( 'New NU_city' ),
		'menu_name' => __( 'NU_Cities' ),
	);
	register_taxonomy('NU_cities','venues',array(
		'hierarchical' => true,
		'labels' => $NUcity_labels,
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => array('slug' => 'NUcity' )
	));

	// Ontario Cities
	$ONcity_labels = array(
		'name' => _x( 'Cities in Ontario', 'taxonomy general name' ),
		'singular_name' => _x( 'ON_City', 'taxonomy singular name' ),
		'search_items' =>  __( 'Search in ON_city' ),
		'all_items' => __( 'All ON_cities' ),
		'most_used_items' => null,
		'parent_item' => null,
		'parent_item_colon' => null,
		'edit_item' => __( 'Edit ON_city' ), 
		'update_item' => __( 'Update ON_city' ),
		'add_new_item' => __( 'Add new ON_city' ),
		'new_item_name' => __( 'New ON_city' ),
		'menu_name' => __( 'ON_Cities' ),
	);
	register_taxonomy('ON_cities','venues',array(
		'hierarchical' => true,
		'labels' => $ONcity_labels,
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => array('slug' => 'ONcity' )
	));

	// Prince Edward Island Cities
	$PEcity_labels = array(
		'name' => _x( 'Cities in Prince Edward Island', 'taxonomy general name' ),
		'singular_name' => _x( 'PE_City', 'taxonomy singular name' ),
		'search_items' =>  __( 'Search in PE_city' ),
		'all_items' => __( 'All PE_cities' ),
		'most_used_items' => null,
		'parent_item' => null,
		'parent_item_colon' => null,
		'edit_item' => __( 'Edit PE_city' ), 
		'update_item' => __( 'Update PE_city' ),
		'add_new_item' => __( 'Add new PE_city' ),
		'new_item_name' => __( 'New PE_city' ),
		'menu_name' => __( 'PE_Cities' ),
	);
	register_taxonomy('PE_cities','venues',array(
		'hierarchical' => true,
		'labels' => $PEcity_labels,
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => array('slug' => 'PEcity' )
	));

	// Quebec Cities
	$QCcity_labels = array(
		'name' => _x( 'Cities in Quebec', 'taxonomy general name' ),
		'singular_name' => _x( 'QC_City', 'taxonomy singular name' ),
		'search_items' =>  __( 'Search in QC_city' ),
		'all_items' => __( 'All QC_cities' ),
		'most_used_items' => null,
		'parent_item' => null,
		'parent_item_colon' => null,
		'edit_item' => __( 'Edit QC_city' ), 
		'update_item' => __( 'Update QC_city' ),
		'add_new_item' => __( 'Add new QC_city' ),
		'new_item_name' => __( 'New QC_city' ),
		'menu_name' => __( 'QC_Cities' ),
	);
	register_taxonomy('QC_cities','venues',array(
		'hierarchical' => true,
		'labels' => $QCcity_labels,
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => array('slug' => 'QCcity' )
	));

	// Saskatchewan Cities
	$SKcity_labels = array(
		'name' => _x( 'Cities in Saskatchewan', 'taxonomy general name' ),
		'singular_name' => _x( 'SK_City', 'taxonomy singular name' ),
		'search_items' =>  __( 'Search in SK_city' ),
		'all_items' => __( 'All SK_cities' ),
		'most_used_items' => null,
		'parent_item' => null,
		'parent_item_colon' => null,
		'edit_item' => __( 'Edit SK_city' ), 
		'update_item' => __( 'Update SK_city' ),
		'add_new_item' => __( 'Add new SK_city' ),
		'new_item_name' => __( 'New SK_city' ),
		'menu_name' => __( 'SK_Cities' ),
	);
	register_taxonomy('SK_cities','venues',array(
		'hierarchical' => true,
		'labels' => $SKcity_labels,
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => array('slug' => 'SKcity' )
	));

	// Yukon Cities
	$YTcity_labels = array(
		'name' => _x( 'Cities in Yukon', 'taxonomy general name' ),
		'singular_name' => _x( 'YT_City', 'taxonomy singular name' ),
		'search_items' =>  __( 'Search in YT_city' ),
		'all_items' => __( 'All YT_cities' ),
		'most_used_items' => null,
		'parent_item' => null,
		'parent_item_colon' => null,
		'edit_item' => __( 'Edit YT_city' ), 
		'update_item' => __( 'Update YT_city' ),
		'add_new_item' => __( 'Add new YT_city' ),
		'new_item_name' => __( 'New YT_city' ),
		'menu_name' => __( 'YT_Cities' ),
	);
	register_taxonomy('YT_cities','venues',array(
		'hierarchical' => true,
		'labels' => $YTcity_labels,
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => array('slug' => 'YTcity' )
	));

	// Genres
		$genres_labels = array(
		'name' => _x( 'Genres', 'taxonomy general name' ),
		'singular_name' => _x( 'Genre', 'taxonomy singular name' ),
		'search_items' =>  __( 'Search genres' ),
		'popular_items' => __( 'Popular genres' ),
		'all_items' => __( 'All genres' ),
		'most_used_items' => null,
		'parent_item' => null,
		'parent_item_colon' => null,
		'edit_item' => __( 'Edit genre' ), 
		'update_item' => __( 'Update genre' ),
		'add_new_item' => __( 'Add new genre' ),
		'new_item_name' => __( 'New genre name' ),
		'separate_items_with_commas' => __( 'Separate genres with commas' ),
	    'add_or_remove_items' => __( 'Add or remove genres' ),
	    'choose_from_most_used' => __( 'Choose from the most used genres' ),
		'menu_name' => __( 'Genres' ),
	);
	register_taxonomy('genres','venues',array(
		'hierarchical' => false,
		'labels' => $genres_labels,
		'show_ui' => true,
		'update_count_callback' => '_update_post_term_count',
		'query_var' => true,
		'rewrite' => array('slug' => 'genre' )
	));

	
	// Capacity
	$capacity_labels = array(
		'name' => _x( 'Capacity', 'taxonomy general name' ),
		'singular_name' => _x( 'Capacity', 'taxonomy singular name' ),
		'search_items' =>  __( 'Search in capacities' ),
		'all_items' => __( 'All capacities' ),
		'most_used_items' => null,
		'parent_item' => null,
		'parent_item_colon' => null,
		'edit_item' => __( 'Edit capacity' ), 
		'update_item' => __( 'Update capacity' ),
		'add_new_item' => __( 'Add new capacity' ),
		'new_item_name' => __( 'New capacity' ),
		'menu_name' => __( 'Capacities' ),
	);
	register_taxonomy('capacity','venues',array(
		'hierarchical' => true,
		'labels' => $capacity_labels,
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => array('slug' => 'capacity' )
	));


	// Live Music
	$capacity_labels = array(
		'name' => _x( 'Hosts live music on (days of the week)', 'taxonomy general name' ),
		'singular_name' => _x( 'Live_Music', 'taxonomy singular name' ),
		'search_items' =>  __( 'Search in live_musics' ),
		'all_items' => __( 'All live_musics' ),
		'most_used_items' => null,
		'parent_item' => null,
		'parent_item_colon' => null,
		'edit_item' => __( 'Edit live_music' ), 
		'update_item' => __( 'Update live_music' ),
		'add_new_item' => __( 'Add new live_music' ),
		'new_item_name' => __( 'New live_music' ),
		'menu_name' => __( 'Live Music' ),
	);
	register_taxonomy('live_music','venues',array(
		'hierarchical' => true,
		'labels' => $capacity_labels,
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => array('slug' => 'live_music' )
	));
}
?>