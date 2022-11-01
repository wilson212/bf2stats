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

/*
| ---------------------------------------------------------------
| Require the needed scripts to launch the system
| ---------------------------------------------------------------
*/
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
 
 // Make sure we have an ID and PID
$pid = (isset($_GET['pid'])) ? $_GET['pid'] : false;
$id = (isset($_GET['id'])) ? $_GET['id'] : false;
if(!$pid || !is_numeric($pid) || !$id || !is_numeric($id)) 
{
    $out = "E\nH\tasof\terr\n" .
        "D\t" . time() . "\tInvalid Syntax!\n";
	$num = strlen(preg_replace('/[\t\n]/', '', $out));
	echo $out, "$\t$num\t$";
}
else
{
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
    
    // Prevent sql injection attempts
    $pid = intval($pid);
    $id = intval($id);

    // First, check for available unlocks
    $availunlocks = 0;
    $result = $connection->query("SELECT `availunlocks` FROM `player` WHERE `id` = {$pid}");
    if($result instanceof PDOStatement)
    {
        $availunlocks = $result->fetchColumn();
    }

    if ($availunlocks > 0) {
        // Update the unlock state of the chosen weapon
        $query = "UPDATE `unlocks` SET `state` = 's' WHERE (`id` = {$pid}) AND (`kit` = {$id})";
        $connection->exec($query);

        // First, remove an available unlock
        $unlocks = $result->fetchColumn();
        $availunlocks -= 1;
        // Update, removing 1 available unlock from the player
        $connection->exec("UPDATE `player` SET `availunlocks` = {$availunlocks} WHERE `id` = {$pid}");

        // Add one to the used unlocks
        $result = $connection->query("SELECT `usedunlocks` FROM `player` WHERE `id` = {$pid}");
        if($result instanceof PDOStatement)
        {
            $used = $result->fetchColumn();
            $used += 1;
            // Update, adding 1 used unlock from the player
            $connection->exec("UPDATE `player` SET `usedunlocks` = {$used} WHERE `id` = {$pid}");
        }
    }else {
        $out = "E\nH\tasof\terr\n" . 
            "D\t" . time() . "\tPlayer has no available unlocks\n";
        $num = strlen(preg_replace('/[\t\n]/', '', $out));
        $out .= "$\t$num\t$";
        die($out);
    }
    
    echo "O\nOK";
}
?>
