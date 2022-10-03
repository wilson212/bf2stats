<?php
/* 
| --------------------------------------------------------------
| BF2 Statistics Admin Util
| --------------------------------------------------------------
| Author:       Steven Wilson 
| Copyright:    Copyright (c) 2012
| License:      GNU GPL v3
| ---------------------------------------------------------------
| Class: Auth()
| ---------------------------------------------------------------
|
*/

class Auth
{
    // Clients IP address
    public static $clientIp;

/*
| ---------------------------------------------------------------
| Function: load_user()
| ---------------------------------------------------------------
|
| This method checks to see if the user is logged in by session.
| If not then a username, id, and account level are set at guest.
| Also checks for login expire time.
|
*/

    public static function IsValidSession()
    {
        // Session isnt set
		if(!isset($_SESSION['adminAuth'])) 
			return false;
		
		// If the password set is wrong
		if($_SESSION['adminAuth'] != sha1(Config::Get('admin_user').':'.Config::Get('admin_pass'))) 
			return false;
		
		// If the session time is expired
		if($_SESSION['adminTime'] < time() - (30*60)) 
			return false;
		
		// Everything is good, update the session time
        $_SESSION['adminTime'] = time();
        return true;
    }

/*
| ---------------------------------------------------------------
| Function: login()
| ---------------------------------------------------------------
|
| The main login script!
|
| @Param: (String) $username - The username logging in
| @Param: (String) $password - The unencrypted password
| @Return (Bool) True upon success, FALSE otherwise
|
*/

    public static function Login($username, $password)
    {
        // Initialize or retrieve the current values for the login variables
		if(!isset($_POST['loginAttempts'])) $_POST['loginAttempts'] = 1;
		$loginAttempts = $_POST['loginAttempts'];
		
		// If the posted username and/or password doesnt match whats set in config.
		if($username != Config::Get('admin_user') || $password != Config::Get('admin_pass')) 
		{
			// If first login attempt, initiate a login attempt counter
			if($loginAttempts == 0) 
			{
				$_POST['loginAttempts'] = 1;
				return FALSE;
			}
			
			// Otherwise, check if attempts are at 3, if so then lock the ASP for now
			else
			{
				if( $loginAttempts >= 3 )
				{
					echo "<blink><p align='center' style=\"font-weight:bold;font-size:170px;color:red;font-family:sans-serif;\">Max Login Attempts Reached</p></blink>";		
					exit;
				}
				else
				{
					$_POST['loginAttempts'] += 1;
					return FALSE;
				}
			}
		}
		
		// Else, the username and password matched, login is a success
		else 
		{
			// Start Session, set session variables
			$_SESSION['adminAuth'] = sha1(Config::Get('admin_user') .':'. Config::Get('admin_pass'));
			$_SESSION['adminTime'] = time();
			$SID = session_id();
			return TRUE;
		}
    }
/*
| ---------------------------------------------------------------
| Function: logout()
| ---------------------------------------------------------------
|
| Logs the user out and sets all session variables to Guest.
|
| @Return (None)
|
*/

    public static function Logout()
    {
        // If sessions is already killed, just return
		if(!self::IsValidSession()) return;
		
		// Reset Session Values
		$_SESSION['adminAuth'] = '';
		$_SESSION['adminTime'] = '';
		
		// If session exists, unregister all variables that exist and destroy session
		session_destroy();
    }
    
    /**
     * Returns the Remote connected IP address
     *
     * @return string The validated remote IP address. Returns 0.0.0.0 if
     *   the IP address could not be determined
     */
    public static function ClientIp()
    {
        // Return it if we already determined the IP
        if(empty(self::$clientIp))
        {
            // Check to see if the server has the IP address
            if(isset($_SERVER['HTTP_CLIENT_IP']) && filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
            {
                self::$clientIp = $_SERVER['HTTP_CLIENT_IP'];
            }
            elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']))
            {
                // HTTP_X_FORWARDED_FOR can be an array og IPs!
                $ips = explode(",", $_SERVER['HTTP_X_FORWARDED_FOR']);
                foreach($ips as $ip) 
                {
                    if(filter_var($ip, FILTER_VALIDATE_IP))
                    {
                        self::$clientIp = $ip;
                        break;
                    }
                }
            }
            elseif(isset($_SERVER['HTTP_X_FORWARDED']) && filter_var($_SERVER['HTTP_X_FORWARDED'], FILTER_VALIDATE_IP))
            {
                self::$clientIp = $_SERVER['HTTP_X_FORWARDED'];
            }
            elseif(isset($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && filter_var($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'], FILTER_VALIDATE_IP))
            {
                self::$clientIp = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
            }
            elseif(isset($_SERVER['HTTP_FORWARDED_FOR']) && filter_var($_SERVER['HTTP_FORWARDED_FOR'], FILTER_VALIDATE_IP))
            {
                self::$clientIp = $_SERVER['HTTP_FORWARDED_FOR'];
            }
            elseif(isset($_SERVER['HTTP_FORWARDED']) && filter_var($_SERVER['HTTP_FORWARDED'], FILTER_VALIDATE_IP))
            {
                self::$clientIp = $_SERVER['HTTP_FORWARDED'];
            }
            elseif(isset($_SERVER['HTTP_VIA']) && filter_var($_SERVER['HTTP_VIA'], FILTER_VALIDATE_IP))
            {
                self::$clientIp = $_SERVER['HTTP_VIA'];
            }
            elseif(isset($_SERVER['REMOTE_ADDR']) && !empty($_SERVER['REMOTE_ADDR']))
            {
                self::$clientIp = $_SERVER['REMOTE_ADDR'];
            }

            // If we still have a false IP address, then set to 0's
            if(empty(self::$clientIp)) self::$clientIp = '0.0.0.0';
        }
        return self::$clientIp;
    }
}

session_start();
// EOF
