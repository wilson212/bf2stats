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
$pid = (isset($_GET['pid'])) ? intval($_GET['pid']) : false;
if(!$pid) 
{
	$out = "E\nH\tasof\terr\n" .
        "D\t" . time() . "\tInvalid Syntax!\n";
	$num = strlen(preg_replace('/[\t\n]/', '', $out));
	echo $out, "$\t$num\t$";
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
    
// Prepare output
$out = "";
$earned = 0;
$availunlocks = 0;

switch(Config::Get('game_unlocks'))
{
    case 0:
        // Get Player Data
        $result = $connection->query("SELECT `name`, `score`, `rank`, `usedunlocks` FROM `player` WHERE `id` = {$pid}");
        if($result instanceof PDOStatement)
        {
            $row = $result->fetch();
            if(!$row) 
            {
                $num = 0;
                $out = "O\n" .
                       "H\tpid\tnick\tasof\n" .
                       "D\t$pid\tNo_Player\t" . time() . "\n" .
                       "H\tenlisted\tofficer\nD\t0\t0\n" .
                       "H\tid\tstate\tD\t11\tn\tD\t22\tn\t" .
                       "D\t33\tn\tD\t44\tn\tD\t55\tn\tD\t66\tn\t" .
                       "D\t77\tn\tD\t88\tn\tD\t99\tn\tD\t111\tn\t" .
                       "D\t222\tn\tD\t333\tn\tD\t444\tn\tD\t555\tn\n"; 
                       
                $num += strlen(preg_replace('/[\t\n]/','',$out));
                
                print $out;
                print "$\t$num\t$";
                die();
            }
            $nick = $row['name'];
            $rank = $row['rank'];
            
            // Determine Earned Unlocks due to Rank
            $rankunlocks = getRankUnlocks($rank);
            
            // Determine Bonus Unlocks due to Kit Bdages
            $bonusunlocks = getBonusUnlocks($pid, $rank);
            
            // Available Unlocks
            $availunlocks = $rankunlocks + $bonusunlocks;
            
            // Check Used Unlocks
            $query = "SELECT COUNT(`id`) AS `count` FROM `unlocks` WHERE (`id` = {$pid}) AND (`state` = 's')";
            $result = $connection->query($query);
            if($result instanceof PDOStatement)
            {
                $usedunlocks = $result->fetchColumn();
                
                // Determine total unlocks available
                $availunlocks -= $usedunlocks;
                
                // Update Unlocks Data
                $query = "UPDATE player SET availunlocks = {$availunlocks}, usedunlocks = {$usedunlocks} WHERE id = {$pid}";
                $connection->exec($query);
            }
            
            $query = "SELECT `kit`, `state` FROM `unlocks` WHERE (`id` = {$pid}) AND (`kit` < 78)";
            $result = $connection->query($query);
            if($result instanceof PDOStatement)
            {
                while($row = $result->fetch()) 
                    $out .= "D\t{$row['kit']}\t{$row['state']}\n";
            } 
            else 
            {
                for ($i = 11; $i < 80; $i += 11) 
                {
                    $out .= "D\t$i\tn\n";
                }
            }
            
            $out .= checkUnlock(88, 22);
            $out .= checkUnlock(99, 33);
            $out .= checkUnlock(111, 44);
            $out .= checkUnlock(222, 55);
            $out .= checkUnlock(333, 66);
            $out .= checkUnlock(444, 11);
            $out .= checkUnlock(555, 77);
            
        }
        break;
    case 1:
        $nick = "All_Unlocks";
        for ($i = 11; $i < 100; $i += 11) {$out .= "D\t$i\ts\n";}
        for ($i = 111; $i < 556; $i += 111)	{$out .= "D\t$i\ts\n";}
        break;
    default:
        $nick = "No_Unlocks";
        for ($i = 11; $i < 100; $i += 11) {$out .= "D\t$i\tn\n";}
        for ($i = 111; $i < 556; $i += 111)	{$out .= "D\t$i\tn\n";}
        break;
}

// Build Response Header
$head = "O\n" .
      "H\tpid\tnick\tasof\n" .
      "D\t$pid\t$nick\t" . time() . "\n" .
      "H\tenlisted\tofficer\n" .
      "D\t$availunlocks\t0\n" .
      "H\tid\tstate\n";

$num = strlen(preg_replace('/[\t\n]/','',$head));
$num += strlen(preg_replace('/[\t\n]/','',$out));

print $head . $out . "$\t" . $num . "\t$";


function checkUnlock($want, $need)
{
	global $pid, $connection;

	$query = "SELECT `state` FROM `unlocks` WHERE (`id` = {$pid}) AND (`state` = 's') AND (`kit` = {$need})";
	$result = $connection->query($query);
    if($result instanceof PDOStatement && $result->rowCount() > 0)
    {
		$query = "SELECT `state`, `kit` FROM `unlocks` WHERE (`id` = {$pid}) AND (`kit` = {$want})";
        $result = $connection->query($query);
        if($result instanceof PDOStatement)
        {
            $row = $result->fetch();
            $return = "D\t{$row['kit']}\t{$row['state']}\n";
        } 
	}
	else
	{
		// Unlock NOT available yet. ;)
		$return = "";
	}
	return $return;
}

function getRankUnlocks($rank) 
{
	// Determine Earned Unlocks due to Rank
	if ($rank >= 9) {$rankunlocks = 7;}          // Unlock7 => Master Gunnery Sergeant
	elseif ($rank >= 7) {$rankunlocks = 6;}      // Unlock6 => Master Sergeant
	elseif ($rank >= 6) {$rankunlocks = 5;}      // Unlock5 => Gunnery Sergeant
	elseif ($rank >= 5) {$rankunlocks = 4;}      // Unlock4 => Staff Sergeant
	elseif ($rank >= 4) {$rankunlocks = 3;}      // Unlock3 => Sergeant
	elseif ($rank >= 3) {$rankunlocks = 2;}      // Unlock2 => Corporal
	elseif ($rank >= 2) {$rankunlocks = 1;}      // Unlock1 => Lance Corporal
	else {$rankunlocks = 0;}
	return $rankunlocks;
}

function getBonusUnlocks($pid, $rank)
{
	global $cfg, $connection;
	
	// Check if Minimu Rank Unlocks obtained
	if ($rank < Config::Get('game_unlocks_bonus_min')) 
		return 0;
	
	// Are bonus Unlocks available?
	if(!Config::Get('game_unlocks_bonus')) 
		return 0;
	
	// Define Kit Badges Array
	$kitbadges = array(
		"1031119",		// Assult
		"1031120",		// Anti-tank
		"1031109",		// Sniper
		"1031115",		// Spec-Ops
		"1031121",		// Support
		"1031105",		// Engineer
		"1031113"		// Medic
	);
	
	// Count number of kit bagdes obtained
	$checkawds = "'" . implode("','", $kitbadges) . "'";
	$query = "SELECT COUNT(`id`) AS `count` FROM `awards` WHERE `id` = {$pid} AND (`awd` IN ({$checkawds}) AND `level` = ". Config::Get('game_unlocks_bonus').")";
	$result = $connection->query($query);
    return ($result instanceof PDOStatement) ? $result->fetchColumn() : 0;
}
?>