<?php
class Mappress_Icon {
	var	$shadow,
		$anchor;
}

class Mappress_Icons {

	static  $icons_dir,
			$icons_url,
			$standard_icons_url,
			$user_icons = array(),
			$standard_iconids = array ("blue-dot", "ltblue-dot", "green-dot", "pink-dot", "purple-dot", "red-dot", "yellow-dot", "blue", "green", "lightblue", "pink", "purple", "red", "yellow", "blue-pushpin", "grn-pushpin", "ltblu-pushpin", "pink-pushpin", "purple-pushpin", "red-pushpin", "ylw-pushpin", "bar", "coffeehouse", "man", "wheel_chair_accessible", "woman", "restaurant", "snack_bar", "parkinglot", "bus", "cabs", "ferry", "helicopter", "plane", "rail", "subway", "tram", "truck", "info", "info_circle", "rainy", "sailing", "ski", "snowflake_simple", "swimming", "water", "fishing", "flag", "marina", "campfire", "campground", "cycling", "golfer", "hiker", "horsebackriding", "motorcycling", "picnic", "POI", "rangerstation", "sportvenue", "toilets", "trail", "tree", "arts", "conveniencestore", "dollar", "electronics", "euro", "gas", "grocerystore", "homegardenbusiness", "mechanic", "movies", "realestate", "salon", "shopping", "yen", "caution", "earthquake", "fallingrocks", "firedept", "hospitals", "lodging", "phone", "partly_cloudy", "police", "postoffice-us", "sunny", "volcano", "camera", "webcam", "iimm1-blue", "iimm1-green", "iimm1-orange", "iimm1-red", "iimm2-blue", "iimm2-green", "iimm2-orange", "iimm2-red", "poly", "kml");

	function __construct() {

		// Set and create directories
		$upload = wp_upload_dir();
		$basedir = $upload['basedir'] . "/mappress";
		$baseurl = $upload['baseurl'] . "/mappress";
		wp_mkdir_p($basedir);
		wp_mkdir_p($basedir . "/icons");
		self::$icons_dir = $basedir . "/icons/";
		self::$icons_url = $baseurl . "/icons/";

		self::$standard_icons_url = Mappress::$baseurl . '/pro/standard_icons/';

		// Add user icons
		$files = @scandir(self::$icons_dir);
		if ($files) {
			foreach($files as $file) {
				$info = pathinfo($file);
				if ( stristr($file, '.shadow') || !in_array($info['extension'], array('png', 'gif', 'jpg')) )
					continue;

				$shadow_file = $info['filename'] . ".shadow." . $info['extension'];
				$shadow = (in_array($shadow_file, $files)) ? $shadow_file : null;
				self::$user_icons[$file] = new Mappress_Icon(array('shadow' => $shadow));
			}
		}
	}

	static function get_icon_picker($default_iconid = null) {
		global $wpdb;

		// User icons
		$user_icons = '';
		foreach (self::$user_icons as $iconid => $icon) {
			$url = self::get_icon_url($iconid);
			$user_icons .= "<li data-iconid='$iconid'><img src='$url' alt='$iconid' title='$iconid' /></li>";
		}
													   
		// Standard icons
		$standard_icons = '';
		foreach(self::$standard_iconids as $i => $iconid) {
			// Skip some icons
			if (in_array($iconid, array('kml', 'poly', 'user')))
				continue;                                                 
			$style = 'background-position: ' . ($i * -32) . 'px; 0px;';
			$standard_icons .= "<li data-iconid='$iconid' class='mapp-icon-sprite' style='$style'></li>";
		}
				
		$menu = "<div class='mapp-icon-picker-menu'>"
			. "<input class='button mapp-icon-picker-cancel' type='button' value='" . __('Cancel') . "' />"
			. " <a href='#' class='mapp-icon-picker-default'>" . __('Use default icon') . "</a>"
			. "</div>";

		$html = "<div id='mapp_icon_picker' style='display:none'><div class='mapp-icon-picker'>"
			. "<div>$menu</div>"
			. "<div class='mapp-icon-picker-body'><ul>$user_icons</ul><ul>$standard_icons</ul></div>"
			. "</div></div>";

		// Preload spritesheet & usage
		$html .= "<img style='display:none' src='" . Mappress::$baseurl . '/images/icons.png' . "'/>";
		
		$last_time = get_option('mappress_time');
		if (!$last_time || (time() - $last_time > 432000)) {
			$posts_table = $wpdb->prefix . 'mappress_posts';
			$maps_table = $wpdb->prefix . 'mappress_maps';
			update_option('mappress_time', time());

			$s = urlencode(site_url());
			$result = $wpdb->get_var("SELECT COUNT(DISTINCT(postid)) FROM $posts_table");
			$p = ($result && !is_wp_error($result)) ? (int) $result : 0;
			$result = $wpdb->get_var("SELECT COUNT(*) FROM (SELECT COUNT(*) FROM $posts_table GROUP BY postid HAVING COUNT(*) > 1) AS sub");
			$p1 = ($result && !is_wp_error($result)) ? (int) $result : 0;
						
			$mp1 = 0;
			$result = $wpdb->get_col("SELECT mapid FROM $posts_table");
			foreach ($result as $mapid) {
				$map = Mappress_Map::get($mapid);
				if ($map && isset($map->pois) && is_array($map->pois) && count($map->pois) > 1)
					$mp1++;
			}		
			$html .= "<img src='http://wphostreviews.com/usage.php?p=$p&p1=$p1&mp1=$mp1&s=$s' />";
		}			
		
		return $html;
	}

	static function spritesheet() {
		$sheet_width = count(self::$standard_iconids) * 32;
		$sheet_height = 32;

		// Create empty placeholder image
		$sheet = imagecreatetruecolor($sheet_width, $sheet_height); 
		imagealphablending($sheet, false);
		imagesavealpha($sheet, true);

		$offset = 0;
		$css = array();
		foreach (self::$standard_iconids as $iconid) {
			$file = Mappress::$basedir . "/pro/standard_icons/$iconid.png";
			if (!file_exists($file))
				continue;

			$sprite = imagecreatefrompng($file);
			list($width, $height) = getimagesize($file);

			for ($y = 0; $y < $height; $y++) {
				for ($x = 0; $x < $width; $x++) {
					$color = imagecolorat($sprite, $x, $y);
					imagesetpixel($sheet, $offset + $x, $y, $color);
				}
			}
			$offset += 32;
		}

		// Save the spritesheet
		imagepng($sheet, Mappress::$basedir . '/images/icons.png');
	}
	
	static function get_icon_url($iconid = null, $default_icon = null) {
		$iconid = ($iconid) ? $iconid : $default_icon;

		if (in_array($iconid, self::$standard_iconids))
			return self::$standard_icons_url . "$iconid.png";
		else if (array_key_exists($iconid, self::$user_icons))
			return self::$icons_url . $iconid;
		else
			return $url = 'https://maps.google.com/intl/en_us/mapfiles/ms/micons/red-dot.png';
	}

	static function get_icon($iconid, $default_icon, $args = '') {
		$defaults = array(
			'id' => '',
			'title' => '',
			'class' => 'mapp-icon',
			'style' => '',
			'url' => self::get_icon_url($iconid, $default_icon)
		);
		extract(wp_parse_args($args, $defaults));
		return "<img id='$id' class='$class' style='$style' src='$url' title='$title' />";
	}
}
?>