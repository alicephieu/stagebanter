<?php
/*
Plugin Name: Advance WP Query Search Filter
Plugin URI: http://www.9-sec.com/
Description: This plugin let you using wp_query to filter taxonomy,custom meta and post type as search result.
Version: 1.0.10
Author: TC 
Author URI: http://www.9-sec.com/
*/
/*  Copyright 2012 TCK (email: devildai@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
if ( ! defined( 'AWQSFURL' ) )
	define( 'AWQSFURL', untrailingslashit( dirname( __FILE__ ) ) );
if ( ! defined( 'AWQSADMIN' ) )
	define( 'AWQSADMIN', untrailingslashit( dirname( __FILE__ ) ) );
if ( ! defined( 'AWQS' ) )
	define( 'AWQS', admin_url('?page=awqsf') );
include_once (AWQSFURL.'/includes/class-awqsf-form-table.php');

include_once (AWQSFURL.'/includes/process.php');
if(!class_exists('awqsf')){
class awqsf{

	const post_type = 'awqsf';
	function __construct(){
			//I18n
			add_action('init', array($this, 'AWQSFLanguage'),1);
			//plugin admin setting
			add_action( 'init' , array( $this,'awqsf_init_setting' ) );
			add_action('admin_menu', array($this,'awqsf_menu'));
			//save post
			add_action('admin_init', array($this,'awqsf_save_data'));
			//ajax taxo
			add_action( 'wp_ajax_nopriv_taxo_ajax', array( $this,'taxo_ajax') );  
			add_action( 'wp_ajax_taxo_ajax', array( $this,'taxo_ajax') ); 
			// ajax cmf
			add_action( 'wp_ajax_nopriv_cmf_ajax', array( $this,'cmf_ajax') );  
			add_action( 'wp_ajax_cmf_ajax', array( $this,'cmf_ajax') ); 
			//remove dona
			add_action( 'wp_ajax_nopriv_remove_dona', array( $this,'remove_dona') );  
			add_action( 'wp_ajax_remove_dona', array( $this,'remove_dona') );
			// style for front end
			add_action( 'wp_enqueue_scripts', array($this,'awsqf_front_styles'), false, '1.0', 'all',1 );	
			//plugin activation/deactivation
			register_activation_hook( __FILE__, array( $this, 'awqsf_active' ) );
			register_deactivation_hook( __FILE__,  array( $this,'awqsf_deactivate') );			
			
		}
	function awqsf_active(){
		update_option( 'awqsf_spt', '1' );
	}
	function awqsf_deactivate() {
		$option = get_option('awqsf_spt');		
		if($option && $option == '1'){
		delete_option( 'awqsf_spt' );}
	}
	function AWQSFLanguage(){
			load_plugin_textdomain( 'AWQSFTXT', false, 'advance-wqsf/lang' );
		}
	function remove_dona(){
		$option = get_option('awqsf_spt');		
		if($option){
		delete_option( 'awqsf_spt' );}
		exit;

	}
	function awqsf_init_setting() {
		register_post_type( self::post_type, array(
			'labels' => array(
				'name' => __( 'Advance WPSF', 'AWQSFTXT' ),
				'singular_name' => __( 'Advance WPSF', 'AWQSFTXT' ) ),
  			'exclude_from_search'=>true,
			'publicly_queryable'=>false,
			'rewrite' => false,
			'query_var' => false ) );

		add_shortcode('awsqf-form', array($this, 'awqsf_form_shortcode'));

	}

	function awqsf_menu(){
		$plugin_page = add_menu_page(__("Advance Wp Query Search Filter","AWQSFTXT"),__("Advance WQSF","AWQSFTXT"),'manage_options','awqsf', array($this,'awqsf_page'));
		add_action('admin_print_scripts-'.$plugin_page, array($this,'jscontrol'));
		add_action('admin_print_styles-'.$plugin_page, array($this,'awqsf_css'));
	

	}
	function jscontrol(){
		wp_enqueue_script('thickbox',null,array('jquery'));
		wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_script('js', plugins_url('js/awqsfjs.js', __FILE__), array('jquery'), '1.0', true);
		wp_localize_script( 'ajax-request', 'MyAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) ); 
		
	}
	function awqsf_css(){
		wp_enqueue_style('awqsfcss', plugins_url('css/awqsf.css', __FILE__), '1.0', true);
		wp_enqueue_style('thickbox.css', '/'.WPINC.'/js/thickbox/thickbox.css', null, '1.0');

	}
	function awsqf_front_styles(){
			// Register the style in the front end for the front:
		wp_register_style( 'awqsf-custom-style', plugins_url( 'css/awqsf-style.css', __FILE__ ), array(),  'all' );
		wp_enqueue_style( 'awqsf-custom-style' );
		wp_register_script( 'awqsf-frontjs', plugins_url( 'js/awqsf-front.js', __FILE__ ), array('jquery'), '1.0' );
		wp_enqueue_script('awqsf-frontjs');

	}
			
	function awqsf_page(){
		
		if(!isset($_GET['formid']) && !isset($_GET['formaction']) ){
		require_once AWQSFURL . '/html/mainpage.php';
		}
		if(isset($_GET['formid']) && isset($_GET['formaction']) && $_GET['formaction']=='new' && $_GET['formaction']=='new'){
			
			require_once AWQSFURL . '/html/add-new-awqsf.php';
		}
		if(isset($_GET['formid']) && isset($_GET['formaction'])){
			$post_id = $_GET['formid'];
			require_once AWQSFURL . '/html/add-new-awqsf.php';
		}
		
	}

	function dona_support(){
		$option = get_option('awqsf_spt');
		if($option && $option == '1'){
			
		$text = __( "If this plugin has benefited you in some way, please consider donating. Your contribution is needed for making this plugin better. Thanks", 'AWQSFTXT' );
		?>
		<div class="donation">
		<p><?php echo esc_html( $text ); ?> <a href="<?php echo esc_url_raw( __( 'http://9-sec.com/donation/', 'AWQSFTXT' ) ); ?>" class="button" target="_blank"><?php echo esc_html( __( "Donate", 'AWQSFTXT' ) ); ?></a><span class="closedona"><input type="button" class="closex button" value="<?php _e('Close X','AWQSFTXT') ;?>"></span></p>
		
		</div>
		<?php
		}
	}
	
	function awqsf_save_data(){
	   if(isset($_POST['awqsfsubmit'])){

		if (! wp_verify_nonce($_POST['nonce'], 'awqsfedit') ) {
			$this->handle_error()->add('nonce', __("No naughty business here, dude", 'AWQSFTXT'));
			return; 
		}


		$postid =sanitize_text_field($_POST['formid']);
		$cptarray = $taxoarray = $cmfarray =$relarray ='';
			if(!empty($_POST['awqsf']['cpt'])){
				foreach($_POST['awqsf']['cpt'] as $cv){
						$cptarray[] = sanitize_text_field($cv);
					
				}
			}
			if(isset($_POST['awqsf']['taxo'])){
				
				foreach($_POST['awqsf']['taxo'] as $tv){
					$taxoarray[]=array(
							'taxname' => sanitize_text_field($tv['taxname']),
							'taxlabel'=> sanitize_text_field($tv['taxlabel']),
							'taxall' => sanitize_text_field($tv['taxall']),
							'hide' => sanitize_text_field($tv['hide']),
							'exc' => sanitize_text_field($tv['exc']),
							'type' => sanitize_text_field($tv['type'])
						);
					
					
					}
			}

			if(isset($_POST['awqsf']['cmf'])){
				foreach($_POST['awqsf']['cmf'] as $cv){
						$cmfarray[] = array(
							'metakey' => sanitize_text_field($cv['metakey']),
							'label' => sanitize_text_field($cv['label']),
							'all' => sanitize_text_field($cv['all']),
							'compare' => wp_filter_nohtml_kses( stripslashes($cv['compare'])),
							'type' => sanitize_text_field($cv['type']),
							'opt' => sanitize_text_field($cv['opt'])
						);
					
					}
			}

		
				foreach($_POST['awqsf']['rel'] as $rv){
						$relarray[] = array(
							'tax'=> isset($rv['tax']) ? sanitize_text_field($rv['tax']) : '',
							'cmf'=> isset($rv['cmf']) ? sanitize_text_field($rv['cmf']) : '',
							'strchk'=> isset($rv['strchk']) ? sanitize_text_field($rv['strchk']) : '',
							'strlabel'=> $rv['strlabel'],
							'smetekey'=> $rv['smetekey'],
							'otype'=> isset($rv['otype']) ? sanitize_text_field($rv['otype']) : '',
							'sorder'=> isset($rv['sorder']) ? sanitize_text_field($rv['sorder']) : '',
							'button'=> $rv['button'],
							'resultc'=> $rv['resultc']
						);
					}
		
		 if($postid == 'new'){

				$post_information = array(
					'post_title' => sanitize_text_field($_POST['ftitle']) ,
					'post_type' => self::post_type,
					'post_status' => 'publish'
					);
				$newform_id = wp_insert_post($post_information);
				if(empty($newform_id)){
					$this->handle_error()->add('insert', __("Error! Try to create again.", 'AWQSFTXT'));
					return; 
					
				}
				if(!empty($cptarray) ){
				update_post_meta($newform_id, 'awqsf-cpt', $cptarray);}
				if(!empty($taxoarray)){
				update_post_meta($newform_id, 'awqsf-taxo', $taxoarray);}
				if(!empty($cmfarray)){
				update_post_meta($newform_id, 'awqsf-cmf', $cmfarray);}
				if(!empty($relarray)){
				update_post_meta($newform_id, 'awqsf-relbool', $relarray);}
				
				$returnlink = add_query_arg(array('formid' => $newform_id, 'formaction' => 'edit','status'=>'success'), AWQS);
				wp_redirect( $returnlink ); exit;
		}//end add new


		if($postid != 'new' && absint($postid) ){

			 $updateform = array();
 			 $updateform['ID'] = $postid ;
 			 $updateform['post_title'] = sanitize_text_field($_POST['ftitle']);
			$update = wp_update_post( $updateform );
			if(empty($update)){
					$this->handle_error()->add('update', __("Error! Something wrong when updating your setting.", 'AWQSFTXT'));
					return; 
					
				}
			
				$oldcpt = get_post_meta($postid, 'awqsf-cpt', true);
				$oldtaxo = get_post_meta($postid, 'awqsf-taxo', true);
				$oldcmf = get_post_meta($postid, 'awqsf-cmf', true);	
				$oldrel = get_post_meta($postid, 'awqsf-relbool', true);
				
				$newcpt = !empty($cptarray) ? $cptarray : '';
				$newtaxo = !empty($taxoarray) ? $taxoarray : '';
				$newcmf = !empty($cmfarray) ? $cmfarray : '';
				$newrel = !empty($relarray) ? $relarray : '';

				if (empty($newcpt)) {
				delete_post_meta($postid, 'awqsf-cpt', $oldcpt);	
				
				} elseif($oldcpt != $newcpt) {
				update_post_meta($postid, 'awqsf-cpt', $newcpt);
				}
				
				if (empty($newtaxo)) {
				delete_post_meta($postid, 'awqsf-taxo', $oldtaxo);
				
				} elseif($newtaxo != $oldtaxo) {
				update_post_meta($postid, 'awqsf-taxo', $newtaxo);
				}

				if (empty($newcmf)) {
				delete_post_meta($postid, 'awqsf-cmf', $oldcmf);
				} elseif ($newcmf != $oldcmf) {
				update_post_meta($postid, 'awqsf-cmf', $newcmf);
				}	
				
				
				if (empty($newrel)) {
				delete_post_meta($postid, 'awqsf-relbool', $oldrel);
				} elseif ($newrel != $oldrel) {
				update_post_meta($postid, 'awqsf-relbool', $newrel);
				}
				


				$returnlink = add_query_arg(array('formid' => $postid, 'formaction' => 'edit','status'=>'updated'), AWQS);
				wp_redirect( $returnlink ); exit;

			
		 }//end update
		
	   }//end submit
		
	}

	function taxo_ajax(){
		include AWQSFURL . '/ajax/add_taxo_ajax.php';
	}
	function cmf_ajax(){
		include AWQSFURL . '/ajax/add_cmf_ajax.php';
	}
	function get_all_keys(){
		global $wpdb;
		$table = $wpdb->prefix.'postmeta';
		$keys = $wpdb->get_results( "SELECT meta_key FROM $table GROUP BY meta_key",ARRAY_A);

		foreach($keys as $key){
			if($key['meta_key']=='awqsf-cpt' || $key['meta_key']=='awqsf-taxo' || $key['meta_key']=='awqsf-relbool' || $key['meta_key']=='awqsf-cmf'){
			}
			else{
				$meta_keys[] = 	$key['meta_key'];		 
				}
		}
		return $meta_keys;
	}

	function get_all_mvalue($metakey){
		global $wpdb;
		$table = $wpdb->prefix.'postmeta';
		$values = $wpdb->get_results( "SELECT meta_value FROM $table WHERE meta_key = '$metakey' GROUP BY meta_value", ARRAY_A);
		foreach($values as $value){
			 $metavalue[] = $value['meta_value']; 
			}
		
		return $metavalue;
	}

	function awqsf_form_shortcode($atts){
		extract(shortcode_atts(array('id' => false, 'formtitle' =>1), $atts));
		if($id)
		{
			 ob_start();
			 $output = include AWQSFURL . '/html/searchform.php';
			 $output = ob_get_clean();
			 return $output;
		}
		else{
			echo 'no form added.';
		}

	}

	function handle_error(){
		static $wp_error; // Will hold global variable safely
    		return isset($wp_error) ? $wp_error : ($wp_error = new WP_Error(null, null, null));	
	}

	function show_messages() {
		if($codes = $this->handle_error()->get_error_codes()) {
			echo '<div style="display: block;background:#FFEBE8;border:#CC0000 solid 1px;padding:5px;margin-bottom:5px;width:auto">';
		        foreach($codes as $code){
			   $message = $this->handle_error()->get_error_message($code);
		          echo '<span style="color:#333333"><strong>' . __('Error') . '</strong>: ' . $message . '</span><br/>';
		        }
		
		 echo '</div>';
		}
		elseif(isset($_GET['status'])){
		  if($_GET['status'] == 'success'){		
		  echo '<div style="display: block;background:#FFFFE0;border:#E6DB55 solid 1px;padding:5px;margin-bottom:5px;width:auto">';	
		  echo '<span style="color:#333333"><strong>' . __('Success','AWQSFTXT') . '</strong>: '.__('New Search Filter Form Created','AWQSFTXT').'</span><br/></div>';
		  }
                  if($_GET['status'] == 'updated'){
		  echo '<div style="display: block;background:#FFFFE0;border:#E6DB55 solid 1px;padding:5px;margin-bottom:5px;width:auto">';	
		  echo '<span style="color:#333333"><strong>' . __('Success','AWQSFTXT') . '</strong>: '.__('Updated','AWQSFTXT').'</span><br/></div>';	
		  }
			
		}	
		
	}	


	}//end class
}//end if class exists
add_filter('widget_text', 'do_shortcode');
global $awqsf;
$awqsf = new awqsf();
