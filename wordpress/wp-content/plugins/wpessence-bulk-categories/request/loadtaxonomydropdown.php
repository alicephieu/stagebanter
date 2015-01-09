<?php
/**
 * Load the taxonomy dropdown
 * Call this via an AJAX request
 */

// Make sure it is the right request
if ($_SERVER['REQUEST_METHOD'] == 'POST' and $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
	// Request parameters
	$taxonomy = $_POST['taxonomy'];
	
	wp_dropdown_categories(array(
		'show_option_none' => __('&lt; no parent category &gt;'),
		'hide_empty' => false,
		'tab_index' => 2,
		'hierarchical' => true,
		'selected' => -1,
		'taxonomy' => $taxonomy
	));
	
	die();
}
?>