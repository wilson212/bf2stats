<?php
class Upgradedb
{
	public function Init() 
	{
		// Make sure the database if offline
		if(DB_VER == '0.0.0')
			redirect('home');
			
		// Check for post data
		if($_POST['action'] == 'upgrade')
		{
			$this->Process();
		}
		else
		{
			// Db Version Compare
			if(verCmp( DB_VER ) < verCmp( CODE_VER ))
			{
				$button = 'Run Updates';
				$disabled = '';
			}
			else
			{
				$button = 'System Up To Date';
				$disabled = 'disabled="disabled"';
			}
		
			// Setup the template
			$Template = new Template();
			$Template->set('button_text', $button);
			$Template->set('disabled', $disabled);
			$Template->render('upgradedb');
		}
	}
	
	public function Process()
	{
		// Load the config / Database
		$DB = Database::GetConnection();
		$errors = array();
		
		// Remove our time limit!
		ini_set('max_execution_time', 0);
		
		// Get DB Version
		$curdbver = verCmp(DB_VER);
		
		// Import Upgrade Schema/Data
		require( SYSTEM_PATH . DS . 'database'. DS .'sql.dbupgrade.php' );
		
		// Process each upgrade only if the version is newer
		foreach ($sqlupgrade as $query) 
		{
			if ($curdbver < verCmp($query[1])) 
			{
				if($DB->exec($query[2]) === false)
				{
					$error = $DB->errorInfo();
					$errors[] = $query[0]." *FAILED*: ". $error[2];
				}
			} 
		}
		
		// Prepare for Output
		$html = '';
		if( !empty($errors) )
		{
			$html .= 'Upgrade failed to install all the neccessary database data...<br /><br />List of Errors:<br /><ul>';
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
					'message' => 'System Upgraded Successfully!'
				)
			);
		}
	}
}
