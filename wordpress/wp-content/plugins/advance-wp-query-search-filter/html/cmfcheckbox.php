<?php
     echo '<div class="awqsf_box cmfcheck-'.$i.'"><label class="taxo-cmf-'.$i.'">'.$v['label'].'</label><br>';
     echo '<input type="hidden" name="cmf['.$i.'][metakey]" value="'.$v['metakey'].'">';
     echo '<input type="hidden" name="cmf['.$i.'][compare]" value="'.$v['compare'].'">';
     $checked = isset($_GET['cmf'][$i]['call']) ? 'checked="checked"' : '';
     echo '<label class="cmfcheckbox"><input type="checkbox" id="cmf-'.$i.'" name="cmf['.$i.'][call]" class="checkall" '.$checked.'>'.$v['all'].'</label>';
     $opts = explode("|", $v['opt']);
	foreach ( $opts as $opt ) {

	    
		if(isset($_GET['cmf'][$i]['value'])){
			if(is_array($_GET['cmf'][$i]['value']) ){
		$selected = (isset($_GET['cmf'][$i]['value']) && in_array($opt, $_GET['cmf'][$i]['value'])) ? 'checked="checked"' : '';}
		else{
		 $selected = (isset($_GET['cmf']) && trim($_GET['cmf'][$i]['value'])==$opt) ? 'selected="selected"' : '';
		}
	 }


	echo '<label class="cmfcheckbox"><input type="checkbox" id="cmf-'.$i.'" name="cmf['.$i.'][value][]" value="'.$opt.'" '.$selected.'>'.$opt.'</label>';	
		}
 
	echo '<div style="clear:both"></div></div>';

?>
