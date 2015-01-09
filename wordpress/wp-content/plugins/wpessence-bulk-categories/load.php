<?php
// Global variables
global $wpdb;

// Plug-in data
require_once 'config/plugindata.php';

// Database tables
require_once 'config/dbtables.php';

// Defines
define('WPEBC_ABSPATH', ABSPATH . '/wp-content/plugins/' . $plugindata['plugin_name']);

// Plug-in class (class can be used in themes)
require_once 'includes/class.Plugin.php';

// Plug-in classes
require_once 'includes/class.BulkCategories.php';
require_once 'includes/class.BulkCategories_Frontend.php';
require_once 'includes/class.BulkCategories_Admin.php';
?>