<?php
class Mappress_Query {
	function __construct() {}
	
	function register() {
		// Query parameters
		add_filter('query_vars', array(__CLASS__, 'filter_query_vars'));
		add_filter('parse_query', array(__CLASS__, 'filter_parse_query'));
		add_filter('posts_where', array(__CLASS__, 'filter_posts_where'), 10, 2);

		// AJAX
		add_action('wp_ajax_mapp_query', array(__CLASS__, 'ajax_query'));
		add_action('wp_ajax_nopriv_mapp_query', array(__CLASS__, 'ajax_query'));
	}
	
	/**
	* Parse a query from a shortcode
	* 
	* Special 'query' parameter values:
	*   query='' or query='current' : current blog posts (default)
	*   query='all' : show ALL posts with a map
	* 
	* @param array $atts - shortcode atts
	* @return array - query for the shortcode (as an array)
	*/
	static function parse_query($atts) {
		$query = (isset($atts['query'])) ? $atts['query'] : array();

		// Back-compat for old mashup atts ('show' and 'show_query')
		if (isset($atts['show']) && !isset($atts['query'])) {
			$show = $atts['show'];
			$show_query = (isset($atts['show_query'])) ? $atts['show_query'] : '';
			$query = (empty($show) || $show == 'current' || $show == 'all') ? $show : $show_query;
		}

		// Special case 'all'
		if ($query == 'all') {
			$query = array('post_type' => 'any');
			$query['posts_per_page'] = isset($query['posts_per_page']) ? $query['posts_per_page'] : -1;
						
		// Special case 'current' : treat this as a static map using the current posts verbatim
		} elseif ($query == 'current' || empty($query)) {
			$query = null;
		
		// If query is a querystring, convert it to an array
		} elseif (!is_array($query)) {

			// If query came from a shortcode WP replaces "&" with "&amp" so convert back
			$query = str_replace(array('&amp;', '&#038;'), array('&', '&'), $query);

			parse_str($query, $query);

			// Explode array parameters into arrays.  Note that WP *requires* some parameters as comma-separated strings
			// for example 'category_name' and old-style custom taxonomies (mytax=a,b), *require* a comma-separated string; 'author' and 'cat' work either way
			// These parameters should always be arrays:
			$array_keys = array('category__in', 'category__not_in', 'category__and', 'post__in', 'post__not_in', 'tag__in', 'tag__not_in', 'tag__and', 'tag_slug__in', 'tag_slug__and');

			// These parameters may be a string or an array
			$string_array_keys = array('post_type', 'post_status');
			
			foreach($query as $index => $arg) {
				if (in_array($index, $array_keys)) 
					$query[$index] = explode(',', $arg);

				else if (in_array($index, $string_array_keys) && strpos($arg, ',') !== false)
					$query[$index] = explode(',', $arg);					
			}
		}
				
		return $query;
	}	
	
	static function ajax_query() {
		global $mappress;

		// Capture any unwanted output
		ob_start();

		// Parameters are sent as json
		$name = (isset($_POST['name'])) ? $_POST['name'] : null;
		$query = (isset($_POST['query'])) ? $_POST['query'] : null;		
		$options = isset($_POST['options']) ? $_POST['options'] : null;

		// Until paged results is implemented, default to all posts		
		$query['posts_per_page'] = isset($query['posts_per_page']) ? $query['posts_per_page'] : -1;		
		
		// The options contain booleans which are sent in $_POST as strings; convert back to booleans
		$options = Mappress::string_to_boolean($options);
		$map = new Mappress_Map($options);
		$map->name = $name;
		$map->query = $query;

		// Run the new query 
		$results = self::query($map->query);
		$map->pois = $results->pois;
		
		// Prepare the map
		$map->prepare();

		// Get the poi list
		$poi_list = ($map->options->poiList) ? $mappress->get_poi_list($map) : "";
		Mappress::ajax_response('OK', array('pois' => $map->pois, 'poiList' => $poi_list));
	}
			
	/**
	* Get query results
	* 
	* @param mixed $map
	*/
	static function query($query) {
		// Set query fields
		$query['map'] = true; // only get posts with maps
		$query['post_type'] = (isset($query['post_type'])) ? $query['post_type'] : 'any';	// commonly forgotten
		$query['cache_results'] = false;

		// Run the query
		$wpq = new WP_Query($query);
		
		// Fetch POIs
		$pois = self::get_query_pois($wpq);
			
		$query_result = array('posts_per_page' => $wpq->query_vars['posts_per_page'], 'paged' => $wpq->query_vars['paged'], 'max_num_pages' => $wpq->max_num_pages, 'found_posts' => $wpq->found_posts);
		return (object) array('queryResult' => $query_result, 'pois' => $pois);
	}

	/** 
	* Check if a query is empty, i.e. returns no POIs
	* 	
	* @param mixed $query
	* @return mixed
	*/
	static function is_empty($query) {
		global $wp_query;

		// For 'current' query just check current posts
		if (empty($query))
			return ( count($wp_query->posts) == 0 );
		
		// Set query fields
		$query['map'] = true; // only get posts with maps
		$query['post_type'] = (isset($query['post_type'])) ? $query['post_type'] : 'any';	// commonly forgotten
		$query['cache_results'] = false;
		$query['posts_per_page'] = 1;	// Max 1 post returned
			
		// Check that at least 1 post will be returned
		$wpq = new WP_Query($query);
		return ( count($wpq->posts) == 0 );
	}
		
		
	
	/**
	* Get pois for a wp_query object
	* 	
	* @param mixed $wpq
	*/
	static function get_query_pois($wpq) {
		// Gather the pois for all posts
		$pois = array();
		foreach($wpq->posts as $post) {
			
			// Get the maps for each post
			$maps = Mappress_Map::get_post_map_list($post->ID);

			// Add the pois for each map
			foreach((array)$maps as $map) {
				foreach ($map->pois as $poi) {					
					$poi->postid = $post->ID;
					$pois[] = $poi;
				}
			}
		}		
		return $pois;
	}

	/**
	* Remove the is_admin flag from map queries 
	* During frontend AJAX calls WP thinks it's running in the admin, so WP_Query will return private, draft, etc. posts that should be hidden
	* See: http://core.trac.wordpress.org/ticket/12400
	* 
	* @param mixed $query
	*/
	function filter_parse_query( $query ) {
		if (isset($query->query_vars['map']) && $query->query_vars['map'])
			$query->is_admin = false;
		return $query;
	}
	
	
	/**
	* Add map query variables
	*
	* @param mixed $qvars
	*/
	function filter_query_vars ( $qv ) {
		$qv[] = 'map';
		return $qv;
	}

	/**
	* Join to map table to select only posts with at least one map
	*
	* @param mixed $join
	* @param mixed $query
	*/
	function filter_posts_where($where, $query) {
		global $wpdb;

		$qv = $query->query_vars;

		if (isset($qv['map']) && $qv['map']) {
			$posts_table = $wpdb->prefix . 'mappress_posts';
			$where .= " AND EXISTS ( SELECT mapid FROM $posts_table WHERE $posts_table.postid = $wpdb->posts.ID ) ";
		}
		return $where;
	}		
}
?>
