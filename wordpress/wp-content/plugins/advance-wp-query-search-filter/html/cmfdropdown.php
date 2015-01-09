<?php
  echo '<div class="awqsf_box cmfdrop-'.$i.'"><label class="taxo-cmf-'.$i.'">'.$v['label'].'</label><br>';
     echo '<input type="hidden" name="cmf['.$i.'][metakey]" value="'.$v['metakey'].'">';
     echo '<input type="hidden" name="cmf['.$i.'][compare]" value="'.$v['compare'].'">';
     echo  '<select id="cmf-'.$i.'" name="cmf['.$i.'][value]">'; 
     echo '<option value="wqsfcmfall">'.$v['all'].'</option>';
     $opts = explode("|", $v['opt']);
	foreach ( $opts as $opt ) {
	     $selected2 = (isset($_GET['cmf']) && $_GET['cmf'][$i]['value']==$opt) ? 'selected="selected"' : '';
		echo '<option value="'.$opt.'" '.$selected2.'>'.$opt.'</option>';
		}
 	echo '</select><br>';
	echo '</div>';
?>
