<?php
class Mappress_Pro_Settings extends Mappress_Settings {
	function __construct() {
		parent::__construct();
	}

	function admin_init() {
		parent::admin_init();

		add_settings_field('poiList', __('POI list', 'mappress'), array(&$this, 'set_poi_list'), 'mappress', 'appearance_settings');
		add_settings_field('dataTables', __('Use DataTables', 'mappress'), array(&$this, 'set_data_tables'), 'mappress', 'appearance_settings');

		add_settings_field('iwType', __('InfoWindow type', 'mappress'), array(&$this, 'set_iw_type'), 'mappress', 'poi_settings');
		add_settings_field('iwDisableAutoPan', __('InfoWindow panning', 'mappress'), array(&$this, 'set_iw_disable_auto_pan'), 'mappress', 'poi_settings');
		
		add_settings_field('defaultIcon', __('Default icon', 'mappress'), array($this, 'set_default_icon'), 'mappress', 'icons_settings');
		add_settings_field('customIconsDir', __('Custom icons directory', 'mappress'), array($this, 'set_custom_icons_dir'), 'mappress', 'icons_settings');
				
		add_settings_field('mashupTitle', __('Mashup POI title', 'mappress'), array(&$this, 'set_mashup_title'), 'mappress', 'mashup_settings');
		add_settings_field('mashupBody', __('Mashup POI body', 'mappress'), array(&$this, 'set_mashup_body'), 'mappress', 'mashup_settings');
		add_settings_field('mashupClick', __('Mashup POI click', 'mappress'), array(&$this, 'set_mashup_click'), 'mappress', 'mashup_settings');
		add_settings_field('mashupLink', __('Link title', 'mappress'), array(&$this, 'set_mashup_link'), 'mappress', 'mashup_settings');
		add_settings_field('thumbs', __('Mashup Thumbnails', 'mappress'), array($this, 'set_thumbs'), 'mappress', 'mashup_settings');
		add_settings_field('thumbSize', __('Thumbnail Size', 'mappress'), array($this, 'set_thumb_size'), 'mappress', 'mashup_settings');

		add_settings_field('styles', __('Styled maps', 'mappress'), array($this, 'set_styles'), 'mappress', 'styled_maps_settings');
		add_settings_field('style', __('Default style', 'mappress'), array($this, 'set_style'), 'mappress', 'styled_maps_settings');

		add_settings_field('geocoders', __('Geocoder(s)', 'mappress'), array(&$this, 'set_geocoders'), 'mappress', 'geocoding_settings');
		add_settings_field('metaKeys', __('Geocoding fields', 'mappress'), array(&$this, 'set_meta_keys'), 'mappress', 'geocoding_settings');

		add_settings_field('api_key', __('API key (optional)', 'mappress'), array(&$this, 'set_api_key'), 'mappress', 'misc_settings');
		add_settings_field('mapSizes', __('Map sizes', 'mappress'), array(&$this, 'set_map_sizes'), 'mappress', 'misc_settings');
		add_settings_field('forceresize', __('Force resize', 'mappress'), array(&$this, 'set_force_resize'), 'mappress', 'misc_settings');
	}

	function set_options($input) {
		// Remove blank entries from address metakeys
		$keys = array();
		foreach($input['metaKeyAddress'] as $key) {
			if (!empty($key))
				$keys[] = $key;
		}
		$input['metaKeyAddress'] = $keys;

		// Cast thumbnail width/height to integer
		$input['thumbWidth'] = (int) $input['thumbWidth'];
		$input['thumbHeight'] = (int) $input['thumbHeight'];

		// Convert styles to associative array
		$input['styles'] = array();
		foreach($input['style_names'] as $i => $name) {
			$json = $input['style_jsons'][$i];
			if (empty($name) || empty($json))
				continue;
			$input['styles'][$name] = $json;
		}

		return parent::set_options($input);
	}

	function set_poi_list() {
		echo self::checkbox($this->options->poiList, 'mappress_options[poiList]', __("Show a list of POIs under each map", 'mappress'));
	}

	function set_data_tables() {
		$link = "<a href='http://www.datatables.net'>DataTable</a>";
		echo self::checkbox($this->options->dataTables, 'mappress_options[dataTables]', sprintf(__("Show the POI list as a sortable %s", 'mappress'), $link));
	}
	
	function set_mashup_title() {
		$title_types = array('poi' => __('POI title', 'mappress'), 'post' => __('Post title', 'mappress'));
		echo self::radio($title_types, $this->options->mashupTitle, 'mappress_options[mashupTitle]');
	}

	function set_mashup_body() {
		$body_types = array('poi' => __('POI body', 'mappress'), 'address' => __('Address', 'mappress'), 'post' => __('Post excerpt', 'mappress'));
		echo self::radio($body_types, $this->options->mashupBody, 'mappress_options[mashupBody]');
	}
	
	function set_mashup_link() {
		echo self::checkbox($this->options->mashupLink, 'mappress_options[mashupLink]', __("Link POI titles to the underlying post", 'mappress'));
	}
	
	function set_mashup_click() {
		$types = array('poi' => __('Open the POI', 'mappress'), 'post' => __('Go directly to the post'));
		echo self::radio($types, $this->options->mashupClick, 'mappress_options[mashupClick]');
	}

	function set_iw_type() {
		$iw_types = array(
			'iw' => __('Google InfoWindow', 'mappress'),
			'ib' => __('InfoBox (can be styled and extend outside the map)', 'mappress')
		);
		echo self::radio($iw_types, $this->options->iwType, 'mappress_options[iwType]');
	}
	
	function set_iw_disable_auto_pan() {
		echo self::checkbox($this->options->iwDisableAutoPan, 'mappress_options[iwDisableAutoPan]', __("Disable map panning when infoWindow / infoBox is opened", 'mappress'));
	}

	function set_thumbs() {
		echo self::checkbox($this->options->thumbs, 'mappress_options[thumbs]', __("Show featured image thumbnails in mashup POIs", 'mappress'));
	}

	function set_thumb_size() {
		// Note: WP doesn't return dimensions, just the size names - ticket is > 6 months old now: http://core.trac.wordpress.org/ticket/18947
		$sizes = get_intermediate_image_sizes();
		$sizes = array_combine(array_values($sizes), array_values($sizes));

		_e("Use existing size: ", 'mappress');
		echo self::dropdown($sizes, $this->options->thumbSize, 'mappress_options[thumbSize]', array('none' => true));

		echo "<br/>" . __("or resize to (px): ", 'mappress');
		echo "<input name='mappress_options[thumbWidth]' size='3' maxlength='3' value='" . $this->options->thumbWidth . "'/> X ";
		echo "<input name='mappress_options[thumbHeight]' size='3' maxlength='3' value='" . $this->options->thumbHeight . "'/>";
	}

	function set_default_icon() {
		echo "<input type='hidden' name='mappress_options[defaultIcon]' value='{$this->options->defaultIcon}' class='mapp-p-iconid' />";
		echo Mappress_Icons::get_icon_picker();
	}

	function set_custom_icons_dir() {
		echo "<code>" . Mappress_Icons::$icons_dir . "</code>";
	}
	
	function set_styles() {
		$styles_link = "<a href='https://developers.google.com/maps/documentation/javascript/styling' target='_blank'>" . __("styled maps", 'mappress') . "</a>";
		$wizard_link = "<a href='http://gmaps-samples-v3.googlecode.com/svn/trunk/styledmaps/wizard/index.html' target='_blank'>" . __('styled maps wizard', 'mappress') . "</a>";

		echo sprintf(__("Enter JSON for %s from Google's %s", 'mappress'), $styles_link, $wizard_link) . ": <br/>";

		$styles = $this->options->styles;

		// Add a blank row if the table is empty
		if (empty($styles))
			$styles = array('' => '');

		$rows = array();
		$headers = array(__("Style name", 'mappress'), 'JSON', '');
		foreach($styles as $style_name => $style) {
			$rows[] = array(
				"<input type='text' size='20' name='mappress_options[style_names][]' value='$style_name' />",
				"<textarea rows='1' class='mapp-expand' name='mappress_options[style_jsons][]'>$style</textarea>"
			);
		}

		echo $this->table($headers, $rows, array('id' => 'mapp_styles', 'col_styles' => array('', '', '')));
	}

	function set_style() {
		$styles = $this->options->styles;
		if (empty($styles)) {
			_e('No styles have been defined yet', 'mappress');
			return;
		}

		$style_names = array_combine(array_keys($styles), array_keys($styles));
		echo self::dropdown($style_names, $this->options->style, 'mappress_options[style]', array('none' => true));
	}
	
	function set_geocoders() {
		$labels = array(
			'google' => __('Google', 'mappress'),
			'nominatim' => __('Nominatim', 'mappress')
		);
		echo self::checkbox_list($this->options->geocoders, 'mappress_options[geocoders][]', $labels);
	}

	function set_meta_keys() {
		$meta_keys = self::get_meta_keys();
		$zooms = array_combine(range(1,19), range(1,19));

		$this->options->metaKeyAddress = array_pad($this->options->metaKeyAddress, 4, '');

		$fields = array(
			__('Address Line 1', 'mappress') => array($this->options->metaKeyAddress[0], 'mappress_options[metaKeyAddress][0]'),
			__('Address Line 2', 'mappress') => array($this->options->metaKeyAddress[1], 'mappress_options[metaKeyAddress][1]'),
			__('Address Line 3', 'mappress') => array($this->options->metaKeyAddress[2], 'mappress_options[metaKeyAddress][2]'),
			__('Address Line 4', 'mappress') => array($this->options->metaKeyAddress[3], 'mappress_options[metaKeyAddress][3]'),
			__('Latitude', 'mappress') => array($this->options->metaKeyLat, 'mappress_options[metaKeyLat]'),
			__('Longitude', 'mappress') => array($this->options->metaKeyLng, 'mappress_options[metaKeyLng]'),
			__('Icon', 'mappress') => array($this->options->metaKeyIconid, 'mappress_options[metaKeyIconid]'),
			__('Title', 'mappress') => array($this->options->metaKeyTitle, 'mappress_options[metaKeyTitle]'),
			__('Body', 'mappress') => array($this->options->metaKeyBody, 'mappress_options[metaKeyBody]'),
			__('Map Zoom', 'mappress') => array($this->options->metaKeyZoom, 'mappress_options[metaKeyZoom]')
		);

		$headers = array(__('Map', 'mappress'), __('Custom Field', 'mappress'));
		$rows = array();

		foreach($fields as $label => $field) {
			$rows[] = array($label, self::dropdown($meta_keys, $field[0], $field[1], array('none' => true)));
		}

		echo $this->table($headers, $rows);
		echo "<br/>";
		echo self::checkbox($this->options->metaSyncSave, 'mappress_options[metaSyncSave]', __('Overwrite existing maps when upating', 'mappress'));
	}
	
	function set_api_key() {
		echo "<input type='text' size='50' name='mappress_options[apiKey]' value='{$this->options->apiKey}' />";
		$link = "<a href='https://developers.google.com/maps/documentation/javascript/tutorial#api_key'>" . __('usage tracking', 'mappress') . "</a>";
		echo "<br/><i>" .  sprintf(__("API keys is needed only for premium services or %s", 'mappress'), $link) . "</i>";
	}
	
	function set_map_sizes() {
		echo __('Enter default map sizes', 'mappress');
		echo ": <br/>";

		echo "<table class='mapp-table'>";
		echo "<tr><th>" . __('Width(px)', 'mappress') . "</th><th>" . __('Height(px)', 'mappress') . "</th></tr>";
		foreach($this->options->mapSizes as $i => $size) {
			echo "<tr>";
			echo "<td><input type='text' size='3' name='mappress_options[mapSizes][$i][width]' value='{$size['width']}' /></td>";
			echo "<td><input type='text' size='3' name='mappress_options[mapSizes][$i][height]' value='{$size['height']}' /></td>";
			echo "</tr>";
		}
		echo "</table>";
	}

	function set_force_resize() {
		$from = "<input type='text' size='2' name='resize_from[width]' value='' />"
			. "x<input type='text' size='2' name='resize_from[height]' value='' /> ";
		$to = "<input type='text' size='2' name='resize_to[width]]' value='' />"
			. "x<input type='text' size='2' name='resize_to[height]]' value='' /> ";
		echo __('Permanently resize existing maps', 'mappress');
		echo ": <br/>";
		printf(__('from %s to %s', 'mappress'), $from, $to);
		echo "<input type='submit' name='force_resize' class='button' value='" . __('Force Resize', 'mappress') . "' />";
	}	
} // End class Mappress_Pro_Settings
?>