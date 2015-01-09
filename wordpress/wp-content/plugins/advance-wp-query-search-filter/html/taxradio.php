<?php
if ( $count > 0 ){
	echo '<div class="awqsf_box taxoradio-'.$c.'"><label class="taxo-label-'.$c.'">'.$v['taxlabel'].'</label><br>';
	echo '<input  type="hidden" name="taxo['.$c.'][name]" value="'.$v['taxname'].'">';
	echo '<label class="taxradio"><input type="radio" id="taxo-'.$c.'" name="taxo['.$c.'][term]" value="wqsftaxoall" ',(isset($_GET['taxo'][$c]['term']) &&  $_GET['taxo'][$c]['term']=='wqsftaxoall') ? 'checked="checked"' : '','>'.$v['taxall'].'</label>';
	foreach ( $terms as $term ) {
		$selected = '';
		
		$selected = (isset($_GET['taxo'][$c]['term']) && $_GET['taxo'][$c]['term']==$term->slug) ? 'checked="checked"' : '';
		echo '<label class="taxradio"><input type="radio" id="taxo-'.$c.'" name="taxo['.$c.'][term]" value="'.$term->slug.'" '.$selected.'>'.$term->name.'</label>';
	}

echo '<br><div style="clear:both"></div>';
echo '</div>';
}
?>
