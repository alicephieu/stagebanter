<?php
/*
Plugin Name: WPEssence Bulk Categories
Description: Easily add multiple categories, including options for slugs and category parents. It is possible to use a full path to the category, so you can also easily add categories to a specific parent category, and add custom taxonomies such as tags, but also taxonomies registered by your theme or plugin.
Version: 1.2
Author: WPEssence
Author URI: http://www.wpessence.com
Plugin URI: http://www.wpessence.com/plugins/bulk-categories.html
License: GPLv2 or later
*/

if (!function_exists('add_action')) {
	// Include the WordPress configuration file
	require_once '../../../wp-config.php';
}

// Load plug-in essentials
require_once 'load.php';

if (!is_admin()) {
	$wpebc = new WPEBC_BulkCategories_Frontend($plugindata, $dbtables);
	require_once 'frontend/index.php';
}
else {
	$wpebc = new WPEBC_BulkCategories_Admin($plugindata, $dbtables);
	require_once 'admin/index.php';
}

WPEBC_Plugin::$plugin = $wpebc;

// Plug-in activation
register_activation_hook(__FILE__, array(&$wpebc, 'install'));
?>