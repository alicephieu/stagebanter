<?php
class Mappress_Geocoder_Result {
	var $corrected_address,
		$geocoder,
		$lat,
		$lng,
		$viewport
		;
}

class Mappress_Geocoders {

	var $language,
		$country,
		$email
		;

	function __construct() {
		$this->language = Mappress::$options->language;
		$this->country = Mappress::$options->country;
		$this->email = get_option('admin_email');
	}

	/**
	* Geocode an address
	*
	* @param mixed $address
	* @return Mappress_Geocoder_Result object on success | array of WP_Error on failure
	*/
	function geocode($address) {

		$wp_errors = new WP_Error();

		$address = urlencode($address);

		$geocoders = Mappress::$options->geocoders;
		foreach($geocoders as $geocoder) {
			$result = $this->$geocoder($address);

			if (is_wp_error($result))
				$wp_errors->add($geocoder, $result->get_error_message());
			else
				return $result;
		}

		$result = apply_filters('mappress_geocode', '', $address);
		if (is_wp_error($result))
			$wp_errors->add('filter', $result->get_error_message());
		else if (!empty($result))
			return $result;

		if (empty($wp_errors->errors))
			return new WP_Error('geocode', __('No results, check that a geocoder is configured', 'mappress'));

		return $wp_errors;
	}

	/**
	* Geocode an address using http
	*
	* @return true | WP_Error on failure
	*/
	function google($address) {

		$url = "https://maps.googleapis.com/maps/api/geocode/json?address=$address&sensor=false&output=json";
		$url = ($this->country) ? $url . "&region=$this->country" : $url;
		$url = ($this->language) ? $url . "&language=$this->language" : $url;

		$json = $this->get_json($url, array('sslverify' => false));
		if (is_wp_error($json))
			return $json;

		// Check status
		$status = isset($json->status) ? $json->status : null;
		if ($status != 'OK')
			return new WP_Error('geocode', sprintf(__("Invalid status: %s, address: %s", 'mappress'), $status, $address));

		// Discard empty results
		foreach((array)$json->results as $key=>$result) {
			if(empty($result->formatted_address))
				unset($json->results[$key]);
		}

		if (!$json  || !isset($json->results) || empty($json->results[0]) || !isset($json->results[0]))
			return new WP_Error('geocode', sprintf(__('No results for address: %s', 'mappress'), $address));

		$placemark = $json->results[0];

		// Lat/lng
		$location = new Mappress_Geocoder_Result();
		$location->geocoder = 'google';
		$location->lat = $placemark->geometry->location->lat;
		$location->lng = $placemark->geometry->location->lng;

		// Viewport
		if (isset($placemark->geometry->viewport)) {
			$location->viewport = array(
				'sw' => array('lat' => $placemark->geometry->viewport->southwest->lat, 'lng' => $placemark->geometry->viewport->southwest->lng),
				'ne' => array('lat' => $placemark->geometry->viewport->northeast->lat, 'lng' => $placemark->geometry->viewport->northeast->lng)
			);
		}

		// Corrected address
		$location->corrected_address = $placemark->formatted_address;
		return $location;
	}

	function nominatim($address) {

		$url = "http://nominatim.openstreetmap.org/search?format=json&polygon=0&addressdetails=1&q=$address&accept-language=$this->language&email=$this->email";

		$json = $this->get_json($url);
		if (is_wp_error($json))
			return $json;

		// First response
		$placemark = $json[0];

		// Lat/lng
		$location = new Mappress_Geocoder_Result();
		$location->geocoder = 'nominatim';
		$location->lat = $placemark->lat;
		$location->lng = $placemark->lon;

		// Viewport
		if (isset($placemark->boundingbox)) {
			$location->viewport = array(
				'sw' => array('lat' => $placemark->boundingbox[0], 'lng' => $placemark->boundingbox[2]),
				'ne' => array('lat' => $placemark->boundingbox[1], 'lng' => $placemark->boundingbox[3])
			);
		}

		// Corrected address
		$location->corrected_address = $placemark->display_name;
		return $location;
	}

	function get_json($url, $args = array()) {
		$response = wp_remote_get($url, $args);

		if (is_wp_error($response))
			return $response;

		if ($response['response']['code'] != 200)
			return new WP_Error('geocode', sprintf(__('Error: %s %s', 'mappress'), $response['response']['code'], $response['response']['message']));

		$json = json_decode($response['body']);
		if (empty($json))
			return new WP_Error('geocode', sprintf(__('No results for address: %s', 'mappress'), $address));

		return $json;
	}

	/**
	* Parse an address.  It will split the address into 1 or 2 lines.
	* The splitting is really a best-guess, and it depends on the geocoder
	* You can use filter 'mappress_address_split' to implement your own logic.
	*
	* @param mixed $address
	* @param mixed $geocoder
	* @return array $result - array containing 1 or 2 address lines
	*/
	static function parse_address($address) {

		// USA Addresses - Nominatimr
		$address = str_replace(', United States of America', '', $address);

		// USA Addresses - Google
		$address = str_replace(', USA', '', $address);

		// Nominatim writes street # followed by a comma ("100, main street, clevelend") - try to remove that first comma if present
		// (see if there's a numeric first part just before the first comma, if so remove the comma)
		$first_comma = strpos($address, ",");
		$first_part = substr($address, 0, $first_comma);
		if (is_numeric($first_part) && $first_comma !== false)
			$address = $first_part . substr($address, $first_comma + 1);

		// If 0 or 1 remaining commas then use a single line, e.g. "Paris, France" or "Ohio"
		// Otherwise return first line up to first comma, second line after, e.g. "Paris, France" => "Paris<br>France"
		if (!strpos($address, ','))
			$result = array($address);
		else
			$result = array(substr($address, 0, strpos($address, ",")), trim(substr($address, strpos($address, ",") + 1)));

		return apply_filters('mappress_address_split', $result, $address);
	}
}
?>
