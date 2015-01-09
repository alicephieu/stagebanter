<?php

/*
 * render some custom JS to get the settings to work
 */
function gfcpt_render_editor_js(){
    ?>
    <script type='text/javascript'>

        jQuery(document).bind("gform_load_field_settings", function(event, field, form){
            //only show taxonomy for selects and radios
            var valid_types = new Array('select', 'radio', 'checkbox', 'multiselect', 'post_custom_field');

            var field_type = field['type'];

            //alert(field_type);

            if(jQuery.inArray(field_type, valid_types) != -1) {

                //check if we dealing with a custom field type
                var custom_field_type = jQuery('#post_custom_field_type').val();
                if (jQuery.inArray(custom_field_type, valid_types) != -1) {
                  field_type = custom_field_type;
                }

                var $taxonomy_setting_container = jQuery(".populate_with_taxonomy_field_setting");
                //show the setting container!
                $taxonomy_setting_container.show();

                //get the saved taxonomy
                var populateTaxonomy = (typeof field['populateTaxonomy'] != 'undefined' && field['populateTaxonomy'] != '') ? field['populateTaxonomy'] : false;

                if (populateTaxonomy != false) {
                  
                  var taxonomyExclusions = (typeof field['taxonomyExclusions'] != 'undefined' && field['taxonomyExclusions'] != '') ? field['taxonomyExclusions'] : '';
                  var taxonomyTopLevelOnly = (typeof field['taxonomyTopLevelOnly'] != 'undefined' && field['taxonomyTopLevelOnly'] != '') ? field['taxonomyTopLevelOnly'] : false;

                  if (taxonomyTopLevelOnly != false) {
                    $taxonomy_setting_container.find(".check_show_top").attr("checked", "checked");
                  } else {
                    $taxonomy_setting_container.find(".check_show_top").removeAttr("checked");
                  }
                  
                  if (field_type == 'select') {
                    
                    $taxonomy_setting_container.find(".link_to_parent_form").show();
                    var taxonomyLinkToParent = (typeof field['taxonomyLinkToParent'] != 'undefined' && field['taxonomyLinkToParent'] != '') ? field['taxonomyLinkToParent'] : false;
                    var taxonomyLinkToParentId = (typeof field['taxonomyLinkToParentId'] != 'undefined' && field['taxonomyLinkToParentId'] != '') ? field['taxonomyLinkToParentId'] : '';
                    var taxonomyLinkToParentUnselected = (typeof field['taxonomyLinkToParentUnselected'] != 'undefined' && field['taxonomyLinkToParentUnselected'] != '') ? field['taxonomyLinkToParentUnselected'] : '';
                    var taxonomyLinkToParentDefault = (typeof field['taxonomyLinkToParentDefault'] != 'undefined') ? field['taxonomyLinkToParentDefault'] : '<?php _e("--select--", "gravityforms"); ?>';

                    if (taxonomyLinkToParent != false) {
                      $taxonomy_setting_container.find(".check_link_to_parent").attr("checked", "checked");
                      $taxonomy_setting_container.find(".link_to_parent_container").show();
                    } else {
                      $taxonomy_setting_container.find(".check_link_to_parent").removeAttr("checked");
                      $taxonomy_setting_container.find(".link_to_parent_container").hide();
                    }
                    
                    //set the parent field id
                    $taxonomy_setting_container.find("input.taxonomy_parent_field_id").val(taxonomyLinkToParentId);
                    //set the parent unselected
                    $taxonomy_setting_container.find("input.taxonomy_parent_unselected").val(taxonomyLinkToParentUnselected);
                    //set the parent default
                    $taxonomy_setting_container.find("input.taxonomy_parent_default").val(taxonomyLinkToParentDefault);
                  
                  } else {
                    
                    $taxonomy_setting_container.find(".link_to_parent_form").hide();
                    
                  }
                  
                  //check the checkbox if previously checked
                  $taxonomy_setting_container.find("input:checkbox:first").attr("checked", "checked");
                  //set the select
                  $taxonomy_setting_container.find("select").val(populateTaxonomy);
                  //set the exclusions
                  $taxonomy_setting_container.find("input.taxonomy_exclusions").val(taxonomyExclusions);
                  //show it!
                  $taxonomy_setting_container.find("div:first").show();

                } else {
                  $taxonomy_setting_container.find("input:checkbox:first").removeAttr("checked");
                  $taxonomy_setting_container.find("select").val('');
                  $taxonomy_setting_container.find("input.taxonomy_exclusions").val('');
                  $taxonomy_setting_container.find("input.taxonomy_parent_field_id").val('');
                  $taxonomy_setting_container.find("input.taxonomy_parent_unselected").val('');
                  $taxonomy_setting_container.find("input.taxonomy_parent_default").val('<?php _e("--select--", "gravityforms"); ?>');
                  $taxonomy_setting_container.find(".link_to_parent_container").hide();
                  $taxonomy_setting_container.find(".check_link_to_parent").removeAttr("checked");
                  $taxonomy_setting_container.find("div:first").hide();
                }

                if (field_type == 'select') {
                  
                  var $populate_post_type_container = jQuery(".populate_with_post_type_field_setting");
                  $populate_post_type_container.show();

                  //hide the link to parent fields
                  $taxonomy_setting_container.find(".link_to_parent_form").show();

                  //get the saved post type
                  var populatePostType = (typeof field['populatePostType'] != 'undefined' && field['populatePostType'] != '') ? field['populatePostType'] : false;

                  if (populatePostType != false) {
                    //check the checkbox if previously checked
                    $populate_post_type_container.find("#field_enable_populate_with_post_type").attr("checked", "checked");
                    //set the select
                    $populate_post_type_container.find("select").val(populatePostType);
                    //set the exclusions
                    $populate_post_type_container.find(".post_type_exclusions").val();
                    //show the div
                    $populate_post_type_container.find("div").show();

                    //get the saved check for setting the parent post
                    var setParent = (typeof field['setParentPost'] != 'undefined' && field['setParentPost'] != '') ? field['setParentPost'] : false;
                    if (setParent != false) {
                      $populate_post_type_container.find(".check_parent").attr("checked", "checked");
                    } else {
                      $populate_post_type_container.find(".check_parent").removeAttr("checked");
                    }
                      
                    var postExclusions = (typeof field['populatePostTypeExclude'] != 'undefined' && field['populatePostTypeExclude'] != '') ? field['populatePostTypeExclude'] : '';
                    $populate_post_type_container.find(".post_type_exclusions").val(postExclusions);
                      
                  } else {
                    $populate_post_type_container.find("input.toggle_setting").removeAttr("checked");
                    $populate_post_type_container.find("select").val('');
                    $populate_post_type_container.find(".post_type_exclusions").val('');
                    $populate_post_type_container.find(".check_parent").removeAttr("checked");
                  }

                } else {
                  $taxonomy_setting_container.find(".link_to_parent_form").hide();
                }

            } else if (field_type == 'post_title') {
                var $cpt_setting_container = jQuery(".custom_post_type_field_setting");

                $cpt_setting_container.show();

                var saveAsCPT = (typeof field['saveAsCPT'] != 'undefined' && field['saveAsCPT'] != '') ? field['saveAsCPT'] : false;

                if (saveAsCPT != false) {
                    //check the checkbox if previously checked
                    $cpt_setting_container.find("input:checkbox").attr("checked", "checked");
                    //set the select and show
                    $cpt_setting_container.find("select").val(saveAsCPT).show();
                } else {
                    $cpt_setting_container.find("input:checkbox").removeAttr("checked");
                    $cpt_setting_container.find("select").val('').hide();
                }
            } else if (field_type == 'text') {
                var $tax_setting_container = jQuery('.save_to_taxonomy_field_setting');

                $tax_setting_container.show();

                var saveToTax = (typeof field['saveToTaxonomy'] != 'undefined' && field['saveToTaxonomy'] != '') ? field['saveToTaxonomy'] : false;

                if (saveToTax != false) {
                    //check the checkbox if previously checked
                    $tax_setting_container.find("input.toggle_setting").attr("checked", "checked");
                    //set the select
                    $tax_setting_container.find("select").val(saveToTax);
                    //show the div
                    $tax_setting_container.find("div").show();

                    //get the saved check for using enhanced UI
                    var useEnhancedUI = (typeof field['taxonomyEnhanced'] != 'undefined' && field['taxonomyEnhanced'] != '') ? field['taxonomyEnhanced'] : false;
                    if (useEnhancedUI != false) {
                      $tax_setting_container.find(".check_tax_enhanced").attr("checked", "checked");
                    } else {
                      $tax_setting_container.find(".check_tax_enhanced").removeAttr("checked");
                    }

                } else {
                    $tax_setting_container.find("input.toggle_setting").removeAttr("checked");
                    $tax_setting_container.find("div").hide();
                    $tax_setting_container.find(".check_tax_enhanced").removeAttr("checked");
                    $tax_setting_container.find("select").val('');
                }
            }
        });

        jQuery(".populate_with_taxonomy_field_setting input.toggle_setting").click(function() {
            var checked = jQuery(this).is(":checked");
            var $div = jQuery(this).parent(".populate_with_taxonomy_field_setting:first").find("div:first");
            if(checked){
                $div.slideDown();

                //uncheck post type
                var $pt_container = jQuery(this).parents("ul:first").find(".populate_with_post_type_field_setting:first");
                var $pt_check = $pt_container.find("input.toggle_setting");
                var $pt_div = $pt_container.find("div");
                if ($pt_check.is(":checked")) {

                  SetFieldProperty('populatePostType','');
                  $pt_div.slideUp();
                  $pt_check.removeAttr('checked');

                }

            } else {
                SetFieldProperty('populateTaxonomy','');
                $div.slideUp();
            }
        });

        jQuery(".custom_post_type_field_setting input:checkbox").click(function() {
            var checked = jQuery(this).is(":checked");
            var $select = jQuery(this).parent(".custom_post_type_field_setting:first").find("select");
            if(checked){
                $select.slideDown();
            } else {
                SetFieldProperty('saveAsCPT','');
                $select.slideUp();
            }
        });

        jQuery(".populate_with_post_type_field_setting .toggle_setting").click(function() {
            var checked = jQuery(this).is(":checked");
            var $div = jQuery(this).parent(".populate_with_post_type_field_setting:first").find("div");
            if(checked){
                $div.slideDown();
                //uncheck taxonomy
                var $tax_container = jQuery(this).parents("ul:first").find(".populate_with_taxonomy_field_setting:first");
                var $tax_check = $tax_container.find("input:checkbox");
                var $tax_select = $tax_container.find("select");
                if ($tax_check.is(":checked")) {

                  SetFieldProperty('populateTaxonomy','');
                  $tax_container.find('div:first').slideUp();
                  $tax_check.removeAttr('checked');

                }

            } else {
                SetFieldProperty('populatePostType','');
                $div.slideUp();
            }
        });

        jQuery(".save_to_taxonomy_field_setting .toggle_setting").click(function() {
            var checked = jQuery(this).is(":checked");
            var $div = jQuery(this).parent(".save_to_taxonomy_field_setting:first").find("div");
            if(checked){
                $div.slideDown();
            } else {
                SetFieldProperty('saveToTaxonomy','');
                $div.slideUp();
            }
        });

        jQuery(".check_link_to_parent").click(function() {
          var checked = jQuery(this).is(":checked");
          var $parent = jQuery(this).parents(".populate_with_taxonomy_field_setting:first");
          var $div = $parent.find(".link_to_parent_container");

          if(checked){
            $div.slideDown();
          } else {
            SetFieldProperty('taxonomyLinkToParent','');
            $div.slideUp();
          }
        });

    </script>
    <?php
}


?>
