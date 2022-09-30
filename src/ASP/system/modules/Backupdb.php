<?php
class Backupdb
{
	public function Init() 
	{
		// Make sure the database if offline
		if(DB_VER == '0.0.0')
			redirect('home');
		
		// Check for post data
		if($_POST['action'] == 'backup')
		{
			$this->Process();
		}
		else
		{
			// Setup the template
			$Template = new Template();
			$Template->render('backupdb');
		}
	}
	
	public function Process()
	{
		// Load the config / Database
		$DB = Database::GetConnection();
		$tables = getDataTables();
		$errors = array();
		
		// Create Backup Folder
		$backupPath  = str_replace(array('/','\\'), DS, Config::Get('admin_backup_path'));
		
		// Make sure the path is writable before attempting to make the dir
		if( !FileSystem::IsWritable($backupPath) )
		{
			echo json_encode( 
				array(
					'success' => false,
					'message' => 'Database backup path ('. $backupPath .') is *NOT* Writable by the system. Please set the proper permissions to allow the system to create new directories.'
				)
			);
			die();
		}
		
		// Continue making the directory
		$backupPath .= "bak_".date('Ymd_Hi');
		if( !FileSystem::CreateDir($backupPath, 0777) )
		{
			echo json_encode( 
				array(
					'success' => false,
					'message' => 'Unable to create new backup path ('. $backupPath .'). Please set the proper permissions to allow the system to create new directories.'
				)
			);
			die();
		}
		
		// Remove our time limit!
		ini_set('max_execution_time', 0);
		
		// Process each upgrade only if the version is newer
		foreach ($tables as $DataTable) 
		{
			// Check Table Exists
			$result = $DB->query("SHOW TABLES LIKE '" . $DataTable . "'");
			if($result->rowCount() > 0)
			{
				// Table Exists, lets back it up
				$backupFile = $backupPath . DS . $DataTable . Config::Get('admin_backup_ext');
				$query = "SELECT * INTO OUTFILE '". addslashes($backupFile) ."' FROM {$DataTable};";
				$result = false;
				
				// Try to execute
				try {
					$result = $DB->exec($query);
				}
				catch( PDOException $e ) {}
				
				// Report any error
				if( $result === false )
				{
					$error = $DB->errorInfo();
					$errors[] = "Table (" . $DataTable . ") *NOT* Backed Up: [{$error[1]}] {$error[2]}";
				}
			}
		}
		
		// Prepare for Output
		$html = '';
		if( !empty($errors) )
		{
			$html .= 'Failed to backup all database tables... <br /><br />List of Errors:<br /><ul>';
			foreach($errors as $e)
			{
				$html .= '<li>'. $e .'</li>';
			}
			$html .= '</ul>';
			
			echo json_encode( 
				array(
					'success' => false,
					'message' => $html
				)
			);
		}
		else
		{
			echo json_encode( 
				array(
					'success' => true,
					'message' => 'System Data Backup Successfull! Backup Directory Used: '. $backupPath
				)
			);
		}
	}
}