<?php 
		$type = $_POST['type'];
		$metakey = isset($_POST['key']) ? sanitize_text_field($_POST['key']) : '';
		$label = isset($_POST['metalabel']) ? sanitize_text_field($_POST['metalabel']) : '';
		$all = isset($_POST['all']) ? sanitize_text_field($_POST['all']) : '';
		$com = isset($_POST['compare']) ? sanitize_text_field($_POST['compare']) : '';
		$check = isset($_POST['check']) ? sanitize_text_field($_POST['check']) : '';
		$option =isset($_POST['opt']) ? sanitize_text_field($_POST['opt']) : '';
		$c = isset($_POST['cmfcounter']) ? sanitize_text_field($_POST['cmfcounter']) : '';
		$campares = array( '1' => '=', '2' =>'!=', '3' =>'>', '4' =>'>=', '5' =>'<', '6' =>'<=', '7' =>'LIKE', '8' =>'NOT LIKE', '9' =>'IN', '10' =>'NOT IN', '11' =>'BETWEEN', '12' =>'NOT BETWEEN','13' => 'NOT EXISTS');	
$html ='';
		if($type == 'form'){
		$html .= '<tr style="background:#BEF781"><td><input type="hidden" id="cmfcounter" name="cmfcounter" value="'.$c.'"/>';
		$html .= '<select id="cmfkey" name="awqsf[cmf]['.$c.'][metakey]">';
			$keys = $this->get_all_keys();
			foreach($keys as $key){
				$selected = ($metakey==$key) ? 'selected="selected"' : '';	
					$html .= '<option value="'.$key.'" '.$selected.'>'.$key.'</option>';		
				}	
		$html .= '</select><br></td>';
		
		$html .=  '<td>';
		$html .= '<input type="text" id="cmflabel" name="awqsf[cmf]['.$c.'][label]" value="'.$label.'"/>';
		$html .= '<br></td>';
		
		$html .=  '<td>';
		$html .= '<input type="text" id="cmfalltext" name="awqsf[cmf]['.$c.'][all]" value="'.$all.'"/>';
		$html .= '<br></td>';
		
		$html .=  '<td>';
		$html .= '<select id="cmfcom" name="awqsf[cmf]['.$c.'][compare]">';
				foreach ($campares  as $ckey => $cvalue  ) {
				$selected = ($com==$ckey) ? 'selected="selected"' : '';	
		$html .= '<option value="'.$ckey.'" '.$selected.'>'.$cvalue.'</option>';}
		$html .= '</select><br></td>';
		
	        $html .= '<td><textarea id="cmflabel" name="awqsf[cmf]['.$c.'][opt]" >'.$option.'</textarea></td>';

		$html .= '<td>';
		$dropdown = $radio = '';
			if($check== "dropdown"){ $dropdown = 'checked="checked"';}
			if($check== "radio"){ $radio = 'checked="checked"';}	
			if($check== "checkbox"){ $checkbox = 'checked="checked"';}	
					
		$html .= '<label><input type="radio" id="taxtype" name="awqsf[cmf]['.$c.'][type]" value="dropdown" '.$dropdown.' />'.__("Drop Down","AWQSFTXT").'</label>'; 
		$html .= '<label><input type="radio" id="taxtype" name="awqsf[cmf]['.$c.'][type]" value="radio" '.$radio.'/>'.__("Radio Button","AWQSFTXT").'</label>';
		$html .= '<label><input type="radio" id="taxtype" name="awqsf[cmf]['.$c.'][type]" value="checkbox" '.$checkbox.'/>'.__("Checkbox","AWQSFTXT").'</label>';
		$html .= '<br></td>'; 
		
		$html .= '<td><span class="remove_row button-secondary">'.__("Remove","WQFS").'</span></td></tr>';

		}
	if($type == 'meta'){
		$values = $this->get_all_mvalue($metakey);
		$html .= implode(" | ", $values);
	}

       	echo $html;
	exit;

?>
