<?php
class Installdb
{
	public function Init() 
	{
		// Check for post data
		if($_POST['action'] == 'install')
		{
			$this->Install();
		}
		else
		{
			// Setup the template
			$Template = new Template();
			$Template->render('installdb');
		}
	}
	
	public function Install()
	{
		// Load the config / Database
		$errors = array();
		
		// Remove our time limit! Ip2Nation can take awhile
		ini_set('max_execution_time', 0);
		
		// Store New/Changed config items
		foreach ($_POST as $item => $val) 
		{
			$key = explode('__', $item);
			if ($key[0] == 'cfg') 
				Config::Set($key[1],$val);
		}
		Config::Save();
		
		// Load the database
        $DB = false;
        try {
            $DB = Database::Connect('bf2stats',
                array(
                    'driver' => 'mysql',
                    'host' => Config::Get('db_host'), 
                    'port' => Config::Get('db_port'), 
                    'database' => Config::Get('db_name'), 
                    'username' => Config::Get('db_user'), 
                    'password' => Config::Get('db_pass')
                )
            );
        }
        catch( PDOException $e ) {
            echo json_encode( 
                array(
                    'success' => false, 
                    'errors' => false,
                    'message' => 'Failed to establish connection to ('. Config::Get('db_host') .'): '. $e->getMessage()
                )
            );
            die;
        }
		
		// Import Schema and Default data
		require( SYSTEM_PATH . DS . 'database'. DS .'sql.dbschema.php' );
		require( SYSTEM_PATH . DS . 'database'. DS .'sql.dbdata.php' );
		
		// Process Schema
		foreach ($sqlschema as $query) 
		{
			if($DB->exec($query[1]) === false) 
			{
                $e = $DB->errorInfo();
				$errors[] = $query[0]." *NOT* Installed: [{$e[1]}] {$e[2]}";
			} 
		}
		
		// Process Defaut Data
		$i = 0;
		foreach ($sqldata as $query) 
		{
			if($DB->exec($query[1]) === false ) 
			{
				$e = $DB->errorInfo();
				$errors[] = $query[0]." *NOT* Installed: [{$e[1]}] {$e[2]}";
			}
		}
		
		// Prepare for Output
		$html = '';
		if( !empty($errors) )
		{
			$html .= 'Installation failed to install all the neccessary database data...<br /><br />List of Errors:<br /><ul>';
			foreach($errors as $e)
			{
				$html .= '<li>'. $e .'</li>';
			}
			$html .= '</ul>';
			
			echo json_encode( 
				array(
					'success' => false,
					'errors' => true,
					'message' => $html
				)
			);
		}
		else
		{
			echo json_encode( 
				array(
					'success' => true,
					'errors' => false,
					'message' => 'System Installed Successfully! <a href="?task=testconfig">Click here to go to the System Test screen</a> to make sure everything is in working order.'
				)
			);
		}
	}
}