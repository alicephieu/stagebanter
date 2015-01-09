<?php

if (!class_exists('GFCPTAddonLogic')) {

    /*
     * Base class for the GFCPT Addon. All common code is in here and differences per version are overrided
     */
    class GFCPTAddonLogic {

      protected $_has_tag_inputs = false;
      protected $_included_tag_js;
      protected $_tag_inputs = array();
      protected $_tag_map = array();
      protected $_tag_terms = array();
      protected $_taxonomies = array();
      
      protected $_has_linked_to_parents = false;
      protected $_linked_dropdowns = array();
      protected $_included_linked_dropdown_js;
      

        /*
         * Main initilize method for wiring up all the hooks
         */
        public function init() {
            //alter the way forms are rendered by inserting taxomony dropdowns,radios and checkboxes
            add_filter('gform_pre_render' , array(&$this, 'setup_form') );
            
            //alter the way forms are rendered by the admin too!
            add_filter('gform_admin_pre_render' , array(&$this, 'setup_form') );

            //alter the form for submission - this is mainly for checkboxes
            add_filter('gform_pre_submission_filter', array(&$this, 'setup_form') );
            
            add_action('gform_pre_submission', array(&$this, 'init_taxonomies'), 10, 2);

            //set the post type when saving a post
            add_filter("gform_post_data", array(&$this, 'set_post_values'), 10, 2);

            //intercept the form save and save any taxonomy links if needed
            add_action('gform_after_submission', array(&$this, 'save_taxonomies'), 10, 2);

            //enqueue scripts to the page
            add_action('gform_enqueue_scripts', array(&$this, 'enqueue_custom_scripts'), 10, 2);

            add_action('wp_print_scripts', array(&$this, 'enqueue_scripts'), 10, 2);

            add_filter("gform_preview_styles", array(&$this, 'preview_print_styles'), 10, 2);
            
            add_filter('gfcpt_taxonomy_filter', array(&$this, 'taxonomy_filter'), 10, 3);
            
            add_filter('gfcpt_post_filter', array(&$this, 'post_filter'), 10, 3);
            
            add_filter('gform_save_field_value', array(&$this, 'save_taxonomy_field_entry'), 10, 4);
            
            //add our advanced options to the form builder
            add_action('gform_field_advanced_settings', array(&$this, 'render_field_advanced_settings'), 10, 2);

            //include javascript for the form builder
            add_action('gform_editor_js', array(&$this, 'render_editor_js'));

            // filter to add a new tooltip
            add_filter('gform_tooltips', array(&$this, 'add_gf_tooltips'));            
        }
        
        function init_taxonomies($form) {
          $this->_taxonomies = array();
        }

        /*
         * Setup the form with any taxonomies etc
         */
        function setup_form( $form ) {
        
          //loop thru all fields
          foreach($form['fields'] as &$field) {

            //see if the field is using a taxonomy
            $taxonomy = $this->get_field_taxonomy( $field );

            if($taxonomy) {
              $this->setup_taxonomy_field( $field, $taxonomy );
              continue;
            }

            //if its a select then check if we have set a post type
            if ($this->get_type($field) == 'select') {

              $post_type = $this->get_field_post_type( $field );

              if ($post_type) {
                $this->setup_post_type_field( $field, $post_type );
                continue;
              }

            }
          }

          return $form;
        }

        function enqueue_scripts() {
          $this->enqueue_scripts_for_tag_inputs();
          
          $this->enqueue_scripts_for_linked_dropdowns();
        }
        
        function enqueue_scripts_for_linked_dropdowns() {
          if ($this->_has_linked_to_parents) {
            $script_block = '';
            if (sizeof($this->_linked_dropdowns)>0) {
              $script_block = 'var gfcpt_linked_selects = {"items": [';
              $linked_selects = array();
              foreach($this->_linked_dropdowns as $input_id => $taxonomy_data) {
                $terms = get_terms($taxonomy_data['taxonomy'], 'hide_empty=0');
                $term_array = array();
                foreach ($terms as $term) {
                  $term_array[] = '{id: "'.$term->term_id.'", name: "'.$term->name.'", parent: "'.$term->parent.'"}';
                }
                $term_script = implode(', ', $term_array);
                $linked_selects[] = '{target: "'.$input_id.'", source: "'.$taxonomy_data['linked_to'].'", unselected: "'.$taxonomy_data['unselected'].'", default_option: "'.$taxonomy_data['default'].'", terms: ['.$term_script.']}';
              }
              $script_block .= implode(', ', $linked_selects);
              $script_block .= ']};
';
            }

            if (strlen($script_block) > 0) {
            ?>
  <script type='text/javascript'>
    <?php
    echo $script_block;
    ?>
  </script>
  <?php
            }
          }          
        }        
        
        function enqueue_scripts_for_tag_inputs() {
          if ($this->_has_tag_inputs) {
            $script_block = '';
            if (sizeof($this->_tag_inputs)>0) {
              $script_block = 'var gfcpt_tag_inputs = {"tag_inputs": [';
              $input_ids = array();
              foreach($this->_tag_inputs as $input_id => $taxonomy) {
                $input_ids[] = '{input: "'.$input_id.'", taxonomy: "'.$taxonomy.'"}';
              }
              $script_block .= implode(', ', $input_ids);
              $script_block .= ']};
';
            }

            if (sizeof($this->_tag_terms)>0) {
              $script_block .= 'var gfcpt_tag_taxonomies = [];
';
              foreach($this->_tag_terms as $taxonomy => $terms) {
                $script_block .= 'gfcpt_tag_taxonomies["'.$taxonomy.'"] = ["'.implode('", "', $terms).'"];
';
              }
            }
            if (strlen($script_block) > 0) {
            ?>
  <script type='text/javascript'>
    <?php
    echo $script_block;
    ?>
  </script>
  <?php
            }
          }          
        }

        function preview_print_styles($styles, $form){
            return array('gfcpt_jquery_ui_theme', 'gfcpt_tagit_css');
        }

        function enqueue_custom_scripts($form, $is_ajax) {
          //loop thru all fields
          foreach($form['fields'] as &$field) {
            
            //if its a text field, see if we have set it to save to a taxonomy
            if ($this->get_type($field) == 'text' && array_key_exists('saveToTaxonomy', $field)) {
              $saveToTaxonomy = $field['saveToTaxonomy'];

              if (taxonomy_exists($saveToTaxonomy) && array_key_exists('taxonomyEnhanced', $field)) {
                if ($field['taxonomyEnhanced']) {

                  $this->_has_tag_inputs = true;

                  $tag_input_id = '#input_'.$form['id'].'_'.$field['id'];

                  $this->_tag_inputs[$tag_input_id] = $saveToTaxonomy;

                  if ( !array_key_exists($saveToTaxonomy, $this->_tag_terms) ) {
                    //get the existing taxonomies and add them to an array to render later
                    $terms = get_terms($saveToTaxonomy, 'orderby=name&hide_empty=0&fields=names');
                    $this->_tag_terms[$saveToTaxonomy] = $terms;
                  }

                  if (!$this->_included_tag_js) {

                    //enqueue some scripts for the enhaced UI
                    $this->_included_tag_js = true;

                    wp_register_style(
                            $handle = 'gfcpt_jquery_ui_theme',
                            $src = plugins_url( 'css/custom/jquery-ui-1.8.16.custom.css' , __FILE__ ) );
                    wp_enqueue_style('gfcpt_jquery_ui_theme');

                    wp_register_style(
                            $handle = 'gfcpt_tagit_css',
                            $src = plugins_url( 'css/jquery.tagit.css' , __FILE__ ) );
                    wp_enqueue_style('gfcpt_tagit_css');


                    wp_register_script(
                            $handle = 'gfcpt_jquery_ui',
                            $src = plugins_url( 'js/jquery-ui-1.8.16.custom.min.js' , __FILE__ ),
                            $deps = array('jquery') );

                    wp_enqueue_script('gfcpt_jquery_ui');

                    wp_register_script(
                            $handle = 'gfcpt_tagit_js',
                            $src = plugins_url( 'js/tag-it.js' , __FILE__ ),
                            $deps = array('gfcpt_jquery_ui') );

                    wp_enqueue_script('gfcpt_tagit_js');

                    wp_register_script(
                            $handle = 'gfcpt_tagit_init_js',
                            $src = plugins_url( 'js/tag-it.init.js' , __FILE__ ),
                            $deps = array('gfcpt_tagit_js') );

                    wp_enqueue_script('gfcpt_tagit_init_js');
                  }

                }
              }
            } else if ($this->get_type($field) == 'select') {
              
              $linked_to_parent_id = $this->get_field_taxonomy_link_to_parent($field);

              //check if the link_to_parent check is checked
              if ( !empty($linked_to_parent_id) ) {

                $this->_has_linked_to_parents = true;

                $tag_input_id = '#input_'.$form['id'].'_'.$field['id'];
                $linked_input_id = '#input_'.$form['id'].'_'.$linked_to_parent_id;

                $taxonomy_data = array();
                $taxonomy_data['field_id'] = $field['id'];
                $taxonomy_data['taxonomy'] = $this->get_field_taxonomy($field);
                $taxonomy_data['unselected'] = $this->get_field_taxonomy_link_to_parent_unselected($field);
                $taxonomy_data['default'] = $this->get_field_taxonomy_link_to_parent_default($field);
                $taxonomy_data['linked_to'] = $linked_input_id;

                $this->_linked_dropdowns[$tag_input_id] = $taxonomy_data;
                
                if (!$this->_included_linked_dropdown_js) {

                  //enqueue some scripts for the linked dropdowns
                  $this->_included_linked_dropdown_js = true;
                  
                  wp_register_script(
                          $handle = 'gfcpt_linked_selects_init_js',
                          $src = plugins_url( 'js/linked-selects-init.js' , __FILE__ ),
                          $deps = array('jquery') );

                  wp_enqueue_script('gfcpt_linked_selects_init_js');                  
                  
                }
                
              }
            }

          }
        }

        /*
         * Set the post values (if neccessary)
         */
        function set_post_values( $post_data, $form ) {

            //check if the form saves a post
            if ( $this->is_form_a_post_form($form) ) {
                $target_post_type = $this->get_form_post_type( $form );

                if ($target_post_type)
                    $post_data["post_type"] = $target_post_type;

                //then check if we have set a parent
                $parent_post_id = $this->get_form_parent_post_id( $form );

                if ($parent_post_id > 0) {
                  $post_data["post_parent"] = $parent_post_id;
                }
            }
            return $post_data;

        }

        /*
         * Checks if a form includes a 'post field'
         */
        function is_form_a_post_form( $form ) {
            foreach ($form["fields"] as $field) {
                if(in_array($field["type"],
                        array("post_category","post_title","post_content",
                            "post_excerpt","post_tags","post_custom_fields","post_image")))
                    return true;
            }
            return false;
        }

        /*
         * Override. Gets the post type
         */
        function get_field_post_type( $field ) {
          if (array_key_exists('populatePostType', $field)) {
            return $field['populatePostType'];
          } else {
            return false;
          }
        }

        /*
         * Override. Gets the taxonomy 
         */
        function get_field_taxonomy( $field ) {
          if (array_key_exists('populateTaxonomy', $field)) {
            return $field['populateTaxonomy'];
          } else if (array_key_exists('saveToTaxonomy', $field)) {
            return $field['saveToTaxonomy'];
          } else {
            return false;
          }
        }
        
        /*
         * Gets the taxonomy exclusions
         */
        function get_field_taxonomy_exclusions( $field ) {
          if (array_key_exists('taxonomyExclusions', $field)) {
            return $field['taxonomyExclusions'];
          } else {
            return false;
          }
        }
        
        /*
         * Gets the post exclusions
         */        
        function get_field_post_exclusions ( $field ) {
          if (array_key_exists('populatePostTypeExclude', $field)) {
            return $field['populatePostTypeExclude'];
          } else {
            return false;
          }
        }
        
        function get_field_taxonomy_link_to_parent( $field ) {
          if (array_key_exists('taxonomyLinkToParent', $field) &&
                  array_key_exists('taxonomyLinkToParentId', $field)) {
            return $field['taxonomyLinkToParentId'];
          } else {
            return false;
          }
        }
        
        function get_field_taxonomy_top_level( $field ) {
          if (array_key_exists('taxonomyTopLevelOnly', $field)) {
            return $field['taxonomyTopLevelOnly'];
          } else {
            return false;
          }
        }
        
        function get_field_taxonomy_link_to_parent_unselected( $field ) {
          if (array_key_exists('taxonomyLinkToParentUnselected', $field)) {
            return $field['taxonomyLinkToParentUnselected'];
          } else {
            return '';
          }
        }
        
        function get_field_taxonomy_link_to_parent_default( $field ) {
          if (array_key_exists('taxonomyLinkToParentDefault', $field)) {
            return $field['taxonomyLinkToParentDefault'];
          } else {
            return '';
          }          
        }
        
        /*
         * Override. Gets the custom post type from the post title field value
         */
        function get_form_post_type( $form ) {
            foreach ( $form['fields'] as $field ) {
                if ( $this->get_type($field) == 'post_title' && isset($field['saveAsCPT']) && $field['saveAsCPT'] )
                    return $field['saveAsCPT'];
            }
            return false;
        }

        function get_form_parent_post_id( $form ) {
            foreach ( $form['fields'] as $field ) {
                if ( $this->get_type($field) == 'select' && isset($field['setParentPost']) && $field['setParentPost'] ) {
                    $parent_id = RGForms::post('input_'.$field['id']);
                    return $parent_id;
                }
            }
            return 0;
        }
        
        /*
         * setup a field if it is linked to a post type
         */
        function setup_post_type_field( &$field, $post_type ) {
          $field['choices'] = $this->load_post_type_choices( $post_type, $field );
        }

        function load_post_type_choices($post_type, &$field) {
          
          //force enableChoiceValue to true
          $field['enableChoiceValue'] = true;
          
          $first_choice = '';
          if (isset($field['choices']) && isset($field['choices'][0])) {
            $first_choice = $field['choices'][0]['text'];
          }

          if (isset($field['choices']) && count($field['choices']) > 0 ) {
            $selected = $this->get_selected_choices($field['choices']);
          }else{
            $selected = array();
          }
          
          $isSelected = in_array('', $selected) ? 1 : null;
          
          $posts = $this->load_posts_hierarchical( $post_type );
          if ($first_choice === '' || $first_choice === 'First Choice'){
            // if no default option is specified, dynamically create based on post type name
            $post_type_obj = get_post_type_object($post_type);
            $choices[] = array('text' => "-- select a {$post_type_obj->labels->singular_name} --", 'value' => '', 'isSelected' => $isSelected);
          } else {
            $choices[] = array('text' => $first_choice, 'value' => '', 'isSelected' => $isSelected);
          }

          //apply our filter
          $posts = apply_filters('gfcpt_post_filter', $posts, $post_type, $field);            

          foreach($posts as $post) {
            $isSelected = in_array($post->ID, $selected) ? 1 : null;
            $choices[] = array('value' => strval($post->ID), 'text' => $post->post_title, 'isSelected' => $isSelected);
          }

          return $choices;
        }
        
        /*
         * Filters a list of posts
         */
        function post_filter($posts, $post_type, $field) {
          if ( !empty($posts) && !array_key_exists("errors",$posts) ) {
            
            $exclusions = $this->get_field_post_exclusions( $field );

            if ( !empty($exclusions) ) {
              
              $exclusions = array_map('trim',explode(',',$exclusions));
            
              $new_posts = array();
              foreach($posts as $post) {
                if ( !in_array($post->ID, $exclusions) )
                  $new_posts[] = $post;
              }
              return $new_posts;
            }
          }
          return $posts;
        }      

        /*
         * Get a hierarchical list of posts
         */
        function load_posts_hierarchical( $post_type ) {
          $args = array(
              'post_type'     => $post_type,
              'numberposts'   => -1,
              'orderby'       => 'title',
              'post_status'   => 'publish'
          );

          $args = apply_filters( 'gfcpt_get_post_args', $args );
          $posts = get_posts( $args );
          $posts = apply_filters( 'gfcpt_get_post_filter', $posts );
          return $this->walk_posts( $posts );
        }

        /*
         * Helper function to recursively 'walk' the posts
         */
        function walk_posts( $input_array, $parent_id=0, &$out_array=array(), $level=0 ){
          foreach ( $input_array as $item ) {
            if ( $item->post_parent == $parent_id ) {
              $item->post_title = str_repeat('--', $level) . $item->post_title;
              $out_array[] = apply_filters( 'gfcpt_alter_hierarchical_post', $item );
              $this->walk_posts( $input_array, $item->ID, $out_array, $level+1 );
            }
          }
          return $out_array;
        }
        
        function get_type($field) {
          $type = '';
        
          if ( array_key_exists( 'type', $field ) ) {
            $type = $field['type'];
            
            if ($type == 'post_custom_field') {
              if ( array_key_exists( 'inputType', $field ) ) {
                $type = $field['inputType'];
                //print_r($type);
              }
            }
            
            return $type;
          }
        }

        /*
         * setup a field if it is linked to a taxonomy
         */
        function setup_taxonomy_field( &$field, $taxonomy ) {
            $field['choices'] = $this->load_taxonomy_choices( $taxonomy, $field );

            //now check if we are dealing with a checkbox list and do some extra magic
            if ( $this->get_type($field) == 'checkbox' ) {
                //clear the inputs first
                $field['inputs'] = array();

                $counter = 0;
                //recreate the inputs so they are captured correctly on form submission
                foreach( $field['choices'] as $choice ) {
                    $counter++;
                    if ( ($counter % 10) == 0 ) $counter++; //thanks to Peter Schuster for the help on this fix
                    $id = floatval( $field['id'] . '.' . $counter );
                    $field['inputs'][] = array('label' => $choice['text'], 'id' => $id);
                }
            }
        }

        /*
         * Load any taxonomy terms
         */
        function load_taxonomy_choices($taxonomy, &$field) {
            $first_choice = '';
            if (isset($field['choices']) && isset($field['choices'][0])) {
              $first_choice = $field['choices'][0]['text'];
            }
            
            if (isset($field['choices']) && count($field['choices']) > 0 ) {
              $selected = $this->get_selected_choices($field['choices']);
            }else{
              $selected = array();
            }
            
            $type = $this->get_type($field);
            
            $choices = array();

            if ($type === 'select') {
              
              //force enableChoiceValue to true
              $field['enableChoiceValue'] = true;              
              
              $link_to_parent = $this->get_field_taxonomy_link_to_parent( $field );
              
              $isSelected = in_array('', $selected) ? 1 : null;
              
              if ($link_to_parent) {
                //return the default choice only
                $unselected = $this->get_field_taxonomy_link_to_parent_unselected( $field );
                
                $choices[] = array('text' => $unselected, 'value' => '', 'isSelected' => $isSelected);
                return $choices;
              }
              
              $load_top_level = $this->get_field_taxonomy_top_level( $field );

              $terms = $this->load_taxonomy_hierarchical( $taxonomy, $load_top_level );
              
              if ($first_choice === '' || $first_choice === 'First Choice'){
                  // if no default option is specified, dynamically create based on taxonomy name
                  $taxonomy = get_taxonomy($taxonomy);
                  $choices[] = array('text' => "-- select a {$taxonomy->labels->singular_name} --", 'value' => '', 'isSelected' => $isSelected);
              } else {
                  $choices[] = array('text' => $first_choice, 'value' => '', 'isSelected' => $isSelected);
              }
            } else {
              $terms = get_terms($taxonomy, 'orderby=name&hide_empty=0');
            }
            
            //apply our filter
            $terms = apply_filters('gfcpt_taxonomy_filter', $terms, $taxonomy, $field);            

            if ( !empty($terms) && !array_key_exists("errors",$terms) ) {
              foreach($terms as $term) {
                $isSelected = in_array($term->term_id, $selected) ? 1 : null;
                $choices[] = array('value' => strval($term->term_id), 'text' => $term->name, 'isSelected' => $isSelected);
              }
            }

            return $choices;
        }
        
        /*
         * Filters a list of taxonomies
         */
        function taxonomy_filter($terms, $taxonomy, $field) {
          if ( !empty($terms) && !array_key_exists("errors",$terms) ) {
            
            $exclusions = $this->get_field_taxonomy_exclusions( $field );

            if ( !empty($exclusions) ) {
              
              $exclusions = array_map('trim',explode(',',$exclusions));
            
              $new_terms = array();
              foreach($terms as $term) {
                if ( !in_array($term->term_id, $exclusions) )
                  $new_terms[] = $term;
              }
              return $new_terms;
            }
          }
          return $terms;
        }        

        /*
         * Get a hierarchical list of taxonomies
         */
        function load_taxonomy_hierarchical( $taxonomy, $load_top_level ) {
          
          $args = array(
            'taxonomy'      => $taxonomy,
            'orderby'       => 'name',
            'hierarchical'  => 1,
            'hide_empty'    => 0
          );
          
          if ( $load_top_level ) {
            $args['parent'] = 0;
          }
          
          $terms = get_categories( $args );

          if ( array_key_exists("errors",$terms) ) {
            return $terms;
          }
          else
            return $this->walk_terms( $terms );
        }

        /*
         * Helper function to recursively 'walk' the taxonomy terms
         */
        function walk_terms( $input_array, $parent_id=0, &$out_array=array(), $level=0 ){
          foreach ( $input_array as $item ) {
            if ( $item->parent == $parent_id ) {
              $item->name = str_repeat('--', $level) . $item->name;
              $out_array[] = apply_filters( 'gfcpt_alter_hierarchical_term', $item );
              $this->walk_terms( $input_array, $item->term_id, $out_array, $level+1 );
            }
          }
          return $out_array;
        }

        /*
         * Loop through all fields and save any linked taxonomies
         */
        function save_taxonomies( $entry, $form ) {
            // Check if the submission contains a WordPress post
            if ( isset ( $entry['post_id'] ) ) {

                foreach( $form['fields'] as &$field ) {

                    $taxonomy = $this->get_field_taxonomy( $field );

                    if ( !$taxonomy ) continue;

                    $this->save_taxonomy_field( $field, $entry, $taxonomy );
                }
            }
        }

        /*
         * Save linked taxonomies for a single field
         */
        function save_taxonomy_field( &$field, $entry, $taxonomy ) {
          
          $field_id = $field['id'];
          
          if ( is_array($this->_taxonomies) && array_key_exists( $field_id, $this->_taxonomies ) ) {
          
            //get the taxonomy data
            $taxonomy_data = $this->_taxonomies[$field_id];
            
            $type = $this->get_type($field);
          
            if ( $type == 'checkbox' || $type == 'multiselect' ) {
              
              $term_ids = $taxonomy_data['term_ids'];
              if ( !empty ( $term_ids )) {
                wp_set_object_terms( $entry['post_id'], $term_ids, $taxonomy, true );
              }
            
                
            } else if ( $type == 'text' ) {
              
              $terms = $taxonomy_data['original'];
              if ( !empty($terms) ) {
                $terms = explode(',', $terms);

                foreach ( $terms as $term ) {
                  $term_info = term_exists($term, $taxonomy);
                  
                  if (empty($term_info)) {
                    //add the term
                    $term_info = wp_insert_term($term, $taxonomy);

                    if ( !is_wp_error($term_info) ) {
                      $term_ids[] = (int) $term_info['term_id'];
                    }
                  } else {
                    //term exists
                    $term_ids[] = (int) $term_info['term_id'];
                  }
                }
                
                if ( !empty ( $term_ids )) {
                  wp_set_object_terms( $entry['post_id'], $term_ids, $taxonomy, true );                
                }
              }
              
            } else if ( $type == 'radio' && rgar($field, 'enableOtherChoice') ) {
              
              if (isset($taxonomy_data['term_to_add'])) {
                //add the term
                $term_info = wp_insert_term($taxonomy_data['term_to_add'], $taxonomy);
                
                if ( !is_wp_error($term_info) ) {
                  $term_id = (int) $term_info['term_id'];
                  wp_set_object_terms( $entry['post_id'], $term_id, $taxonomy, true );
                }
                
              } else {
                $term_id = (int) $taxonomy_data['term_id'];
                if ( $term_id > 0 ) {
                  wp_set_object_terms( $entry['post_id'], $term_id, $taxonomy, true );                
                }
              }
            
              
            } else {
              $term_id = (int) $taxonomy_data['term_id'];
              if ( $term_id > 0 ) {
                wp_set_object_terms( $entry['post_id'], $term_id, $taxonomy, true );
              }
            }
            
          }
        }
        
        function save_taxonomy_field_entry($value, $lead, $field, $form) {
          
          $taxonomy = $this->get_field_taxonomy( $field );

          if ( !$taxonomy ) return $value;
          
          $field_id = $field['id'];
          
          if ( array_key_exists($field_id, $this->_taxonomies) ) {
            $taxonomy_data = $this->_taxonomies[$field_id];
          } else {
            $taxonomy_data = array();
            $taxonomy_data['field_id'] = $field_id;
            $taxonomy_data['taxonomy'] = $taxonomy;
            $taxonomy_data['original'] = $value;
            $taxonomy_data['term_id'] = 0;
          }
          
          $type = $this->get_type($field);
          
          if ( $type == 'checkbox' ) {
            $term_id = (int)$value;
            $taxonomy_data['term_ids'][] = $term_id;
            
            $term = get_term_by('id', (int)$term_id, $taxonomy);
            
            $taxonomy_data['term_names'][] = $term->name;
            
            $value = $term->name;
          } else if ( $type == 'text' ) {
            //in the form of "Blue,Black"
            
            
          } else if ( $type == 'multiselect' ) {
            //in the form of "20,21"
            $term_ids = explode(',', $value);
            $value = '';
            foreach ( $term_ids as $term_id ) {
              $term_id = (int)$term_id;
              $taxonomy_data['term_ids'][] = $term_id;

              $term = get_term_by('id', $term_id, $taxonomy);

              $taxonomy_data['term_names'][] = $term->name;
            }
            
            if (isset($taxonomy_data['term_names'])) {
              $value = implode(',', $taxonomy_data['term_names']);
            }
            
          } else if ( $type == 'radio' && rgar($field, 'enableOtherChoice') ) {
            $real_value = rgpost("input_{$field['id']}");
            
            if ($real_value == 'gf_other_choice') {
              //other was selected
              $term = get_term_by('name', $value, $taxonomy);

              if ($term === false) {
                //we are adding a term
                $taxonomy_data['term_to_add'] = $value;
              } else {
                $taxonomy_data['term_id'] = $term->term_id;
                $value = $taxonomy_data['entry'] = $term->name;
              }
            } else {
              $term_id = (int)$value;
              
              $term = get_term_by('id', $term_id, $taxonomy);
              
              $taxonomy_data['term_id'] = $term->term_id;
              $value = $taxonomy_data['entry'] = $term->name;              
            }
            
          } else {
            $term_id = (int)$value;
            if ($term_id > 0) {
              $taxonomy_data['term_id'] = $term_id;
              $term = get_term_by('id', $term_id, $taxonomy);
              
              $taxonomy_data['term_id'] = $term->term_id;
              $value = $taxonomy_data['entry'] = $term->name;
              
            } else {
              $value = ''; //set to blank if no value was selected
            }
          }
          
          $this->_taxonomies[$field_id] = $taxonomy_data;
          
          return $value;
        }
        
        /*
         * Add tooltips for the new field values
         */
        function add_gf_tooltips($tooltips){
           $tooltips["form_field_populate_post_type"] = "<h6>Populate with a Post Type</h6>Check this box to populate this field from a specific post type.";
           $tooltips["form_field_populate_post_type_exclude"] = "<h6>Exclude Posts</h6>Enter the id's of the posts you want to exlude from the list.";
           $tooltips["form_field_set_parent_post"] = "<h6>Try to set parent</h6>If this is checked, and the form creates a post type, then the parent for the newly created post type will be set from the value of this field. Please note that this only works for heirarcical post typs e.g. pages";
           $tooltips["form_field_custom_taxonomy"] = "<h6>Populate with a Taxonomy</h6>Check this box to populate this field from a taxonomy.";
           $tooltips["form_field_custom_post_type"] = "<h6>Save As Post Type</h6>Check this box to save this form to a specific post type.";
           $tooltips["form_field_save_to_taxonomy"] = "<h6>Save To Taxonomy</h6>Check this box to save this field to a specific custom taxonomy. Please note that the taxonomy must NOT be hierarchical.";
           $tooltips["form_field_tax_enhanced"] = "<h6>Enable Enhanced UI</h6>By selecting this option, this field will be tranformed into a 'tag input' control which makes it more user-friendly for selecting existing and capturing new taxonomies.";
           $tooltips["form_field_custom_taxonomy_exclusions"] = "<h6>Exclude Taxonomies</h6>Enter the id's of the taxonomies you want to exlude from the list.";
           $tooltips["form_field_custom_taxonomy_top_level"] = "<h6>Only Show Top Level Terms</h6>Check this to only show the top level terms for a heirarchical taxonomy. Top level meaning all terms that have no parents.";
           $tooltips["form_field_custom_taxonomy_link_to_parent"] = "<h6>Link To Parent Field</h6>Check this to link the dropdown with a parent dropdown populated by the same hierarchical taxonomy. Once linked, this dropdown will show child items based on the parent dropdown's selection.";
           $tooltips["form_field_custom_taxonomy_link_to_parent_id"] = "<h6>Parent Field ID</h6>Enter the Gravity Form field ID of the parent dropdown you with to link to.";
           $tooltips["form_field_custom_taxonomy_link_to_parent_unselected"] = "<h6>Unselected Value</h6>The value that is displayed when no option is chosen from the parent dropdown. E.g. 'select a parent category first'";
           $tooltips["form_field_custom_taxonomy_link_to_parent_default"] = "<h6>Default Value</h6>The value that is selected when the parent dropdown value has been selected.";
          return $tooltips;
        }
        
        /*
         * Add some advanced settings to the fields
         */
        function render_field_advanced_settings($position, $form_id) {
          gfcpt_render_field_advanced_settings($position, $form_id);
        }
        
        /*
         * render some custom JS to get the settings to work
         */
        function render_editor_js() {        
          gfcpt_render_editor_js();
        }
        
        /**
         * returns an array of the selected choices
         */
        function get_selected_choices( $choices ) {
          $selected = array();
          foreach ($choices as $choice) {
            if (isset($choice['isSelected']) && $choice['isSelected']=='1') {
              $selected[] = $choice['value'];
            }
          }
          return $selected;
        }        
    }
}

?>