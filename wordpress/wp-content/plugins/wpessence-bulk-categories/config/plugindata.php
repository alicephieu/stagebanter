<?php
// Unset the plugin data variable if it is set
unset($plugindata);

// Set the scope for the variable to store the data in to global
global $plugindata;

// Plugin data
$plugindata = array(
	'version' => '1.1',
	'unique_plugin_identifier' => 'wpe_bulkcategories',
	'plugin_name' => basename(dirname(dirname(__FILE__))),
	'prefixes' => array(
		'general' => 'wpebc_',
		'dbtable' => $wpdb->prefix . 'wpebc_',
		'options' => 'wpebc_',
		'postmeta' => 'wpebc_',
		'html' => 'tl-',
		'head_title' => __('WPEssence Bulk Categories Plug-in: ')
	),
	'noncename' => 'wpe_bulkcategories_nonce',
	'textdomain' => 'wpe_bulkcategories'
);

// Additional plugin data, based of off (part of) the existing plugin data
$plugindata['plugin_page'] = 'admin.php?page=' . $plugindata['unique_plugin_identifier'];
$plugindata['plugin_url'] = WP_PLUGIN_URL . '/' . $plugindata['plugin_name'];
?>