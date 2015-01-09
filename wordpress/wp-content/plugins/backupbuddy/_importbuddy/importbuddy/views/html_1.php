<?php
$ITXAPI_KEY = 'ixho7dk0p244n0ob';
$ITXAPI_URL = 'http://api.ithemes.com';


$step = '1';


if ( pb_backupbuddy::$options['password'] == pb_backupbuddy::_GET( 'v' ) ) { // Hash passed for magic migration.
	$need_auth = false;
	$page_title = 'Choose your backup file';

	pb_backupbuddy::$options['password_verify'] = pb_backupbuddy::$options['password'];
	pb_backupbuddy::save();

} else { // No hash passed; authenticate normally.
	
	// Stash set values using framework settings form. Other forms may use this in future.
	if ( isset( $_POST['pb_backupbuddy_password'] ) ) {
		$_POST['password'] = $_POST['pb_backupbuddy_password'];
	}
	if ( isset( $_POST['pb_backupbuddy_options'] ) ) {
		$_POST['options'] = $_POST['pb_backupbuddy_options'];
	}
	
	if (
			( pb_backupbuddy::_POST( 'password' ) == '' )
			||
			( pb_backupbuddy::$options['password'] != md5( pb_backupbuddy::_POST( 'password' ) ) )
		) { // LOGIN FAILURE OR MISSING.
		$need_auth = true;
		$page_title = 'Authentication Required';
	} else { // LOGIN SUCCESS.
		$need_auth = false;
		$page_title = 'Choose your backup file';
	
		pb_backupbuddy::$options['password_verify'] = pb_backupbuddy::$options['password'];
		pb_backupbuddy::save();
	}
}
require_once( '_header.php' );


upload(); // Handle any uploading of a backup file.

// Set variables needed by script for this page.
global $detected_max_execution_time;
$detected_max_execution_time = str_ireplace( 's', '', ini_get( 'max_execution_time' ) );
if ( is_numeric( $detected_max_execution_time ) ) {
	$detected_max_execution_time = $detected_max_execution_time;
}
$backup_archives = get_archives_list();
//print_r( $backup_archives );
$wordpress_exists = wordpress_exists();




echo pb_backupbuddy::$classes['import']->status_box( 'Step 1 debugging information for ImportBuddy ' . pb_backupbuddy::settings( 'version' ) . ' from BackupBuddy v' . pb_backupbuddy::$options['bb_version'] . '...', true );
?>

<div class="wrap">

<?php
if ( $need_auth !== false ) { // Need authentication.
	if ( pb_backupbuddy::_POST( 'password' ) != '' ) {
		pb_backupbuddy::alert( 'Invalid password. Please enter the password you provided within BackupBuddy Settings.' );
		echo '<br>';
	}
	?>
	Enter your ImportBuddy password to continue.
	<br><br>
	<form method="post" action="?step=1">
		<input type="password" name="password">
		<input type="submit" name="submit" value="Authenticate" class="button">
	</form>
	
	</div><!-- /wrap -->
<?php
} else {
	
	
	if ( pb_backupbuddy::_GET( 'file' ) == '' ) {
		echo '<p>Select the BackupBuddy backup file you would like to import or migrate.</p>';
	}
	?>
	
	<p>
		Throughout the restore process you may hover over question marks
		<?php pb_backupbuddy::tip( 'This is an example help tip. Hover over these for additional help.' ); ?> 
		for additional help. For support see the <a href="http://ithemes.com/codex/page/BackupBuddy" target="_blank">Knowledge Base</a>
		or <a href="http://pluginbuddy.com/support/" target="_blank">Support Forum</a>.
	</p>
	
	
	<?php
	
	
	echo '<br><br>';
	
	
	
	function pb_advanced_options() {
		global $detected_max_execution_time;
		?>
		<div style="margin-left: 15px; font-size: 16px;">
			<span class="toggle button-secondary" id="serverinfo">Server Information</span> <span class="toggle button-secondary" id="advanced">Advanced Configuration Options</span>
			<div id="toggle-advanced" class="toggled" style="margin-top: 12px;">
				<?php
				//pb_backupbuddy::alert( 'WARNING: These are advanced configuration options.', 'Use caution as improper use could result in data loss or other difficulties.' );
				?>
				<b>WARNING:</b> Improper use of Advanced Options could result in data loss.<br>
				&nbsp;&nbsp;&nbsp;&nbsp;Leave as is unless you understand what these settings do.
				<br><br>
				
				
				
				<input type="checkbox" name="wipe_database" onclick="
					if ( !confirm( 'WARNING! WARNING! WARNING! WARNING! WARNING! \n\nThis will clear any existing WordPress installation or other content in this database that matches the new site database prefix you specify. This could result in loss of posts, comments, pages, settings, and other software data loss. Verify you are using the exact database settings you want to be using. PluginBuddy & all related persons hold no responsibility for any loss of data caused by using this option. \n\n Are you sure you want to do this and potentially wipe existing data matching the specified table prefix? \n\n WARNING! WARNING! WARNING! WARNING! WARNING!' ) ) {
						return false;
					}
				" /> Wipe database tables that match new prefix on import. <span style="color: orange;">Use caution.</span> <?php pb_backupbuddy::tip( 'WARNING: Checking this box will have this script clear ALL existing data from your database that match the new database prefix prior to import, possibly including non-WordPress data. This is useful if you are restoring over an existing site or repairing a failed migration. Use caution when using this option and double check the destination prefix. Use with caution. This cannot be undone.' ); ?><br>
				
				<input type="checkbox" value="on" name="wipe_database_all" onclick="
					if ( !confirm( 'WARNING! WARNING! WARNING! WARNING! WARNING! \n\nThis will clear any existing WordPress installation or other content in this database that matches the new site database prefix you specify. This could result in loss of posts, comments, pages, settings, and other software data loss. Verify you are using the exact database settings you want to be using. PluginBuddy & all related persons hold no responsibility for any loss of data caused by using this option. \n\n Are you sure you want to do this and potentially wipe ALL existing data? \n\n WARNING! WARNING! WARNING! WARNING! WARNING!' ) ) {
						return false;
					}
				" /> Wipe <b>ALL</b> database tables, erasing <b>ALL</b> database content. <span style="color: red;">Use extreme caution.</span> <?php pb_backupbuddy::tip( 'WARNING: Checking this box will have this script clear ALL existing data from your database, period, including non-WordPress data found. Use with extreme caution, verifying you are using the exact correct database settings. This cannot be undone.' ); ?><br>
				
				
				<input type="checkbox" value="on" name="ignore_sql_errors"> Ignore existing WordPress tables and import (merge tables) anyways. <?php pb_backupbuddy::tip( 'When checked ImportBuddy will allow importing database tables that have the same name as existing tables. This results in a merge of the existing data with the imported database being merged. Note that this is does NOT update existing data and only ADDS new database table rows. All other SQL conflict errors will be suppressed as well. Use this feature with caution.' ); ?><br>
				<input type="checkbox" value="on" name="skip_files"> Skip zip file extraction. <?php pb_backupbuddy::tip( 'Checking this box will prevent extraction/unzipping of the backup ZIP file.  You will need to manually extract it either on your local computer then upload it or use a server-based tool such as cPanel to extract it. This feature is useful if the extraction step is unable to complete for some reason.' ); ?><br>
				<input type="checkbox" value="on" name="skip_database_import"> Skip import of database. <br>
				<input type="checkbox" value="on" name="mysqlbuddy_compatibility"> Force database import compatibility (pre-v3.0) mode. <br>
				<input type="checkbox" value="on" name="skip_database_migration"> Skip migration of database. <br>
				<input type="checkbox" value="on" name="skip_htaccess"> Skip migration of .htaccess file. <br>
				<!-- TODO: <input type="checkbox" name="merge_databases" /> Ignore existing WordPress data & merge database.<?php pb_backupbuddy::tip( 'This may result in data conflicts, lost database data, or incomplete restores.' ); ?></a><br> -->
				<input type="checkbox" value="on" name="force_compatibility_medium" /> Force medium speed compatibility mode (ZipArchive). <br>
				<input type="checkbox" value="on" name="force_compatibility_slow" /> Force slow speed compatibility mode (PCLZip). <br>
				<?php //<input type="checkbox" name="force_high_security"> Force high security on a normal security backup<br> ?>
				<input type="checkbox" value="on" name="show_php_warnings" /> Show detailed PHP warnings. <br>
				<br>
				PHP Maximum Execution Time: <input type="text" name="max_execution_time" value="<?php echo $detected_max_execution_time; ?>" size="5"> seconds. <?php pb_backupbuddy::tip( 'The maximum allowed PHP runtime. If your database import step is timing out then lowering this value will instruct the script to limit each `chunk` to allow it to finish within this time period.' ); ?>
				<br>
				Error Logging to importbuddy.txt: <select name="log_level">
					<option value="0">None</option>
					<option value="1" selected>Errors Only (default)</option>
					<option value="2">Errors & Warnings</option>
					<option value="3">Everything (debug mode)</option>
				</select> <?php pb_backupbuddy::tip( 'Errors and other debugging information will be written to importbuddy.txt in the same directory as importbuddy.php.  This is useful for debugging any problems encountered during import.  Support may request this file to aid in tracking down any problems or bugs.' ); ?>
			</div>
			<?php
			echo '<div id="toggle-serverinfo" class="toggled" style="margin-top: 12px;">';
			$server_info_file = ABSPATH . 'importbuddy/controllers/pages/server_info.php';
			if ( file_exists( $server_info_file ) ) {
				require_once( $server_info_file );
			} else {
				echo '{Error: Missing server tools file `' . $server_info_file . '`.}';
			}
			echo '</div>';
			?>
		</div>
		<?php
	}
	
	
	
	
	if ( pb_backupbuddy::_GET( 'file' ) != '' ) {
		echo '
		<div style="padding: 15px; background: #FFFFFF;">Restoring from backup <i>' . htmlentities( pb_backupbuddy::_GET( 'file' ) ) . '</i></div>
		<form action="?step=2" method="post">
			<input type="hidden" name="options" value="' . htmlspecialchars( serialize( pb_backupbuddy::$options ) ) . '" />
			<input type="hidden" name="file" value="' . htmlspecialchars( pb_backupbuddy::_GET( 'file' ) ) . '">
		';
		pb_advanced_options();
		
	} else {
		?>
		
		<div id="pluginbuddy-tabs">
			<ul>
				<li><a href="#pluginbuddy-tabs-server"><span>Server</span></a></li>
				<li><a href="#pluginbuddy-tabs-upload"><span>Upload</span></a></li>
				<li><a href="#pluginbuddy-tabs-stash"><span>Stash (beta)</span></a></li>
			</ul>
			<div id="pluginbuddy-tabs-stash">
				<div class="tabs-item">
					
					<?php
					
						//print_r( $_POST );
					
						$credentials_form = new pb_backupbuddy_settings( 'pre_settings', false, 'step=1&upload=stash#pluginbuddy-tabs-stash' ); // name, savepoint|false, additional querystring
						
						$credentials_form->add_setting( array(
							'type'		=>		'hidden',
							'name'		=>		'password',
							'default'	=>		htmlentities( pb_backupbuddy::_POST( 'password' ) ),
						) );
						$credentials_form->add_setting( array(
							'type'		=>		'hidden',
							'name'		=>		'options',
							'default'	=>		htmlspecialchars( serialize( pb_backupbuddy::$options ) ),
						) );
						
						$credentials_form->add_setting( array(
							'type'		=>		'text',
							'name'		=>		'itxapi_username',
							'title'		=>		__( 'iThemes username', 'it-l10n-backupbuddy' ),
							'tip'		=>		__( '[Example: kerfuffle] - Your iThemes.com / PluginBuddy membership username.', 'it-l10n-backupbuddy' ),
							'rules'		=>		'required|string[1-45]',
						) );
						$credentials_form->add_setting( array(
							'type'		=>		'password',
							'name'		=>		'itxapi_password_raw',
							'title'		=>		__( 'iThemes password', 'it-l10n-backupbuddy' ),
							'tip'		=>		__( '[Example: 48dsds!s08K%x2s] - Your iThemes.com / PluginBuddy membership password.', 'it-l10n-backupbuddy' ),
							'rules'		=>		'required|string[1-45]',
						) );
						
						$settings_result = $credentials_form->process();
						$login_welcome = __( 'Log in with your iThemes.com member account to begin.', 'it-l10n-backupbuddy' );
						
						if ( count( $settings_result ) == 0 ) { // No form submitted.
							
							echo $login_welcome;
							$credentials_form->display_settings( 'Submit' );
						} else { // Form submitted.
							if ( count( $settings_result['errors'] ) > 0 ) { // Form errors.
								echo $login_welcome;
								
								pb_backupbuddy::alert( implode( '<br>', $settings_result['errors'] ) );
								$credentials_form->display_settings( 'Submit' );
								
							} else { // No form errors; process!
								
								
								$itx_helper_file = dirname( dirname( __FILE__ ) ) . '/classes/class.itx_helper.php';
								require_once( $itx_helper_file );
								
								$itxapi_username = $settings_result['data']['itxapi_username'];
								$itxapi_password = ITXAPI_Helper::get_password_hash( $itxapi_username, $settings_result['data']['itxapi_password_raw'] ); // Generates hash for use as password for API.
								
								
								$requestcore_file = dirname( dirname( __FILE__ ) ) . '/lib/requestcore/requestcore.class.php';
								require_once( $requestcore_file );
								
								
								$stash = new ITXAPI_Helper( $ITXAPI_KEY, $ITXAPI_URL, $itxapi_username, $itxapi_password );
								
								$files_url = $stash->get_files_url();
								
								$request = new RequestCore( $files_url );
								$response = $request->send_request(true);
								
								// See if the request was successful.
								if(!$response->isOK())
									pb_backupbuddy::status( 'error', 'Stash request for files failed.' );
								
								// See if we got a json response.
								if(!$stash_files = json_decode($response->body, true))
									pb_backupbuddy::status( 'error', 'Stash did not get valid json response.' );
								
								// Finally see if the API returned an error.
								if(isset($stash_files['error'])) {            
									if ( $stash_files['error']['code'] == '3002' ) {
										pb_backupbuddy::alert( 'Invalid iThemes.com Member account password. Please verify your password. <a href="http://ithemes.com/member/member.php" target="_new">Forget your password?</a>' );
									} else {
										pb_backupbuddy::alert( implode( ' - ', $stash_files['error'] ) );
									}
									
									$credentials_form->display_settings( 'Submit' );
								} else { // NO ERRORS
									
									/*
									echo '<pre>';
									print_r( $stash_files );
									echo '</pre>';
									*/
									
									$backup_list_temp = array();
									foreach( $stash_files['files'] as $stash_file ) {
										$file = $stash_file['filename'];
										$url = $stash_file['link'];
										$size = $stash_file['size'];
										$modified = $stash_file['last_modified'];
										
										if ( substr( $file, 0, 3 ) == 'db/' ) {
											$backup_type = 'Database';
										} elseif ( substr( $file, 0, 5 ) == 'full/' ) {
											$backup_type = 'Full';
										} elseif( $file == 'importbuddy.php' ) {
											$backup_type = 'ImportBuddy Tool';
										} else {
											if ( stristr( $file, '/db/' ) !== false ) {
												$backup_type = 'Database';
											} elseif( stristr( $file, '/full/' ) !== false ) {
												$backup_type = 'Full';
											} else {
												$backup_type = 'Unknown';
											}
										}
										
										$backup_list_temp[ $modified ] = array(
											$url,
											$file,
											pb_backupbuddy::$format->date( pb_backupbuddy::$format->localize_time( $modified ) ) . '<br /><span class="description">(' . pb_backupbuddy::$format->time_ago( $modified ) . ' ago)</span>',
											pb_backupbuddy::$format->file_size( $size ),
											$backup_type,
										);
									}
									
									krsort( $backup_list_temp );
									
									$backup_list = array();
									foreach( $backup_list_temp  as $backup_item ) {
										$backup_list[ $backup_item[0] ] = array(
											$backup_item[1],
											$backup_item[2],
											$backup_item[3],
											$backup_item[4],
											'<form action="?step=1#pluginbuddy-tabs-server" method="POST">
												<input type="hidden" name="password" value="' . htmlentities( pb_backupbuddy::_POST( 'password' ) ) . '">
												<input type="hidden" name="upload" value="stash">
												<input type="hidden" name="options" value="' . htmlspecialchars( serialize( pb_backupbuddy::$options ) ) . '">
												<input type="hidden" name="link" value="' . $backup_item[0] . '">
												<input type="hidden" name="itxapi_username" value="' . $itxapi_username . '">
												<input type="hidden" name="itxapi_password" value="' . $itxapi_password . '">
												<input type="submit" name="submit" value="Import" class="button-primary">
											</form>
											'
										);
									}
									unset( $backup_list_temp );
									
									
									// Render table listing files.
									if ( count( $backup_list ) == 0 ) {
										echo '<b>';
										_e( 'You have not sent any backups to Stash yet (or files are still transferring).', 'it-l10n-backupbuddy' );
										echo '</b>';
									} else {
										echo 'Select a backup to import from Stash (beta feature):<br><br>';
										pb_backupbuddy::$ui->list_table(
											$backup_list,
											array(
												//'action'		=>	pb_backupbuddy::page_url() . '&custom=remoteclient&destination_id=' . htmlentities( pb_backupbuddy::_GET( 'destination_id' ) ) . '&remote_path=' . htmlentities( pb_backupbuddy::_GET( 'remote_path' ) ),
												'columns'		=>	array( 'Backup File', 'Uploaded <img src="' . pb_backupbuddy::plugin_url() . '/images/sort_down.png" style="vertical-align: 0px;" title="Sorted most recent first">', 'File Size', 'Type', '&nbsp;' ),
												'css'			=>		'width: 100%;',
											)
										);
									}
									
									
									
									if ( $stash_files === false ) {
										$credentials_form->display_settings( 'Submit' );
									}
								} // end no errors getting file info from API.
								
							}
							
						} // end form submitted.
					?>
					
				</div>
			</div>
			<div class="tabs-borderwrap">
				
				<div id="pluginbuddy-tabs-upload">
					<div class="tabs-item">
						<?php
						if ( pb_backupbuddy::$options['password'] == '#PASSWORD#' ) {
							echo 'To prevent unauthorized file uploads an importbuddy password must be configured and properly entered to use this feature.';
						} else {
						?>
						<form enctype="multipart/form-data" action="?step=1" method="POST">
							<input type="hidden" name="password" value="<?php echo htmlentities( pb_backupbuddy::_POST( 'password' ) ); ?>">
							<input type="hidden" name="upload" value="local">
							<input type="hidden" name="options" value="<?php echo htmlspecialchars( serialize( pb_backupbuddy::$options ) ); ?>'" />
							Choose backup to upload: &nbsp; 
							<input name="file" type="file" />
							<br><br>
							<input type="submit" value="Upload" class="toggle button">
						</form>
						<?php
						}
						?>
					</div>
				</div>
				
				<div id="pluginbuddy-tabs-server">
					<div class="tabs-item">
						<?php
						if ( empty( $backup_archives ) ) { // No backups found.
							pb_backupbuddy::alert( '<b>No BackupBuddy Zip backup found in directory</b> - 
								You must upload a backup file by FTP (into the same directory as this importbuddy.php file), the upload tab, or import from Stash via the Stash tab above to continue.
								Do not rename the backup file. If you manually extracted/unzipped, upload the backup file,
								select it, then select <i>Advanced Troubleshooting Options</i> & click <i>Skip Zip Extraction</i>. Refresh this page once you have uploaded the backup.' );
						} else { // Found one or more backups.
							?>
								<form action="?step=2" method="post">
									<input type="hidden" name="options" value="<?php echo htmlspecialchars( serialize( pb_backupbuddy::$options ) ); ?>'" />
							<?php
							echo '<div class="backup_select_text">Select from your stored backups ';
							echo pb_backupbuddy::tip( 'Select the backup file you would like to restore data from. This must be a valid BackupBuddy backup archive with its original filename. Remember to delete importbuddy.php and this backup file from your server after migration.', '', true );
							echo '</div>';
							foreach( $backup_archives as $backup_id => $backup_archive ) {
								echo '<input type="radio" ';
								if ( $backup_id == 0 ) {
									echo 'checked="checked" ';
								}
								echo 'name="file" value="' . $backup_archive['file'] . '"> ' . $backup_archive['file'] . '<br>';
								if ( $backup_archive['comment'] != '' ) {
									echo '<div style="margin-left: 27px; margin-top: 6px;" class="description">Note: ' . $backup_archive['comment'] . '</div>';
								}
							}
							
							//echo '&nbsp;&nbsp;&nbsp;<span class="toggle button-secondary" id="pb_importbuddy_gethash">Get MD5 Hash</span>';
							pb_advanced_options();
						}
						?>
						
						
					</div>
				</div>
				

				
			</div>
		</div>
	<?php } // End file not given in querystring. ?>
	
	

	<br>
	<?php
	echo '<br>';
	
	/********* Start warnings for existing files. *********/
	if ( wordpress_exists() === true ) {
		pb_backupbuddy::alert( 'WARNING: Existing WordPress installation found. It is strongly recommended that existing WordPress files and database be removed prior to migrating or restoring to avoid conflicts. You should not install WordPress prior to migrating.' );
	}
	if ( phpini_exists() === true ) {
		pb_backupbuddy::alert( 'WARNING: Existing php.ini file found. If your backup also contains a php.ini file it may overwrite the current one, possibly resulting in changes in cofiguration or problems. Make a backup of your existing file if your are unsure.' );
	}
	if ( htaccess_exists() === true ) {
		pb_backupbuddy::alert( 'WARNING: Existing .htaccess file found. If your backup also contains a .htaccess file it may overwrite the current one, possibly resulting in changed in configuration or problems. Make a backup of your existing file if you are unsure.' );
	}
	/********* End warnings for existing files. *********/
	
	// If one or more backup files was found then provide a button to continue.
	if ( !empty( $backup_archives ) ) {
		echo '</div><!-- /wrap -->';
		echo '<div class="main_box_foot">';
		echo '<input type="submit" name="submit" value="Next Step &rarr;" class="button">';
		echo '</div>';
	} else {
		//pb_backupbuddy::alert( 'Upload a backup file to continue.' );
		echo '<b>You must upload a backup file by FTP, the upload tab, or import from Stash to continue.</b>';
		echo '</div><!-- /wrap -->';
	}
	echo '</form>';
	?>
	
<?php
}
require_once( '_footer.php' );
?>