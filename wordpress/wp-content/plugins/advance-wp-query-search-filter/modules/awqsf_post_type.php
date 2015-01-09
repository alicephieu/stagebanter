<h3><?php echo esc_html( __( 'Post Type', 'AWQSFTXT' ) ); ?></h3>
<?php

	  echo '<label>'.__("Choose the post type you want to include in the search","AWQSFTXT").'</label><br>';
			$post_types=get_post_types('','names'); 
			unset($post_types['revision']); unset($post_types['attachment']);unset($post_types['nav_menu_item']);unset($post_types['awqsf']);
			$post_id = isset($_GET['formid']) ? $_GET['formid'] : null;
			
			$oldcpts = get_post_meta($post_id, 'awqsf-cpt', true);
			
			foreach($post_types as $post_type ) {
			    $checked = null;		
			   
			    if(!empty($oldcpts)){
				  foreach ($oldcpts as $checkedtyped)
				   {
					if($checkedtyped == $post_type)  $checked = 'checked="checked"';   
				   }
			     }
			  
			  
			  echo '<div class="wqsf_cpt_div"><input '.$checked.' id="cpt" name="awqsf[cpt][]" type="checkbox" value="'.$post_type.'" />'.$post_type.'</div>';
			
			}
	echo '<div class="clear"></div>';	
?>

