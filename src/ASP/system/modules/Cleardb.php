<?php
class Cleardb
{
	public function Init() 
	{
		// Make sure the database if offline
		if(DB_VER == '0.0.0')
			redirect('home');
			
		// Check for post data
		if($_POST['action'] == 'clear')
		{
			$this->Process();
		}
		else
		{
			// Setup the template
			$Template = new Template();
			$Template->render('cleardb');
		}
	}
	
	public function Process()
	{
		// Load the config / Database
		$DB = Database::GetConnection();
		$tables = getDataTables();
		$errors = array();
		
		// Remove our time limit!
		ini_set('max_execution_time', 0);
		
		// Process each upgrade only if the version is newer
		foreach ($tables as $DataTable) 
		{
			// Check Table Exists
			$query = "SHOW TABLES LIKE '" . $DataTable . "'";
			$result = $DB->query($query);
			if( $result !== false && $result->rowCount() ) 
			{
				// Table Exists, lets clear it
				$query = "TRUNCATE TABLE `" . $DataTable . "`;";
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
                    $errors[] = "Table (" . $DataTable . ") *NOT* Cleared: [{$error[1]}] {$error[2]}";
                }
			}
		}
		
		// Prepare for Output
		$html = '';
		if( !empty($errors) )
		{
			$html .= 'Failed to clear all database tables... <br /><br />List of Errors:<br /><ul>';
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
					'message' => 'System Data Cleared Successfully!'
				)
			);
		}
	}
}