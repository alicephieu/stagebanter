<?php
	$misc = get_post_meta($post_id, 'awqsf-relbool', true);
	
	$string = !empty($misc[0]['strchk']) && (sanitize_text_field($misc[0]['strchk']) == '1') ? 'checked="checked"' : null;
	$slabel = !empty($misc[0]['strlabel']) ? sanitize_text_field($misc[0]['strlabel']) : __("Search by Keyword","AWQSFTXT");
	$meta = !empty($misc[0]['smetekey']) ? sanitize_text_field($misc[0]['smetekey']) : null;
	$word =  !empty($misc[0]['otype']) && ($misc[0]['otype'] == 'meta_value' )  ? 'checked="checked"' : null;
	$number =  !empty($misc[0]['otype']) && ($misc[0]['otype'] == 'meta_value_num' )  ? 'checked="checked"' : null;
	$desc = !empty($misc[0]['sorder']) && ($misc[0]['sorder'] == 'DESC' )  ? 'checked="checked"' : null; 
	$asc = !empty($misc[0]['sorder']) && ($misc[0]['sorder'] == 'ASC' )  ? 'checked="checked"' : null; 
	$button = !empty($misc[0]['button']) ? sanitize_text_field($misc[0]['button']) : 'Search'; 
	$defualtresult = get_option('posts_per_page');
	$result = !empty($misc[0]['resultc']) ? sanitize_text_field($misc[0]['resultc']) : $defualtresult; 
	
	$html = "";
	$html .= '<h3>'.__("Add String Search?","AWQSFTXT").'</h3>';
    $html.= '<p><input '.$string.'  name="awqsf[rel][0][strchk]" type="checkbox" value="1" />'.__("Enabling string search","AWQSFTXT").'<br>';
    $html.= '<span class="desciption">'.__("This will add string search in the form. Note that when user using this to search, the taxonomy and custom meta filter that defined above will not be used. However, the search will still go through the post type defined above.","AWQSFTXT").'</span><br>';
    $html.= '<p><label>'.__("Label for string search.","AWQSFTXT").':</label><br>';
    $html.= '<input type="text"  name="awqsf[rel][0][strlabel]" id="stringlabel" value="'.$slabel.'" /><br>';
   
   


    $html .= '<h3>'.__("Result Page Setting","AWQSFTXT").'</h3>';
    $html.= '<p><label>'.__("Sorting Meta Key.","AWQSFTXT").':</label><br>';
	$keys = $this->get_all_keys();
    $html .= '<select name="awqsf[rel][0][smetekey]"><option value=""></option>';
	foreach($keys as $key){
		$selected = ($meta==$key) ? 'selected="selected"' : '';	
    $html .= '<option value="'.$key.'" '.$selected.'>'.$key.'</option>';		
	}		

    $html .=  '</select><br>';	
    $html.= '<span class="desciption">'.__("Insert the meta key that will be used for the result sorting. Leave empty will using the default 'date' value for sorting.","AWQSFTXT").'</span></p>';
    
    $html.= '<p><label>'.__("Meta Value Type","AWQSFTXT").':</label><br>';
    $html.= '<label><input '.$word.' type="radio" id="taxhide" name="awqsf[rel][0][otype]" value="meta_value"/>'.__("Words","AWQSFTXT").'</label>';
	$html.= '<label><input '.$number.' type="radio" id="taxhide" name="awqsf[rel][0][otype]" value="meta_value_num"/>'.__("Numeric", "AWQSFTXT").'</label>';
    $html.= '<br><span class="desciption">'.__("What is the meta value type of the sorting meta key? eg. sorting meta key = 'price', then the meta value type should be numeric. Leave it blank if your sorting meta key is empty.","AWQSFTXT").'</span></p>';
    
    $html.= '<p><label>'.__("Sorting Order","AWQSFTXT").':</label><br>';
    $html.= '<label><input '.$desc.' type="radio" id="taxhide" name="awqsf[rel][0][sorder]" value="DESC"/>'.__("Descending","AWQSFTXT").'</label>';
	$html.= '<label><input '.$asc.' type="radio" id="taxhide" name="awqsf[rel][0][sorder]" value="ASC"/>'.__("Ascending","AWQSFTXT").'</label><br>';
    $html.= '<span class="desciption">'.__("The search result sorting order. Default is descending","AWQSFTXT").'</span></p>';
    
    $html.= '<p><label>'.__("Results per Page","AWQSFTXT").':</label>';
    $html.= '<input type="text" id="numberpost" name="awqsf[rel][0][resultc]" value="'.$result.'" size="2"/><br>';
    $html.= '<span class="desciption">'.__("How many posts shows at each result page?","AWQSFTXT").'</span></p>';

    $html.= '<p><label>'.__("Search Button Text","AWQSFTXT").':</label>';
    $html.= '<input type="text" id="numberpost" name="awqsf[rel][0][button]" value="'.$button.'" /><br>';
    $html.= '<span class="desciption">'.__("The text of the submit button?","AWQSFTXT").'</span></p>';		
    
    echo $html;

?>
