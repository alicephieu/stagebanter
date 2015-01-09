<div class="wrap">
	<div id="icon-edit" class="icon32"></div>
	<h2><?php echo $this->get_prefix('head_title'); _e('Bulk Add Categories', $this->get_setting('textdomain')); ?></h2>
	
	<?php
	// Show messages
	foreach ($messages as $index => $message) {
		switch ($message['type']) {
		case 'error':
			$messageclass = 'error';
			break;
		default:
			$messageclass = 'updated';
			break;
		}
	?>
		<div id="message" class="<?php echo $messageclass; ?> below-h2">
			<p><?php echo $message['message']; ?></p>
		</div>
	<?php
	}
	?>
	
	<form action="" method="post">
		<table class="form-table">
			<tr>
				<th><label for="bac-taxonomy"><?php _e('Taxonomy'); ?></label></th>
				<td>
					<select name="bac-taxonomy" id="bac-taxonomy">
						<?php
						$object_types = get_post_types(array(
								'show_ui' => true
						));
						
						$taxonomies = get_object_taxonomies($object_types, 'objects');
						
						foreach ($taxonomies as $index => $taxonomy) {
						?>
							<option value="<?php echo $taxonomy->name; ?>"><?php echo $taxonomy->label; ?></option>
						<?php
						}
						?>
					</select>
					<img src="<?php echo esc_url(admin_url('images/wpspin_light.gif')); ?>" class="ajax-loading" id="ajax-loading" alt="" />
				</td>
			</tr>
			<tr>
				<th><label for="bac-categories"><?php _e('Categories'); ?></label></th>
				<td>
					<textarea name="bac-categories" id="bac-categories" class="categories-box code" tabindex="1"></textarea>
					<textarea name="bac-categories-slugs" id="bac-categories-slugs" class="categories-slugs-box code" tabindex="1"></textarea>
					<div class="description-spacing"></div>
					<span class="description">
						<?php
						_e("<p>Left textbox: Categories<br/>Right textbox: Slugs for the categories in the left textbox (leave (lines) empty to generate automatically)</p><h3>Categories</h3><p>Enter the categories you want to add in the left textbox, separating different categories by newlines. You can assign categories to parent categories by entering the full category path, separating different category names by slashes (<code>/</code>).</p><h3>Slugs</h3><p>Slugs will be automatically generated if they are not manually set, but it is also possible to enter specific slugs for each category. You can do this by putting the slugs in the right textbox on the lines of that textbox corresponding with the lines in the left textbox (for the category paths).</p><h3>Example</h3><p>You can view an example by <a href='#' class='show-categories-example'>clicking here</a>.<br/>The example text gets prepended to the current textbox content, so you will not lose your data.</p>", $this->get_setting('textdomain'));
						?>
					</span>
				</td>
			</tr>
			<tr>
				<th><label for="bac-top-parent-category"><?php _e('Top parent category', $this->get_setting('textdomain')); ?></label></th>
				<td id="top-parent-category">
					<?php
					wp_dropdown_categories(array(
						'show_option_none' => __('&lt; no parent category &gt;'),
						'hide_empty' => false,
						'tab_index' => 2,
						'hierarchical' => true,
						'selected' => -1
					));
					?>
				</td>
			</tr>
			<tr>
				<th><?php _e('Parent categories', $this->get_setting('textdomain')); ?></th>
				<td><label for="bac-create-inexistent-categories"><input type="checkbox" name="bac-create-inexistent-categories" id="bac-create-inexistent-categories" tabindex="3" checked="checked" /> <?php _e('Create inexistent parent categories'); ?></label></td>
			</tr>
		</table>
		<p class="submit">
			<input type="submit" name="bac-submit" class="button-primary" value="<?php _e('Add'); ?>" />
		</p>
	</form>
</div>