<?php
/* 
Template Name: Cities
*/

//custom hooks below here...

remove_action('genesis_loop', 'genesis_do_loop');
/**
 * 
 * 
 * 
*/
add_action('genesis_loop', 'sb_custom_cities_archive_loop');
function sb_custom_cities_archive_loop() {
		echo '<h3> Alberta </h3>';
		$args = array( 
			'taxonomy' => 'AB_cities',
			'hide_empty' => 0,
			);

		$terms = get_terms('AB_cities', $args);

		$count = count($terms); $i=0;
		if ($count > 0) {
		    $term_list = '<p class="my_term-archive">';
		    foreach ($terms as $term) {
		        $i++;
		    	$term_list .= '<a href="/ABcity/' . $term->slug . '" title="' . sprintf(__('View all post filed under %s', 'my_localization_domain'), $term->name) . '">' . $term->name . '</a>';
		    	if ($count != $i) $term_list .= ' &middot; '; else $term_list .= '</p>';
		    }
		    echo $term_list;
		}

		echo '<h3> British Columbia </h3>';
		$args = array( 
			'taxonomy' => 'BC_cities', 
			'hide_empty' => 0,
			);

		$terms = get_terms('BC_cities', $args);

		$count = count($terms); $i=0;
		if ($count > 0) {
		    $term_list = '<p class="my_term-archive">';
		    foreach ($terms as $term) {
		        $i++;
		    	$term_list .= '<a href="/BCcity/' . $term->slug . '" title="' . sprintf(__('View all post filed under %s', 'my_localization_domain'), $term->name) . '">' . $term->name . '</a>';
		    	if ($count != $i) $term_list .= ' &middot; '; else $term_list .= '</p>';
		    }
		    echo $term_list;
		}

		echo '<h3> Manitoba </h3>';
		$args = array( 'taxonomy' => 'MB_cities' );

		$terms = get_terms('MB_cities', $args);

		$count = count($terms); $i=0;
		if ($count > 0) {
		    $term_list = '<p class="my_term-archive">';
		    foreach ($terms as $term) {
		        $i++;
		    	$term_list .= '<a href="/MBcity/' . $term->slug . '" title="' . sprintf(__('View all post filed under %s', 'my_localization_domain'), $term->name) . '">' . $term->name . '</a>';
		    	if ($count != $i) $term_list .= ' &middot; '; else $term_list .= '</p>';
		    }
		    echo $term_list;
		}
}

genesis(); // <- everything important: make sure to include this. 