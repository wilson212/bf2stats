<?php
class Validateranks
{
	public function Init() 
	{
		// Make sure the database if offline
		if(DB_VER == '0.0.0')
			redirect('home');
			
		// Check for post data
		if($_POST['action'] == 'validate')
		{
			$this->Process();
		}
		else
		{
			// Setup the template
			$Template = new Template();
			$Template->render('validateranks');
		}
	}
	
	public function Process()
	{
		// Load the Player class
		$Player = new Player();
		$DB = Database::GetConnection();
		
        // Load all the players
		$query = "SELECT `id` FROM player WHERE `score` > 1";
		$result = $DB->query($query);
		if($result instanceof PDOStatement)
		{
			while(($row = $result->fetch()) !== false)
			{
				$Player->validateRank($row['id']);
			}
		}
        
        // Begin logging
        $data = "Validate Rank Logging Started: ". date('Y-m-d H:i:s') . PHP_EOL;
        
        // Log the Player messages if any'
        $messages = $Player->messages();
        if(count($messages))
        {
            foreach($messages as $err)
            {
                $data .= $err . PHP_EOL;
            }
        }
        else
        {
            $data .= " -> All player ranks are properly assigned". PHP_EOL;
        }
        
        // Write the logs to the log file
        $data .= PHP_EOL;
        $log = SYSTEM_PATH . DS . 'logs' . DS . 'validate_ranks.log';
        $file = @fopen($log, 'a');
        @fwrite($file, $data);
        @fclose($file);
        
        // Echo out success message
        echo json_encode(array('success' => true));
	}
}
?>