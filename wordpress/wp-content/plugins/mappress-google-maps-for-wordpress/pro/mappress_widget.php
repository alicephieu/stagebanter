<?php
class Mappress_Widget extends WP_Widget {

	var $defaults = array(
		'center' => array('lat' => 0, 'lng' => 0),
		'directions' => 'none',
		'height' => 250,
		'hideEmpty' => false,
		'mapTypeId' => 'roadmap',
		'mashupLink' => true,
		'other' => '',
		'poiList' => false,
		'show' => 'current',                    // query radio button: "all" = maps from ALL posts, "current" = maps from current posts, "query" = custom query
		'show_query' => null,                   // Custom query string
		'widget_title' => 'MapPress Map',
		'width' => 200,
		'zoom' => null
	);

	function Mappress_Widget() {
		parent::__construct(false, $name = 'MapPress Map');
	}

	function widget($args, $instance) {
		global $mappress;

		extract($args);
		
		// Get widget attributes
		$atts = wp_parse_args($instance, $this->defaults);
		
		// Convert query input fields into a query
		if ($atts['show'] == 'current')
			$atts['query'] = '';
		else if ($atts['show'] == 'all')
			$atts['query'] = 'all';
		else
			$atts['query'] = $atts['show_query'];
		unset($atts['show'], $atts['show_query']);
				
		// Merge in any misc (shortcode) atts
		$other = shortcode_parse_atts($atts['other']);
		$other = Mappress::scrub_atts($other);
		$atts = array_merge($atts, $other);
		
		// Widget defaults 
		$atts['initialopeninfo'] = false;

		// Get the map html
		$html = $mappress->get_mashup($atts);
		
		// If html is empty, then assume the map was suppressed using hideEmpty
		if (empty($html))
			return;
			
		echo $before_widget;
		echo $before_title . $instance['widget_title'] . $after_title;
		echo $html;						   
		echo $after_widget;
	}

	function update($new_instance, $old_instance) {
		// Set true/false/null
		$new_instance['hideEmpty'] = (isset($new_instance['hideEmpty'])) ? true : false;
		$new_instance['mashupLink'] = (isset($new_instance['mashupLink'])) ? true : false;
		$new_instance['traffic'] = (isset($new_instance['traffic'])) ? true : false;
		$new_instance['poiList'] = (isset($new_instance['poiList'])) ? true : false;
		$new_instance['zoom'] = (isset($new_instance['zoom'])) ? (int) $new_instance['zoom'] : null;
		$new_instance['center']['lat'] = ($new_instance['center']['lat'] == "") ? 0 : $new_instance['center']['lat'];
		$new_instance['center']['lng'] = ($new_instance['center']['lng'] == "") ? 0 : $new_instance['center']['lng'];
		return $new_instance;
	}

	function form($instance) {
		extract(shortcode_atts($this->defaults, $instance));
		?>
			<p>
				<?php _e('Widget title', 'mappress'); ?>:
				<input class="widefat" id="<?php echo $this->get_field_id('widget_title'); ?>" name="<?php echo $this->get_field_name('widget_title'); ?>" type="text" value="<?php echo $widget_title ?>" />
			</p>

			<p>
				<?php _e('Map size', 'mappress'); ?>:
				<input size="3" id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" type="text" value="<?php echo $width; ?>" />
				x <input size="3" id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" type="text" value="<?php echo $height; ?>" />
			</p>

			<p>
				<?php _e('Show', 'mappress'); ?>:<br/>
				<input type="radio" name="<?php echo $this->get_field_name('show'); ?>" value="current" <?php checked($show, 'current'); ?> /> <?php _e('Current posts', 'mappress');?>
				&nbsp;( <input type="checkbox" name="<?php echo $this->get_field_name('hideEmpty'); ?>" <?php checked($hideEmpty); ?> /> <?php _e('Hide if empty', 'mappress'); ?> )
				<br/>
				<input type="radio" name="<?php echo $this->get_field_name('show'); ?>" value="all" <?php checked($show, 'all'); ?> /> <?php _e('All posts', 'mappress');?><br/>
				<input type="radio" name="<?php echo $this->get_field_name('show'); ?>" value="query" <?php checked($show, 'query'); ?> /> <?php _e('Custom query', 'mappress');?>
				<input type="text" style='width:100%' name="<?php echo $this->get_field_name('show_query'); ?>" value="<?php echo $show_query ?>" />

				<br/><i><?php echo "<a target='_none' href='http://codex.wordpress.org/Function_Reference/query_posts'>" . __('Learn about queries', 'mappress') . "</a>" ?></i>
			</p>

			<p>
				<input type="checkbox" name="<?php echo $this->get_field_name('poiList'); ?>" <?php checked($poiList); ?> />
				<?php _e('Show POI list', 'mappress');?>
				<br/>
				<input type="checkbox" name="<?php echo $this->get_field_name('mashupLink'); ?>" <?php checked($mashupLink); ?> /> <?php _e('Link POIs to posts', 'mappress');?>
			</p>

			<p>
				<?php _e('Directions', 'mappress'); ?>:<br/>
				<input type="radio" name="<?php echo $this->get_field_name('directions'); ?>" value="inline" <?php checked($directions, 'inline'); ?> /> <?php _e('Inline', 'mappress'); ?>
				<input type="radio" name="<?php echo $this->get_field_name('directions'); ?>" value="google" <?php checked($directions, 'google'); ?> /> <?php _e('Google', 'mappress'); ?>
				<input type="radio" name="<?php echo $this->get_field_name('directions'); ?>" value="none" <?php checked($directions, 'none'); ?> /> <?php _e ('None', 'mappress'); ?>
			</p>

			<table>
				<tr>
					<td><?php _e('Center', 'mappress');?>:</td>
					<td>
						<input type="text" size="4" name="<?php echo $this->get_field_name('center][lat'); ?>" value="<?php echo $center['lat']; ?>" />,
						<input type="text" size="4" name="<?php echo $this->get_field_name('center][lng'); ?>" value="<?php echo $center['lng']; ?>" />
					</td>
				</tr>

				<tr>
					<td><?php _e('Zoom', 'mappress');?>:</td>
					<td>
						<select name="<?php echo $this->get_field_name('zoom'); ?>">
						<option <?php selected($zoom, null)?> value="">Automatic</option>
						<?php
							for ($i = 1; $i <= 20; $i++)
								echo "<option " . selected($zoom, $i, false) . " value='$i'>$i</option>";
						?>
						</select>
					</td>
				</tr>

				<tr>
					<td><?php _e('Map type', 'mappress');?>:</td>
					<td>
						<select name="<?php echo $this->get_field_name('mapTypeId'); ?>">
						<option <?php selected($mapTypeId, "roadmap")?> value="roadmap"><?php _e('Map')?></option>
						<option <?php selected($mapTypeId, "hybrid")?> value="hybrid"><?php _e('Hybrid')?></option>
						<option <?php selected($mapTypeId, "satellite")?> value="satellite"><?php _e('Satellite')?></option>
						<option <?php selected($mapTypeId, "terrain")?> value="terrain"><?php _e('Terrain')?></option>
						</select>
					</td>
				</tr>
			</table>

			<p>
				<br/>
				<?php _e('Other Settings', 'mappress'); ?>:<br/>
				<input type="text" style='width:100%' name="<?php echo $this->get_field_name('other'); ?>" value="<?php echo esc_attr($other); ?>" />
				<br/>
				<i><?php echo __('Example: traffic="false" maplinks=""', 'mappress');?></i>
			</p>
		<?php
	} // End class Mappress_Pro
}
?>