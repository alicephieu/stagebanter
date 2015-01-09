
<?php
/* 
Template Name: Venues
*/



remove_action('genesis_loop', 'genesis_do_loop');
remove_action( 'genesis_post_content', 'genesis_do_post_content' );
remove_action('genesis_before_post_content', 'genesis_post_info' );	
remove_action( 'genesis_after_post_content', 'genesis_post_meta' );
/**
 * Example function that replaces the default loop with a custom loop querying 'PostType' CPT.
 * Remove this function (along with the remove action hook) to show default page content.
 * Or feel free to update the $args to make it work for you.
*/
add_action( 'genesis_before_post', 'sb_venue_post_snippets' );

function sb_venue_post_snippets() { ?>

<div id="venuesnippets">			
			<?php
			}

add_action('genesis_loop', 'sb_custom_venue_archive_loop');
function sb_custom_venue_archive_loop() {

    global $paged;

		$args = array(
			'posts_per_page' => 50,
			'post_type' => 'venues',
			'paged' => $paged,
	);	// Accepts WP_Query args (http://codex.wordpress.org/Class_Reference/WP_Query)
    genesis_custom_loop( $args );

}

add_action( 'genesis_before_post_content', 'venue_post_meta' );

function venue_post_meta() { ?>
			
			<?php
			//snippets meta info
				if(function_exists('the_ratings')) { the_ratings(); }
			
			?>

	<div id="venuecontact">
			<?php if (get_the_term_list( $post->ID, array('AB_cities','BC_cities','MB_cities','NB_cities','NL_cities','NS_cities','NT_cities','NU_cities','ON_cities','PE_cities','QC_cities','SK_cities','YT_cities') , '' , ',' , '' ) != null ) { ?>
				<?php echo get_the_term_list( $post->ID, array('AB_cities','BC_cities','MB_cities','NB_cities','NL_cities','NS_cities','NT_cities','NU_cities','ON_cities','PE_cities','QC_cities','SK_cities','YT_cities') , '' , ',' , ',' ); ?>
			<?php } ?>

			<?php if (get_the_term_list( $post->ID, 'provinces/territories' ) != null ) { ?>
				<?php echo get_the_term_list( $post->ID, 'provinces/territories', '', ', ', '' ); ?>
			<?php } ?> <br>

			<?php $address = genesis_get_custom_field('Address');
			if (!empty($address)) {echo $address;} ?><br>
			
			<?php $postal_code = genesis_get_custom_field('Postal Code');?>
				<?php if (!empty($postal_code)) {
				echo $postal_code;
				} else {
					echo "<em>The postal code is missing.</em>";;
				} ?><br>

			<?php $phone = genesis_get_custom_field('Phone Number');
			if (!empty($phone)) {echo $phone;}
				else {
					echo "<em>The phone number is missing.</em>";
				} ?> <br>

			<?php $email = genesis_get_custom_field('Email');
			if (!empty($email)) {
				echo '<a href="mailto:' . $email . '">';
				echo $email;
				echo "</a>";
			}	else {
					echo "<em>The email is missing.</em>";
				} ?> <br>

			<?php $website = genesis_get_custom_field('Website');
			if (!empty($website)) {
				echo '<a href="' . $website . '">';
				echo $website;
				echo "</a>";
			}else {
					echo "<em>The website is missing.</em>";
				} ?> <br>
	</div>
	<div id="venuedetails">
			<?php if (get_the_term_list( $post->ID, 'genres' ) != null ) { ?>
				<?php echo get_the_term_list( $post->ID, 'genres', 'Genres: ', ', ', '' ); ?>
			<?php }else {
					echo "<em>There are no genres associated with this venue.</em>";
				} ?> <br>

			<?php if (get_the_term_list( $post->ID, 'capacity' ) != null ) { ?>
				<?php echo get_the_term_list( $post->ID, 'capacity', 'Capacity: ', ', ', '' ); ?>
			<?php }else {
					echo "<em>The capacity is missing.</em>";
				}
			 ?> <br>

			<?php if (get_the_term_list( $post->ID, 'live_music' ) != null ) { ?>
				<?php echo get_the_term_list( $post->ID, 'live_music', 'Live Music On: ', ', ', '' ); ?>
			<?php }else {
					echo "<em>The days of the week with live music are missing.</em>";
				} ?> <br><br>
			<a href="<?php echo get_permalink(); ?>">more info...</a>
	</div>


 <?php }  ////<!-- close venue post meta function -->
add_action( 'genesis_after_post', 'sb_venue_post_snippets_end' );

function sb_venue_post_snippets_end() { ?>

</div> <br>  <!-- end snippets div -->		
			<?php
			}


genesis(); // <- everything important: make sure to include this. 