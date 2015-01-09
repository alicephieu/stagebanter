<h3><?php echo esc_html( __( 'Custom Meta Field', 'AWQSFTXT' ) ); ?></h3>
<?php
	$bool = get_post_meta($post_id, 'awqsf-relbool', true);
	$items = array("AND", "OR");
	echo '<span>'.__("Boolean relationship between the meta queries", "AWQSFTXT").'</span><br>';
	foreach($items as $item) {
		
		$checked = !empty($bool[0]['cmf']) && ($bool[0]['cmf']==$item) ? 'checked="checked"' : '';
		echo '<label><input id = "cmfrel" '.$checked.' value="'.$item.'" name="awqsf[rel][0][cmf]" type="radio" />'.$item.'</label>';
	}	
	
		echo '<ul><i><li class="desc">'.__("AND - Must meet all meta field search.","AWQSFTXT").'</li>';
		echo '<li class="desc">'.__("OR - Either one of the meta field search is meet.","AWQSFTXT").'</li></i></ul>';
				
?>

	<div class="formbutton">
	<input alt="#TB_inline?height=580&amp;width=600&amp;inlineId=add_cmf_form" title="Add Custom Meta Field" class="thickbox button-secondary" type="button" value="<?php _e("Add Custom Meta",'AWQSFTXT') ;?>" />
	</div>  
   	<table id="cmf_table" class="widefat">
	
			<thead>
				<tr>
				<th><?php _e('Meta key','AWQSFTXT'); ?></th>
				<th><?php _e('Label','AWQSFTXT'); ?></th>
				<th><?php _e('"Search All" Text','AWQSFTXT'); ?></th>
				<th><?php _e('Compare','AWQSFTXT'); ?></th>
				<th><?php _e('Options','AWQSFTXT'); ?></th>
				<th><?php _e('Display Type','AWQSFTXT'); ?></th>
				<th><?php _e('Remove?','AWQSFTXT'); ?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
				<th><?php _e('Meta key','AWQSFTXT'); ?></th>
				<th><?php _e('Label','AWQSFTXT'); ?></th>
				<th><?php _e('"Search All" Text','AWQSFTXT'); ?></th>
				<th><?php _e('Compare','AWQSFTXT'); ?></th>
				<th><?php _e('Options','AWQSFTXT'); ?></th>
				<th><?php _e('Display Type','AWQSFTXT'); ?></th>
				<th><?php _e('Remove?','AWQSFTXT'); ?></th>
				</tr>
			</tfoot>	
			
			<tbody id="sortable2" class="cmfbody">
			 <?php 	 $html = '<br>';
			$cmf = get_post_meta($post_id, 'awqsf-cmf', true);
			
			 $c =0; 
			 $campares = array( '1' => '=', '2' =>'!=', '3' =>'>', '4' =>'>=', '5' =>'<', '6' =>'<=', '7' =>'LIKE', '8' =>'NOT LIKE', '9' =>'IN', '10' =>'NOT IN', '11' =>'BETWEEN', '12' =>'NOT BETWEEN','13' => 'NOT EXISTS');	
			 if(!empty($cmf)){
			  	foreach($cmf as $k => $v){
					$html .= '<tr>';
					$html .=  '<td><input type="hidden" id="cmfcounter" name="cmfcounter" value="'.$c.'"/>';//counter
					//for custom meta key
					$keys = $this->get_all_keys();
					$html .= '<select id="cmfkey" name="awqsf[cmf]['.$c.'][metakey]">';
						foreach($keys as $key){
								$selected = ($v['metakey']==$key) ? 'selected="selected"' : '';	
								$html .= '<option value="'.$key.'" '.$selected.'>'.$key.'</option>';		
							}	
					$html .= '</select><br></td>';
					//for Label
					$html .=  '<td>';
					$html .= '<input type="text" id="cmflabel" name="awqsf[cmf]['.$c.'][label]" value="'.sanitize_text_field($v['label']).'"/>';
					$html .= '<br></td>';
					//search all text
					$html .=  '<td>';
					$html .= '<input type="text" id="cmfalltext" name="awqsf[cmf]['.$c.'][all]" value="'.sanitize_text_field($v['all']).'"/>';
					$html .= '<br></td>';
					//for compare
					$html .=  '<td>';
					$html .= '<select id="cmfcom" name="awqsf[cmf]['.$c.'][compare]">';
						foreach ($campares  as $ckey => $cvalue ) {
						$selected = ($v['compare']==$ckey) ? 'selected="selected"' : '';	
					$html .= '<option value="'.$ckey.'" '.$selected.'>'.$cvalue.'</option>';}
					$html .= '</select><br></td>';
					
					//for options
					$html .=  '<td>';
					
					$html .= '<textarea id="cmflabel" name="awqsf[cmf]['.$c.'][opt]" >'.esc_html($v['opt']).'</textarea>';
					$html .= '</td>';

					//display type

					//dispay type
					$html .= '<td>';
					$radio = $dropdown = $checkbox ='';
					
					if($v['type']== "dropdown"){ $dropdown = 'checked="checked"';}
					if($v['type']== "radio"){ $radio = 'checked="checked"';}	
					if($v['type']== "checkbox"){ $checkbox = 'checked="checked"';}
					$html .= '<label><input type="radio" id="taxtype" name="awqsf[cmf]['.$c.'][type]" value="dropdown" '.$dropdown.' />'.__("Drop Down","AWQSFTXT").'</label>'; 
					$html .= '<label><input type="radio" id="taxtype" name="awqsf[cmf]['.$c.'][type]" value="radio" '.$radio.'/>'.__("Radio Button","AWQSFTXT").'</label>';
					$html .= '<label><input type="radio" id="taxtype" name="awqsf[cmf]['.$c.'][type]" value="checkbox" '.$checkbox.'/>'.__("Checkbox","AWQSFTXT").'</label>';
	   				$html .= '<br></td>'; 
					
				    $html .= '<td><span class="remove_row button-secondary">'.__("Remove","AWQSFTXT").'</span></td></tr>';
				  $c++; 
				}
				
				
			 }
			 	echo $html; 
			 ?>
			</tbody>
	
	</table>
<span class="drag"><?php _e('*Drag and Drop to reorder your table row. The table row order indicates the order of the search form fields in the frontend. ','AWQSFTXT') ;?></span>	
