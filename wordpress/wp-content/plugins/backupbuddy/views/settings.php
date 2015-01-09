<?php
pb_backupbuddy::$ui->start_metabox( 'BackupBuddy Settings', true, 'width: 100%; max-width: 1200px;' );
$settings_form->display_settings( 'Save Settings' );
echo '<br><br>';
pb_backupbuddy::$ui->end_metabox();




/*

REMOVED v3.1.

pb_backupbuddy::$ui->start_metabox( __('Remote Offsite Storage / Destinations', 'it-l10n-backupbuddy' ) . ' ' . pb_backupbuddy::video( 'PmXLw_tS42Q#177', __( 'Remote Offsite Management / Remote Clients Tutorial', 'it-l10n-backupbuddy' ), false ), true, 'width: 100%; max-width: 1200px;' );
//echo '<h3>' . __('Remote Offsite Storage / Destinations', 'it-l10n-backupbuddy' ) . ' ' . pb_backupbuddy::video( 'PmXLw_tS42Q#177', __( 'Remote Offsite Management / Remote Clients Tutorial', 'it-l10n-backupbuddy' ), false ) . '</h3>';
//echo '<br>';
echo '<a href="' . pb_backupbuddy::ajax_url( 'destination_picker' ) . '&action_verb=to%20manage%20files&#038;TB_iframe=1&#038;width=640&#038;height=600" class="thickbox button secondary-button" style="margin-top: 3px;" title="' . __( 'Manage Destinations & Archives', 'it-l10n-backupbuddy' ) . '">' . __('Manage Destinations & Archives', 'it-l10n-backupbuddy' ) . '</a>';
echo '&nbsp;&nbsp;&nbsp;';
_e( 'Add & configure destinations or select a destination to browse & manage its files.', 'it-l10n-backupbuddy' );
echo '<br><br>';
pb_backupbuddy::$ui->end_metabox();
*/




?>
<script type="text/javascript">
	function pb_backupbuddy_selectdestination( destination_id, destination_title, callback_data ) {
		window.location.href = '<?php
			if ( is_network_admin() ) {
				echo network_admin_url( 'admin.php' );
			} else {
				echo admin_url( 'admin.php' );
			}
		?>?page=pb_backupbuddy_backup&custom=remoteclient&destination_id=' + destination_id;
	}
</script>


<?php
// Handles thickbox auto-resizing. Keep at bottom of page to avoid issues.
if ( !wp_script_is( 'media-upload' ) ) {
	wp_enqueue_script( 'media-upload' );
	wp_print_scripts( 'media-upload' );
}
?>