<?php
$nonce = wp_create_nonce  ('awqsfsearch');
$taxo = get_post_meta($id, 'awqsf-taxo', true);
$cmf = get_post_meta($id, 'awqsf-cmf', true);
$options = get_post_meta($id, 'awqsf-relbool', true);
echo '<div id="aqsfformid">';
if($formtitle){
echo '<span class="form_title">'.get_the_title($id).'</span>';}
echo '<form method="get"  id="awqsf_search_form_'.$id.'" action="'.home_url( '/' ).'">';
echo '<input type="hidden" name="s" value="'.$nonce.'" /><input type="hidden" name="formid" value="'.$id.'">';
do_action('awqsf_fhead', $id);
if(!empty($taxo)){
 $c = 0;
 foreach($taxo as $k => $v){
		$eid = explode(",", $v['exc']);
		$args = array('hide_empty'=>$v['hide'],'exclude'=>$eid );	
                $terms = get_terms($v['taxname'],$args);
 	       $count = count($terms);
		if($v['type'] == 'dropdown'){
			include "taxdropdown.php";
		}
		if($v['type'] == 'checkbox'){
 			include "taxcheckbox.php";
		}
		if($v['type'] == 'radio'){
 			include "taxradio.php";
		}
	      if(empty($v['type'])){include "taxdropdown.php";}
	$c++;			
  }
	$newtaxo ='';
	$newtaxo = apply_filters('awpqsf_addextra_taxo', $newtaxo, $c,$id);
  	echo $newtaxo;

}

if(!empty($cmf)){  
   $i=0;
    foreach($cmf as $k => $v){
	if(isset($v['type'])){
      	
      if($v['type'] == 'dropdown'){
   	include "cmfdropdown.php";
      }
      if($v['type'] == 'radio'){
 	include "cmfradio.php";
      }	
      if($v['type'] == 'checkbox'){
 	include "cmfcheckbox.php";
      }		
	
     }else{include "cmfdropdown.php";}
     $i++;
   }	
      $newcmf ='';
      $newcmf = apply_filters('awpqsf_addextra_cmf', $newcmf, $i,$id);
      echo $newcmf;	

}

if(isset($options[0]['strchk']) && ($options[0]['strchk'] == '1') ){
		echo '<div class="awqsf_box"><center><label class="awqsf-label-keyword">'.$options[0]['strlabel'].'</center></label>';
		echo '<input id="awqsf_keyword" type="text" name="skeyword" value="" />';
                echo '<br></div>';
}
do_action('awqsf_fbottom',$id);
echo '<div class="awqsf_box"><p class="awqsf-button"><input type="submit" id="awqsf_submit" value="'.$options[0]['button'].'" alt="[Submit]" name="wqsfsubmit" title="Search" /></p></div>';
				
echo '</form>';


echo '</div>';

?>
