<?php
/* 
| --------------------------------------------------------------
| BF2 Statistics Admin Util
| --------------------------------------------------------------
| Author:       Steven Wilson
| Copyright:    Copyright (c) 2012
| License:      GNU GPL v3
| ---------------------------------------------------------------
| Class: System()
| ---------------------------------------------------------------
|
*/

class System
{
	public static function Run() 
	{
		// Register AutoLoader
        AutoLoader::Register();
        AutoLoader::RegisterPath( path( SYSTEM_PATH, 'core' ) );
		
		// First, Lets make sure the IP can view the ASP
		if(!isIPInNetArray( Auth::ClientIp(), Config::Get('admin_hosts') ))
			die("<font color='red'>ERROR:</font> You are NOT Authorised to access this Page! (Ip: ". Auth::ClientIp() .")");
        
        // Connect to the bf2stats database
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
        catch( Exception $e ) {}
		
		// Define our database version!
        $stmt = ($DB instanceof PDO) ? $DB->query("SELECT `dbver` FROM `_version`;") : false;
		define('DB_VER', ($stmt == false) ? '0.0.0' : $stmt->fetchColumn());
		
		// Make sure config expected DB version is up to date
		if(verCmp( DB_VER ) > verCmp( Config::Get('db_expected_ver') ))
		{
			Config::Set('db_expected_ver', DB_VER);
			Config::Save();
		}
		
		// Always set a post and get actions
		if(!isset($_POST['action'])) $_POST['action'] = null;
		if(!isset($_GET['action']))  $_GET['action'] = null;
		
		// Get / Set our current task
		$task = (isset($_GET['task'])) ? $_GET['task'] : false;
		if($task == false)
		{
			(isset($_POST['task'])) ? $_GET['task'] = $_POST['task'] : $_GET['task'] = 'home';
		}

		// Check for login / logout requests
		if($_POST['action'] == 'login' && isset($_POST['username']) && isset($_POST['password'])) 
			Auth::Login($_POST['username'], $_POST['password']);
		elseif($_POST['action'] == 'logout' || $_GET['action'] == 'logout') 
			Auth::Logout();

		// Check and see if the user is logged in
		if( !Auth::IsValidSession() )
		{
			include SYSTEM_PATH . DS . 'modules' . DS . 'Login.php';
			$Module = new Login();
			$Module->Init();
		}
		else
		{
			// Uppercase the classname
            $task = ucfirst( strtolower($_GET['task']) );
            
            // Process the task by making sure the module exists
            $file = SYSTEM_PATH . DS . 'modules' . DS . $task . '.php';
            if( !file_exists($file) )
            {
                // 404
                $Template = new Template();
                $Template->render('404');
                return;
            }
            
            // Load the module and run!
            include $file ;
            $Module = new $task();
            $Module->Init();
		}
	}
}
?>