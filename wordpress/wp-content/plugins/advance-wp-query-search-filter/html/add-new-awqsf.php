<?php
if ( ! defined( 'ABSPATH' ) )
die( '-1' );
$addlink = add_query_arg(array('formid' => 'new', 'formaction' => 'new'), AWQS);
?>
<div class="wrap"><div id="icon-options-general" class="icon32"></div><h2><?php echo esc_html( __( 'Advance WP Query Search Filter', 'AWQSFTXT' ) ); ?><a href="<?php echo $addlink;?>" class="add-new-h2"> <?php echo esc_html( __( 'Add New Search Form', 'AWQSFTXT' ) ); ?></a></h2>


<?php   $this->dona_support();
	$this->show_messages();

 ?>
<br>
<?php 
$postid = absint($_GET['formid'])  ? esc_attr($_GET['formid']) : '';
if(isset($postid) && absint($postid)){
echo '<div class="showcode"><h2>'.strtolower("[awsqf-form id=$postid]").'</h2><span class="drag">'.esc_html( __( 'Copy this code and paste it into your post, page or text widget content.', 'AWQSFTXT' ) ).'</span></div>';
}
?>
<br>
<form method="post" action="" id="awqsf-cpt">

<?php 
$nonce = wp_create_nonce  ('awqsfedit');

echo '<input type="hidden" name="formid" value="'.esc_attr($_GET['formid']).'" ><input type="hidden" name="nonce" value="'.$nonce.'" />'
;?>
<span class="title"><?php _e('Form Title','AWQSFTXT'); ?> : <input type="text" class="form_title" name="ftitle" value="<?php echo get_the_title($postid); ?> "></span><br>

<?php require_once AWQSFURL . '/modules/awqsf_post_type.php'; ?>
<?php require_once AWQSFURL . '/modules/awqsf_taxonomy.php'; ?>
<?php require_once AWQSFURL . '/modules/awqsf_cmf.php'; ?>
<?php require_once AWQSFURL . '/modules/misc.php'; ?>
<?php

echo '<input type="submit" class="button-primary" name="awqsfsubmit" value="Save" >'; 
?>
</form>
<?php require_once AWQSFURL . '/modules/add_taxo_form.php'; ?>
<?php require_once AWQSFURL . '/modules/add_cmf_form.php'; ?>

</div>
