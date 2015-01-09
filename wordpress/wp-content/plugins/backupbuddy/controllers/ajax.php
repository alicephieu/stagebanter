<?php

class pb_backupbuddy_ajax extends pb_backupbuddy_ajaxcore {
	
	
	
	public function ajax_controller_callback_function() {
		echo 'a_post_variable: ' . pb_backupbuddy::_POST( 'a_post_variable' );  // aka pb_backupbuddy::_POST( 'a_post_variable' )
		echo 'selection: ' . pb_backupbuddy::_POST( 'selection' ); // aka pb_backupbuddy::_POST( 'selection' )
		
		die();
	}
	
	
	
	// Used for recent backup listing.
	public function backup_step_status() {
		$serial = pb_backupbuddy::_GET( 'serial' );
		pb_backupbuddy::load();
		pb_backupbuddy::$ui->ajax_header();
		
		echo '<h3>Backup Process Technical Details</h3>';
		echo '<textarea style="width: 100%; height: 70%;" wrap="off">';
		print_r( pb_backupbuddy::$options['backups'][$serial] );
		echo '</textarea><br><br>This information is primarily used for troubleshooting when working with support. If you are encountering problems providing this information to support may assist in troubleshooting.';
		
		pb_backupbuddy::$ui->ajax_footer();
		die();
		
	} // End backup_step_status().
	
	
	public function backup_status() {
		// Make sure the serial exists.
		if ( ( pb_backupbuddy::_POST( 'serial' ) == '' ) || empty( pb_backupbuddy::$options['backups'][pb_backupbuddy::_POST( 'serial' )] ) ) {
			echo '!' . pb_backupbuddy::$format->localize_time( time() ) . '|~|0|~|' . round( memory_get_peak_usage() / 1048576, 2 ) . '|~|error|~|Error #9031. Invalid backup serial (' . htmlentities( pb_backupbuddy::_POST( 'serial' ) ) . '). Please check directory permissions for your wp-content/uploads/ directory recursively, your PHP error_log for any errors, and that you have enough free disk space. Fatal error.' . "\n";
			echo '!' . pb_backupbuddy::$format->localize_time( time() ) . '|~|0|~|' . round( memory_get_peak_usage() / 1048576, 2 ) . '|~|action|~|halt_script' . "\n";
		} else {
		
			$return_status = '';
			
			//error_log( print_r( pb_backupbuddy::$options['backups'], true ) );
			foreach( pb_backupbuddy::$options['backups'][pb_backupbuddy::_POST( 'serial' )]['steps'] as $step ) {
				if ( ( $step['start_time'] != 0 ) && ( $step['finish_time'] == 0 ) ) { // A step has begun but has not finished. This should not happen but the WP cron is funky. Wait a while before continuing.
					
					// For database dump step output the SQL file current size.
					if ( $step['function'] == 'backup_create_database_dump' ) {
						$sql_file = pb_backupbuddy::$options['backups'][pb_backupbuddy::_POST( 'serial' )]['temp_directory'] . 'db_1.sql';
						if ( file_exists( $sql_file ) ) {
							$sql_filesize = pb_backupbuddy::$format->file_size( filesize( $sql_file ) );
						} else { // No SQL file yet.
							$sql_filesize = '[SQL file not found yet]';
						}
						pb_backupbuddy::status( 'details', 'Current SQL database dump file size: ' . $sql_filesize . '.', pb_backupbuddy::_POST( 'serial' ) );
					}
					
					pb_backupbuddy::status( 'details', 'Waiting for function `' . $step['function'] . '` to complete. Started ' . ( time() - $step['start_time'] ) . ' seconds ago.', pb_backupbuddy::_POST( 'serial' ) );
					if ( ( time() - $step['start_time'] ) > 300 ) {
						pb_backupbuddy::status( 'warning', 'The function `' . $step['function'] . '` is taking an abnormally long time to complete (' . ( time() - $step['start_time'] ) . ' seconds). The backup may have stalled.', pb_backupbuddy::_POST( 'serial' ) );
					}
				} elseif ( $step['start_time'] == 0 ) { // Step that has not started yet.
				} else { // Last case: Finished. Skip.
					// Do nothing.
				}
			}
			
			
			$status_lines = pb_backupbuddy::get_status( pb_backupbuddy::_POST( 'serial' ), true, false, true ); // Clear file, dont unlink file (pclzip cant handle files unlinking mid-zip), dont show getting status message.
			if ( $status_lines !== false ) { // Only add lines if there is status contents.
				foreach( $status_lines as $status_line ) {
					//$return_status .= '!' . $status_line[0] . '|' . $status_line[3] . '|' . $status_line[4] . '( ' . $status_line[1] . 'secs / ' . $status_line[2] . 'MB )' . "\n";
					$return_status .= '!' . implode( '|~|', $status_line ) . "\n";
				}
			}
			
			
			$return_status .= '!' . pb_backupbuddy::$format->localize_time( time() ) . "|~|0|~|0|~|ping\n";
			
			
			/********** Begin file sizes for status updates. *********/
			
			$temporary_zip_directory = pb_backupbuddy::$options['backup_directory'] . 'temp_zip_' . pb_backupbuddy::_POST( 'serial' ) . '/';
			if ( file_exists( $temporary_zip_directory ) ) { // Temp zip file.
				$directory = opendir( $temporary_zip_directory );
				while( $file = readdir( $directory ) ) {
					if ( ( $file != '.' ) && ( $file != '..' ) && ( $file != 'exclusions.txt' ) ) {
						$stats = stat( $temporary_zip_directory . $file );
						$return_status .= '!' . pb_backupbuddy::$format->localize_time( time() ) . '|~|' . round ( microtime( true ) - pb_backupbuddy::$start_time, 2 ) . '|~|' . round( memory_get_peak_usage() / 1048576, 2 ) . '|~|details|~|' . __('Temporary ZIP file size', 'it-l10n-backupbuddy' ) .': ' . pb_backupbuddy::$format->file_size( $stats['size'] ) . "\n";;
						$return_status .= '!' . pb_backupbuddy::$format->localize_time( time() ) . '|~|' . round ( microtime( true ) - pb_backupbuddy::$start_time, 2 ) . '|~|' . round( memory_get_peak_usage() / 1048576, 2 ) . '|~|action|~|archive_size^' . pb_backupbuddy::$format->file_size( $stats['size'] ) . "\n";
					}
				}
				closedir( $directory );
				unset( $directory );
			}
			if( file_exists( pb_backupbuddy::$options['backups'][pb_backupbuddy::_POST( 'serial' )]['archive_file'] ) ) { // Final zip file.
				$stats = stat( pb_backupbuddy::$options['backups'][pb_backupbuddy::_POST( 'serial' )]['archive_file'] );
				$return_status .= '!' . pb_backupbuddy::$format->localize_time( time() ) . '|~|' . round ( microtime( true ) - pb_backupbuddy::$start_time, 2 ) . '|~|' . round( memory_get_peak_usage() / 1048576, 2 ) . '|~|details|~|' . __('Completed backup final ZIP file size', 'it-l10n-backupbuddy' ) . ': ' . pb_backupbuddy::$format->file_size( $stats['size'] ) . "\n";;
				$return_status .= '!' . pb_backupbuddy::$format->localize_time( time() ) . '|~|' . round ( microtime( true ) - pb_backupbuddy::$start_time, 2 ) . '|~|' . round( memory_get_peak_usage() / 1048576, 2 ) . '|~|action|~|archive_size^' . pb_backupbuddy::$format->file_size( $stats['size'] ) . "\n";
			}
			
			/********** End file sizes for status updates. *********/
			
			
			
			// Return messages.
			echo $return_status;
		}
		
		die();
	} // End backup_status().
	
	
	
	
	public function importbuddy() {
		
		if ( !isset( pb_backupbuddy::$classes['core'] ) ) {
			require_once( pb_backupbuddy::plugin_path() . '/classes/core.php' );
			pb_backupbuddy::$classes['core'] = new pb_backupbuddy_core();
		}
		pb_backupbuddy::$classes['core']->importbuddy(); // Outputs importbuddy to browser for download.
		
		die();
	} // End importbuddy().
	
	
	
	public function repairbuddy() {
		
		if ( !isset( pb_backupbuddy::$classes['core'] ) ) {
			require_once( pb_backupbuddy::plugin_path() . '/classes/core.php' );
			pb_backupbuddy::$classes['core'] = new pb_backupbuddy_core();
		}
		pb_backupbuddy::$classes['core']->repairbuddy(); // Outputs repairbuddy to browser for download.
		
		die();
	} // End repairbuddy().
	
	
	
	public function hash() {
		pb_backupbuddy::load();
		
		pb_backupbuddy::$ui->ajax_header();
		
		require_once( 'ajax/_hash.php' );
		
		pb_backupbuddy::$ui->ajax_footer();
		die();
		
	} // End destination_picker().
	
	
	
	public function destination_picker() {
		pb_backupbuddy::load();
		
		pb_backupbuddy::$ui->ajax_header();
		
		$mode = 'destination';
		require_once( 'ajax/_destination_picker.php' );
		
		pb_backupbuddy::$ui->ajax_footer();
		die();
		
	} // End destination_picker().
	
	
	
	public function migration_picker() {
		pb_backupbuddy::load();
		
		pb_backupbuddy::$ui->ajax_header();
		
		$mode = 'migration';
		require_once( 'ajax/_destination_picker.php' );
		
		pb_backupbuddy::$ui->ajax_footer();
		die();
		
	} // End migration_picker().
	
	
	
	/*	remote_send()
	 *	
	 *	Send backup archive to a remote destination manually. Optionally sends importbuddy.php with files.
	 *	Sends are scheduled to run in a cron and are passed to the cron.php remote_send() method.
	 *	
	 *	@return		null
	 */
	public function remote_send() {
		if ( defined( 'PB_DEMO_MODE' ) ) {
			die( 'Access denied in demo mode.' );
		}
		
		$success_output = false; // Set to true onece a leading 1 has been sent to the javascript to indicate success.
		$destination_id = pb_backupbuddy::_POST( 'destination_id' );
		if ( pb_backupbuddy::_POST( 'file' ) != 'importbuddy.php' ) {
			$backup_file = pb_backupbuddy::$options['backup_directory'] . pb_backupbuddy::_POST( 'file' );
		} else {
			$backup_file = '';
		}
		
		if ( pb_backupbuddy::_POST( 'send_importbuddy' ) == '1' ) {
			$send_importbuddy = true;
			pb_backupbuddy::status( 'details', 'Cron send to be scheduled with importbuddy sending.' );
		} else {
			$send_importbuddy = false;
			pb_backupbuddy::status( 'details', 'Cron send to be scheduled WITHOUT importbuddy sending.' );
		}
		
		
		// For Stash we will check the quota prior to initiating send.
		if ( pb_backupbuddy::$options['remote_destinations'][$destination_id]['type'] == 'stash' ) {
			// Pass off to destination handler.
			require_once( pb_backupbuddy::plugin_path() . '/destinations/bootstrap.php' );
			$send_result = pb_backupbuddy_destinations::get_info( 'stash' ); // Used to kick the Stash destination into life.
			$stash_quota = pb_backupbuddy_destination_stash::get_quota( pb_backupbuddy::$options['remote_destinations'][$destination_id] );
			//print_r( $stash_quota );
			
			if ( $backup_file != '' ) {
				$backup_file_size = filesize( $backup_file );
			} else {
				$backip_file_size = 50000;
			}
			if ( ( $backup_file_size + $stash_quota['quota_used'] ) > $stash_quota['quota_total'] ) {
				echo "You do not have enough Stash storage space to send this file. Please upgrade your Stash storage or delete files to make space.\n\n";
				
				echo 'Attempting to send file of size ' . pb_backupbuddy::$format->file_size( $backup_file_size ) . ' but you only have ' . $stash_quota['quota_available_nice'] . ' available. ';
				echo 'Currently using ' . $stash_quota['quota_used_nice'] . ' of ' . $stash_quota['quota_total_nice'] . ' (' . $stash_quota['quota_used_percent'] . '%).';
				die();
			} else {
				if ( isset( $stash_quota['quota_warning'] ) && ( $stash_quota['quota_warning'] != '' ) ) {
					echo '1Warning: ' . $stash_quota['quota_warning'] . "\n\n";
					$success_output = true;
				}
			}
			
		}
		
		
		wp_schedule_single_event( time(), pb_backupbuddy::cron_tag( 'remote_send' ), array( $destination_id, $backup_file, pb_backupbuddy::_POST( 'trigger' ), $send_importbuddy ) );
		spawn_cron( time() + 150 ); // Adds > 60 seconds to get around once per minute cron running limit.
		update_option( '_transient_doing_cron', 0 ); // Prevent cron-blocking for next item.
		
		// SEE cron.php remote_send() for sending function that we pass to via the cron above.
		
		if ( $success_output === false ) {
			echo 1;
		}
		die();
	} // End remote_send().
	
	
	
	/*	migrate_status()
	 *	
	 *	Gives the current migration status. Echos.
	 *	
	 *	@return		null
	 */
	function migrate_status() {
		
		$step = pb_backupbuddy::_POST( 'step' );
		$backup_file = pb_backupbuddy::_POST( 'backup_file' );
		$url = trim( pb_backupbuddy::_POST( 'url' ) );
		
		switch( $step ) {
			case 'step1': // Make sure backup file has been transferred properly.
				// Find last migration.
				$last_migration_key = '';
				foreach( pb_backupbuddy::$options['remote_sends'] as $send_key => $send ) { // Find latest migration send for this file.
					if ( basename( $send['file'] ) == $backup_file ) {
						if ( $send['trigger'] == 'migration' ) {
							$last_migration_key = $send_key;
						}
					}
				} // end foreach.
				$migrate_send_status = pb_backupbuddy::$options['remote_sends'][$last_migration_key]['status'];
				
				if ( $migrate_send_status == 'timeout' ) {
					$status_message = 'Status: Waiting for backup to finish uploading to server...';
					$next_step = '1';
				} elseif ( $migrate_send_status == 'failure' ) {
					$status_message = 'Status: Sending backup to server failed.';
					$next_step = '0';
				} elseif ( $migrate_send_status == 'success' ) {
					$status_message = 'Status: Success sending backup file.';
					$next_step = '2';
				}
				die( json_encode( array(
					'status_code' 		=>		$migrate_send_status,
					'status_message'	=>		$status_message,
					'next_step'			=>		$next_step,
				) ) );
				
				break;
				
			case 'step2': // Hit importbuddy file to make sure URL is correct, it exists, and extracts itself fine.
				
				$url = rtrim( $url, '/' ); // Remove trailing slash if its there.
				if ( strpos( $url, 'importbuddy.php' ) === false ) { // If no importbuddy.php at end of URL add it.
					$url .= '/importbuddy.php';
				}
				
				if ( ( false === strstr( $url, 'http://' ) ) && ( false === strstr( $url, 'https://' ) ) ) { // http or https is missing; prepend it.
					$url = 'http://' . $url;
				}
				
				$response = wp_remote_get( $url . '?api=ping', array(
						'method' => 'GET',
						'timeout' => 45,
						'redirection' => 5,
						'httpversion' => '1.0',
						'blocking' => true,
						'headers' => array(),
						'body' => null,
						'cookies' => array()
					)
				);
				
				
				if( is_wp_error( $response ) ) {
					die( json_encode( array(
						'status_code' 		=>		'failure',
						'status_message'	=>		'Status: HTTP error checking for importbuddy.php at `' . $url . '`. Error: `' . $response->get_error_message() . '`.',
						'next_step'			=>		'0',
					) ) );
				}
				
				
				if ( trim( $response['body'] ) == 'pong' ) { // Importbuddy found.
					die( json_encode( array(
						'import_url'		=>		$url . '?display_mode=embed&file=' . pb_backupbuddy::_POST( 'backup_file' ) . '&v=' . pb_backupbuddy::$options['importbuddy_pass_hash'],
						'status_code' 		=>		'success',
						'status_message'	=>		'Sucess verifying URL is valid importbuddy.php location. Continue migration below.',
						'next_step'			=>		'0',
					) ) );
				} else { // No importbuddy here.
					die( json_encode( array(
						'status_code' 		=>		'failure',
						'status_message'	=>		'<b>Error</b>: The importbuddy.php file uploaded was not found at <a href="' . $url . '">' . $url . '</a>. Please verify the URL properly matches & corresponds to the upload directory entered for this destination\'s settings.<br><br><b>Tip:</b> This error is only caused by URL not properly matching, permissions on the destination server blocking the script, or other destination server error. You may manually verify that the importbuddy.php scripts exists in the expected location on the destination server and that the script URL <a href="' . $url . '">' . $url . '</a> properly loads the ImportBuddy tool. You may manually upload importbuddy.php and the backup ZIP file to the destination server & navigating to its URL in your browser for an almost-as-quick alternative.',
						'next_step'			=>		'0',
					) ) );
				}
				
				break;
				
			default:
				echo 'Invalid migrate_status() step: `' . pb_backupbuddy::_POST( 'step' ) . '`.';
				break;
		} // End switch on action.
		
		die();
		
	} // End migrate_status().
	
	
	
	/*	icicle()
	 *	
	 *	Builds and returns graphical directory size listing. Echos.
	 *	
	 *	@return		null
	 */
	public function icicle() {
		pb_backupbuddy::set_greedy_script_limits(); // Building the directory tree can take a bit.
		
		if ( !isset( pb_backupbuddy::$classes['core'] ) ) {
			require_once( pb_backupbuddy::plugin_path() . '/classes/core.php' );
			pb_backupbuddy::$classes['core'] = new pb_backupbuddy_core();
		}
		$response = pb_backupbuddy::$classes['core']->build_icicle( ABSPATH, ABSPATH, '', -1 );
		
		echo $response[0];
		die();
	} // End icicle().
	
	
	
	
	
	public function remote_delete() {
		
		pb_backupbuddy::verify_nonce(); // Security check.
		
		// Destination ID.
		$destination_id = pb_backupbuddy::_GET( 'pb_backupbuddy_destinationid' );
		
		// Delete the destination.
		require_once( pb_backupbuddy::plugin_path() . '/destinations/bootstrap.php' );
		$delete_response = pb_backupbuddy_destinations::delete_destination( $destination_id, true );
		
		// Response.
		if ( $delete_response !== true ) { // Some kind of error so just echo it.
			echo 'Error #544558: `' . $delete_response . '`.';
		} else { // Success.
			echo 'Destination deleted.';
		}
		
		die();
			
	} // End remote_delete().
	
	
	
	/*	remote_test()
	 *	
	 *	Remote destination testing. Echos.
	 *	
	 *	@return		null
	 */
	function remote_test() {
		
		if ( defined( 'PB_DEMO_MODE' ) ) {
			die( 'Access denied in demo mode.' );
		}
		
		
		require_once( pb_backupbuddy::plugin_path() . '/destinations/bootstrap.php' );
		
		$form_settings = array();
		foreach( pb_backupbuddy::_POST() as $post_id => $post ) {
			if ( substr( $post_id, 0, 15 ) == 'pb_backupbuddy_' ) {
				$id = substr( $post_id, 15 );
				if ( $id != '' ) {
					$form_settings[$id] = $post;
				}
			}
		}
		
		$test_result = pb_backupbuddy_destinations::test( $form_settings );
		
		
		if ( $test_result === true ) {
			echo 'Test successful.';
		} else {
			echo "Test failed.\n\n";
			echo $test_result;
		}
		
		die();
		
	} // End remote_test().
	
	
	
	/*	remote_save()
	 *	
	 *	Remote destination saving.
	 *	
	 *	@return		null
	 */
	public function remote_save() {
		
		pb_backupbuddy::verify_nonce();
		
		
		require_once( pb_backupbuddy::plugin_path() . '/destinations/bootstrap.php' );
		$settings_form = pb_backupbuddy_destinations::configure( array( 'type' => pb_backupbuddy::_POST( 'pb_backupbuddy_type' ) ), 'save' );
		$save_result = $settings_form->process();
		
				
		$destination_id = trim( pb_backupbuddy::_GET( 'pb_backupbuddy_destinationid' ) );
		

		if ( count( $save_result['errors'] ) == 0 ) {
			
			if ( $destination_id == 'NEW' ) { // ADD NEW.
			
				// Dropbox Kludge. Sigh.
				$save_result['data']['token'] = pb_backupbuddy::$options['dropboxtemptoken'];
				
				pb_backupbuddy::$options['remote_destinations'][] = $save_result['data'];
				
				pb_backupbuddy::save();
				echo 'Destination Added.';
			} elseif ( !isset( pb_backupbuddy::$options['remote_destinations'][$destination_id] ) ) { // EDITING NONEXISTANT.
				echo 'Error #54859. Invalid destination ID.';
			} else { // EDITING EXISTING -- Save!
				pb_backupbuddy::$options['remote_destinations'][$destination_id] = $save_result['data'];
				//echo '<pre>' . print_r( pb_backupbuddy::$options['remote_destinations'][$destination_id], true ) . '</pre>';
				
				pb_backupbuddy::save();
				echo 'Settings saved.';
			}
			
		} else {
			echo "Error saving settings.\n\n";
			echo implode( "\n", $save_result['errors'] );
		}
		die();
		
	} // End remote_save().
	
	
	
	/*	refresh_site_size()
	 *	
	 *	Server info page site size refresh. Echos out the new site size (pretty version).
	 *	
	 *	@return		null
	 */
	public function refresh_site_size() {
		if ( !isset( pb_backupbuddy::$classes['core'] ) ) {
			require_once( pb_backupbuddy::plugin_path() . '/classes/core.php' );
			pb_backupbuddy::$classes['core'] = new pb_backupbuddy_core();
		}
		
		$site_size = pb_backupbuddy::$classes['core']->get_site_size(); // array( site_size, site_size_sans_exclusions ).
		
		echo pb_backupbuddy::$format->file_size( $site_size[0] );
		
		die();
	} // End refresh_site_size().
	
	
	
	/*	refresh_site_size_excluded()
	 *	
	 *	Server info page site size (sans exclusions) refresh. Echos out the new site size (pretty version).
	 *	
	 *	@return		null
	 */
	public function refresh_site_size_excluded() {
		if ( !isset( pb_backupbuddy::$classes['core'] ) ) {
			require_once( pb_backupbuddy::plugin_path() . '/classes/core.php' );
			pb_backupbuddy::$classes['core'] = new pb_backupbuddy_core();
		}
		
		$site_size = pb_backupbuddy::$classes['core']->get_site_size(); // array( site_size, site_size_sans_exclusions ).
		
		echo pb_backupbuddy::$format->file_size( $site_size[1] );
		
		die();
	} // End refresh_site_size().
	
	
	
	/*	refresh_database_size()
	 *	
	 *	Server info page database size refresh. Echos out the new site size (pretty version).
	 *	
	 *	@return		null
	 */
	public function refresh_database_size() {
		if ( !isset( pb_backupbuddy::$classes['core'] ) ) {
			require_once( pb_backupbuddy::plugin_path() . '/classes/core.php' );
			pb_backupbuddy::$classes['core'] = new pb_backupbuddy_core();
		}
		
		$database_size = pb_backupbuddy::$classes['core']->get_database_size(); // array( database_size, database_size_sans_exclusions ).
		
		echo pb_backupbuddy::$format->file_size( $database_size[1] );
		
		die();
	} // End refresh_site_size().
	
	
	
	/*	refresh_database_size_excluded()
	 *	
	 *	Server info page database size (sans exclusions) refresh. Echos out the new site size (pretty version).
	 *	
	 *	@return		null
	 */
	public function refresh_database_size_excluded() {
		if ( !isset( pb_backupbuddy::$classes['core'] ) ) {
			require_once( pb_backupbuddy::plugin_path() . '/classes/core.php' );
			pb_backupbuddy::$classes['core'] = new pb_backupbuddy_core();
		}
		
		$database_size = pb_backupbuddy::$classes['core']->get_database_size(); // array( database_size, database_size_sans_exclusions ).
		
		echo pb_backupbuddy::$format->file_size( $database_size[1] );
		
		die();
	} // End refresh_site_size().
	
	
	
	/*	exclude_tree()
	 *	
	 *	Directory exclusion tree for settings page.
	 *	
	 *	@return		null
	 */
	function exclude_tree() {
		$root = ABSPATH . urldecode( pb_backupbuddy::_POST( 'dir' ) );
		
		if( file_exists( $root ) ) {
			$files = scandir( $root );
			
			natcasesort( $files );
			
			// Sort with directories first.
			$sorted_files = array(); // Temporary holder for sorting files.
			$sorted_directories = array(); // Temporary holder for sorting directories.
			foreach( $files as $file ) {
				if( is_file( $root . $file ) ) {
					array_push( $sorted_files, $file );
				} else {
					array_unshift( $sorted_directories, $file );
				}
			}
			$files = array_reverse( $sorted_directories ) + $sorted_files;
			unset( $sorted_files );
			unset( $sorted_directories );
			unset( $file );
			
			
			if( count( $files ) > 2 ) { /* The 2 accounts for . and .. */
				echo '<ul class="jqueryFileTree" style="display: none;">';
				foreach( $files as $file ) {
					if( file_exists( $root . $file ) && ( $file != '.' ) && ( $file != '..' ) ) {
						if ( is_dir( $root . $file ) ) {
							echo '<li class="directory collapsed"><a href="#" rel="' . htmlentities( str_replace( ABSPATH, '', $root ) . $file) . '/">' . htmlentities($file) . ' <img src="' . pb_backupbuddy::plugin_url() . '/images/bullet_delete.png" style="vertical-align: -3px;" /></a></li>';
						} else {
							echo '<li class="file collapsed"><a href="#" rel="' . htmlentities( str_replace( ABSPATH, '', $root ) . $file) . '">' . htmlentities($file) . ' <img src="' . pb_backupbuddy::plugin_url() . '/images/bullet_delete.png" style="vertical-align: -3px;" /></a></li>';
						}
					}
				}
				echo '</ul>';
			} else {
				echo '<ul class="jqueryFileTree" style="display: none;">';
				echo '<li><a href="#" rel="' . htmlentities( pb_backupbuddy::_POST( 'dir' ) . 'NONE' ) . '"><i>Empty Directory ...</i></a></li>';
				echo '</ul>';
			}
		} else {
			echo 'Error #1127555. Unable to read site root.';
		}
		
		die();
	} // End exclude_tree().
	
	
	
	/*	download_archive()
	 *	
	 *	Handle allowing download of archive.
	 *	
	 *	@param		
	 *	@return		
	 */
	public function download_archive() {
		
		if ( is_multisite() && !current_user_can( 'manage_network' ) ) { // If a Network and NOT the superadmin must make sure they can only download the specific subsite backups for security purposes.
			// Load core if it has not been instantiated yet.
			if ( !isset( pb_backupbuddy::$classes['core'] ) ) {
				require_once( pb_backupbuddy::plugin_path() . '/classes/core.php' );
				pb_backupbuddy::$classes['core'] = new pb_backupbuddy_core();
			}
			
			// Only allow downloads of their own backups.
			if ( !strstr( pb_backupbuddy::_GET( 'backupbuddy_backup' ), pb_backupbuddy::$classes['core']->backup_prefix() ) ) {
				die( 'Access Denied. You may only download backups specific to your Multisite Subsite. Only Network Admins may download backups for another subsite in the network.' );
			}
		}
		
		// Make sure file exists we are trying to get.
		if ( !file_exists( pb_backupbuddy::$options['backup_directory'] . pb_backupbuddy::_GET( 'backupbuddy_backup' ) ) ) { // Does not exist.
			die( 'The requested backup file does not exist. It may have already been deleted.' );
		}
		
		$download_url = site_url() . '/wp-content/uploads/backupbuddy_backups/' . pb_backupbuddy::_GET( 'backupbuddy_backup' );
		
		if ( pb_backupbuddy::$options['lock_archives_directory'] == '1' ) { // High security mode.
			
			if ( file_exists( pb_backupbuddy::$options['backup_directory'] . '.htaccess' ) ) {
				$unlink_status = @unlink( pb_backupbuddy::$options['backup_directory'] . '.htaccess' );
				if ( $unlink_status === false ) {
					die( 'Error #844594. Unable to temporarily remove .htaccess security protection on archives directory to allow downloading. Please verify permissions of the BackupBuddy archives directory or manually download via FTP.' );
				}
			}
			
			header( 'Location: ' . $download_url );
			ob_clean();
			flush();
			sleep( 8 ); // Wait 8 seconds before creating security file.
			
			$htaccess_creation_status = @file_put_contents( pb_backupbuddy::$options['backup_directory'] . '.htaccess', 'deny from all' );
			if ( $htaccess_creation_status === false ) {
				die( 'Error #344894545. Security Warning! Unable to create security file (.htaccess) in backups archive directory. This file prevents unauthorized downloading of backups should someone be able to guess the backup location and filenames. This is unlikely but for best security should be in place. Please verify permissions on the backups directory.' );
			}
			
		} else { // Normal mode.
			header( 'Location: ' . $download_url );
		}
		
		
		
		die();
	} // End download_archive().
	
	
	
	// Server info page phpinfo button.
	public function phpinfo() {
		phpinfo();
		die();
	}
	
	
	
	/*	set_backup_note()
	 *	
	 *	Used for setting a note to a backup archive.
	 *	
	 *	@return		null
	 */
	public function set_backup_note() {
		if ( !isset( pb_backupbuddy::$classes['zipbuddy'] ) ) {
			require_once( pb_backupbuddy::plugin_path() . '/lib/zipbuddy/zipbuddy.php' );
			pb_backupbuddy::$classes['zipbuddy'] = new pluginbuddy_zipbuddy( pb_backupbuddy::$options['backup_directory'] );
		}
		
		$backup_file = pb_backupbuddy::$options['backup_directory'] . pb_backupbuddy::_POST( 'backup_file' );
		$note = pb_backupbuddy::_POST( 'note' );
		$note = ereg_replace( "[[:space:]]+", ' ', $note );
		$note = ereg_replace( "[^[:print:]]", '', $note );
		$note = htmlentities( substr( $note, 0, 200 ) );
		
		
		// Returns true on success, else the error message.
		$comment_result = pb_backupbuddy::$classes['zipbuddy']->set_comment( $backup_file, $note );
		
		
		if ( $comment_result !== true ) {
			echo $comment_result;
		} else {
			echo '1';
		}
		
		// Even if we cannot save the note into the archive file, store it in internal settings.
		$serial = pb_backupbuddy::$classes['core']->get_serial_from_file( $backup_file );
		pb_backupbuddy::$options['backups'][$serial]['integrity']['comment'] = $note;
		pb_backupbuddy::save();
		
		
		die();
	} // End set_backup_note().
	
	
	
	public function integrity_status() {
		$serial = pb_backupbuddy::_GET( 'serial' );
		pb_backupbuddy::load();
		pb_backupbuddy::$ui->ajax_header();
		
		echo '<h3>Integrity Technical Details</h3>';
		echo '<textarea style="width: 100%; height: 175px;" wrap="off">';
		foreach( pb_backupbuddy::$options['backups'][$serial]['integrity'] as $item_name => $item_value ) {
			$item_value = str_replace( '<br />', '<br>', $item_value );
			$item_value = str_replace( '<br><br>', '<br>', $item_value );
			$item_value = str_replace( '<br>', "\n     ", $item_value );
			echo $item_name . ' => ' . $item_value . "\n";
		}
		echo '</textarea><br><br><b>Note:</b> It is normal to see several "file not found" entries as BackupBuddy checks for expected files in multiple locations, expecting to only find each file once in one of those locations.<br><br>If you are encountering problems providing this information to support may assist in troubleshooting.';
		
		pb_backupbuddy::$ui->ajax_footer();
		die();
		
	} // End integrity_status().
	
	
	
	/*	db_check()
	 *	
	 *	Check database integrity on a specific table. Used on server info page.
	 *	
	 *	@return		null
	 */
	public function db_check() {
		
		$table = base64_decode( pb_backupbuddy::_GET( 'table' ) );
		$check_level = 'MEDIUM';
		
		pb_backupbuddy::$ui->ajax_header();
		echo '<h2>Database Table Check</h2>';
		echo 'Checking table `' . $table . '` using ' . $check_level . ' scan...<br><br>';
		$result = mysql_query( "CHECK TABLE `" . mysql_real_escape_string( $table ) . "` " . $check_level );
		echo '<b>Results:</b><br><br>';
		echo '<table class="widefat">';
		while( $rs = mysql_fetch_array( $result ) ) {
			echo '<tr>';
			echo '<td>' . $rs['Msg_type'] . '</td>';
			echo '<td>' . $rs['Msg_text'] . '</td>';
			echo '</tr>';
		}
		echo '</table>';
		pb_backupbuddy::$ui->ajax_footer();
		
		die();
		
	} // End db_check().
	
	
	
	/*	db_repair()
	 *	
	 *	Repair specific table. Used on server info page.
	 *	
	 *	@return		null
	 */
	public function db_repair() {
		
		$table = base64_decode( pb_backupbuddy::_GET( 'table' ) );
		
		pb_backupbuddy::$ui->ajax_header();
		echo '<h2>Database Table Repair</h2>';
		echo 'Repairing table `' . $table . '`...<br><br>';
		$result = mysql_query( "REPAIR TABLE `" . mysql_real_escape_string( $table ) . "`" );
		echo '<b>Results:</b><br><br>';
		echo '<table class="widefat">';
		while( $rs = mysql_fetch_array( $result ) ) {
			echo '<tr>';
			echo '<td>' . $rs['Msg_type'] . '</td>';
			echo '<td>' . $rs['Msg_text'] . '</td>';
			echo '</tr>';
		}
		echo '</table>';
		pb_backupbuddy::$ui->ajax_footer();
		
		die();
		
	} // End db_repair().
	
	
	/*	php_max_runtime_test()
	 *	
	 *	Tests the ACTUAL PHP maximum runtime of the server by echoing and logging to the status log the seconds elapsed.
	 *	
	 *	@param		int		$stop_time_limit		Time after which the test will stop if it is still running.
	 *	@return		null
	 */
	public function php_max_runtime_test() {
		
		$stop_time_limit = 240;
		pb_backupbuddy::set_greedy_script_limits(); // Crank it up for the test!
		
		$m = "# Starting BackupBuddy PHP Max Execution Time Tester. Determines what your ACTUAL limit is (usually shorter than the server reports so now you can find out the truth!). Stopping test if it gets to `{$stop_time_limit}` seconds. When your browser stops loading this page then the script has most likely timed out at your actual PHP limit.";
		pb_backupbuddy::status( 'details', $m );
		echo $m . "<br>\n";
		
		$t = 0; // Time = 0;
		while( $t < $stop_time_limit ) {
			
			pb_backupbuddy::status( 'details', 'Max PHP Execution Time Test status: ' . $t );
			echo $t . "<br>\n";
			sleep( 1 );
			flush();
			$t++;
			
		}
		
		$m = '# Ending BackupBuddy PHP Max Execution Time The test was stopped as the test time limit of ' . $stop_time_limit . ' seconds.';
		pb_backupbuddy::status( 'details', $m );
		echo $m . "<br>\n";
		die();
	} // End php_max_runtime_test().
	
	
	
	public function disalert() {
		$unique_id = pb_backupbuddy::_POST( 'unique_id' );
		
		pb_backupbuddy::$options['disalerts'][$unique_id] = time();
		pb_backupbuddy::save();
		
		die('1');
		
	} // End disalert().
	
	
	
}
?>