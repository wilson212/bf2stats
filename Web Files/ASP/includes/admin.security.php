<?php
/***************************************************************
 * No direct access
 ***************************************************************/
defined( '_BF2_ADMIN' ) or die( 'Restricted access' );

// **************************************************************
// Start Session

function start_session()
{
	static $started = false;
	if(!$started)
	{
		session_start();
		$started = true;
	}
}

// === START THE SESSION! == //
start_session();

// **************************************************************
// Check the session and see if  1) User is logged in
// and 2) The session isnt expired

function checkSession() 
{
	global $cfg;
	
	// Session isnt set
	if(!isset($_SESSION['adminAuth'])) 
	{
		return false;
	}
	
	// If the password set is wrong
	elseif(($_SESSION['adminAuth']) != md5($cfg->get('admin_user').$cfg->get('admin_pass'))) 
	{
		return false;
	}
	
	// If the session time is expired
	elseif($_SESSION['adminTime'] < time() - (30*60)) 
	{
		return false;
	}
	
	// Everything is good, update the session time
	else
	{
		// Update Session Time
		$_SESSION['adminTime'] = time();
		return true;
	}
}

// **************************************************************
// Main login script

function processLogin() 
{
	global $cfg;
	
	// Initialize or retrieve the current values for the login variables
	$loginAttempts = !isset($_POST['loginAttempts']) ? 1 : $_POST['loginAttempts'];
	$formUser = !isset($_POST['formUser']) ? NULL : $_POST['formUser'];
	$formPassword = !isset($_POST['formPassword']) ? NULL : $_POST['formPassword'];
	
	// If the posted username and/or password doesnt match whats set in config.
	if(($formUser != $cfg->get('admin_user')) || ($formPassword != $cfg->get('admin_pass'))) 
	{
		// If first login attempt, initiate a login attempt counter
		if($loginAttempts == 0) 
		{
			$_POST['loginAttempts'] = 1;
			$auth = false;
			return;
		}
		
		// Otherwise, check if attempts are at 3, if so then lock the ASP for now
		else
		{
			if( $loginAttempts >= 3 )
			{
				echo "<blink><p align='center' style=\"font-weight:bold;font-size:170px;color:red;font-family:sans-serif;\">Log In<br>Failed.</p></blink>";		
				exit;
			}
			else
			{
				$_POST['loginAttempts'] += 1;
				return;
			}
		}
	}
	
	// Else, the username and password matched, login is a success
	elseif (($formUser == $cfg->get('admin_user') ) && ($formPassword == $cfg->get('admin_pass') )) 
	{
		// Start Session, set session variables
		start_session();
		$_SESSION['adminAuth'] = md5($cfg->get('admin_user').$cfg->get('admin_pass'));
		$_SESSION['adminTime'] = time();
		$SID = session_id();
		$_POST['task'] = 'home';
	}
	else
	{
		$_POST['loginAttempts'] += 1;
		return;
	}
}

// **************************************************************
// Main Logout Script

function processLogout() 
{
	// If sessions is already killed, just return
	if(!checkSession()) 
	{
		return;
	}
	
	// Reset Session Values
	$_SESSION['adminAuth'] = '';
	$_SESSION['adminTime'] = '';
	
	// If session exists, unregister all variables that exist and destroy session
	$exists = false;
	$session_array = explode(";",session_encode());
	for($x = 0; $x < count($session_array); $x++)
	{
		$name  = substr($session_array[$x], 0, strpos($session_array[$x],"|")); 
		if(session_is_registered($name)) 
		{
			session_unregister('$name');
			$exists = true;
		}
	}
	
	// Do a final session check, if still alive, destroy it
	if($exists)
	{
		session_destroy();
	}
}
?>