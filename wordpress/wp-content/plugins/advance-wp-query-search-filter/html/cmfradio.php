<?php
     echo '<div class="awqsf_box cmfradio-'.$i.'"><label class="taxo-cmf-'.$i.'">'.$v['label'].'</label><br>';
     echo '<input type="hidden" name="cmf['.$i.'][metakey]" value="'.$v['metakey'].'">';
     echo '<input type="hidden" name="cmf['.$i.'][compare]" value="'.$v['compare'].'">';
     
     echo '<label class="cmfradio"><input type="radio" id="cmf-'.$i.'" name="cmf['.$i.'][value]" value="wqsfcmfall" ',(isset($_GET['cmf'][$i]['value']) &&  $_GET['cmf'][$i]['value']=='wqsfcmfall') ? 'checked="checked"' : '','>'.$v['all'].'</label>';
     $opts = explode("|", $v['opt']);
	
	foreach ( $opts as $opt ) {
	     $checked = '';
	     $checked =  (isset($_GET['cmf'][$i]['value']) &&  $_GET['cmf'][$i]['value']==$opt) ? 'checked="checked"' : '';		
	
	echo '<label class="cmfradio"><input type="radio" id="cmf-'.$i.'" name="cmf['.$i.'][value]" value="'.$opt.'" '. $checked.' >'.$opt.'</label>';	
		}
 
	echo '<div style="clear:both"></div></div>';

?>
