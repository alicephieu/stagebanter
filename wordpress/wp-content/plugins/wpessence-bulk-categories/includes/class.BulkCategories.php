<?php
/**
 * WPEssence Bulk Categories Plug-in: Main class
 *
 * @author		WPEssence
 * @copyright	2011 WPEssence
 * @website		wpessence.com
 */

class WPEBC_BulkCategories
{

	/**
	 * Settings
	 * @access	protected
	 * @var	array (settingname => value)
	 */
	protected $_settings;
	
	/**
	 * Database	tables
	 * @access	protected
	 * @var	array (alias => tablename)
	 */
	protected $_dbtables;
	
	/**
	 * Vars stored, used by the magic setter and getter
	 * @access	private
	 * @var	array
	 */
	private $_vars;
	
	/**
	 * Constructor
	 * @param array $settings Settings
	 * @param array $tables Database tables
	 */
	public function __construct($settings = false, $tables = false)
	{
		// Variables
		$this->_vars = array();
		
		// Plugin settings
		if (is_array($settings)) {
			$this->_settings = $settings;
		}
		
		// Plugin DB tables
		if (is_array($tables)) {
			$this->_dbtables = $tables;
		}
		
		// Add actions
		add_action('init', array(&$this, 'action_init'));
	}
	
	/**
	 * Plug-in install
	 */
	public final function install()
	{
		$current_version = get_option($this->get_setting('options_prefix') . 'version');
		
		if ($current_version === false) {
			add_option($this->get_setting('options_prefix') . 'version', $this->get_setting('version'));
		}
		else {
			update_option($this->get_setting('options_prefix') . 'version', $this->get_setting('version'));
		}
		
		$defaults = array();
		
		foreach ($defaults as $index => $default) {
			if (get_option($index) === false) {
				add_option($this->get_setting('options_prefix') . $index, $default);
			}
		}
	}
	
	/**
	 * Plug-in uninstall
	 */
	public final function uninstall()
	{
	}
	
	/*****************************
	 * ACTIONS
	 *****************************/
	
	/**
	 * Action: init
	 */
	public function action_init()
	{
		// Load plugin textdomain
		load_plugin_textdomain($this->get_setting('textdomain'), false, $this->get_setting('plugin_name') . '/languages');
	}
	
	/*****************************
	 * PLUGIN FUNCTIONS
	 *****************************/
	
	/**
	 * Set plug-in settings
	 * @param	array $settings	Array of plug-in settings to set
	 * @return	plug-in Settings
	 */
	public function set_settings($settings)
	{
		foreach ($settings as $index => $setting) {
			$this->_settings[$index] = $setting;
		}
		
		return $this->_settings;
	}
	
	/**
	 * Get single plugin setting
	 * @param mixed Multiple parameters possible for nested settings
	 * @return	Single plugin setting
	 */
	public function get_setting($setting)
	{
		$setting = $this->_settings;
		
		$args = func_get_args();
		
		while (isset($args[0])) {
			$setting = $setting[$args[0]];
			
			array_shift($args);
		}
		
		return $setting;
	}
	
	/**
	 * Get all plug-in settings
	 * @return	plug-in Settings
	 */
	public function get_settings()
	{
		return $this->_settings;
	}
	
	/**
	 * Set DB tables
	 * @param	array $settings	Array of DB tables to set
	 * @return	DB tables
	 */
	public function set_dbtables($tables)
	{
		foreach ($tables as $index => $table) {
			$this->_dbtables[$index] = $table;
		}
		
		return $this->_dbtables;
	}
	
	/**
	 * Get single DB table
	 * @param	string $table	Name of DB table to set
	 * @return	Single DB table
	 */
	public function get_dbtable($table)
	{
		return $this->_dbtables[$table];
	}
	
	/**
	 * Get all DB tables
	 * @return DB tables
	 */
	public function get_dbtables()
	{
		return $this->_dbtables;
	}
	
	/**
	 * Get a prefix from the settings
	 * @param string $prefix Prefix name
	 * @return string Prefix
	 */
	public function get_prefix($prefix)
	{
		return $this->get_setting('prefixes', $prefix);
	}
	
	/**
	 * Get postmeta from post meta with the plugin postmeta prefix
	 * @param int $post_id Post ID
	 * @param string $key Meta key
	 * @param bool $single If set to true, return a string, return an array of matching postmetas if set te false (default: false)
	 * @return string|array String/array of postmetas
	 */
	public function get_post_meta($post_id, $key, $single = false)
	{
		return get_post_meta($post_id, $this->get_prefix('postmeta') . $key, $single);
	}
	
	/**
	 * Update or add (if the key (including prefix) didn't exist) postmeta and prepend the postmeta prefix to the key
	 * @param int $post_id Post ID
	 * @param string $key Meta key (without prefix)
	 * @param string $value Meta value
	 */
	public function addupdate_post_meta($post_id, $key, $value)
	{
		update_post_meta($post_id, $this->get_prefix('postmeta') . $key, $value);
	}
	
	/**
	 * Update or add (if the option name (including prefix) didn't exist) option and prepend the postmeta prefix to the option name
	 * @param string $option Option name (without prefix)
	 * @param string $value Option value
	 */
	public function addupdate_option($option, $value)
	{
		update_option($option, $this->get_prefix('options') . $key, $value);
	}
	
	/**
	 * Load JS
	 * @param string $file Server path to the file
	 */
	public function load_js($file)
	{
		echo '<script type="text/javascript">';
		include $file;
		echo '</script>';
	}
	
	/**
	 * Load CSS
	 * @param string $file Server path to the file
	 */
	public function load_css($file)
	{
		echo '<style type="text/css">';
		include $file;
		echo '</style>';
	}
	
	/*****************************
	 * MAGIC METHODS
	 *****************************/
	
	/**
	 * __set Magic function
	 * @param mixed $name Variable name
	 * @param mixed $value Variable value
	 */
	public function __set($name, $value)
	{
		if (is_array($value)) {
			if (!array_key_exists($name, $this->_vars)) {
				$this->_vars[$name] = array();
			}
			
			$this->_vars[$name] = array_merge($this->_vars[$name], $value);
		}
		else {
			$this->_vars[$name] = $value;
		}
	}
	
	/**
	 * __get Magic function
	 * @param mixed $name Variable name
	 */
	public function &__get($name)
	{
		return $this->_vars[$name];
	}

}
?>