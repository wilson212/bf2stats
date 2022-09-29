<?php
class Restoredb
{
	public function Init() 
	{
		// Make sure the database if offline
		if(DB_VER == '0.0.0')
			redirect('home');
			
		// Check for post data
		if($_POST['action'] == 'restore')
		{
			$this->Process();
		}
		else
		{
			// Get Existing Backup List
			$baklist = array();
			$dir = dir( Config::Get('admin_backup_path') );
			while($file = $dir->read()) 
			{
				if($file != "." && $file != ".." && is_dir( Config::Get('admin_backup_path').$file )) 
					$baklist[] = $file;
				
			}
			sort($baklist);
			$dir->close();
			
			// Build our options list
			$list = array();
			foreach($baklist as $backup)
			{
				$list[] = array('name' => $backup);
			}
	
			// Setup the template
			$Template = new Template();
			$Template->set('options', $list);
			$Template->render('restoredb');
		}
	}
	
	public function Process()
	{
		// Load the config / Database
		$DB = Database::GetConnection();
		$tables = getDataTables();
		$errors = array();
		
		// Get our backup path
		$backupPath  = Config::Get('admin_backup_path');
		$backupPath .= $_POST["backupname"];
		
		// Remove our time limit!
		ini_set('max_execution_time', 0);
		
		// Process each upgrade only if the version is newer
		foreach ($tables as $DataTable) 
		{
			// Check Table Exists
			$result = $DB->query("SHOW TABLES LIKE '" . $DataTable . "'");
			if($result instanceof PDOStatement && $result->fetchColumn())
			{
				// Table Exists, lets back it up
				$backupFile = $backupPath ."/". $DataTable . Config::Get('admin_backup_ext');
				if(file_exists($backupFile)) 
				{
					$result = $DB->exec("LOAD DATA INFILE '{$backupFile}' INTO TABLE {$DataTable};");
					if( $result === false ) 
					{
                        $e = $DB->errorInfo();
						$errors[] = "Table (" . $DataTable . ") *NOT* Restored: [{$e[1]}] {$e[2]}";
					}
				}
				else
				{
					$errors[] = "Data File (" . $backupFile . ") does *NOT* Exist!!";
				}
			}
		}
		
		// Prepare for Output
		$html = '';
		if( !empty($errors) )
		{
			$html .= 'Failed to restore all database tables... <br /><br />List of Errors:<br /><ul>';
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
					'message' => 'System Data Restored Successfully!'
				)
			);
		}
	}
}