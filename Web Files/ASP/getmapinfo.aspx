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

// Make sure we have the needed params
$pid 	 = (isset($_GET['pid'])) ? intval($_GET['pid']) : 0;
$mapid 	 = (isset($_GET['mapid'])) ? intval($_GET['mapid']) : 0;
$mapname = (isset($_GET['mapname'])) ? $_GET['mapname'] : '';
$limit	 = (isset($_GET['customonly'])) ? intval($_GET['customonly']) : 0;

// Limit results to custom maps ONLY
$maplimit = ($limit == 1) ? " AND `id` >= " . Config::Get('game_custom_mapid') : '';

// Prepare output
$out = "";

if($pid) 
{
	// Build our query
	$query = "SELECT m.*, mi.name AS mapname" .
		"\nFROM maps m JOIN mapinfo mi ON m.mapid = mi.id" .
		"\nWHERE m.id = {$pid}" .
		"\nORDER BY mapid";
	$result = $connection->query($query);
	if($result instanceof PDOStatement && ($row = $result->fetch()))
	{   
		$out = "O\nH\tmapid\tmapname\ttime\twin\tloss\tbest\tworst\n";
		do {
			$out .= "D\t{$row['mapid']}\t{$row['mapname']}\t{$row['time']}\t{$row['win']}\t{$row['loss']}\t{$row['best']}\t{$row['worst']}\n";
		} 
		while($row = $result->fetch()); 
	}
	else
		$out = "E\nH\terr\nD\tMap Data Not Found!\n";
} 
else 
{
	// Get the proper query
	$query = "SELECT `id`, `name`, `score`, `time`, `times`, `kills`, `deaths` FROM `mapinfo` ";
	if($mapid) 
		$query .= "WHERE `id` = {$mapid} {$maplimit}";
	elseif(!empty($mapname))
		$query .= "WHERE `name` = '". substr($connection->quote($mapname), 1, -1) ."' {$maplimit}";
	else 
		$query .= "WHERE `name` <> '' {$maplimit} ORDER BY `id`";
	
	$result = $connection->query($query);
	if($result instanceof PDOStatement && ($row = $result->fetch()))
	{
		$out = "O\nH\tmapid\tname\tscore\ttime\ttimes\tkills\tdeaths\n";
		do {
			$out .= "D\t{$row['id']}\t{$row['name']}\t{$row['score']}\t{$row['time']}\t{$row['times']}\t{$row['kills']}\t{$row['deaths']}\n";
		}
		while($row = $result->fetch());
	}
	else
		$out = "E\nH\terr\nD\tMap Data Not Found!\n";
}

// Output data
$num = strlen(preg_replace('/[\t\n]/','',$out));
print $out . "$\t" . $num . "\t$";
?>