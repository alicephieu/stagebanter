<?php
/* 
Template Name: Search Page
*/

//custom hooks below here...

remove_action('genesis_loop', 'genesis_do_loop');
/**
 * 
 * 
 * 
*/
add_action('genesis_loop', 'sb_custom_search');
function sb_custom_search() { ?>
	<?php $args = array(
	'show_option_all'    => '',
	'show_option_none'   => '',
	'orderby'            => 'ID', 
	'order'              => 'ASC',
	'show_count'         => 0,
	'hide_empty'         => 0, 
	'child_of'           => 0,
	'exclude'            => '',
	'echo'               => 1,
	'selected'           => 0,
	'hierarchical'       => 0, 
	'name'               => 'cat',
	'id'                 => '',
	'class'              => 'postform',
	'depth'              => 0,
	'tab_index'          => 0,
	'taxonomy'           => 'AB_cities',
	'hide_if_empty'      => false
); ?>

	<li id="categories">
		
		<form action="" method="get">
		<div>
		<?php wp_dropdown_categories($args); ?>
		<input type="submit" name="submit" value="view" />
		</div>
		</form>
	</li>


<?php
}

 echo do_shortcode("[mam_list_term_posts tax_slug='QC_cities' term_slug='montreal' post_type='venues']");


genesis(); // <- everything important: make sure to include this. 