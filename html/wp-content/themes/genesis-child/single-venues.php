
<?php 
//register post type
//
get_template_part( 'content', get_post_type() ); 

remove_action('genesis_before_post_content', 'genesis_post_info' );	
add_action( 'genesis_before_post_content', 'venue_post_meta' );
remove_action( 'genesis_post_content', 'genesis_do_post_content');
add_action( 'genesis_post_content', 'venue_booking_info' );

function venue_post_meta() { ?>
			
			<?php
			//ratings function
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
					echo "<em>The postal code is missing, add it using the <a href='#editform'>form</a> at the bottom of this post.</em>";;
				} ?><br>

			<?php $phone = genesis_get_custom_field('Phone Number');
			if (!empty($phone)) {echo $phone;}
				else {
					echo "<em>The phone number is missing, add it using the <a href='#editform'>form</a> at the bottom of this post.</em>";
				} ?> <br>

			<?php $email = genesis_get_custom_field('Email');
			if (!empty($email)) {
				echo '<a href="mailto:' . $email . '">';
				echo $email;
				echo "</a>";
			}	else {
					echo "<em>The email is missing, add it using the <a href='#editform'>form</a> at the bottom of this post.</em>";
				} ?> <br>

			<?php $website = genesis_get_custom_field('Website');
			if (!empty($website)) {
				echo '<a href="' . $website . '" target="_blank">';
				echo $website;
				echo "</a>";
			}else {
					echo "<em>The website is missing, add it using the <a href='#editform'>form</a> at the bottom of this post.</em>";
				} ?> <br>
			
	</div>
	<div id="venuedetails">
			<?php if (get_the_term_list( $post->ID, 'genres' ) != null ) { ?>
				<?php echo get_the_term_list( $post->ID, 'genres', 'Genres: ', ', ', '' ); ?>
			<?php }else {
					echo "<em>There are no genres associated with this venue, add them using the <a href='#editform'>form</a> at the bottom of this post.</em>";
				} ?> <br>

			<?php if (get_the_term_list( $post->ID, 'capacity' ) != null ) { ?>
				<?php echo get_the_term_list( $post->ID, 'capacity', 'Capacity: ', ', ', '' ); ?>
			<?php }else {
					echo "<em>The capacity is missing, add it using the <a href='#editform'>form</a> at the bottom of this post.</em>";
				}
			 ?> <br>

			<?php if (get_the_term_list( $post->ID, 'live_music' ) != null ) { ?>
				<?php echo get_the_term_list( $post->ID, 'live_music', 'Live Music On: ', ', ', '' ); ?>
			<?php }else {
					echo "<em>The days of the week with live music are missing, add them using the <a href='#editform'>form</a> at the bottom of this post.</em>";
				} ?> <!-- <br>
					<p><strong>Add Photos</strong></p> -->
				<!--venue photos funcion -->
			<?php ////$photos = genesis_get_custom_field('Photos');?>
			<?php ////$photos_output = do_shortcode($photos);?>
			<?php ////$photos_upload_shortcode = genesis_get_custom_field('photos_upload');?>
			<?php ////$photos_uploader = do_shortcode($photos_upload_shortcode);?>
				<?////php if (!empty($photos_output)) {
				///echo $photos_output;
				///} else {
				///	echo "<em>There are no photos associated with this post.  Add some using the uploader below</em>";;
				///} ?><!-- <br> -->
				
				<?////php {
				////echo $photos_uploader;
				////} 
          		////?>
          		<br>


	</div><br>
	
	

	


 <?php } 
//custom venue booking info (content)
add_action('genesis_before_post_content', 'sb_before_post_content');

function sb_before_post_content(){
	echo '<h4 class="booking-header">Booking Info</h4>';
}

function venue_booking_info() { ?>
	<?php 
		$booking_info = genesis_do_post_content(); 
		if (!empty($booking_info))  {
				echo '<pre> ' . $booking_info . '</pre>';
		} 
	}?>


<?php
//edit venue form
add_action('genesis_after_post_content', 'sb_add_edit_venue_form');
function sb_add_edit_venue_form() {
	echo "<br><h4>Edit Venue<a name='editform'></a> </h4>";
	do_action('gform_update_post/edit_link', array(
    'post_id' => $post->ID,
    'url'     => home_url('/add-a-venue/'),
	) );
	echo '<div id="map">';
	global $mappress; echo $mappress->shortcode_map();
	echo '</div><br>';
}


?>



<?php
//remove "filed under:"

 remove_action('genesis_after_post_content', 'genesis_post_meta');


genesis();
?>
