<?php 

function get_awqsf_taxo($id) {
	global $wp_query;
	$options = get_post_meta($id, 'awqsf-relbool', true);
	$taxrel = isset($options[0]['tax']) ? $options[0]['tax'] : 'AND';
	if(!empty($_GET['taxo'])){
				
		$taxo=array('relation' => $taxrel,'');
		foreach($_GET['taxo'] as  $v){
		   if(isset($v['term']))	{	
			if( $v['term'] == 'wqsftaxoall'){
			  $taxo[] = array(
					'taxonomy' => strip_tags( stripslashes($v['name'])),
					'field' => 'slug',
					'terms' => strip_tags( stripslashes($v['term'])),
					'operator' => 'NOT IN'
				);
			  
			  }
			elseif(is_array($v['term'])){
		  	 $taxo[] = array(
					'taxonomy' =>  strip_tags( stripslashes($v['name'])),
					'field' => 'slug',
					'terms' =>$v['term']
				);
			}
			else{
		  
			$taxo[] = array(
					'taxonomy' => strip_tags( stripslashes($v['name'])),
					'field' => 'slug',
					'terms' => strip_tags( stripslashes($v['term']))
				);
			}
		   }
		} //end foreach
	unset($taxo[0]);
			return $taxo ;				
			
	}
}

function get_awqsf_cmf($id){
	$options = get_post_meta($id, 'awqsf-relbool', true);
	$cmfrel = isset($options[0]['cmf']) ? $options[0]['cmf'] : 'AND';
	
	if(isset($_GET['cmf'])){
		$cmf=array('relation' => $cmfrel,'');
		foreach($_GET['cmf'] as  $v){
		   $campares = array( '1' => '=', '2' =>'!=', '3' =>'>', '4' =>'>=', '5' =>'<', '6' =>'<=', '7' =>'LIKE', '8' =>'NOT LIKE', '9' =>'IN', '10' =>'NOT IN', '13' => 'NOT EXISTS');//avoid tags stripped 
		    if(isset($v['value'])){
			if($v['value'] == 'wqsfcmfall'){
				    $cmf[] = array(
						'key' => strip_tags( stripslashes($v['metakey'])),
						'value' => 'get_all_cmf_except_me',
						'compare' => '!='
				);
				  
				}
			elseif( $v['compare'] == '11'){
				$range = explode("-", strip_tags( stripslashes($v['value'])));
			    $cmf[] = array(
						'key' => strip_tags( stripslashes($v['metakey'])),
						'value' => $range,
						'type' => 'numeric',
						'compare' => 'BETWEEN'
				);
			  
			  }
			  elseif( $v['compare'] == '12'){
				$range = explode("-", strip_tags( stripslashes($v['value'])));
			    $cmf[] = array(
						'key' => strip_tags( stripslashes($v['metakey'])),
						'value' => $range,
						'type' => 'numeric',
						'compare' => 'NOT BETWEEN'
				);
			  
			  }elseif( $v['compare'] == '9' || $v['compare'] == '10' ){
				foreach($campares as $ckey => $cval)
					{  if($ckey == $v['compare'] ){ $safec = $cval;}        }
					$trimmed_array=array_map('trim',$v['value']);
				//implode(',',$v['value'])
			    $cmf[] = array(
						'key' => strip_tags( stripslashes($v['metakey'])),
						'value' =>$trimmed_array,
						'compare' => $safec 
				);
			  
			  }elseif( $v['compare'] == '3' || $v['compare'] == '4' || $v['compare'] == '5' || $v['compare'] == '6'){
				 	
					foreach($campares as $ckey => $cval)
					{  if($ckey == $v['compare'] ){ $safec = $cval;}        }
					
					$cmf[] = array(
					'key' => strip_tags( stripslashes($v['metakey'])),
					'value' => strip_tags( stripslashes($v['value'])),
					'type' => 'DECIMAL',
					'compare' => $safec
				);
			}else{
				 	
					foreach($campares as $ckey => $cval)
					{  if($ckey == $v['compare'] ){ $safec = $cval;}        }
					
					$cmf[] = array(
					'key' => strip_tags( stripslashes($v['metakey'])),
					'value' => strip_tags( stripslashes($v['value'])),
					'compare' => $safec
				);
			}
			
		   }//end isset
		}//end foreach
	unset($cmf[0]);
			return $cmf ;				
			
	}
	
}


function awqsf_search_query( $query ) {
		
	if($query->is_search()){
		
		//if($query->query_vars['s'] == '12345' )	
		if(wp_verify_nonce($query->query_vars['s'], 'awqsfsearch') )	{	
			$id = absint($_GET['formid']);
			$options = get_post_meta($id, 'awqsf-relbool', true);
			$cpts = get_post_meta($id, 'awqsf-cpt', true);
	
			$default_number = get_option('posts_per_page');
			
			$cpt        = !empty($cpts) ? $cpts : 'any';
			$ordermeta  = !empty($options[0]['smetekey']) ? $options[0]['smetekey'] : null;
			$ordervalue = (!empty($options[0]['otype']) && $ordermeta) ? $options[0]['otype'] : null;
			$order      = !empty($options[0]['sorder']) ? $options[0]['sorder'] : null;
			$number      = !empty($options[0]['resultc']) ? $options[0]['resultc'] : $default_number;
			$paged = ( get_query_var( 'paged') ) ? get_query_var( 'paged' ) : 1;
			$keyword = !empty($_GET['skeyword']) ?	 sanitize_text_field($_GET['skeyword']) : null;
			$get_tax = get_awqsf_taxo($id);
			$get_meta = get_awqsf_cmf($id);
			$tax_query = isset($get_tax) && empty($keyword) ? 	$get_tax : null;
			$meta_query = isset($get_meta) && empty($keyword) ? $get_meta : null;    
			   
				
				
				$query->query_vars['posts_per_page'] = $number ;
				$query->set( 'meta_key', $ordermeta );
				$query->set( 'orderby', $ordervalue );
				$query->set( 'order', $order  );
				$query->set( 'page', $paged );
				$query->set( 'post_type', $cpt );
				$query->set( 'tax_query', $tax_query );
				$query->set( 'meta_query', $meta_query );
					
				$query->query_vars['s'] = esc_html($keyword);
					
			   return $query;}
	   
	   
	  return $query; 
	   
    }
 
}


add_action( 'pre_get_posts', 'awqsf_search_query',1000);

//the title and search query filter

function change_awqsf_var( $s )
{
if(is_search()){
if(isset($_GET['cmf']) || isset($_GET['taxo'])){
if(isset($_GET['cmf']) ){
 	 $cmfterms = $_GET['cmf'];$sterm = '';
	foreach( $cmfterms as $v){
	if(isset($v['value']) && $v['value'] !='wqsfcmfall')
	//exclude 'all text' in custom meta field. You can use to display it with other text you like by responding the meta key. exp if($v['value'] =='wqsfcmfall' && $v[metakey] == 'responding meta key'){$sterm[] = 'All price' ; }
	 {if(!empty($v['value']))$sterm[] = $v['value'];}
	}
	if(is_array($sterm) && !empty($sterm)){
	$searchterm1 =  implode(", ", $sterm);//you can implode with your value as well. But this applied with all values (including null value) 
	}else{$searchterm1 = $sterm;}

}
if(isset($_GET['taxo']) ){
 	 $taxterms = $_GET['taxo'];$sterm2 = '';
	foreach( $taxterms as $v){
	if(isset($v['term']) && $v['term'] !='wqsftaxoall')//exclude 'all text' in taxonomy term.  You can use to display it with other text you like by responding the taxonomy. exp if($v['term'] =='wqsfcmfall' && $v['name'] == 'responding meta key'){$sterm[] = 'All cities' ; }
	 {$sterm2 = $v['term'];}
	}
	if(is_array($sterm2)){
	$searchterm2 =  implode(", ", $sterm2);}//you can implode with your value as well. But this applied with all values (including null value) 
	else{$searchterm2 = $sterm2;}

}

$query = (!empty($searchterm1) && !empty($searchterm2)) ?  esc_attr($searchterm1).', '.esc_attr($searchterm2).' ' : esc_attr($searchterm1).' '.esc_attr($searchterm2).' ';
}
elseif(isset($_GET['s'])){

 $query = get_query_var( 's' );
 		
      $query = esc_attr( $query );
    

}
return apply_filters('awqsf_search_query',$query); //filter for custom query
}
}
add_filter( 'get_search_query', 'change_awqsf_var', 20, 1 );

function awqsf_search_title($title  )
{
if(is_search()){
if(isset($_GET['cmf']) || isset($_GET['taxo'])){
if(isset($_GET['cmf']) ){
 	 $cmfterms = $_GET['cmf'];$sterm = '';
	foreach( $cmfterms as $v){
	if(isset($v['value']) && $v['value'] !='wqsfcmfall')
	//exclude 'all text' in custom meta field. You can use to display it with other text you like by responding the meta key. exp if($v['value'] =='wqsfcmfall' && $v[metakey] == 'responding meta key'){$sterm[] = 'All price' ; }
	 {if(!empty($v['value']))$sterm[] = $v['value'];}
	}
	if(is_array($sterm)){
	$searchterm1 =  implode(", ", $sterm);//you can implode with your value as well. But this applied with all values (including null value) 
	}else{$searchterm1 = $sterm;}

}
if(isset($_GET['taxo']) ){
 	 $taxterms = $_GET['taxo'];$sterm2 = '';
	foreach( $taxterms as $v){
	if(isset($v['term']) && $v['term'] !='wqsftaxoall')//exclude 'all text' in taxonomy term.  You can use to display it with other text you like by responding the taxonomy. exp if($v['term'] =='wqsfcmfall' && $v['name'] == 'responding meta key'){$sterm[] = 'All cities' ; }
	 {$sterm2 = $v['term'];}
	}
	if(is_array($sterm2)){
	$searchterm2 =  implode(", ", $sterm2);}//you can implode with your value as well. But this applied with all values (including null value) 
	else{$searchterm2 = $sterm2;}

}

$query = (!empty($searchterm1) && !empty($searchterm2)) ?  ' | '.esc_attr($searchterm1).', '.esc_attr($searchterm2).' ' : ' | '.esc_attr($searchterm1).' '.esc_attr($searchterm2).' ';
}
elseif(isset($_GET['s'])){

 $query = get_query_var( 's' );
 		
      $query = esc_attr( $query );
    

}
return apply_filters('awqsf_search_title',$query); //filter for custom query
}
else{return $title; }
}
add_filter('wp_title', 'awqsf_search_title');


;?>
