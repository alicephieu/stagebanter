<h3><?php echo esc_html( __( 'Taxonomy', 'AWQSFTXT' ) ); ?></h3>
<?php 
$post_id = $_GET['formid'] ? $_GET['formid'] : null;
$items = array("AND", "OR");
	echo '<span>'.__("Boolean relationship between the taxonomy queries", "AWQSFTXT").'</span><br>';
	foreach($items as $item) {
		$bool = get_post_meta($post_id, 'awqsf-relbool', true);
		
		$checked = !empty($bool[0]['tax']) && ($bool[0]['tax']==$item) ? 'checked="checked"' : '';
		echo '<label><input id = "taxrel" '.$checked.' value="'.$item.'" name="awqsf[rel][0][tax]" type="radio" />'.$item.'</label>';
	}	
	
	echo '<ul><i><li class="desc">'.__("AND - Must meet all taxonomy search.","AWQSFTXT").'</li>';
	echo '<li class="desc">'.__("OR - Either one of the taxonomy search is meet.","AWQSFTXT").'</li></i></ul>';
	
?>
<div class="formbutton"><input alt="#TB_inline?height=500&amp;width=450&amp;inlineId=add_taxo_form" title="Add Taxonomy" class="thickbox button-secondary" type="button" value="<?php _e("Add Taxonomy",'AWQSFTXT') ;?>" /></div>  
	
	<table id="taxo_table" class="widefat">
    			<thead>
				<tr>
				<th><?php _e('Taxonomy','AWQSFTXT'); ?></th>
				<th><?php _e('Label','AWQSFTXT'); ?></th>
				<th><?php _e('"Search All" Text','AWQSFTXT'); ?></th>
				<th><?php _e('Hide Empty?','AWQSFTXT'); ?></th>
				<th><?php _e('Exculde ID','AWQSFTXT'); ?></th>
				<th><?php _e('Display Type','AWQSFTXT'); ?></th>
				<th><?php _e('Remove?','AWQSFTXT'); ?></th>
				</tr>
			</thead>
		 
			<tfoot>
				<tr>
				<th><?php _e('Taxonomy','AWQSFTXT'); ?></th>
				<th><?php _e('Label','AWQSFTXT'); ?></th>
				<th><?php _e('"Search All" Text','AWQSFTXT'); ?></th>
				<th><?php _e('Hide Empty?','AWQSFTXT'); ?></th>
				<th><?php _e('Exculde ID','AWQSFTXT'); ?></th>
				<th><?php _e('Display Type','AWQSFTXT'); ?></th>
				<th><?php _e('Remove?','AWQSFTXT'); ?></th>
				</tr>
			</tfoot>
			
				
	<tbody id="sortable"  class="taxbody">
	<?php $html = '<br>';
	$taxo = get_post_meta($post_id, 'awqsf-taxo', true);
	if(!empty($taxo)){
		$c =0; 
		$args=array('public'   => true, '_builtin' => false); 
		$output = 'names'; // or objects
		$operator = 'and'; // 'and' or 'or'
		$taxonomies=get_taxonomies($args,$output,$operator); 
		foreach($taxo as $k => $v){
				$html .= '<tr>';
				$html .=  '<td><input type="hidden" id="taxcounter" name="taxcounter" value="'.$c.'"/>';
				//for display taxonomy
			
				$html .= '<select id="taxo" name="awqsf[taxo]['.$c.'][taxname]">';
				$catselect = ($v['taxname']== 'category') ? 'selected="selected"' : '';
				$html .= '<option value="category" '.$catselect.'>'.__("category","AWQSFTXT").'</option>';
					foreach ($taxonomies  as $taxonomy ) {
				$selected = ($v['taxname']==$taxonomy) ? 'selected="selected"' : '';		
				$html .= '<option value="'.$taxonomy.'" '.$selected.'>'.$taxonomy.'</option>';
						}
				$html .= '</select><br></td>';
				//for label
				$html .=  '<td>';
				$html .= '<input type="text" id="taxlabel" name="awqsf[taxo]['.$c.'][taxlabel]" value="'.sanitize_text_field($v['taxlabel']).'"/>';
				$html .= '<br></td>';
				//search all text
				$html .=  '<td>';
				$html .= '<input type="text" id="taxall" name="awqsf[taxo]['.$c.'][taxall]" value="'.sanitize_text_field($v['taxall']).'"/>';
				$html .= '<br></td>';
				//hide empty
				$html .= '<td>';
				
				$check1="";
				$check0="";
				if($v['hide'] == 1){$check1 = 'checked="checked"'; };
				if($v['hide'] == 0){$check0 = 'checked="checked"'; };
				$html .= '<label><input '.$check1.' type="radio" id="taxhide" name="awqsf[taxo]['.$c.'][hide]" value="1"/>Yes</label>';
				$html .= '<label><input '.$check0.' type="radio" id="taxhide" name="awqsf[taxo]['.$c.'][hide]" value="0"/>No</label>'; 
				$html .= '<br></td>';
				//exlude id
				$html .= '<td><input type="text" id="taxexculde" name="awqsf[taxo]['.$c.'][exc]" value="'.sanitize_text_field($v['exc']).'"/></td>';

				//dispay type
				$html .= '<td>';
				$checkbox = $radio = $dropdown = '';
				if($v['type']== "checkbox"){ $checkbox = 'checked="checked"';}
				if($v['type']== "dropdown"){ $dropdown = 'checked="checked"';}
				if($v['type']== "radio"){ $radio = 'checked="checked"';}	
				$html .= '<label><input type="radio" id="taxtype" name="awqsf[taxo]['.$c.'][type]" value="checkbox" '.$checkbox.'/>Check Box</label>';
				$html .= '<label><input type="radio" id="taxtype" name="awqsf[taxo]['.$c.'][type]" value="dropdown" '.$dropdown.' />Drop Down</label>'; 
				$html .= '<label><input type="radio" id="taxtype" name="awqsf[taxo]['.$c.'][type]" value="radio" '.$radio.'/>Radio Button</label>';
	   			$html .= '<br></td>';
				//action
				$html .= '<td><span class="remove_row button-secondary">'.__("Remove","AWQSFTXT").'</span><br></td>';
				$html .= '</tr>';
				$c++;
					
		}
	   			
    }
	echo $html; 
	?>
	
	</tbody>
	</table>
<span class="drag"><?php _e('*Drag and Drop to reorder your table row. The table row order indicates the order of the search form fields in the frontend. ','AWQSFTXT') ;?></span>	
