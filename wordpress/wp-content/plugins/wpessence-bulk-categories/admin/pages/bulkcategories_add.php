<?php
// Messages to display to the user
$messages = array();

// Deprecation notice
$bulkpressurl = admin_url('plugin-install.php?tab=search&s=bulkpress');

$messages[] = array(
	'type' => 'error',
	'message' => sprintf(__('DEPRECATION NOTICE: As of the <strong>14th of march, 2013</strong>, this plugin is <strong>no longer supported</strong> as it has been superseded by the <a href="%s" target="_blank">BulkPress plugin</a>. The BulkPress plugin does everything the WPEssence Bulk Categories plugin does but adds enhanced functionality to adding terms and allows you to add other types of content, such as posts.', $this->get_setting('textdomain')), $bulkpressurl)
);

$messages[] = array(
	'type' => 'error',
	'message' => sprintf(__('You are strongly encouraged to remove this plugin and install the <a href="%s" target="_blank">BulkPress plugin</a> instead.', $this->get_setting('textdomain')), $bulkpressurl)
);

// Handle form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	// Form prefix
	$formprefix = 'bac-';
	
	// Array holding the form values, keys are without the form prefix
	$formvalues = array();
	
	// Fill the form values array with the post data
	foreach ($_POST as $index => $postvalue) {
		if (strpos($index, $formprefix) === 0) {
			$formvalues[substr($index, 4)] = stripslashes($postvalue);
		}
	}
	
	$formvalues['create-inexistent-categories'] = $formvalues['create-inexistent-categories'] ? true : false;
	
	// All category paths from the input
	$categorypaths = explode("\n", $formvalues['categories']);
	$categoryslugs = explode("\n", $formvalues['categories-slugs']);
	
	// Get the categories from each category pathe
	foreach ($categorypaths as $index => $categorypath) {
		$categorypath = trim($categorypath, ' /');
		
		if (!$categorypath) {
			continue;
		}
		
		$parts = array();
		$path = $categorypath;
		$inquotes = false;
		
		$i = 0;
		
		$newcategories = array();
		
		while ($slashpos !== false or $quotepos !== false or !$i) {
			$slashpos = strpos($path, '/');
			$quotepos = strpos($path, '"');
			
			if ($quotepos !== false and ($inquotes or $quotepos <= $slashpos)) {
				if ($inquotes) {
					$part = substr($path, 0, $quotepos);
					
					$path = substr($path, $quotepos + 1);
					$inquotes = false;
				}
				else {
					$path = substr($path, 1);
					$inquotes = true;
				}
			}
			else if ($slashpos !== false) {
				$part = substr($path, 0, $slashpos);
				
				$path = substr($path, $slashpos + 1);
			}
			else {
				$part = $path;
			}
			
			$part = trim($part);
			
			if ($part) {
				$parts[] = $part;
			}
			
			$part = '';
			
			$i++;
		}
		
		$fullcategorypaths[] = $parts;
	}
	
	$taxonomy = get_taxonomy($formvalues['taxonomy']);
	
	$categories = get_categories(array(
		'hide_empty' => false,
		'taxonomy' => $formvalues['taxonomy'],
		'type' => $taxonomy->object_type[0]
	));
	
	foreach ($fullcategorypaths as $index => $categorypath) {
		$parent = max(0, intval($_POST['cat']));
		
		foreach ($categorypath as $index2 => $part) {
			$category_found = false;
			
			foreach ($categories as $index3 => $category) {
				if ($part == $category->name and $category->category_parent == $parent) {
					$parent = $category->cat_ID;
					$category_found = true;
					break;
				}
			}
			
			if (!$category_found) {
				if ($index2 < count($categorypath) - 1 and !$formvalues['create-inexistent-categories']) {
					break;
				}
				
				$category_settings = array(
					'name' => $part,
					'parent' => $parent
				);
				
				if ($categoryslugs[$index] and $index2 == count($categorypath) - 1) {
					$category_settings['slug'] = $categoryslugs[$index];
				}
				
				$newcat = wp_insert_term($category_settings['name'], $formvalues['taxonomy'], $category_settings);
				
				if (is_wp_error($newcat)) {
					continue;
				}
				
				$categories[] = (object) array(
					'cat_ID' => $newcat['term_id'],
					'category_parent' => $parent,
					'name' => $part
				);
				
				$parent = $newcat['term_id'];
			}
		}
	}
	
	// Fix the category hierarchy
	delete_option($formvalues['taxonomy'] . '_children');
	
	$messages[] = array(
		'type' => 'success',
		'message' => __('The categories have been created successfully', $this->get_setting('textdomain'))
	);
}

// Page output
include WPEBC_ABSPATH . '/admin/output/bulkcategories_add.php';
?>