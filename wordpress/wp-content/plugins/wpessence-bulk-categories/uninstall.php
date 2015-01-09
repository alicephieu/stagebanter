<?php
// Load plug-in essentials
require_once 'load.php';

$wpebc = new WPEBC_BulkCategories($plugindata);

$wpebc->uninstall();
?>