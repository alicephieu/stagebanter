<?php

 echo '<div class="awqsf_box taxodrop-'.$c.'"><label class="taxo-label-'.$c.'">'.$v['taxlabel'].'</label><br>';
 echo '<input  type="hidden" name="taxo['.$c.'][name]" value="'.$v['taxname'].'">';
 echo  '<select id="taxo-'.$c.'" name="taxo['.$c.'][term]"><option selected value="wqsftaxoall">'.$v['taxall'].'</option>'; 
if ( $count > 0 ){
foreach ( $terms as $term ) {
$selected = (isset($_GET['taxo']) && $_GET['taxo'][$c]['term']==$term->slug) ? 'selected="selected"' : '';
echo '<option value="'.$term->slug.'" '.$selected.'>'.$term->name.'</option>';}}				
echo '</select><br>';
echo '</div>';

?>
