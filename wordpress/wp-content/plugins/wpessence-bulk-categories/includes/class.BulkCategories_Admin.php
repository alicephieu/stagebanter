<?php
/**
 * WPEssence Bulk Categories Plug-in: Admin class
 *
 * @author		WPEssence
 * @copyright	2011 WPEssence
 * @website		wpessence.com
 * @see			WPEBC_BulkCategories
 */

class WPEBC_BulkCategories_Admin extends WPEBC_BulkCategories
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
		
		// Add actions
		add_action('admin_menu', array(&$this, 'action_admin_menu'));
		add_action('admin_print_styles', array(&$this, 'action_admin_print_styles'));
		add_action('admin_init', array(&$this, 'action_admin_init'));
		add_action('wp_ajax_jwsr_loadtaxonomydropdown', array(&$this, 'action_ajax_jwsr_loadtaxonomydropdown'));
		add_action('wp_ajax_nopriv_jwsr_loadtaxonomydropdown', array(&$this, 'action_ajax_nopriv_jwsr_loadtaxonomydropdown'));
	}
	
	/*****************************
	 * ACTIONS
	 *****************************/
	
	/**
	 * Action: admin_init
	 */
	public function action_admin_init()
	{
		// Register scripts
		wp_register_script('jquery-linedtextarea', $this->get_setting('plugin_url') . '/public/js/jquery-linedtextarea.js', array('jquery'));
		wp_register_script($this->get_prefix('general') . 'admin', $this->get_setting('plugin_url') . '/public/js/admin.js', array('jquery', 'jquery-linedtextarea'));
		
		// Ajax URL
		wp_localize_script($this->get_prefix('general') . 'admin', 'WPEBC_Ajax', array('ajaxurl' => admin_url('admin-ajax.php')));
		
		// Enqueue scripts
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-linedtextarea');
		wp_enqueue_script($this->get_prefix('general') . 'admin');
	}
	
	/**
	 * Action: admin_menu
	 * Add menu options to the admin menu
	 */
	public function action_admin_menu()
	{
		add_posts_page(__('Bulk Categories', $this->get_setting('textdomain')), __('Bulk Categories', $this->get_setting('textdomain')), 'manage_categories', $this->get_setting('unique_plugin_identifier') . '_bulkcategories_add', array(&$this, 'page_bulkcategories_add'));
	}
	
	/**
	 * Action: admin_print_styles
	 * Enqueue the admin stylesheets for the plugin
	 */
	public function action_admin_print_styles()
	{
		// Register styles
		wp_register_style($this->get_prefix('general') . 'admin', $this->get_setting('plugin_url') . '/public/css/admin.css');
		wp_register_style('jquery-linedtextarea', $this->get_setting('plugin_url') . '/public/css/jquery-linedtextarea.css');
		
		// Enqueue styles
		wp_enqueue_style($this->get_prefix('general') . 'admin');
		wp_enqueue_style('jquery-linedtextarea');
	}
	
	/**
	 * Action: ajax_jwsr_loadtaxonomydropdown
	 * Ajax: Load the taxonomy dropdown
	 */
	public function action_ajax_jwsr_loadtaxonomydropdown()
	{
		require_once WPEBC_ABSPATH . '/request/loadtaxonomydropdown.php';
	}
	
	/**
	 * Action: ajax_jwsr_nopriv_loadtaxonomydropdown
	 * Ajax: Load the taxonomy dropdown
	 */
	public function action_ajax_nopriv_jwsr_loadtaxonomydropdown()
	{
		$this->action_ajax_jwsr_loadtaxonomydropdown();
	}
	
	/*****************************
	 * PLUGIN FUNCTIONS
	 *****************************/
	
	/**
	 * Load page
	 * @param string $page Page name
	 */
	public function page($page = '')
	{
		global $wpdb;
		
		$do = (in_array($_GET['do'], array('add', 'edit', 'configure', 'delete'))) ? $_GET['do'] : 'manage';
		$do_on = $_GET['on'];
		
		include WPEBC_ABSPATH . '/admin/pages/' . $page . '.php';
	}
	
	/*****************************
	 * PAGES
	 *****************************/
	
	/**
	 * Page: Add categories in bulk
	 */
	public function page_bulkcategories_add()
	{
		$this->page('bulkcategories_add');
	}

}
?>