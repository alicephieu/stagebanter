<?php

if ( $count > 0 ){
echo '<div class="awqsf_box taxocheck-'.$c.'"><label class="taxo-label-'.$c.'">'.$v['taxlabel'].'</label><br>';
echo '<input  type="hidden" name="taxo['.$c.'][name]" value="'.$v['taxname'].'">';
  $checked = isset($_GET['taxo'][$c]['call']) ? 'checked="checked"' : '';
     echo '<label class="taxcheckbox"><input type="checkbox" id="taxo-'.$c.'" name="taxo['.$c.'][call]" class="checkall" '.$checked.'>'.$v['taxall'].'</label>';
foreach ( $terms as $term ) {
$selected = '';
 if(isset($_GET['taxo'][$c]['term'])){
	if(is_array($_GET['taxo'][$c]['term']) ){
		$selected = (isset($_GET['taxo'][$c]['term']) && in_array($term->slug, $_GET['taxo'][$c]['term'])) ? 'checked="checked"' : '';}
	else{
	$selected = (isset($_GET['taxo']) && $_GET['taxo'][$c]['term']==$term->slug) ? 'checked="checked"' : '';
	}
 }
$value = $term->slug;
echo '<label class="taxcheckbox"><input type="checkbox" id="taxo-'.$c.'" name="taxo['.$c.'][term][]" value="'.$value.'" '.$selected.'>'.$term->name.'</label>';
}

echo '<br><div style="clear:both"></div>';
echo '</div>';
}

?>
