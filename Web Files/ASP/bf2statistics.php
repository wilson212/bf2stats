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

/********************************************************
 * 06/12/10 - Changed kills/deaths insert method        *
 * 06/12/10 - Kills converted into Combat scores        *
 * 06/12/10 - Removed passengerAssists/driverAssists    *
 * 06/12/10 - Removed cpNeutralizeAssists/cpNeutralizes *
 * 06/12/10 - Fixed Opponent/Victim kills               *
 * 06/12/10 - Fixed missed wins/loss                    *
 * 02/04/12 - Reviewed for Release	                    *
 ********************************************************/
 
//Disable Zlib Compression
ini_set('zlib.output_compression', '0');

//Make Sure Script doesn't timeout even if the user disconnects!
set_time_limit(0);
ignore_user_abort(true);

// Import configuration
require('includes/utils.php');
$cfg = new Config();
DEFINE("_ERR_RESPONSE","E\nH\tresponse\nD\t<font color=\"red\">ERROR</font>: ");

// Check remote host is authorised (simple security check)
if (!checkIpAuth($cfg->get('game_hosts'))) 
{
	$errmsg = "Unauthorised Access Attempted! (IP: " . $_SERVER['REMOTE_ADDR'] . ")";
	ErrorLog($errmsg, 0);
	die(_ERR_RESPONSE.$errmsg);
}

// Get URL POST data
$rawdata = file_get_contents('php://input');

// Seperate data
if ($rawdata) 
{
	$gooddata = explode('\\', $rawdata);
} 
else 
{
	$errmsg = "SNAPSHOT Data NOT found!";
	ErrorLog($errmsg, 1);
	die(_ERR_RESPONSE.$errmsg);
}

// Make key/value pairs
$prefix = $gooddata[0];
$mapname = strtolower($gooddata[1]);
for ($x = 2; $x < count($gooddata); $x += 2) 
{
	$data[$gooddata[$x]] = $gooddata[$x + 1];
}

// Check for Complete Snapshot data
if ($data['EOF'] != 1) 
{
	$errmsg = "SNAPSHOT Data NOT complete!";
	ErrorLog($errmsg, 1);
	die(_ERR_RESPONSE.$errmsg);
}


// Import Backend Awards Data
require('includes/data.awards.php');
$awardsdata = buildAwardsData($data['v']);
$backendawardsdata = buildBackendAwardsData($data['v']);

// Generate SNAPSHOT Filename
$mapdate = date('Ymd_Hi', (int)$data['mapstart']);
$stats_filename  = '';
if ($prefix != '') {$stats_filename .= $prefix . '-';}
$stats_filename .= $mapname . '_' . $mapdate . $cfg->get('stats_ext');

// SNAPSHOT Data OK
$errmsg = "SNAPSHOT Data Complete ({$mapname}:{$mapdate})";
ErrorLog($errmsg, 3);

// Create SNAPSHOT backup file
if ($data['import'] != 1)
{
	$file = @fopen( chkPath($cfg->get('stats_logs')) . $stats_filename, 'wb');
	@fwrite($file, $rawdata);
	@fclose($file);
	
	$errmsg = "SNAPSHOT Data Logged (" . chkPath($cfg->get('stats_logs')) . $stats_filename . ")";
	ErrorLog($errmsg, 3);
	
	// Tell the game server that the snapshot has been received
	$out = "O\n" .
		"H\tresponse\tmapname\tmapstart\n" .
		"D\tOK\t$mapname\t$data[mapstart]\n";
	echo $out . "$\tOK\t$";
	flush();
}

// Check Database Version
$curdbver = getDbVer();
if ($curdbver != $cfg->get('db_expected_ver')) 
{
	$errmsg = "Database version expected: ".$cfg->get('db_expected_ver').", Found: {$curdbver}";
	ErrorLog($errmsg, 1);
	die();
} 
else 
{
	$errmsg = "Database version expected: ".$cfg->get('db_expected_ver').", Found: {$curdbver}";
	ErrorLog($errmsg, 3);
}

// Open database connection
$connection = @mysql_connect($cfg->get('db_host'), $cfg->get('db_user'), $cfg->get('db_pass'));
@mysql_select_db($cfg->get('db_name'), $connection);

// Global variables
$globals = array();

//Determine Round Time
$globals['roundtime'] = $data[mapend] - $data[mapstart];

// Initialise Other Global Data
$globals['mapscore'] = $globals['mapkills'] = $globals['mapdeaths'] = 0;
$globals['team1_pids'] = $globals['team2_pids'] = 0;			// Team Player Counts
$globals['team1_pids_end'] = $globals['team2_pids_end'] = 0;	// Team Player Counts
$globals['custommap'] = 0;

// Determine GameMode
$globals['mode0'] = 0;	// Mode: gpm_cq	= Conquest
$globals['mode1'] = 0;	// Mode: gpm_sl	= Supply Lines
$globals['mode2'] = 0;	// Mode: gpm_coop	= Co-op (ie, 'Bots)
if (isset($data["gm"])) 
{
	// Unknown will get set to 99, which effectively ignores this mode
	$globals["mode".$data["gm"]] = 1;
}

// Check if this is a Central DB Snapshot update
if(isset($data["cdb_update"])) 
{
	$centralupdate = $data["cdb_update"];
	ErrorLog("Central SNAPSHOT Update Type: $centralupdate",3);
} 
else 
{
	$centralupdate = 0;
}

// Minimum player & time check
if ($data['pc'] >= $cfg->get('stats_players_min') && $globals['roundtime'] >= $cfg->get('stats_min_game_time'))
{
	ErrorLog("Begin Processing ($mapname)...",3);
	
	/********************************
	* Check for 'Custom Map'
	********************************/
	if ($data['m'] == 99) 
    {
		// Set Custom Map Bit
		$globals['custommap'] = 1;
        
		// Check for existing data
		$query = "SELECT id FROM mapinfo WHERE name = '{$mapname}'";
		$result = mysql_query($query);
		checkSQLResult ($result, $query);
		if (mysql_num_rows($result)) 
        {
			// Get Existing MapID#
			$rowmapid = mysql_fetch_array($result);
			$mapid = $rowmapid['id'];
			ErrorLog(" - Existing Custom Map ($mapid)...",3);
		} 
        else 
        {
			// Get next Map ID#
			$query = "SELECT MAX(id) as `id` FROM mapinfo WHERE id >= " . $cfg->get('game_custom_mapid');
			$result = mysql_query($query);
			checkSQLResult ($result, $query);
			if (mysql_num_rows($result) == 1) 
            {
				$rowmapid = mysql_fetch_array($result);
				if (is_null($rowmapid['id']) || $rowmapid['id'] < $cfg->get('game_custom_mapid')) 
                {
					$mapid = $cfg->get('game_custom_mapid');
				} 
                else 
                {
					$mapid = $rowmapid['id'] + 1;
				}
			} 
            else 
            {
				$mapid = $cfg->get('game_custom_mapid');
			}
			ErrorLog(" - New Custom Map ($mapid)...",2);
		}
	} 
    elseif ($data['m'] >= $cfg->get('game_custom_mapid')) 
    {
		// Set Custom Map Bit
		$globals['custommap'] = 1;
		$mapid = $data['m'];
		ErrorLog(" - Predefined Custom Map ($mapid)...",3);
	} 
    else 
    {
		$mapid = $data['m'];
		ErrorLog(" - Standard Map ($mapid)...",3);
	}
	
	ErrorLog("Found {$data['pc']} Player(s)...",3);
	
	/********************************
	* Process 'Player Data'
	********************************/
	$totalplayers = $data['pc'];
	for ($x = 0; $x < $totalplayers; $x++)
	{
		// Check player exisits in SNAPSHOT and that they meet the minimum required play time
		if ($data["pID_$x"] && ($data["ctime_$x"] >= $cfg->get('stats_min_player_game_time'))) 
        {
			// Set global variables
			$globals['mapscore'] += $data["rs_$x"];
			$globals['mapkills'] += $data["kills_$x"];
			$globals['mapdeaths'] += $data["deaths_$x"];

			// Calculate wins/losses 
			$wins = $losses = 0;
			if ($data["t_$x"])
			{
				if ($data['win'] == $data["t_$x"]) {$wins = 1;}
				else {$losses = 1;}
			}

			// Fix LAN IP's (ignore LocalHost as that's for 'bots)
			if(checkPrivateIp($data["ip_$x"]) && $data["ip_$x"] != '127.0.0.1') 
            {
				$data["ip_$x"] = $cfg->get('stats_lan_override');
			}
			
			// Fix Override IP's
			$local_pids = $cfg->get('stats_local_pids');
			if (count($local_pids))
			{
				for ($i = 0; $i < count($local_pids); $i += 2)
				{
					if ($local_pids[$i] == $data["name_$x"])
					{
						$data["ip_$x"] = $local_pids[$i + 1];
						break;
					}
				}
			}
			
			/*omero, 2006-04-13
				this is re-introduced from previous version,
				just in case a N/A address still appears.
				The AI players address is now handled in 
				the bf2.stats.stats python module.
			*/	
			// Fix N/A ip addresses and fix negative's
			if ($data["ip_$x"] == 'N/A') $data["ip_$x"] = '127.0.0.1';
			if ($data["tlw_$x"] < 0) $data["tlw_$x"] = 0;
			if ($data["tsm_$x"] < 0) $data["tsm_$x"] = 0;
			if ($data["tsl_$x"] < 0) $data["tsl_$x"] = 0;

			/********************************
			* Process 'Player' 
			********************************/
			ErrorLog("Processing Player (".$data["pID_$x"].")",3);
			$query = "SELECT * FROM player WHERE id = " . $data["pID_$x"] . "";
			$result = mysql_query($query);
			checkSQLResult ($result, $query);
			if ($data["t_$x"]) 
            {
                $complete = 1;
            }
			else 
            {
                $complete = 0;
            }
            
			if (!mysql_num_rows($result))
			{
				ErrorLog("Adding NEW Player (".$data["pID_$x"].")",3);
                
				// Find country
				$query = "SELECT country FROM ip2nation WHERE ip < INET_ATON('" . $data["ip_$x"] . "') ORDER BY ip DESC LIMIT 1";
				$result = mysql_query($query);
				checkSQLResult ($result, $query);
				if (!mysql_num_rows($result)) 
                {
					$country = 'xx';
				} 
                else 
                {
					$row = mysql_fetch_array($result);
					$country = $row['country'];
				}

				// Insert information 
				$query = "INSERT INTO player SET
					id = " . $data["pID_$x"] . ",
					name = '" . $data["name_$x"] . "',
					country = '{$country}',
					time = " . $data["ctime_$x"] . ",
					rounds = {$complete},
					ip = '" . $data["ip_$x"] . "',
					score = " . $data["rs_$x"] . ",
					cmdscore = " . $data["cs_$x"] . ",
		            skillscore = (" . $data["kk0_$x"] . " + " . $data["kk1_$x"] . " + " . $data["kk2_$x"] . " + " . $data["kk3_$x"] . " + " . $data["kk4_$x"] . " + " . $data["kk5_$x"] . " + " . $data["kk6_$x"] . ") * 2,
					teamscore = " . $data["ts_$x"] . ",
					kills = " . $data["kk0_$x"] . " + " . $data["kk1_$x"] . " + " . $data["kk2_$x"] . " + " . $data["kk3_$x"] . " + " . $data["kk4_$x"] . " + " . $data["kk5_$x"] . " + " . $data["kk6_$x"] . ",
                    deaths = " . $data["dk0_$x"] . " + " . $data["dk1_$x"] . " + " . $data["dk2_$x"] . " + " . $data["dk3_$x"] . " + " . $data["dk4_$x"] . " + " . $data["dk5_$x"] . " + " . $data["dk6_$x"] . ",
					captures = " . $data["cpc_$x"] . ",
					captureassists = " . $data["cpa_$x"] . ",
					defends = " . $data["cpd_$x"] . ",
					damageassists = " . $data["ka_$x"] . ",
					heals = " . $data["he_$x"] . ",
					revives = " . $data["rev_$x"] . ",
					ammos = " . $data["rsp_$x"] . ",
					repairs = " . $data["rep_$x"] . ",
					targetassists = " . $data["tre_$x"] . ",
					driverspecials = " . $data["drs_$x"] . ",
					teamkills = " . $data["tmkl_$x"] . ",
					teamdamage = " . $data["tmdg_$x"] . ",
					teamvehicledamage = " . $data["tmvd_$x"] . ",
					suicides = " . $data["su_$x"] . ",
					killstreak = " . $data["ks_$x"] . ",
					deathstreak = " . $data["ds_$x"] . ",
					rank = " . $data["rank_$x"] . ",
					banned = " . $data["ban_$x"] . ",
					kicked = " . $data["kck_$x"] . ",
					cmdtime = " . $data["tco_$x"] . ",
					sqltime = " . $data["tsl_$x"] . ",
					sqmtime = " . $data["tsm_$x"] . ",
					lwtime = " . $data["tlw_$x"] . ",
					wins = {$wins},
					losses = {$losses},
					availunlocks = 0,
					usedunlocks = 0,
					joined = " . time() . ",
					rndscore = " . $data["rs_$x"] . ",
					lastonline = " . time() . ",
					mode0 = " . $globals['mode0'] . ",
					mode1 = " . $globals['mode1'] . ",
					mode2 = " . $globals['mode2'] . "
				";
				$result = mysql_query($query);
				checkSQLResult ($result, $query);
				
				// Insert unlocks
				for ($i = 11; $i < 100; $i += 11)
				{
					$query = "INSERT INTO unlocks SET
						id = " . $data["pID_$x"] . ",
						kit = {$i},
						state = 'n'
					";
					$result = mysql_query($query);
					checkSQLResult ($result, $query);
				}
				for ($i = 111; $i < 556; $i += 111)
				{
					$query = "INSERT INTO unlocks SET
						id = " . $data["pID_$x"] . ",
						kit = {$i},
						state = 'n'
					";
					$result = mysql_query($query);
					checkSQLResult ($result, $query);
				}
			}
			else
			{
				ErrorLog("Updating EXISTING Player (".$data["pID_$x"].")",3);
				$row = mysql_fetch_array($result);

				// Check IP
				if ($row['ip'] != $data["ip_$x"])
				{
					$query2 = "SELECT country FROM ip2nation WHERE ip < INET_ATON('" . $data["ip_$x"] . "') ORDER BY ip DESC LIMIT 1";
					$result2 = mysql_query($query2);
					checkSQLResult ($result2, $query2);
					if (!mysql_num_rows($result2)) 
                    {
						$country = 'xx';
					} 
                    else 
                    {
						$row2 = mysql_fetch_array($result2);
						$country = $row2['country'];
					}
				}
				else 
                {
                    $country = $row['country'];
                }

				// Verify/Correct Rank
				if ($cfg->get('stats_rank_check')) 
                {
					$score = $row['score'] + $data["rs_$x"];
					$rank  = $data["rank_$x"];
					ErrorLog("Checking Rank for Player (".$data["pID_$x"].") : Score:{$score} : Rank:{$rank}",3);
					$expRank = array();
					
					// NOTE: Ranks 1SG/SGM/BG/MG/SMOC/GEN cannot be awarded here.
					if ($score >= 200000) {$expRank[0] = 20;$expRank[1] = 20;}
					elseif ($score >= 150000) {$expRank[0] = 17;$expRank[1] = 19;}
					elseif ($score >= 125000) {$expRank[0] = 16;$expRank[1] = 16;}
					elseif ($score >= 115000) {$expRank[0] = 15;$expRank[1] = 15;}
					elseif ($score >= 90000) {$expRank[0] = 14;$expRank[1] = 14;}
					elseif ($score >= 75000) {$expRank[0] = 13;$expRank[1] = 13;}
					elseif ($score >= 60000) {$expRank[0] = 12;$expRank[1] = 12;}
					elseif ($score >= 50000) {$expRank[0] = 9;$expRank[1] = 11;}
					elseif ($score >= 20000) {$expRank[0] = 7;$expRank[1] = 8;}
					elseif ($score >= 8000) {$expRank[0] = 6;$expRank[1] = 6;}
					elseif ($score >= 5000) {$expRank[0] = 5;$expRank[1] = 5;}
					elseif ($score >= 2500) {$expRank[0] = 4;$expRank[1] = 4;}
					elseif ($score >= 800) {$expRank[0] = 3;$expRank[1] = 3;}
					elseif ($score >= 500) {$expRank[0] = 2;$expRank[1] = 2;}
					elseif ($score >= 150) {$expRank[0] = 1;$expRank[1] = 1;}
					else {$expRank[0] = 0;$expRank[1] = 0;}
					
					// Only update if Rank is less than expected.
					if($rank < $expRank[0] || $rank > $expRank[1])
                    {
						// Rank seems to be messed up, will reset to minimum rank for this level
						$errmsg = "Rank Correction (".$data["pID_$x"]."): " .
							"Score:".$score."; " . 
							"Expected:".$expRank[0]."-".$expRank[1]."; " .
							"Found:".$data["rank_$x"]."; " .
							"New Rank:".$expRank[0];
						ErrorLog($errmsg,2);
						$data["rank_$x"] = $expRank[0];
					}
				} 
                else 
                {
					// Fail-safe in-case rank data was not obtained and reset to '0' in-game.
					$rank = $data["rank_$x"];
					$rank_db = $row['rank'];
					if($rank_db > $rank) 
                    {
						// SNAPSHOT rank data appears to be incorrect, will use current db rank
						$data["rank_$x"] = $rank_db;
						$errmsg = "Rank Correction (".$data["pID_$x"]."), using db rank ({$rank_db})";
						ErrorLog($errmsg,2);
					}
				}
				
				// Calculate kill/deathstreak
				$killstreak = ($row['killstreak'] > $data["ks_$x"]) ? $row['killstreak'] : $data["ks_$x"];
				$deathstreak = ($row['deathstreak'] > $data["ds_$x"]) ? $row['deathstreak'] : $data["ds_$x"];
				
				// Calculate best round score
				$rndscore = ($row['rndscore'] > $data["rs_$x"]) ? $row['rndscore'] : $data["rs_$x"];
				
				// Check if Minimal Central Update
				if($centralupdate == 2) 
                {
					// Ignore any Rank Data in SnapShot as this could mess up current data
					$data["rank_$x"] = $row['rank'];
				}
				
				// Calculate rank change
				$chng = $decr = 0;
				if($data["rank_$x"] != $row['rank'])
				{
					if ($data["rank_$x"] > $row['rank']) {$chng = 1;}
					else {$decr = 1;}
				}
				
				// Update information 
				$query = "UPDATE player SET
					name = '" . $data["name_$x"] . "',
					country = '{$country}',
					time = `time` + " . $data["ctime_$x"] . ",
					rounds = `rounds` + {$complete},
					ip = '" . $data["ip_$x"] . "',
					score = `score` + " . $data["rs_$x"] . ",
					cmdscore = `cmdscore` + " . $data["cs_$x"] . ",
					skillscore = {$row[skillscore]} + (" . $data["kk0_$x"] . " + " . $data["kk1_$x"] . " + " . $data["kk2_$x"] . " + " . $data["kk3_$x"] . " + " . $data["kk4_$x"] . " + " . $data["kk5_$x"] . " + " . $data["kk6_$x"] . ") * 2,
					teamscore = `teamscore` + " . $data["ts_$x"] . ",
					kills = {$row[kills]} + " . $data["kk0_$x"] . " + " . $data["kk1_$x"] . " + " . $data["kk2_$x"] . " + " . $data["kk3_$x"] . " + " . $data["kk4_$x"] . " + " . $data["kk5_$x"] . " + " . $data["kk6_$x"] . ",
                    deaths = {$row[deaths]} + " . $data["dk0_$x"] . " + " . $data["dk1_$x"] . " + " . $data["dk2_$x"] . " + " . $data["dk3_$x"] . " + " . $data["dk4_$x"] . " + " . $data["dk5_$x"] . " + " . $data["dk6_$x"] . ",
					captures = `captures` + " . $data["cpc_$x"] . ",
					captureassists = `captureassists` + " . $data["cpa_$x"] . ",
					defends = `defends` + " . $data["cpd_$x"] . ",
					damageassists = `damageassists` + " . $data["ka_$x"] . ",
					heals = `heals` + " . $data["he_$x"] . ",
					revives = `revives` + " . $data["rev_$x"] . ",
					ammos = `ammos` + " . $data["rsp_$x"] . ",
					repairs = `repairs` + " . $data["rep_$x"] . ",
					targetassists = `targetassists` + " . $data["tre_$x"] . ",
					driverspecials = `driverspecials` + " . $data["drs_$x"] . ",
					teamkills = `teamkills` + " . $data["tmkl_$x"] . ",
					teamdamage = `teamdamage` + " . $data["tmdg_$x"] . ",
					teamvehicledamage = `teamvehicledamage` + " . $data["tmvd_$x"] . ",
					suicides = `suicides` + " . $data["su_$x"] . ",
					killstreak = {$killstreak},
					deathstreak = {$deathstreak},
					rank = " . $data["rank_$x"] . ",
					banned = `banned` + " . $data["ban_$x"] . ",
					kicked = `kicked` + " . $data["kck_$x"] . ",
					cmdtime = `cmdtime` + " . $data["tco_$x"] . ",
					sqltime = `sqltime` + " . $data["tsl_$x"] . ",
					sqmtime = `sqmtime` + " . $data["tsm_$x"] . ",
					lwtime = `lwtime` + " . $data["tlw_$x"] . ",
					wins = `wins` + {$wins},
					losses = `losses` + {$losses},
					rndscore = {$rndscore},
					lastonline = " . time() . ",
					mode0 = `mode0` + " . $globals['mode0'] . ",
					mode1 = `mode1` + " . $globals['mode1'] . ",
					mode2 = `mode2` + " . $globals['mode2'] . ",
					chng = {$chng},
					decr = {$decr}
					WHERE id = " . $data["pID_$x"] . "
				";
				$result = mysql_query($query);
				checkSQLResult ($result, $query);
			}
			
			/********************************
			* Process 'Player History' 
			********************************/			
			// Insert Player History (for Rising Star Leaderboard) 
			$query = "INSERT INTO player_history SET
				id = " . $data["pID_$x"] . ",
				timestamp = " . time() . ",
				time = " . $data["ctime_$x"] . ",
				score = " . $data["rs_$x"] . ",
				cmdscore = " . $data["cs_$x"] . ",
				skillscore = (" . $data["kk0_$x"] . " + " . $data["kk1_$x"] . " + " . $data["kk2_$x"] . " + " . $data["kk3_$x"] . " + " . $data["kk4_$x"] . " + " . $data["kk5_$x"] . " + " . $data["kk6_$x"] . ") * 2,
				teamscore = " . $data["ts_$x"] . ",
				kills = " . $data["kk0_$x"] . " + " . $data["kk1_$x"] . " + " . $data["kk2_$x"] . " + " . $data["kk3_$x"] . " + " . $data["kk4_$x"] . " + " . $data["kk5_$x"] . " + " . $data["kk6_$x"] . ",
                deaths = " . $data["dk0_$x"] . " + " . $data["dk1_$x"] . " + " . $data["dk2_$x"] . " + " . $data["dk3_$x"] . " + " . $data["dk4_$x"] . " + " . $data["dk5_$x"] . " + " . $data["dk6_$x"] . ",
				rank = " . $data["rank_$x"];
			$result = mysql_query($query);
			checkSQLResult ($result, $query);	
			
			/********************************
			* Process 'Army'
			********************************/
			ErrorLog("Processing Army Data (".$data["pID_$x"].")",3);
			$army = $data["a_$x"];
			
			// Count Players in Team
			if($army == $data["ra1"]) 
            {	
                // Team 1 Player
				$globals['team1_pids']++;
				if ($data["c_$x"]) {$globals['team1_pids_end']++;}
			}
			if($army == $data["ra2"]) 
            {	
                // Team 2 Player
				$globals['team2_pids']++;
				if ($data["c_$x"]) {$globals['team2_pids_end']++;}
			}
			
			if($data['v'] != 'xpack') 
            {
				$data["ta3_$x"] = $data["ta4_$x"] = $data["ta5_$x"] = 0;
				$data["ta6_$x"] = $data["ta7_$x"] = $data["ta8_$x"] = 0;
			}
				
			if($data['v'] != 'poe2') 
            {
				$data["ta10_$x"] = $data["ta11_$x"] = 0;
			}
			
			if($data['v'] != 'aix2') 
            {
				$data["ta12_$x"] = 0;
			}
			
			// Check for missing CANADIAN Army
			if(!$data["ta13_$x"]) 
            {			
				$data["ta13_$x"] = 0;
			}
			
			// Check for missing EU Army
			if(!$data["ta9_$x"])
            {
				$data["ta9_$x"] = 0;
			}
			
			$query = "SELECT * FROM army WHERE id = " . $data["pID_$x"] . "";
			$result = mysql_query($query);
			checkSQLResult ($result, $query);
			if (!mysql_num_rows($result))
			{
				// Insert information
				$query = "INSERT INTO army SET
					id = " . $data["pID_$x"] . ",
					time0 = " . $data["ta0_$x"] . ",
					time1 = " . $data["ta1_$x"] . ",
					time2 = " . $data["ta2_$x"] . ",
					time3 = " . $data["ta3_$x"] . ",
					time4 = " . $data["ta4_$x"] . ",
					time5 = " . $data["ta5_$x"] . ",
					time6 = " . $data["ta6_$x"] . ",
					time7 = " . $data["ta7_$x"] . ",
					time8 = " . $data["ta8_$x"] . ",
					time9 = " . $data["ta9_$x"] . ",
					time10 = " . $data["ta10_$x"] . ",
					time11 = " . $data["ta11_$x"] . ",
					time12 = " . $data["ta12_$x"] . ",
					time13 = " . $data["ta13_$x"] . "
				";
				if ($army < 14) {	// Ignore Unknown Army
					$query .= ", win" . $army . " = {$wins},
						loss" . $army . " = {$losses},
						score" . $army . " = " . $data["rs_$x"] . ",
						best" . $army . " = " . $data["rs_$x"] . ",
						worst" . $army . " = " . $data["rs_$x"] . "
					";
				}
				$result = mysql_query($query);
				checkSQLResult ($result, $query);
			}
			else
			{
				$row = mysql_fetch_array($result);
				// Update information
				$query = "UPDATE army SET
					time0 = `time0` + " . $data["ta0_$x"] . ",
					time1 = `time1` + " . $data["ta1_$x"] . ",
					time2 = `time2` + " . $data["ta2_$x"] . ",
					time3 = `time3` + " . $data["ta3_$x"] . ",
					time4 = `time4` + " . $data["ta4_$x"] . ",
					time5 = `time5` + " . $data["ta5_$x"] . ",
					time6 = `time6` + " . $data["ta6_$x"] . ",
					time7 = `time7` + " . $data["ta7_$x"] . ",
					time8 = `time8` + " . $data["ta8_$x"] . ",
					time9 = `time9` + " . $data["ta9_$x"] . ",
					time10 = `time10` + " . $data["ta10_$x"] . ",
					time11 = `time11` + " . $data["ta11_$x"] . ",
					time12 = `time12` + " . $data["ta12_$x"] . ",
					time13 = `time13` + " . $data["ta13_$x"] . "
				";
				if ($army < 14) 
                {	
                    // Ignore Unknown Army
					// Calculate best/worst score
					$best = ($row["best$army"] > $data["rs_$x"]) ? $row["best$army"] : $data["rs_$x"];
					$worst = ($row["worst$army"] < $data["rs_$x"]) ? $row["worst$army"] : $data["rs_$x"];
					
					$query .= ", win" . $army . " = win" . $army . " + {$wins},
						loss" . $army . " = loss" . $army . " + {$losses},
						score" . $army . " = score" . $army . " + " . $data["rs_$x"] . ",
						best" . $army . " = {$best},
						worst" . $army . " = {$worst}
						WHERE id = " . $data["pID_$x"] . "
					";
				}
				$result = mysql_query($query);
				checkSQLResult ($result, $query);
			}
		
		/********************************
		* Process 'Kills' 
		********************************/ 
		ErrorLog("Processing Kill Data (".$data["pID_$x"].")",3);
		$mvns = array();
		for ($i = 0, $count = 0; $i < count($gooddata); $i++)
		{
			if ($gooddata[$i] == "mvns_$x")
			{
				$mvns[$count] = $gooddata[$i + 1];
				$mvns[++$count] = $gooddata[$i + 3];
				$count++;
			}
		}
		for ($i = 0; $i < count($mvns); $i += 2)
		{
			$query = "SELECT count FROM kills WHERE (attacker = " . $data["pID_$x"] . ") AND (victim = {$mvns[$i]})";
			$result = mysql_query($query);
			checkSQLResult ($result, $query);
			if (!mysql_num_rows($result))
			{
				// Insert information
				$query = "INSERT INTO kills SET
					attacker = " . $data["pID_$x"] . ",
					victim = {$mvns[$i]},
					count = " . $mvns[$i + 1] . "
				";
				$result = mysql_query($query);
				checkSQLResult ($result, $query);
			}
			else
			{
				$row = mysql_fetch_array($result);
				
                // Only highest value can be count
		        $killcount = ($row['count'] > $mvns[$i + 1]) ? $row['count'] : $mvns[$i + 1];
				
				// Update information
				$query = "UPDATE kills SET
					count = {$killcount}
					WHERE (attacker = " . $data["pID_$x"] . ") AND (victim = {$mvns[$i]})
				";
				$result_chk = mysql_query($query);
				checkSQLResult ($result_chk, $query);
				
				// Tag item as done
				$mvns[$i + 1] = 0;
			}
		}
			
			/********************************
			* Process 'Vehicles'
			********************************/
			ErrorLog("Processing Vehicle Data (".$data["pID_$x"].")",3);
			$query = "SELECT * FROM vehicles WHERE id = " . $data["pID_$x"] . "";
			$result = mysql_query($query);
			checkSQLResult ($result, $query);
			if (!mysql_num_rows($result))
			{
				// Insert information
				$query = "INSERT INTO vehicles SET id = " . $data["pID_$x"] . ", ";
				for ($i = 0; $i < 7; $i++)
				{
					$query .= "time" . $i . " = " . $data["tv$i" . '_' . $x] . ",
						kills" . $i . " = " . $data["kv$i" . '_' . $x] . ",
						deaths" . $i . " = " . $data["bv$i" . '_' . $x] . ",
						rk" . $i . " = " . $data["kvr$i" . '_' . $x] . ",
					";
				}
				$query .= "timepara = " . $data["tvp_$x"];
				$result = mysql_query($query);
				checkSQLResult ($result, $query);
			}
			else
			{
				$row = mysql_fetch_array($result);

				// Update information
				$query = "UPDATE vehicles SET ";
				for ($i = 0; $i < 7; $i++)
				{
					$query .= "time" . $i . " = `time$i` + " . $data["tv$i" . '_' . $x] . ",
						kills" . $i . " = `kills$i` + " . $data["kv$i" . '_' . $x] . ",
						deaths" . $i . " = `deaths$i` + " . $data["bv$i" . '_' . $x] . ",
						rk" . $i . " = `rk$i` + " . $data["kvr$i" . '_' . $x] . ",
					";
				}
				$query .= "timepara = `timepara` + " . $data["tvp_$x"] . "
					WHERE id = " . $data["pID_$x"] . "
				";
				$result = mysql_query($query);
				checkSQLResult ($result, $query);
			}
			
			/********************************
			* Process 'Kits'
			********************************/
			ErrorLog("Processing Kit Data (".$data["pID_$x"].")",3);
			$query = "SELECT * FROM kits WHERE id = " . $data["pID_$x"] . "";
			$result = mysql_query($query);
			checkSQLResult ($result, $query);
			if (!mysql_num_rows($result))
			{
				// Insert information
				$query = "INSERT INTO kits SET id = " . $data["pID_$x"];
				for ($i = 0; $i < 7; $i++)
				{
					$query .= ", time" . $i . " = " . $data["tk$i" . '_' . $x] . ",
						kills" . $i . " = " . $data["kk$i" . '_' . $x] . ",
						deaths" . $i . " = " . $data["dk$i" . '_' . $x] . "
					";
				}
				$result = mysql_query($query);
				checkSQLResult ($result, $query);
			}
			else
			{
				$row = mysql_fetch_array($result);

				// Update information
				$query = "UPDATE kits SET ";
				for ($i = 0; $i < 7; $i++)
				{
					if ($i) {$query .= ',';}
					$query .= "time" . $i . " = `time$i` + " . $data["tk$i" . '_' . $x] . ",
						kills" . $i . " = `kills$i` + " . $data["kk$i" . '_' . $x] . ",
						deaths" . $i . " = `deaths$i` + " . $data["dk$i" . '_' . $x] . "
					";
				}
				$query .= "WHERE id = " . $data["pID_$x"];
				$result = mysql_query($query);
				checkSQLResult ($result, $query);
			}
			
			/********************************
			* Process 'Weapons'
			********************************/
			ErrorLog("Processing Weapon Data (".$data["pID_$x"].")",3);
			if ($data['v'] != 'xpack')
			{
				$data["te6_$x"] = 0;
				$data["te7_$x"] = 0;
				$data["te8_$x"] = 0;
				$data["be8_$x"] = 0;
				$data["be9_$x"] = 0;
				$data["de6_$x"] = 0;
				$data["de7_$x"] = 0;
				$data["de8_$x"] = 0;
			}

			$query = "SELECT * FROM weapons WHERE id = " . $data["pID_$x"] . "";
			$result = mysql_query($query);
			checkSQLResult ($result, $query);
			if (!mysql_num_rows($result))
			{
				// Insert information
				$query = "INSERT INTO weapons SET
					id = " . $data["pID_$x"] . ",
					time0 = " . $data["tw0_$x"] . ",
					time1 = " . $data["tw1_$x"] . ",
					time2 = " . $data["tw2_$x"] . ",
					time3 = " . $data["tw3_$x"] . ",
					time4 = " . $data["tw4_$x"] . ",
					time5 = " . $data["tw5_$x"] . ",
					time6 = " . $data["tw6_$x"] . ",
					time7 = " . $data["tw7_$x"] . ",
					time8 = " . $data["tw8_$x"] . ",
					knifetime = " . $data["te0_$x"] . ",
					c4time = " . $data["te1_$x"] . ",
					handgrenadetime = " . $data["te3_$x"] . ",
					claymoretime = " . $data["te2_$x"] . ",
					shockpadtime = " . $data["te4_$x"] . ",
					atminetime = " . $data["te5_$x"] . ",
					tacticaltime = " . $data["te6_$x"] . ",
					grapplinghooktime = " . $data["te7_$x"] . ",
					ziplinetime = " . $data["te8_$x"] . ",
					kills0 = " . $data["kw0_$x"] . ",
					kills1 = " . $data["kw1_$x"] . ",
					kills2 = " . $data["kw2_$x"] . ",
					kills3 = " . $data["kw3_$x"] . ",
					kills4 = " . $data["kw4_$x"] . ",
					kills5 = " . $data["kw5_$x"] . ",
					kills6 = " . $data["kw6_$x"] . ",
					kills7 = " . $data["kw7_$x"] . ",
					kills8 = " . $data["kw8_$x"] . ",
					knifekills = " . $data["ke0_$x"] . ",
					c4kills = " . $data["ke1_$x"] . ",
					handgrenadekills = " . $data["ke3_$x"] . ",
					claymorekills = " . $data["ke2_$x"] . ",
					shockpadkills = " . $data["ke4_$x"] . ",
					atminekills = " . $data["ke5_$x"] . ",
					deaths0 = " . $data["bw0_$x"] . ",
					deaths1 = " . $data["bw1_$x"] . ",
					deaths2 = " . $data["bw2_$x"] . ",
					deaths3 = " . $data["bw3_$x"] . ",
					deaths4 = " . $data["bw4_$x"] . ",
					deaths5 = " . $data["bw5_$x"] . ",
					deaths6 = " . $data["bw6_$x"] . ",
					deaths7 = " . $data["bw7_$x"] . ",
					deaths8 = " . $data["bw8_$x"] . ",
					knifedeaths = " . $data["be0_$x"] . ",
					c4deaths = " . $data["be1_$x"] . ",
					handgrenadedeaths = " . $data["be3_$x"] . ",
					claymoredeaths = " . $data["be2_$x"] . ",
					shockpaddeaths = " . $data["be4_$x"] . ",
					atminedeaths = " . $data["be5_$x"] . ",
					ziplinedeaths = " . $data["be8_$x"] . ",
					grapplinghookdeaths = " . $data["be9_$x"] . ",
					tacticaldeployed = " . $data["de6_$x"] . ",
					grapplinghookdeployed = " . $data["de7_$x"] . ",
					ziplinedeployed = " . $data["de8_$x"] . ",
					fired0 = " . $data["sw0_$x"] . ",
					fired1 = " . $data["sw1_$x"] . ",
					fired2 = " . $data["sw2_$x"] . ",
					fired3 = " . $data["sw3_$x"] . ",
					fired4 = " . $data["sw4_$x"] . ",
					fired5 = " . $data["sw5_$x"] . ",
					fired6 = " . $data["sw6_$x"] . ",
					fired7 = " . $data["sw7_$x"] . ",
					fired8 = " . $data["sw8_$x"] . ",
					knifefired = " . $data["se0_$x"] . ",
					c4fired = " . $data["se1_$x"] . ",
					claymorefired = " . $data["se2_$x"] . ",
					handgrenadefired = " . $data["se3_$x"] . ",
					shockpadfired = " . $data["se4_$x"] . ",
					atminefired = " . $data["se5_$x"] . ",
					hit0 = " . $data["hw0_$x"] . ",
					hit1 = " . $data["hw1_$x"] . ",
					hit2 = " . $data["hw2_$x"] . ",
					hit3 = " . $data["hw3_$x"] . ",
					hit4 = " . $data["hw4_$x"] . ",
					hit5 = " . $data["hw5_$x"] . ",
					hit6 = " . $data["hw6_$x"] . ",
					hit7 = " . $data["hw7_$x"] . ",
					hit8 = " . $data["hw8_$x"] . ",
					knifehit = " . $data["he0_$x"] . ",
					c4hit = " . $data["he1_$x"] . ",
					claymorehit = " . $data["he2_$x"] . ",
					handgrenadehit = " . $data["he3_$x"] . ",
					shockpadhit = " . $data["he4_$x"] . ",
					atminehit = " . $data["he5_$x"] . "
				";
				$result = mysql_query($query);
				checkSQLResult ($result, $query);
			}
			else
			{
				$row = mysql_fetch_array($result);

				// Update information
				$query = "UPDATE weapons SET
					time0 = `time0` + " . $data["tw0_$x"] . ",
					time1 = `time1` + " . $data["tw1_$x"] . ",
					time2 = `time2` + " . $data["tw2_$x"] . ",
					time3 = `time3` + " . $data["tw3_$x"] . ",
					time4 = `time4` + " . $data["tw4_$x"] . ",
					time5 = `time5` + " . $data["tw5_$x"] . ",
					time6 = `time6` + " . $data["tw6_$x"] . ",
					time7 = `time7` + " . $data["tw7_$x"] . ",
					time8 = `time8` + " . $data["tw8_$x"] . ",
					knifetime = `knifetime` + " . $data["te0_$x"] . ",
					c4time = `c4time` + " . $data["te1_$x"] . ",
					handgrenadetime = `handgrenadetime` + " . $data["te3_$x"] . ",
					claymoretime = `claymoretime` + " . $data["te2_$x"] . ",
					shockpadtime = `shockpadtime` + " . $data["te4_$x"] . ",
					atminetime = `atminetime` + " . $data["te5_$x"] . ",
					tacticaltime = `tacticaltime` + " . $data["te6_$x"] . ",
					grapplinghooktime = `grapplinghooktime` + " . $data["te7_$x"] . ",
					ziplinetime = `ziplinetime` + " . $data["te8_$x"] . ",
					kills0 = `kills0` + " . $data["kw0_$x"] . ",
					kills1 = `kills1` + " . $data["kw1_$x"] . ",
					kills2 = `kills2` + " . $data["kw2_$x"] . ",
					kills3 = `kills3` + " . $data["kw3_$x"] . ",
					kills4 = `kills4` + " . $data["kw4_$x"] . ",
					kills5 = `kills5` + " . $data["kw5_$x"] . ",
					kills6 = `kills6` + " . $data["kw6_$x"] . ",
					kills7 = `kills7` + " . $data["kw7_$x"] . ",
					kills8 = `kills8` + " . $data["kw8_$x"] . ",
					knifekills = `knifekills` + " . $data["ke0_$x"] . ",
					c4kills = `c4kills` + " . $data["ke1_$x"] . ",
					handgrenadekills = `handgrenadekills` + " . $data["ke3_$x"] . ",
					claymorekills = `claymorekills` + " . $data["ke2_$x"] . ",
					shockpadkills = `shockpadkills` + " . $data["ke4_$x"] . ",
					atminekills = `atminekills` + " . $data["ke5_$x"] . ",
					deaths0 = `deaths0` + " . $data["bw0_$x"] . ",
					deaths1 = `deaths1` + " . $data["bw1_$x"] . ",
					deaths2 = `deaths2` + " . $data["bw2_$x"] . ",
					deaths3 = `deaths3` + " . $data["bw3_$x"] . ",
					deaths4 = `deaths4` + " . $data["bw4_$x"] . ",
					deaths5 = `deaths5` + " . $data["bw5_$x"] . ",
					deaths6 = `deaths6` + " . $data["bw6_$x"] . ",
					deaths7 = `deaths7` + " . $data["bw7_$x"] . ",
					deaths8 = `deaths8` + " . $data["bw8_$x"] . ",
					knifedeaths = `knifedeaths` + " . $data["be0_$x"] . ",
					c4deaths = `c4deaths` + " . $data["be1_$x"] . ",
					handgrenadedeaths = `handgrenadedeaths` + " . $data["be3_$x"] . ",
					claymoredeaths = `claymoredeaths` + " . $data["be2_$x"] . ",
					shockpaddeaths = `shockpaddeaths` + " . $data["be4_$x"] . ",
					atminedeaths = `atminedeaths` + " . $data["be5_$x"] . ",
					ziplinedeaths = `ziplinedeaths` + " . $data["be8_$x"] . ",
					grapplinghookdeaths = `grapplinghookdeaths` + " . $data["be9_$x"] . ",
					tacticaldeployed = `tacticaldeployed` + " . $data["de6_$x"] . ",
					grapplinghookdeployed = `grapplinghookdeployed` + " . $data["de7_$x"] . ",
					ziplinedeployed = `ziplinedeployed` + " . $data["de8_$x"] . ",
					fired0 = `fired0` + " . $data["sw0_$x"] . ",
					fired1 = `fired1` + " . $data["sw1_$x"] . ",
					fired2 = `fired2` + " . $data["sw2_$x"] . ",
					fired3 = `fired3` + " . $data["sw3_$x"] . ",
					fired4 = `fired4` + " . $data["sw4_$x"] . ",
					fired5 = `fired5` + " . $data["sw5_$x"] . ",
					fired6 = `fired6` + " . $data["sw6_$x"] . ",
					fired7 = `fired7` + " . $data["sw7_$x"] . ",
					fired8 = `fired8` + " . $data["sw8_$x"] . ",
					knifefired = `knifefired` + " . $data["se0_$x"] . ",
					c4fired = `c4fired` + " . $data["se1_$x"] . ",
					claymorefired = `claymorefired` + " . $data["se2_$x"] . ",
					handgrenadefired = `handgrenadefired` + " . $data["se3_$x"] . ",
					shockpadfired = `shockpadfired` + " . $data["se4_$x"] . ",
					atminefired = `atminefired` + " . $data["se5_$x"] . ",
					hit0 = `hit0` + " . $data["hw0_$x"] . ",
					hit1 = `hit1` + " . $data["hw1_$x"] . ",
					hit2 = `hit2` + " . $data["hw2_$x"] . ",
					hit3 = `hit3` + " . $data["hw3_$x"] . ",
					hit4 = `hit4` + " . $data["hw4_$x"] . ",
					hit5 = `hit5` + " . $data["hw5_$x"] . ",
					hit6 = `hit6` + " . $data["hw6_$x"] . ",
					hit7 = `hit7` + " . $data["hw7_$x"] . ",
					hit8 = `hit8` + " . $data["hw8_$x"] . ",
					knifehit = `knifehit` + " . $data["he0_$x"] . ",
					c4hit = `c4hit` + " . $data["he1_$x"] . ",
					claymorehit = `claymorehit` + " . $data["he2_$x"] . ",
					handgrenadehit = `handgrenadehit` + " . $data["he3_$x"] . ",
					shockpadhit = `shockpadhit` + " . $data["he4_$x"] . ",
					atminehit = `atminehit` + " . $data["he5_$x"] . "
					WHERE id = " . $data["pID_$x"] . "
				";
				$result = mysql_query($query);
				checkSQLResult ($result, $query);
			}
			
			/********************************
			* Process 'Maps'
			********************************/
			ErrorLog("Processing Map Data (".$data["pID_$x"].")",3);
			$query = "SELECT * FROM maps WHERE (id = " . $data["pID_$x"] . ") AND (mapid = {$mapid})";
			$result = mysql_query($query);
			checkSQLResult ($result, $query);
			if (!mysql_num_rows($result))
			{
				// Insert information
				$query = "INSERT INTO maps SET
					id = " . $data["pID_$x"] . ",
					mapid = {$mapid},
					time = " . $data["ctime_$x"] . ",
					win = {$wins},
					loss = {$losses},
					best = " . $data["rs_$x"] . ",
					worst = " .$data["rs_$x"] . "
				";
				$result = mysql_query($query);
				checkSQLResult ($result, $query);
			}
			else
			{
				$row = mysql_fetch_array($result);

				// Calculate best/worst round score
				$best = ($row['best'] > $data["rs_$x"]) ? $row['best'] : $data["rs_$x"];
				$worst = ($row['worst'] < $data["rs_$x"]) ? $row['worst'] : $data["rs_$x"];

				// Update information
				$query = "UPDATE maps SET
					time = `time` + " . $data["ctime_$x"] . ",
					win = `win` + {$wins},
					loss = `loss` + {$losses},
					best = {$best},
					worst = {$worst}
					WHERE (id = " . $data["pID_$x"] . ") AND (mapid = {$mapid})
				";
				$result = mysql_query($query);
				checkSQLResult ($result, $query);
			}
			
			/********************************
			* Process 'Awards'
			********************************/
			ErrorLog("Processing Award Data (".$data["pID_$x"].")",3);
			// Check if Minimal Central Update
			$awdsReqComplete = $cfg->get('stats_awds_complete');
			if ($centralupdate == 2) 
            {
				// Ignore any Award Data in SnapShot as this could mess up current data
				$complete = 0;
				$awdsReqComplete = 1;
			}
			
			if ($complete || !$awdsReqComplete)
			{
				// Check Backend Awards
				checkBackendAwards();
				
				$awards = array();
				getAwards();
				if (count($awards))
				{
					for ($i = 0; $i < count($awards); $i += 2)
					{
						if (($awards[$i] > 2000000) && ($awards[$i] < 3000000)) #medals
						{
							$query = "SELECT level FROM awards WHERE (id = " . $data["pID_$x"] . ") AND (awd = {$awards[$i]})";
						}
						else
						{
							$query = "SELECT level FROM awards WHERE (id = " . $data["pID_$x"] . ") AND (awd = {$awards[$i]}) AND (level = " . $awards[$i + 1] . ")";
						}
						$result = mysql_query($query);
						checkSQLResult ($result, $query);
						if (!mysql_num_rows($result))
						{
							if (($awards[$i] > 2000000) && ($awards[$i] < 3000000)) #medals
							{
								$first = time();
							}
							elseif (($awards[$i] < 2000000) && ($awards[$i + 1] > 1)) #badges
							{
								$first = 0;
								// Need to do extra work for Badges as more than one badge per round may have been awarded
								for ($j = 1; $j < $awards[$i + 1]; $j++){
									$query = "SELECT level FROM awards WHERE (id = " . $data["pID_$x"] . ") AND (awd = {$awards[$i]}) AND (level = {$j})";
									$result = mysql_query($query);
									checkSQLResult ($result, $query);
									if (!mysql_num_rows($result)) {
										// Pre-requistite badge missing, insert it with lower timestamp to ensure order is maintained.
										$query = "INSERT INTO awards SET
											id = " . $data["pID_$x"] . ",
											awd = {$awards[$i]},
											level = {$j},
											earned = " . ((time() - 5) + $j) . ",
											first = {$first}";
										$result = mysql_query($query);
										checkSQLResult ($result, $query);
									}
								}
							} 
                            else 
                            {
								$first = 0;
							}
							
							// Insert information
							$query = "INSERT INTO awards SET
								id = " . $data["pID_$x"] . ",
								awd = {$awards[$i]},
								level = " . $awards[$i + 1] . ",
								earned = " . time() . ",
								first = {$first}";
							$result = mysql_query($query);
							checkSQLResult ($result, $query);
						}
						else
						{
							if (($awards[$i] > 2000000) && ($awards[$i] < 3000000)) #medals
							{
								$row = mysql_fetch_array($result);

								// Update information
								$query = "UPDATE awards SET
									level = `level` + 1,
									earned = " . time() . "
									WHERE (id = " . $data["pID_$x"] . ") AND (awd = {$awards[$i]})
								";
								$result = mysql_query($query);
								checkSQLResult ($result, $query);
							}
						}

						// Calculate best in round for army
						if (($awards[$i] == 2051907) && ($wins))
						{
							$army = $data["a_$x"];
							$brnd = "brnd$army";

							$query = "SELECT {$brnd} FROM army WHERE id = " . $data["pID_$x"];
							$result = mysql_query($query);
							checkSQLResult ($result, $query);
							$row = mysql_fetch_array($result);

							$query = "UPDATE army SET " .
							$brnd . " = `brnd$army` + 1 " .
								"WHERE id = " . $data["pID_$x"];
							$result = mysql_query($query);
							checkSQLResult ($result, $query);
						}
					}
				}
			}
		}
		else
		{
			if ( $totalplayers < $cfg->get('stats_players_max')) 
            {
				// Data Hole Detected, increment total player count
				$totalplayers++;
				ErrorLog("Data Hole Detected, Player Count now: $totalplayers",2);
			} 
            else 
            {
				// Too many "data holes" break out!
				ErrorLog("Data Hole Limit Reached: $totalplayers",1);
				break;
			}
		}
		ErrorLog("End Loop $x",3);
	}

	/********************************
	* Process 'Server'
	********************************/
	// Note: Code borrowed from release by ArmEagle (armeagle@gmail.com)
	$gamesrv_ip   = $_SERVER['REMOTE_ADDR'];
	$gamesrv_name = $_SERVER['REMOTE_HOST'];
	ErrorLog("Processing Game Server: {$gamesrv_ip}",3);
	$gamesrv_port = ($data['gameport']) ? $data['gameport'] : 16567;	//Set to Default if no data
	$gamesrv_qryport = ($data['queryport']) ? $data['queryport'] : 29900;	//Set to Default if no data
	$query = "SELECT * FROM servers WHERE ip = '{$gamesrv_ip}' AND prefix = '{$prefix}'";
	$result = mysql_query($query);
	checkSQLResult ($result, $query);
	if (!mysql_num_rows($result)) 
    {
		$query = "INSERT INTO servers SET ".
			"ip = '{$gamesrv_ip}', ".
			"name = '{$prefix}', ".
			"prefix = '{$prefix}', ".
			"port = '{$gamesrv_port}', ".
			"queryport = {$gamesrv_qryport}, ".
			"lastupdate = NOW() ";
		$result = mysql_query($query);
		checkSQLResult ($result, $query);
		$serverid = mysql_insert_id();
	} 
    else 
    {
		$row = mysql_fetch_assoc($result);
		$query = "UPDATE servers SET ".
			"port = '{$gamesrv_port}', ".
			"queryport = {$gamesrv_qryport}, ".
			"lastupdate = NOW() ".
			"WHERE ip = '{$gamesrv_ip}' AND prefix = '{$prefix}' ";
		$result = mysql_query($query);
		checkSQLResult ($result, $query);
		$serverid = $row['id'];
	}
	
	/********************************
	* Process 'MapInfo'
	********************************/
	ErrorLog("Processing Map Info Data ({$mapname}:{$mapid})",3);
	$query = "SELECT * FROM mapinfo WHERE id = {$mapid}";
	$result = mysql_query($query);
	checkSQLResult ($result, $query);
	if (!mysql_num_rows($result))
	{
		$query = "INSERT INTO mapinfo SET
			id = {$mapid},
			name = '{$mapname}',
			score = {$globals[mapscore]},
			time = {$globals[roundtime]},
			times = 1,
			kills = {$globals[mapkills]},
			deaths = {$globals[mapdeaths]},
			custom = {$globals[custommap]}
		";
		$result = mysql_query($query);
		checkSQLResult ($result, $query);
	}
	else
	{
		$row = mysql_fetch_array($result);
		$query = "UPDATE mapinfo SET
			score = `score` + {$globals[mapscore]},
			time = `time` + {$globals[roundtime]},
			times = `times` + 1,
			kills = `kills` + {$globals[mapkills]},
			deaths = `deaths` + {$globals[mapdeaths]},
			custom = {$globals[custommap]}
			WHERE id = {$mapid}
		";
		$result = mysql_query($query);
		checkSQLResult ($result, $query);
	}
	
	/********************************
	* Process 'RoundInfo'
	********************************/
	ErrorLog("Processing Round History Data",3);
	$query = "INSERT INTO round_history SET
		`timestamp` = {$data[mapstart]},
		`mapid` = {$mapid},
		`time` = {$globals[roundtime]},
		`team1` = {$data[ra1]},
		`team2` = {$data[ra2]},
		`tickets1` = {$data[rs1]},
		`tickets2` = {$data[rs2]},
		`pids1` = {$globals[team1_pids]},
		`pids1_end` = {$globals[team1_pids_end]},
		`pids2` = {$globals[team2_pids]},
		`pids2_end` = {$globals[team2_pids_end]}		
	";
	$result = mysql_query($query);
	checkSQLResult ($result, $query);
	
	/********************************
	* Process 'SMoC/GEN'
	********************************/
	/*
		omero, 2006-04-15
		do check for SMOC and General Ranks,
		only for non-AI players
	*/	
	ErrorLog("Processing SMOC and General Ranks",3);
	smocCheck();
	genCheck();

	/********************************
	* Process 'Archive Data File'
	********************************/
	if ($cfg->get('stats_move_logs'))
	{
		$fn_src = chkPath($cfg->get('stats_logs')) . $stats_filename;
		$fn_dest = chkPath($cfg->get('stats_logs_store')) . $stats_filename;
		
		if (file_exists($fn_src)) 
        {
			if (file_exists($fn_dest)) 
            {
				$errmsg = "SNAPSHOT Data File Already Exists, Over-writing! ({$fn_src} -> {$fn_dest})";
				ErrorLog($errmsg, 2);
			}
			copy($fn_src, $fn_dest);
			
			// Remove the original ONLY if it copies
			if (file_exists($fn_dest)) 
            {
				unlink($fn_src);
			}
		}
		
		$errmsg = "SNAPSHOT Data File Moved! ({$fn_src} -> {$fn_dest})";
		ErrorLog($errmsg, 3);
	}
	$errmsg = "SNAPSHOT Data File Processed: {$stats_filename}";
	ErrorLog($errmsg, -1);
}

// Close database connection
@mysql_close($connection);



/****************************************************
*                 Helper Functions                  *
****************************************************/

// Compile Awards from SNAPSHOT
function getAwards()
{
	global $data, $x, $awards, $awardsdata;
	
	foreach ($awardsdata as $award) 
    {
		$awdkey = $award[1] . "_$x";
		if (isset($data[$awdkey])) 
        {
			$awards[] = $award[0];
			$awards[] = ($award[2]==0)?$data[$awdkey]:$award[2];
		}
	}
}

// Check for Backend Awards
function checkBackendAwards() 
{
	
	global $data, $x, $backendawardsdata;
	global $awards_substr;
	
	// Calculate Awards
	foreach ($backendawardsdata as $award) 
    {
		// Check if Player already has Award
		$query = "SELECT awd, level FROM awards WHERE (id = " . $data["pID_$x"] . ") AND (awd = {$award[0]})";
		$awdresult = mysql_query($query);
		checkSQLResult ($awdresult, $query);
		// Check if player has award
		// Recieveing these awards multiple times is NOT supported...yet!
		if (!mysql_num_rows($awdresult) || $award[2] == 2) 
        {
			// Check Criteria
			$chkcriteria = false;
			foreach ($award[3] as $criteria) 
            {
				if ($award[2] == 2) 
                {
					// Can receive multiple times
					if (mysql_num_rows($awdresult)>0) 
                    {
						$rowawd = mysql_fetch_array($awdresult);
						$where = str_replace($awards_substr, $rowawd['level']+1, $criteria[3]);
					} 
                    else 
                    {
						$where = str_replace($awards_substr, 1, $criteria[3]);
					}
				} 
                else 
                {
					$where = $criteria[3];
				}
				$query = "SELECT {$criteria[1]} AS checkval FROM {$criteria[0]}\n" .
					"WHERE (id = " . $data["pID_$x"] . ") AND ({$where})\n" .
					"GROUP BY id;";
				$chkresult = mysql_query($query);
				checkSQLResult ($chkresult, $query);
				if (mysql_num_rows($chkresult)>0) 
                {
					$rowchk = mysql_fetch_array($chkresult);
					if ($rowchk['checkval'] >= $criteria[2]) 
                    {
						$chkcriteria = true;
					} 
                    else 
                    {
						$chkcriteria = false;
						break;
					}
				}
			}
			if ($chkcriteria) 
            {	
				$data[$award[1] . "_$x"] = 1;
			}
		}
	}
}

// Check for SMOC
function smocCheck()
{
	global $cfg;
	
	$players = array();
	$query = "SELECT id, score FROM player WHERE rank = 10 AND ip <> '127.0.0.1'";
	$result = mysql_query($query);
	checkSQLResult ($result, $query);
	if (mysql_num_rows($result))
	{
		while ($row = mysql_fetch_array($result)) {$players[$row['id']] = $row['score'];}
		arsort($players);
		$id = key($players);
		
		// Check for old
		$query = "SELECT id, earned FROM awards WHERE awd = 6666666";
		$result = mysql_query($query);
		checkSQLResult ($result, $query);
		if (mysql_num_rows($result))
		{
			$row = mysql_fetch_array($result);
			
			// Check for same and determine if minimum tenure servred
			$mintenure = $row['earned'] + ($cfg->get('stats_rank_tenure') * 24 * 60 * 60);
			if ($id != $row['id'] && time() >= $mintenure)
			{
				$query = "DELETE FROM awards WHERE (id = " . $row['id'] . ") AND (awd = 6666666)";
				$result = mysql_query($query);
				checkSQLResult ($result, $query);
				
				$query = "UPDATE player SET rank = 10, chng = 0, decr = 1 WHERE id = " . $row['id'];
				$result = mysql_query($query);
				checkSQLResult ($result, $query);
				
				// Award new
				$query = "INSERT INTO awards SET
					id = {$id},
					awd = 6666666,
					earned = " . time() . "
				";
				$result = mysql_query($query);
				checkSQLResult ($result, $query);
				
				$query = "UPDATE player SET rank = 11, chng = 1, decr = 0 WHERE id = {$id}";
				$result = mysql_query($query);
				checkSQLResult ($result, $query);
			}
		}
		else
		{
			// Award new
			$query = "INSERT INTO awards SET
				id = {$id},
				awd = 6666666,
				earned = " . time() . "
			";
			$result = mysql_query($query);
			checkSQLResult ($result, $query);
			
			$query = "UPDATE player SET rank = 11, chng = 1, decr = 0 WHERE id = {$id}";
			$result = mysql_query($query);
			checkSQLResult ($result, $query);
		}
	}
}

// Check for GEN
function genCheck() #TODO
{
	global $cfg;
	
	$players = array();
	$query = "SELECT id, score FROM player WHERE rank >= 20 AND ip <> '127.0.0.1'";
	$result = mysql_query($query);
	checkSQLResult ($result, $query);
	if (mysql_num_rows($result))
	{
		while ($row = mysql_fetch_array($result)) 
        {
            $players[$row['id']] = $row['score'];
        }
		arsort($players);
		$id = key($players);

		// Check for old
		$query = "SELECT id, earned FROM awards WHERE awd = 6666667";
		$result = mysql_query($query);
		checkSQLResult ($result, $query);
		if (mysql_num_rows($result))
		{
			$row = mysql_fetch_array($result);

			// Check for same and determine if minimum tenure servred
			$mintenure = $row['earned'] + ($cfg->get('stats_rank_tenure') * 24 * 60 * 60);
			if ($id != $row['id'] && time() >= $mintenure)
			{
				$query = "DELETE FROM awards WHERE (id = " . $row['id'] . ") AND (awd = 6666667)";
				$result = mysql_query($query);
				checkSQLResult ($result, $query);
				
				$query = "UPDATE player SET rank = 20, chng = 0, decr = 1 WHERE id = " . $row['id'];
				$result = mysql_query($query);
				checkSQLResult ($result, $query);

				// Award new
				$query = "INSERT INTO awards SET
					id = {$id},
					awd = 6666667,
					earned = " . time() . "
				";
				$result = mysql_query($query);
				checkSQLResult ($result, $query);
				
				$query = "UPDATE player SET rank = 21, chng = 1, decr = 0 WHERE id = {$id}";
				$result = mysql_query($query);
				checkSQLResult ($result, $query);
			}
		}
		else
		{
			// Award new
			$query = "INSERT INTO awards SET
				id = {$id},
				awd = 6666667,
				earned = " . time() . "					
			";
			$result = mysql_query($query);
			checkSQLResult ($result, $query);
			
			$query = "UPDATE player SET rank = 21, chng = 1, decr = 0 WHERE id = {$id}";
			$result = mysql_query($query);
			checkSQLResult ($result, $query);
		}
	}
}
?>