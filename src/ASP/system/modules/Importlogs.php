<?php
class Importlogs
{
    public function Init() 
    {
		// Make sure the database if offline
		if(DB_VER == '0.0.0')
			redirect('home');
			
        // Check for post data
        if($_POST['action'] == 'import')
        {
            $this->ProcessTest();
        }
        else
        {
            // Setup the template
            $Template = new Template();
            $Template->render('importlogs');
        }
    }
    
    public function ProcessTest()
    {
        // Make Sure Script doesn't timeout
        set_time_limit(0);
        ignore_user_abort(true);

        // Find Log Files
        $regex = '/([0-9]{6})_([0-9]{4})/';
        $files = array();

        // Get the file names of all incomplete snapshots
        $dir = @opendir( SYSTEM_PATH . DS . 'snapshots' . DS . 'temp' . DS );
        if(!$dir)
        {
            echo json_encode( 
                array(
                    'success' => false,
                    'type' => 'error',
                    'message' => 'Unable to open snapshot directory ('. SYSTEM_PATH . DS . 'snapshots' . DS . 'temp' . DS .')'
                )
            );
            die();
        }
        
        // Read all snapshot log file names
        while(($file = readdir($dir)) !== false)
        {
            if(strpos($file, '.txt'))
            {
                if( preg_match($regex, $file, $sort) )
                {
					// Get timestamp for sorting
					$parts = array_reverse(explode('_', $file));
					$time = str_replace(".txt", '', $parts[0]);
					$date = new DateTime("{$parts[1]} {$time}");
                    $files[$date->getTimestamp()] = SYSTEM_PATH . DS . 'snapshots' . DS . 'temp' . DS . "|" . $file;
                }
            }
        }
        @closedir($dir);
        
        // Aslo add processed files if the user select it
        if($_POST['type'] == 'all')
        {
            $dir = @opendir( SYSTEM_PATH . DS . 'snapshots' . DS . 'processed' . DS );
            if(!$dir)
            {
                echo json_encode( 
                    array(
                        'success' => false,
                        'type' => 'error',                        
                        'message' => 'Unable to open snapshot directory ('. SYSTEM_PATH . DS . 'snapshots' . DS . 'processed' . DS .')'
                    )
                );
                die();
            }
            
            // Read all snapshot log file names
            while(($file = readdir($dir)) !== false)
            {
                if(strpos($file, '.txt'))
                {
                    if( preg_match($regex, $file, $sort) )
                    {
						// Get timestamp for sorting
						$parts = array_reverse(explode('_', $file));
						$time = str_replace(".txt", '', $parts[0]);
						$date = new DateTime("{$parts[1]} {$time}");
                        $files[$date->getTimestamp()] = SYSTEM_PATH . DS . 'snapshots' . DS . 'processed' . DS . "|" . $file;
                    }
                }
            }
            @closedir($dir);
        }
		
		// Sort array by timestamp
		ksort($files);

        // Re-post existing log data to bf2statistics
        $total = 0;
        $count = count($files);
        
        // If we have more then just a few files to process, lets not make the admin wait
        if($count > 50 && !isset($_POST['fprocess']))
        {
            // Open a new request, so we dont have to wait for who knows how long when there is tons of logs!
            $data = "&action=import&fprocess=1&type=". urlencode($_POST['type']);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "http://{$_SERVER['HTTP_HOST']}/ASP/index.php?task=importlogs");
            curl_setopt($ch, CURLOPT_COOKIE, session_name() . "=" . session_id() . "; path=/");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            $result = curl_exec($ch);
            $errno = curl_errno($ch);
            curl_close($ch);

            // If we timed out (err code 28), consider it as success
            if ($errno == 28) 
            {
                // Success
                echo json_encode( 
                    array(
                        'success' => true,
                        'type' => 'info',
                        'message' => "Processing ". $count ." Snapshot logs. Estimated time to completion:  ". formatTime($count * 3) // 3 seconds each
                        .". Time may vary depending on system speed. You can check the stats_debug.log (system/logs/stats_debug.log) frequently to get import status."
                    )
                );
            }
            else
            {
                // Failed
                echo json_encode( 
                    array(
                        'success' => false, 
                        'type' => 'error',
                        'message' => "Failed to open connection to http://{$_SERVER['HTTP_HOST']}!"
                    )
                );
            }
        }
        else
        {
            // Open the stats debug file and log processing start
            ErrorLog("----- Importing ". $count ." Logs -----", -1);
            
            // Process each file
            $start_time = microtime(true);
            foreach($files as $file)
            {
                // Get our file parts
                $file = explode("|", $file);
                
                // Get snapshot data
                $data = file_get_contents($file[0] . $file[1]);
                
                // Make sure we have an "EOF"
                if(strpos($data, '\EOF\1') === false) 
                    continue; // Incomplete snapshot, skip it

                // Make sure we know this is an import of existing log data
                if (strpos($file[1], '\import\\') === false) $data .= '\import\1';
                
                // Post the headers and snapshot data
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "http://{$_SERVER['HTTP_HOST']}/ASP/bf2statistics.php");
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                curl_setopt($ch, CURLOPT_USERAGENT, "GameSpyHTTP/1.0");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
                curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                $result = curl_exec($ch);
                if ($result) {
                    if(strpos($result, "$\tOK\t$") !== false) {
                        $total++;
                    }
                }
                // Close http connection, and sleep for 1 second to prevent an sql error that
                // occurs in the player_history table (timestamp must be unique for each player)
                curl_close($ch);
                sleep(1);
            }
            
            // Open the stats debug file and log processing start
            ErrorLog("----- Import Logs Complete. Imported ". $total ." logs in ". formatTime( round(microtime(true) - $start_time) ) ." -----", -1);

            // Success
            echo json_encode( array('success' => true, 'type' => 'success', 'message' => "All System Snapshots Processed! Total logs imported: ". $total) );
        }
    }
}
?>
