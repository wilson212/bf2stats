<?php

/*
    Copyright (C) 2006  BF2Statistics

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

/********************************************
 * 11/14/05 v0.0.1 - ALPHA build            *
 * 11/14/05 v0.0.2 - Removed name parameter *
 * 11/15/05 v0.0.3 - Added redundancy check *
 * 11/26/05 v0.0.4 - Updated for SF         *
 * 01/03/06 v0.1 - BETA release             *
 * 01/25/06 v0.1.1 - Added allunlocks       *
 * 02/14/06 v0.1.2 - Updated for EF         *
 *                 - Fixed unlocks          *
 * 07/27/06 v0.1.3 - Added unlock tier 		*
 *	support									*
 *  07/27/06 v0.1.4 - Added bonus unlock 	*
 *	support									*
 * 06/12/10 - Fixed undefined unlock 		*
 * 02/04/12 - v1.0 Release, cleaned up		*
 ********************************************/

//Disable Zlib Compression
ini_set('zlib.output_compression', '0');
 
$pid = (isset($_GET['pid'])) ? $_GET['pid'] : false;

if (!$pid || !is_numeric($pid)) 
{
	print 'Invalid syntax!';
}
else
{
	// Import configuration
	require('includes/utils.php');
	$cfg = new Config();
	
	$out = "";
	$earned = 0;
	$availunlocks = 0;
	
	switch ($cfg->get('game_unlocks'))
	{
		case 0:
			// Get Player Data
			$connection = @mysql_connect($cfg->get('db_host'), $cfg->get('db_user'), $cfg->get('db_pass'));
			@mysql_select_db($cfg->get('db_name'), $connection);

			$query = "SELECT name, score, rank, usedunlocks FROM player WHERE id = {$pid}";
			$result = mysql_query($query) or die(mysql_error());
			$row = mysql_fetch_array($result);
			if (!$row) 
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
			$score = $row['score'];
			$rankunlocks = getRankUnlocks($score);
			
			// Determine Bonus Unlocks due to Kit Bdages
			$bonusunlocks = getBonusUnlocks($pid, $rank);
			
			// Available Unlocks
			$availunlocks = $rankunlocks + $bonusunlocks;
			
			// Check Used Unlocks
			$query = "SELECT COUNT(id) AS count FROM unlocks WHERE (id = {$pid}) AND (state = 's')";
			$result = mysql_query($query) or die(mysql_error());
			if (mysql_num_rows($result))
			{
				$row = mysql_fetch_array($result);
				$usedunlocks = $row['count'];
				
				// Determine total unlocks available
				$availunlocks -= $usedunlocks;
				
				// Update Unlocks Data
				$query = "UPDATE player SET availunlocks = {$availunlocks}, usedunlocks = {$usedunlocks} WHERE id = {$pid}";
				mysql_query($query) or die (mysql_error());
			}
			
			$query = "SELECT kit, state FROM unlocks WHERE (id = {$pid}) AND (kit < 78)";
			$result = mysql_query($query) or die(mysql_error());
			if (mysql_num_rows($result)) 
			{
				while ($row = mysql_fetch_array($result)) 
				{
					$out .= "D\t$row[kit]\t$row[state]\n";
				}
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
			
			// Close database connection
			@mysql_close($connection);
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
}

function checkUnlock($want, $need)
{
	global $pid;

	$query = "SELECT state FROM unlocks WHERE (id = {$pid}) AND (state = 's') AND (kit = {$need})";
	$result = mysql_query($query) or die(mysql_error());
	if (mysql_num_rows($result))
	{
		$query = "SELECT state, kit FROM unlocks WHERE (id = {$pid}) AND (kit = {$want})";
		$result = mysql_query($query) or die(mysql_error());
		$row = mysql_fetch_array($result);
		$return = "D\t$row[kit]\t$row[state]\n";
	}
	else
	{
		// Unlock NOT available yet. ;)
		$return = "";
	}
	return $return;
}

function getRankUnlocks($score) 
{
	// Determine Earned Unlocks due to Rank
	if ($score >= 50000) {$rankunlocks = 7;}
	elseif ($score >= 20000) {$rankunlocks = 6;}
	elseif ($score >= 8000) {$rankunlocks = 5;}
	elseif ($score >= 5000) {$rankunlocks = 4;}
	elseif ($score >= 2500) {$rankunlocks = 3;}
	elseif ($score >= 800) {$rankunlocks = 2;}
	elseif ($score >= 500) {$rankunlocks = 1;}
	else {$rankunlocks = 0;}
	return $rankunlocks;
}

function getBonusUnlocks($pid, $rank)
{
	global $cfg;
	
	// Check if Minimu Rank Unlocks obtained
	if ($rank < $cfg->get('game_unlocks_bonus_min')) 
	{
		return 0;
	}
	
	// Are bonus Unlocks available?
	if(!$cfg->get('game_unlocks_bonus')) 
	{
		return 0;
	}
	
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
	$query = "SELECT COUNT(id) AS count FROM awards WHERE id = {$pid} AND (awd IN ({$checkawds}) AND level = ".$cfg->get('game_unlocks_bonus').")";
	$result = mysql_query($query) or die(mysql_error());
	if (mysql_num_rows($result))
	{
		$row = mysql_fetch_array($result);
		return $row['count'];
	} 
	else 
	{
		return 0;
	}
}
?>