<?php
class Checkawards
{
	public function Init() 
	{
		// Make sure the database if offline
		if(DB_VER == '0.0.0')
			redirect('home');

		// Check for post data
		if($_POST['action'] == 'check')
		{
			$this->Process();
		}
		else
		{
			// Setup the template
			$Template = new Template();
			$Template->render('checkawards');
		}
	}
	
	public function Process()
	{
		// Load the Player class
		$Player = new Player();
		$DB = Database::GetConnection();
		
        // Load all the players
		$pids = $DB->query("SELECT `id` FROM `player` WHERE `score` > 1");
        if($pids instanceof PDOStatement)
        {
            while(($row = $pids->fetch()) !== false)
            {
                $Player->checkBackendAwards($row['id']);
            }
        }
        
        // Begin logging process
        $data = "Check Awards Logging Started: ". date('Y-m-d H:i:s') . PHP_EOL;

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
            $data .= " -> All player awards are properly assigned". PHP_EOL;
        }
        
        // Write to log file
        $data .= PHP_EOL;
        $log = SYSTEM_PATH . DS . 'logs' . DS . 'validate_awards.log';
        $file = @fopen($log, 'a');
        @fwrite($file, $data);
        @fclose($file);
        
        // Echo out success message
        echo json_encode(array('success' => true));
	}
}
?>