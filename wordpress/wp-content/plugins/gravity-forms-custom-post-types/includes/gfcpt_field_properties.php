<?php

/*
 * Add some advanced settings to the fields
 */
 function gfcpt_render_field_advanced_settings($position, $form_id){
    if($position == 50){
        ?>
        <li class="populate_with_taxonomy_field_setting field_setting" style="display:list-item;">
            <input type="checkbox" class="toggle_setting" id="field_enable_populate_with_taxonomy" />
            <label for="field_enable_populate_with_taxonomy" class="inline">
                <?php _e("Populate with a Taxonomy", "gravityforms"); ?>
            </label>
            <?php gform_tooltip("form_field_custom_taxonomy") ?>
            <div style="margin-left:30px; display:none;">
              <?php _e("Select a Taxonomy", "gravityforms"); ?>
              <select id="field_populate_taxonomy" onchange="SetFieldProperty('populateTaxonomy', jQuery(this).val());" style="margin-top:10px;">
                <option value="" style="color:#999;"><?php _e("--select--", "gravityforms"); ?></option>
              <?php
              $args=array(
                'public' => true
              );
              $taxonomies = get_taxonomies($args, 'objects');
              foreach($taxonomies as $taxonomy): ?>
                  <option value="<?php echo $taxonomy->name; ?>"><?php echo $taxonomy->label; ?></option>
              <?php endforeach; ?>
              </select>
              <br />
              <?php _e("Exclude terms", "gravityforms"); ?> <input class="taxonomy_exclusions" type="text" id="field_populate_with_taxonomy_exclude" onkeyup="SetFieldProperty('taxonomyExclusions', this.value);" /><?php gform_tooltip("form_field_custom_taxonomy_exclusions") ?>
              <div style="margin-top:5px;">
                <input class="check_show_top" type="checkbox" id="field_populate_with_taxonomy_top_level" onclick="SetFieldProperty('taxonomyTopLevelOnly', this.checked);" />
                <label for="field_populate_with_taxonomy_top_level" class="inline">
                  <?php _e("Only Show Top Level Terms", "gravityforms"); ?>
                </label><?php gform_tooltip("form_field_custom_taxonomy_top_level") ?>
              </div>
              <div class="link_to_parent_form">
                <div style="margin-top:5px;">
                  <input class="check_link_to_parent" type="checkbox" id="field_populate_with_taxonomy_link_to_parent" onclick="SetFieldProperty('taxonomyLinkToParent', this.checked);" />
                  <label for="field_populate_with_taxonomy_link_to_parent" class="inline">
                    <?php _e("Link to Parent Field", "gravityforms"); ?>
                  </label><?php gform_tooltip("form_field_custom_taxonomy_link_to_parent") ?>
                </div>
                <div class="link_to_parent_container" style="margin-left:50px; display:none;">
                  <?php _e("Parent Field ID", "gravityforms"); ?> 
                  <input class="taxonomy_parent_field_id" type="text" id="field_populate_with_taxonomy_link_to_parent_id" onkeyup="SetFieldProperty('taxonomyLinkToParentId', this.value);" />
                  <?php gform_tooltip("form_field_custom_taxonomy_link_to_parent_id") ?>
                  <br />
                  <?php _e("Unselected Value", "gravityforms"); ?> 
                  <input class="taxonomy_parent_unselected" type="text" id="field_populate_with_taxonomy_link_to_parent_unselected" onkeyup="SetFieldProperty('taxonomyLinkToParentUnselected', this.value);" />
                  <?php gform_tooltip("form_field_custom_taxonomy_link_to_parent_unselected") ?>
                  <br />
                  <?php _e("Default Value", "gravityforms"); ?> 
                  <input class="taxonomy_parent_default" type="text" id="field_populate_with_taxonomy_link_to_parent_default" onkeyup="SetFieldProperty('taxonomyLinkToParentDefault', this.value);" />
                  <?php gform_tooltip("form_field_custom_taxonomy_link_to_parent_default") ?>
                </div>
              </div>
            </div>
        </li>
        <li class="populate_with_post_type_field_setting field_setting" style="display:list-item;">
            <input type="checkbox" class="toggle_setting" id="field_enable_populate_with_post_type" />
            <label for="field_enable_populate_with_post_type" class="inline">
                <?php _e("Populate with a Post Type", "gravityforms"); ?>
            </label>
            <?php gform_tooltip("form_field_populate_post_type") ?><br />
            <div style="margin-left:30px; display:none;">
              <?php _e("Select a Post Type", "gravityforms"); ?>
              <select id="field_populate_post_type" onchange="SetFieldProperty('populatePostType', jQuery(this).val());">
                  <option value="" style="color:#999;"><?php _e("--select--", "gravityforms"); ?></option>
              <?php
              $args=array(
                'public' => true
              );
              $post_types = get_post_types($args, 'objects');
              foreach($post_types as $post_type): ?>
                  <option value="<?php echo $post_type->name; ?>"><?php echo $post_type->label; ?></option>
              <?php endforeach; ?>
              </select>
              <br />
              <?php _e("Exclude posts", "gravityforms"); ?>
              <input type="text" class="post_type_exclusions" id="populate_post_type_exclude" 
                     onkeyup="SetFieldProperty('populatePostTypeExclude', this.value);" />
              <?php gform_tooltip("form_field_populate_post_type_exclude") ?>
              <div style="margin-top:5px;">
                <input type="checkbox" class="check_parent" onclick="SetFieldProperty('setParentPost', this.checked);" id="field_set_parent_post" />
                <label for="field_set_parent_post" class="inline">
                    <?php _e("Try to set parent", "gravityforms"); ?>
                </label>
                <?php gform_tooltip("form_field_set_parent_post") ?>
              </div>
            </div>
        </li>
        <li class="custom_post_type_field_setting field_setting" style="display:list-item;">
            <input type="checkbox" id="field_enable_custom_post_type" />
            <label for="field_enable_custom_post_type" class="inline">
                <?php _e("Save As Post Type", "gravityforms"); ?>
            </label>
            <?php gform_tooltip("form_field_custom_post_type") ?><br />
            <select id="field_populate_custom_post_type" onchange="SetFieldProperty('saveAsCPT', jQuery(this).val());" style="margin-top:10px; display:none;">
                <option value="" style="color:#999;">Select a Post Type</option>
            <?php
            $args=array(
              'public' => true
            );
            $post_types = get_post_types($args, 'objects');
            foreach($post_types as $post_type): ?>
                <option value="<?php echo $post_type->name; ?>"><?php echo $post_type->label; ?></option>
            <?php endforeach; ?>
            </select>
        </li>
        <li class="save_to_taxonomy_field_setting field_setting" style="display:list-item;">
            <input type="checkbox" class="toggle_setting" id="field_enable_save_to_taxonomy" />
            <label for="field_enable_save_to_taxonomy" class="inline">
                <?php _e("Save To Taxonomy", "gravityforms"); ?>
            </label>
            <?php gform_tooltip("form_field_save_to_taxonomy") ?>
            <div style="margin-top:10px; display:none;">
              <select id="field_save_to_taxonomy" onchange="SetFieldProperty('saveToTaxonomy', jQuery(this).val());">
                  <option value="" style="color:#999;">Select a Taxonomy</option>
              <?php
              $args=array(
                'public'   => true
              );
              $taxonomies = get_taxonomies($args, 'objects');
              foreach($taxonomies as $taxonomy):
                  if ($taxonomy->hierarchical === false) {?>
                  <option value="<?php echo $taxonomy->name; ?>"><?php echo $taxonomy->label; ?></option>
                  <?php } ?>
              <?php endforeach; ?>
              </select>
              <input type="checkbox" class="check_tax_enhanced" onclick="SetFieldProperty('taxonomyEnhanced', this.checked);" id="field_tax_enhanced" />
              <label for="field_tax_enhanced" class="inline">
                  <?php _e("Enable enhanced UI", "gravityforms"); ?>
              </label>
              <?php gform_tooltip("form_field_tax_enhanced") ?>
            </div>
        </li>
        <?php
    }

}


?>
