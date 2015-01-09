<?php

pb_backupbuddy::$classes['core']->versions_confirm();

$alert_message = array();
$preflight_checks = pb_backupbuddy::$classes['core']->preflight_check();
foreach( $preflight_checks as $preflight_check ) {
	if ( $preflight_check['success'] !== true ) {
		$alert_message[] = $preflight_check['message'];
	}
}
if ( count( $alert_message ) > 0 ) {
	pb_backupbuddy::alert( implode( '<hr style="border: 1px dashed #E6DB55; border-bottom: 0;">', $alert_message ) );
}



$view_data['backups'] = pb_backupbuddy::$classes['core']->backups_list( 'default' );


pb_backupbuddy::load_view( '_backup-home', $view_data );
?>