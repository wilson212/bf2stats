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

// Make sure we have a valid PID
$pid = (isset($_GET['pid'])) ? $_GET['pid'] : false;
if(!$pid || !is_numeric($pid)) 
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

	$pid = intval($pid);
	$query = "SELECT `rank` FROM `player` WHERE `id` = {$pid}";
	$result = $connection->query($query);

	if(!($result instanceof PDOStatement)) 
	{
		print "E\nPlayer not found!";
	}
	else
	{
		$query = "UPDATE `player` SET `chng` = 0, `decr` = 0 WHERE `id` = {$pid}";
		$result = $connection->exec($query);
		$out = ($result === false) ? "O\nFailed to clear rank notification {$pid}\n" : "O\nCleared rank notification {$pid}\n";
		$num = strlen(preg_replace('/[\t\n]/','',$out));
		print $out . "$\t" . $num . "\t$";
	}
}
?>