<script type="text/javascript">
	function pb_backupbuddy_selectdestination( destination_id, destination_title, callback_data ) {
		jQuery( '#pb_backupbuddy_remotedestinations_list' ).append( '<li id="pb_remotedestination_' + destination_id + '">' + destination_title + ' <img class="pb_remotedestionation_delete" src="<?php echo pb_backupbuddy::plugin_url(); ?>/images/bullet_delete.png" style="vertical-align: -3px; cursor: pointer;" title="<?php _e( 'Remove remote destination from this schedule.', 'it-l10n-backupbuddy' ); ?>" /></li>' + "\n" );
		jQuery( '#pb_backupbuddy_deleteafter' ).slideDown();
	}
	
	
	jQuery(document).ready(function() {
		/* Generate the remote destination list upon submission. */
		jQuery('#pb_backupbuddy_scheduling_form').submit(function(e) {
			remote_destinations = '';
			jQuery( '#pb_backupbuddy_remotedestinations_list' ).children('li').each(function () {
				remote_destinations = jQuery(this).attr( 'id' ).substr( 21 ) + '|' + remote_destinations ;
			});
			jQuery( '#pb_backupbuddy_remote_destinations' ).val( remote_destinations );
		});
		
		
		/* Allow deleting of remote destinations from the list. */
		jQuery('.pb_remotedestionation_delete').live( 'click', function(e) {
			jQuery( '#pb_remotedestination_' + jQuery(this).parent( 'li' ).attr( 'id' ).substr( 21 ) ).remove();
		});
		
		
		jQuery('.pluginbuddy_pop').click(function(e) {
			showpopup('#'+jQuery(this).attr('href'),'',e);
			return false;
		});
	});
</script>




<?php
pb_backupbuddy::$ui->title( __('Scheduled Backups', 'it-l10n-backupbuddy' ) );



pb_backupbuddy::$ui->start_metabox( $mode_title . ' ' . pb_backupbuddy::video( 'MGiUdYb68ps', __('Scheduling', 'it-l10n-backupbuddy' ), false ), true, 'width: 100%;' );
$schedule_form->display_settings( '+ ' . $mode_title );
echo '<br><br>';
pb_backupbuddy::$ui->end_metabox();





if ( count( $schedules ) == 0 ) {
	//echo '<h4>' . __( 'No schedules have been created yet.', 'it-l10n-backupbuddy' ) . '</h4>';
} else {
	pb_backupbuddy::$ui->list_table(
		$schedules,
		array(
			'action'		=>		pb_backupbuddy::page_url(),
			'columns'		=>		array(
										__( 'Title', 'it-l10n-backupbuddy' ),
										__( 'Type', 'it-l10n-backupbuddy' ),
										__( 'Interval', 'it-l10n-backupbuddy' ),
										__( 'Destinations', 'it-l10n-backupbuddy' ),
										__( 'First Run', 'it-l10n-backupbuddy' ),
										__( 'Last Run', 'it-l10n-backupbuddy' ) . pb_backupbuddy::tip( __( 'Last run time is the last time that this scheduled backup started. This does not imply that the backup completed, only that it began at this time. The last run time is reset if the schedule is edited.', 'it-l10n-backupbuddy' ), '', false ),
									),
			'hover_actions'	=>		array( 'edit' => 'Edit Schedule' ),
			'bulk_actions'	=>		array( 'delete_schedule' => 'Delete' ),
			'css'			=>		'width: 100%;',
		)
	);
}
echo '<br>';


?>




<br /><br />
<div class="description">
	<b>Note</b>: Due to the way schedules are triggered in WordPress your site must be accessed (frontend or admin area) for scheduled backups to occur.
	WordPress scheduled events (crons) may be viewed or run manually for testing from the <a href="?page=pb_backupbuddy_server_info">Server Information page</a>.
	A <a href="https://www.google.com/search?q=free+website+uptime&oq=free+website+uptime" target="_new">free website uptime</a> service can be used to automatically access your site regularly to help trigger scheduled actions (crons) in cases of low site activity, with the added perk of keeping track of your site uptime.
</div>
<br /><br />



<?php
// Handles thickbox auto-resizing. Keep at bottom of page to avoid issues.
if ( !wp_script_is( 'media-upload' ) ) {
	wp_enqueue_script( 'media-upload' );
	wp_print_scripts( 'media-upload' );
}
?>