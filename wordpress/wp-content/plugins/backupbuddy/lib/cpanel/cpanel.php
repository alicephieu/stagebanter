<?php
/*
EXAMPLE:

require_once( pb_backupbuddy::plugin_path() . '/lib/cpanel/cpanel.php' );
 
$cpanel_user = "*USERNAME*";
$cpanel_password = "*PASSWORD*";
$cpanel_host = "dustinbolton.com";
$db_name = 'apples';
$db_user = 'oranges';
$db_pass = 'bananas';
$create_db_result = pb_backupbuddy_cpanel::create_db( $cpanel_user, $cpanel_password, $cpanel_host, $db_name, $db_user, $db_pass );

if ( $create_db_result === true ) {
	echo 'Success! Created database, user, and assigned used to database.';
} else {
	echo 'Error(s):<br><pre>' . print_r( $create_db_result, true ) . '</pre>';
}

*/



/*	pb_backupbuddy_cpanel Class
 *	
 *	Manage some cpanel settings.
 *	
 *	@author		Dustin Bolton <http://dustinbolton.com> Sept 2012.
 */
class pb_backupbuddy_cpanel {


	// TODO: Use more robust than file_get_contents().

	
	/*	create_db()
	 *	
	 *	Create a database and assign a user to it with all privilages.
	 *	
	 *	@param		
	 *	@return		true|array		Boolean true on success, else an array of errors.
	 */
	public static function create_db( $cpanel_user, $cpanel_password, $cpanel_host, $db_name, $db_user, $db_userpass ) {
		$cpanel_skin = "x3";
		$errors = array();
		
		
		// Calculate base URL.
		$base_url = "http://{$cpanel_user}:{$cpanel_password}@{$cpanel_host}:2082/frontend/{$cpanel_skin}";
		
		// Generate create database URL.
		$create_database_url = $base_url . "/sql/addb.html?db={$db_name}";
		echo $create_database_url . '<br>';
		
		// Generate create database user URL.
		$create_user_url = $base_url . "/sql/adduser.html?user={$db_user}&pass={$db_userpass}";
		echo $create_user_url . '<br>';
		
		// Generate assign user database access URL.
		$assign_user_url = $base_url . "/sql/addusertodb.html?user={$cpanel_user}_{$db_user}&db={$cpanel_user}_{$db_name}&ALL=ALL";
		echo $assign_user_url . '<br>';
		
		// Run create database.
		$result = file_get_contents( $create_database_url );
		if ( stristr( $result, 'Added the Database' ) === false ) { // No sucess adding DB.
			$errors[] = 'Error encountered adding database.';
		}
		if ( stristr( $result, 'problem creating the database' ) !== false ) { // Something failed.
			$errors[] = 'Unable to create database.';
		}
		if ( stristr( $result, 'database name already exists' ) !== false ) { // DB already exists.
			$errors[] = 'The database name already exists.';
		}
		
		
		// Run create database user.
		if ( count( $errors ) === 0 ) {
			$result = file_get_contents( $create_user_url ) . '<br>';
			if ( stristr( $result, 'Added user' ) === false ) { // No success adding user.
				$errors[] = 'Error encountered adding user.';
			}
			if ( stristr( $result, 'exists in the database' ) !== false ) { // Already exists.
				$errors[] = 'Username already exists.';
			}
		}
		
		// Run assign user to database.
		if ( count( $errors ) === 0 ) {
			$result = file_get_contents( $assign_user_url );
			if ( stristr( $result, 'was added to the database' ) === false ) { // No success adding user.
				$errors[] = 'Error encountered assigning user to database.';
			}
		}
		
		if ( count( $errors ) > 0 ) { // One or more errors.
			return $errors;
		} else {
			return true; // Success!
		}
		
	}

} // end class.