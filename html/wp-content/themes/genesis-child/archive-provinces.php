<?php
/* 
Template Name: Provinces/Territories
*/

//custom hooks below here...

remove_action('genesis_loop', 'genesis_do_loop');
/**
 * 
 * 
 * 
*/
add_action('genesis_loop', 'sb_custom_provinces_archive_loop');
function sb_custom_provinces_archive_loop() {
		echo '<h3> Provinces/Territories </h3>';
		$args = array( 
			'taxonomy' => 'provinces/territories',
			'hide_empty' => 0,
			);

		$terms = get_terms('provinces/territories', $args);

		$count = count($terms); $i=0;
		if ($count > 0) {
		    $term_list = '<p class="my_term-archive">';
		    foreach ($terms as $term) {
		        $i++;
		    	$term_list .= '<a href="/province/' . $term->slug . '" title="' . sprintf(__('View all post filed under %s', 'my_localization_domain'), $term->name) . '">' . $term->name . '</a>';
		    	if ($count != $i) $term_list .= ' &middot; '; else $term_list .= '</p>';
		    }
		    echo $term_list;
		}

		
}

genesis(); // <- everything important: make sure to include this. 