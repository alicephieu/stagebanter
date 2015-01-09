<?php
/**
 * WPEssence Bulk Categories Plug-in: Front-end class
 *
 * @author		WPEssence
 * @copyright	2011 WPEssence
 * @website		wpessence.com
 * @see			WPEBC_BulkCategories
 */

class WPEBC_BulkCategories_Frontend extends WPEBC_BulkCategories
{

	/**
	 * Constructor
	 * @param array $settings Settings
	 * @param array $tables Database tables
	 */
	public function __construct($settings = false, $tables = false)
	{
		// Call parent constructor
		parent::__construct($settings, $tables);
	}

}
?>