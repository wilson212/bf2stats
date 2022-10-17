<?php

/*
	Copyright (C) 2006-2013  BF2Statistics

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/*
| ---------------------------------------------------------------
| Define ROOT and system paths
| ---------------------------------------------------------------
*/
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(__FILE__));
define('SYSTEM_PATH', ROOT . DS . 'system');
define("_ERR_RESPONSE", "E\nH\tresponse\nD\t");

/*
| ---------------------------------------------------------------
| Require the needed scripts to launch the system
| ---------------------------------------------------------------
*/
require(SYSTEM_PATH . DS . 'core'. DS .'Auth.php');
require(SYSTEM_PATH . DS . 'core'. DS .'Database.php');
require(SYSTEM_PATH . DS . 'core'. DS .'Config.php');
require(SYSTEM_PATH . DS . 'functions.php');

// Set Error Reporting
error_reporting(E_ALL);
ini_set("log_errors", "1");
ini_set("error_log", SYSTEM_PATH . DS . 'logs' . DS . 'php_errors.log');
ini_set("display_errors", "0");

//Disable Zlib Compression
ini_set('zlib.output_compression', '0');

/*
| ---------------------------------------------------------------
| Security Check
| ---------------------------------------------------------------
*/
if(!isIPInNetArray(Auth::ClientIp(), Config::Get('game_hosts')))
{
    ErrorLog("Unauthorised Access Attempted! (IP: " . Auth::ClientIp() . ")", 0);
    die(_ERR_RESPONSE . "Unauthorised Gameserver");
}

// Make sure we have a PID list
$pidlist = (isset($_GET['playerlist'])) ? $_GET['playerlist'] : 0;

// Get our Player Nick
if(isset($_POST['nick'])) 
{
    $nick = $_POST['nick'];
    $isBot = (isset($_POST['ai'])) ? intval($_POST['ai']) : 0;
} 
else
{
    $nick = (isset($_GET['nick'])) ? $_GET['nick'] : '';
    $isBot = (isset($_GET['ai'])) ? intval($_GET['ai']) : 0;
}

// Connect to the database
$connection = null;
try {
    $connection = Database::Connect('bf2stats',
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
catch( Exception $e ) {
    $out = "E\nH\tasof\terr\n" . 
        "D\t" . time() . "\tDatabase Connect Error\n";
    $num = strlen(preg_replace('/[\t\n]/', '', $out));
    $out .= "$\t$num\t$";
    die($out);
}

if(!empty($nick)) 
{
    // Try to fecth players id
    $query = "SELECT `id` FROM `player` WHERE name = '" . substr($connection->quote($nick), 1, -1) . "' LIMIT 1";
    $result = $connection->query($query);
    if( !($result instanceof PDOStatement) || !($pid = $result->fetchColumn()) )
    {
		// Default PID
		$pid = Config::Get('game_default_pid');
		
		// create new player at 'lowest id' - 1
        $query = "INSERT `player` (id, name, joined, isbot) SELECT LEAST(IFNULL(MIN(id),". $pid ."), ". 
            $pid .")-1 AS id, '". substr($connection->quote($nick), 1, -1) ."' AS name, " . time() . " AS joined, ".
            $isBot ." AS isbot FROM `player`";
        if( $connection->exec($query) !== false ) 
        {
            // get that new minimum PID..
            $query = "SELECT MIN(`id`) AS `min` FROM `player`";
            $result = $connection->query($query);
            if($result instanceof PDOStatement) 
            {
                $pid = $result->fetchColumn();
            }
            else
            {
                $out = "E\nH\tasof\terr\n" . 
                    "D\t" . time() . "\tDatabase Insertion Error\n";
                $num = strlen(preg_replace('/[\t\n]/', '', $out));
                $out .= "$\t$num\t$";
                die($out);
            }
            
            // Insert unlocks
			$query = "INSERT INTO `unlocks` VALUES ";
            for ($i = 11; $i < 100; $i += 11)
                $query .= "($pid, $i, 'n'), ";
            
            for($i = 111; $i < 556; $i += 111)
                $query .= "($pid, $i, 'n'), ";
				
			$connection->exec(trim($query, ", "));
        }
    }
    
    $out = "O\n" .
        "H\tpid\n" .
        "D\t$pid\n";
    
    $num = strlen(preg_replace('/[\t\n]/','',$out));
    print $out . "$\t" . $num . "\t$";
    
} 
elseif($pidlist) 
{
    // Get a list of all PIDS from the database where the IP is non local
    $query = "SELECT `id` FROM `player` WHERE `ip` <> '127.0.0.1'";
    $result = $connection->query($query);
    
    $out = "O\n" .
        "H\tpid\n";
    
    if($result instanceof PDOStatement) 
    {
        while($row = $result->fetch()) 
        {
            $pid = $row['id'];
            $out .= "D\t$pid\n";
        }
    }
    
    $num = strlen(preg_replace('/[\t\n]/','',$out));
    print $out . "$\t" . $num . "\t$";

}
else 
{
    $out = "E\n" .
        "H\terr\n" .
        "D\tNo Nick Specified!\n";
    
    $num = strlen(preg_replace('/[\t\n]/','',$out));
    print $out . "$\t" . $num . "\t$";
}
?>
