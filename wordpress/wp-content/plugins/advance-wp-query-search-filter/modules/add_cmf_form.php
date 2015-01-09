<div class="add_tax_form_div"  style="display:none">
	<form id="add_cmf_form" >
	
	<h3><?php _e("Meta Field Setting","AWQSFTXT");?></h3>
	<p><span><?php _e("Meta Key","AWQSFTXT");?>:</span>
	<select type="text" id="precmfkey" name="prekey"><br>
	<?php
	$keys = $this->get_all_keys();
	echo '<option value="">'.__("Choose a meta key","AWQSFTXT").'</option>';	
		foreach($keys as $key){
			
			 echo  '<option value="'.$key.'">'.$key.'</option>';
		}
	?>	
	</select>
	</p>
	
	<p><span><?php _e("Label","AWQSFTXT");?>:</span>
	<input type="text" id="precmflabel" name="precmflabel" value=""/><br>
	<span class="desciption"><?php _e("To be displayed in the search form", "AWQSFTXT");?></span>
	</p>
	
	<p><span><?php _e("Text For 'Search All' Options","AWQSFTXT");?>:</span>
	<input type="text" id="precmfall" name="precmfall" value=""/><br>
	<span class="desciption"><?php _e("eg, All prices, All weight", "AWQSFTXT") ;?></span>
	</p>
	
	<p><span><?php _e("Compare","AWQSFTXT");?>:</span>
	<select id="precompare" name="precompare">
	<?php $campares = array( '1' => '=', '2' =>'!=', '3' =>'>', '4' =>'>=', '5' =>'<', '6' =>'<=', '7' =>'LIKE', '8' =>'NOT LIKE', '9' =>'IN', '10' =>'NOT IN', '11' =>'BETWEEN', '12' =>'NOT BETWEEN','13' => 'NOT EXISTS');	
		foreach ($campares   as $ckey => $cvalue ) {
			echo '<option value="'.$ckey.'">'.$cvalue.'</option>';
	     }
	?>
	</select><br>
	<span class="desciption"><?php _e("Operator to test. Use it carefully. If you choose 'BETWEEN', then your options should be range." , "AWQSFTXT") ;?></span>
	<?php $link = 'http://wordpress.stackexchange.com/questions/70864/meta-query-compare-operator-explanation/70870#70870';
	echo '<span class="desciption">'.sprintf(__("More about compare, please visit <a href='%s' target='_blank'>here</a>", "AWQSFTXT"), $link ).'</span>';
	;?>
	</p>

	<p>
	<span><?php _e("Display Type?","AWQSFTXT");?></span><br>
	<label><input type="radio" id="pretype" name="cmfdisplay" value="dropdown"   /><?php _e('Drop Down','AWQSFTXT') ;?></label>
	<label><input type="radio" id="pretype" name="cmfdisplay" value="radio"   /><?php _e('Radio Button','AWQSFTXT') ;?></label>
	<label><input type="radio" id="pretype" name="cmfdisplay" value="checkbox"   /><?php _e('Checkbox','AWQSFTXT') ;?></label><br>
	<span class="generate"><?php _e('* Warning! Checkbox only work with "IN" and "NOT IN" compare operator.','AWQSFTXT') ;?> </span>	
	<br>
	</span>
	</p>
	
	<p><span><?php _e("Dropdown Options","AWQSFTXT");?>:</span><br>
	<textarea  id="preopt" name="preopt" rows="5" cols="40"></textarea><br><input type="button" class="genv" value="<?php _e('Generate Value','AWQSFTXT') ;?>">
	<span class="generate"><?php _e('Based on the meta key selected above','AWQSFTXT') ;?> </span><br>
	<span class="desciption"><?php _e("Your options should be exactly same you inserted in your posts. Use '|' to separating your option. For range option, using '-' to define the range. " , "AWQSFTXT") ;?></span><br>
	<span class="desciption"><?php _e("eg: for normal option value 1 | value 2 | value 3...etc" , "AWQSFTXT") ;?></span><br>
	<span class="desciption"><?php _e("eg: for range option 1-100 | 101-200 | 201-300...etc" , "AWQSFTXT") ;?></span>
	
	</p>
	
	
	<input type="submit" value="<?php _e("Add Custom Field","AWQSFTXT");?>" class="add-cmf button-secondary" />
	</form>
	</div>
