<?php
class Mappress_Pro extends Mappress {
	
	static $meta_queue = null;
	
	function __construct() {
		parent::__construct();

		// Add mashup shortcode
		add_shortcode('mashup', array(&$this, 'shortcode_mashup'));

		// Add widget
		add_action('widgets_init', create_function('', 'return register_widget("Mappress_Widget");'));

		// Set up meta key updates only if keys are configured for address or lat/lng
		if ( !empty(self::$options->metaKeyAddress) || (!empty(self::$options->metaKeyLat) && !empty(self::$options->metaKeyLng)) ) {
			
			// WP post publish/update events
			add_action('save_post', array(&$this, 'save_post'), 100, 2);

			// Custom field change events
			add_action('updated_post_meta', array(&$this,'meta_update'), 100, 3);
			add_action('added_post_meta', array(&$this, 'meta_update'), 100, 3);
			add_action('deleted_post_meta', array(&$this, 'meta_update'), 100, 3);			
		}

		// Force update event 
		add_action('mappress_update_meta', array(&$this, 'save_post'), 10, 2);
	}

	/**
	* If a map-related custom field has changed, queue the post for update 
	* 
	* @param mixed $meta_id
	* @param mixed $object_id
	* @param mixed $meta_key
	* @param mixed $_meta_value
	*/
	function meta_update($meta_id, $object_id, $meta_key, $_meta_value = null) {
		$metas = array_merge(self::$options->metaKeyAddress, array(
			self::$options->metaKeyLat, 
			self::$options->metaKeyLng, 
			self::$options->metaKeyIconid,
			self::$options->metaKeyTitle, 
			self::$options->metaKeyBody, 
			self::$options->metaKeyZoom
		));

		// Only consider map-related custom fields
		if (!in_array($meta_key, $metas))
			return;
			
		// If there's already a queued update for another post, flush it 
		if (self::$meta_queue && self::$meta_queue != $object_id)
			$this->create_meta_map($meta_queue);
		
		// Queue current post for update				
		self::$meta_queue = $object_id;
		
		// Add a shutdown action to flush the queue
		if (has_action('shutdown', array($this, 'shutdown')) != 100)
			add_action('shutdown', array($this, 'shutdown'), 100, 2);
	}
	
	/**
	* Create new map when post is saved with custom field.  Existing map will not be changed.
	* This also flushes any queued updates.
	*
	* @param mixed $post_ID
	*/
	function save_post($post_ID) {
		// Ignore save_post for revisions
		if (wp_is_post_revision($post_ID))
			return;

		// If there's another post queued, flush it first
		if (self::$meta_queue && self::$meta_queue != $post_ID)
			$this->create_meta_map(self::$meta_queue);

		$this->create_meta_map($post_ID);
	}
	
	function shutdown() {
		// If there's an update queued, flush it
		if (self::$meta_queue)
			$this->create_meta_map(self::$meta_queue);
	}
		
	/**
	* Create a map from custom fields
	*
	* If only address field(s) are configured, the poi is geocoded and the address is corrected
	* If lat/lng fields are configured, the address is taken verbatim (or left blank) without geocoding
	*
	* @param mixed $postid - post to create the map for
	* @param mixed $atts - attributes for NEW maps (existing maps are also updated)
	* @return - none
	*/
	function create_meta_map($postid) {

		// Clear the queue
		self::$meta_queue = null;
		
		// Get any existing map
		$map = Mappress_Map::get_post_meta_map($postid);

		// If map already exists and update isn't configured then return
		if ($map && !self::$options->metaSyncSave)
			return false;

		$pois = $this->get_meta_pois($postid);

		// Geocode the POIs and discard any that are invalid
		$errors = array();
		foreach ($pois as $i => $poi) {
			$result = $poi->geocode();
			if (is_wp_error($result)) {
				$errors[] = $result;
				unset($pois[$i]);
			}
		}

		// Clear the errors field and update with any new errors
		delete_post_meta($postid, 'mappress_error');

		// Eeach error may contain multiple messages
		foreach($errors as $error) {
			$codes = $error->get_error_codes();
			foreach($codes as $code) {
				$messages = $error->get_error_messages($code);
				foreach($messages as $message)
					add_post_meta($postid, 'mappress_error', "$code : $message");
			}
		}

		// If no pois were found, delete any existing map and return
		if (empty($pois)) {
			if ($map)
				Mappress_Map::delete($map->mapid);
			return;
		}

		// Create a new map if none exists
		if (!$map)
			$map = new Mappress_Map();

		// Update the map with the new pois and recenter it
		$map->center = array('lat' => 0, 'lng' => 0);
		$map->metaKey = true;
		$map->pois = $pois;
		$map->title = __('Automatic', 'mappress');
		$zoom = (!empty(self::$options->metaKeyZoom)) ? get_post_meta($postid, self::$options->metaKeyZoom, true) : null;
		$map->zoom = ($zoom) ? $zoom : 0;
		$map->save($postid);
	}

	function get_meta_pois($postid) {
		$pois = array();

		$address_keys = self::$options->metaKeyAddress;
		$lat_key = self::$options->metaKeyLat;
		$lng_key = self::$options->metaKeyLng;

		// If there's no address or lat/lng field configured, then pois are always empty
		if (empty($address_keys) && (empty($lat_key) || empty($lng_key)) )
			return array();

		// If only 1 address field is configured and the first value contains a double quote, then treat it as a shortcode (back-compat only!)
		if (count($address_keys) == 1) {
			$values = get_post_meta($postid, $address_keys[0], false);
			if (!empty($values) && strpos($values[0], '"') > 0) {
				foreach($values as $value) {
					$atts = shortcode_parse_atts($value);
					$atts = $this->scrub_atts($atts);
					$poi = new Mappress_Poi($atts);
					if ($poi)
						$pois[] = $poi;
				}
				return $pois;
			}
		}

		// Get address
		$parts = array();
		foreach ((array) $address_keys as $key) {
			$value = get_post_meta($postid, $key, true);
			if (!empty($value))
				$parts[] = trim($value);
		}
		if (!empty($parts))
			$address = (count($parts) > 1) ? implode(', ', $parts) : $parts[0];
		else
			$address = null;

		// Get lat/lng
		$point = null;

		if (!empty($lat_key) && !empty($lng_key)) {
			$lat = trim(get_post_meta($postid, $lat_key, true));
			$lng = trim(get_post_meta($postid, $lng_key, true));

			// Only use the values if they're not empty
			if ($lat != "" && $lng != "")
				$point = array('lat' => (float) $lat, 'lng' => (float) $lng);
		}

		// If no address or lat/lng, there's nothing to do
		if (empty($address) && empty($point))
			return array();

		// The POI is valid; create it and assign icon, title, body if keys were defined for them
		$pois[] = new Mappress_Poi(array(
			'address' => $address,
			'iconid' => (!empty(self::$options->metaKeyIconid)) ? get_post_meta($postid, self::$options->metaKeyIconid, true) : null,
			'title' => (!empty(self::$options->metaKeyTitle)) ? get_post_meta($postid, self::$options->metaKeyTitle, true) : null,
			'body' => (!empty(self::$options->metaKeyBody)) ? get_post_meta($postid, self::$options->metaKeyBody, true) : null,
			'point' => $point
		));

		return $pois;
	}

	/**
	* Process the mashup shortcode
	*
	*/
	function shortcode_mashup($atts='') {
		global $post;

		// No feeds
		if (is_feed())
			return;

		// If there's no post variable, there's nothing to do - this can happen when plugins call do_shortcode()
		if (!$post)
			return;

		// Try to protect against calls to do_shortcode() in the post editor...
		if (is_admin())
			return;
			
		$atts = $this->scrub_atts($atts);		
		return $this->get_mashup($atts);
	}
	
	/**
	* Get a mashup - used by shortcode and widget
	* 
	* @param mixed $atts
	*/
	function get_mashup($atts) {
		global $wp_query;
		
		$mashup = new Mappress_Map($atts);
		$mashup->query = Mappress_Query::parse_query($atts);
		
		// If using query 'current' then create a static map for current posts
		if (empty($mashup->query)) 
			$mashup->pois = Mappress_Query::get_query_pois($wp_query);
			
		// If 'hideEmpty' is set, try to suppress the map if there are no POIs
		if ($mashup->options->hideEmpty) {
			// 'current' query - check found pois
			if (empty($mashup->query) && empty($mashup->pois)) 
				return "";
				
			// Other queries - check for at least 1 result
			if (Mappress_Query::is_empty($mashup->query))
				return "";
		}
	
		return $mashup->display();				
	}

	function print_map_styles() {
		$styles = array();

		foreach (Mappress::$options->styles as $name => $json)
			$styles[] = "\"$name\" : $json";

		$styles = implode(',', $styles);
		$styles = str_replace(array("\r\n", "\t"), array(), $styles);

		echo Mappress::script("mapp.Styles = { $styles };");
	}

	/**
	* Print a single map (extended for Pro)
	* 
	* @param mixed $map
	*/
	function print_map($map) {
		// For static maps prepare the pois and print the poi list immediately
		if (empty($map->query)) {
			// Prepare POI contents
			$map->prepare();
				
			if ($map->options->poiList)
				$this->print_poi_list($map);
		} 

		if ($map->options->directions == 'inline')
			$this->print_directions($map);			
	}
		
	function get_poi_list($map) {
		return $this->get_template($map->options->templatePoiList, array('map' => $map));
	}
	
	function print_poi_list($map) {
		if ($map->options->poiList) {
			echo "<div id='{$map->name}_poi_list_' style='display:none'>";
			$this->print_template($map->options->templatePoiList, array('map' => $map));
			echo "</div>";
		}
	}
	
	function print_directions($map) {
		echo "<div id='{$map->name}_directions_' style='display:none'>";
		$this->print_template($map->options->templateDirections, array('map' => $map));
		echo "</div>";
	}
	
	function l10n() {
		$l10n = parent::l10n();

		$l10n = array_merge($l10n, array(
			'icons' => Mappress_Icons::$user_icons,
			'iconsUrl' => Mappress_Icons::$icons_url,
			'id' => 'true',
			'standardIconsUrl' => Mappress_Icons::$standard_icons_url
		));

		return $l10n;
	}

	/**
	* Get a template to the buffer and return it
	*
	* @param mixed $template_name
	* @param mixed $args - see print_template()
	* @return mixed
	*/
	function get_template($template_name, $args = '') {
		ob_start();
		$this->print_template($template_name, $args);
		$html = ob_get_clean();
		$html = str_replace(array("\r\n", "\t"), array(), $html);  // Strip chars that won't display in html anyway
		return $html;
	}


	/**
	* Print a template.  $args:
	*   map         - map global to pass to the template
	*   poi         - poi global to pass to the template
	*
	* @param string $template_name
	* @param mixed $args
	* @return mixed
	*/
	function print_template( $template_name, $args = '' ) {
		$defaults = array(
			'map' => null,
			'poi' => null
		);
		extract(wp_parse_args($args, $defaults));
		$template_name .= ".php";

		$template_file = locate_template($template_name, false);
		if (empty($template_file))
			$template_file = Mappress::$basedir . "/templates/$template_name";
		require($template_file);
	}
} // End Class Mappress_Pro
?>