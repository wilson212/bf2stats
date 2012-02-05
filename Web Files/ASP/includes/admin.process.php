<?php
// No Direct Access
defined( '_BF2_ADMIN' ) or die( 'Restricted access' );

// Build Data Table Array
$DataTables = array('army','awards','kills','kits','mapinfo','maps','player','player_history','round_history','servers','unlocks','vehicles','weapons','data','stats');


// Do Tasks
$task = $_POST['task'] ? $_POST['task'] : '';
switch ($task) 
{
	case "saveconfig":
		showHeader('Save Configuration');
		saveConfig();
		break;
	case "testconfig":
		showHeader('Test Configuration');
		testConfig();
		break;
	case "clanmanager":
		showHeader('Clan Manager');
		//processClanManager();
		break;
	case "changerank":
		showHeader('Edit Players');
		processChangeRank();
		break;
	case "banplayers":
		showHeader('Ban Players');
		processBanPlayers();
		break;
	case "unbanplayers":
		showHeader('Un-Ban Players');
		processUnBanPlayers();
		break;
	case "resetunlocks":
		showHeader('Reset Unlocks');
		processResetUnlocks();
		break;
	case "mergeplayers":
		showHeader('Merge Players');
		processMergePlayers();
		break;
	case "deleteplayers":
		showHeader('Delete Players');
		processDeletePlayers();
		break;
	case "installdb":
		showHeader('Install Database');
		processInstallDB();
		break;
	case "upgradedb":
		showHeader('Upgrade Database');
		processUpgradeDB();
		break;
	case "cleardb":
		showHeader('Clear Database');
		processClearDB();
		break;
	case "backupdb":
		showHeader('Backup Database');
		processBackupDB();
		break;
	case "restoredb":
		showHeader('Restore Database');
		processClearDB();
		processRestoreDB();
		break;
	case "validateranks":
		showHeader('Validate Ranks');
		processValidateRanks();
		break;
	case "checkawards":
		showHeader('Check Backend Awards');
		processCheckAwards();
		break;
	case "importlogs":
		showHeader('Import SNAPSHOT Logs');
		processImportLogs();;
		break;
	default:
		showLoginForm();
		break;
}

// Tidy up HTML
echo "</pre></div>";

function showHeader($str) 
{
	echo "<div class=\"content-head\"><div class=\"desc-title\">Processing: " . $str ."</div></div><div class=\"readme\"><pre>";
}


// Display Log Message to Browser
function showLog($msg) 
{
	global $cfg;
	$outmsg = date('Y-m-d H:i:s')." : ". $msg ."\n";
	echo $outmsg;
	
	if ($cfg->get('admin_log') != '') 
	{
		$file = @fopen($cfg->get('admin_log'), 'a');
		@fwrite($file, $outmsg);
		@fclose($file);
	}
	
	ob_flush();
	flush();
}

function saveConfig() 
{
	$cfg = new Config();

	// Store New/Changed config items
	showLog("Saving Config...");
	foreach ($_POST as $item => $val) 
	{
		$key = explode('__', $item);
		if ($key[0] == 'cfg') 
		{
			showLog(" -> Found Key: '{$key[1]}' => '".((is_array($cfg->get($key[1])))?str_replace("\r\n", ",",$val):$val)."' (Old: ".((is_array($cfg->get($key[1])))?implode(',',$cfg->get($key[1])):$cfg->get($key[1])).")...");
			$cfg->set($key[1],$val);
		}
	}
	
	$cfg->Save();
}

function testConfig() 
{
	include('class.validator.php');
	
	DEFINE('__PASS','<b><font color="green">Pass</font></b>');
	DEFINE('__WARN','<b><font color="orange">Warn</font></b>');
	DEFINE('__FAIL','<b><font color="red">Fail</font></b>');
	
	// Define Test Snapshot String (PID: 111)
	$tst_prefix = 'TST'.uniqid(rand());
	$tst_snapshot = $tst_prefix.'\test_map\queryport\29900\mapstart\1157264950.7\mapend\1157266995.57\win\1\gm\0\m\999\v\bf2\pc\1\rwa\2\ra1\0\rs1\25\ra2\2\rs2\0\pID_0\999\name_0\Test Player\t_0\2\a_0\0\ctime_0\1559\c_0\1\ip_0\0\rs_0\24\cs_0\0\ss_0\18\ts_0\6\kills_0\9\deaths_0\17\cpc_0\0\cpn_0\1\cpa_0\0\cpna_0\0\cpd_0\0\ka_0\0\he_0\0\rev_0\0\rsp_0\0\rep_0\0\tre_0\0\drs_0\0\dra_0\4\pa_0\0\tmkl_0\0\tmdg_0\0\tmvd_0\0\su_0\0\ks_0\6\ds_0\6\rank_0\3\ban_0\0\kck_0\0\tco_0\0\tsl_0\1559\tsm_0\0\tlw_0\0\ta0_0\1559\ta1_0\0\ta2_0\0\ta3_0\0\ta4_0\0\ta5_0\0\ta6_0\0\ta7_0\0\ta8_0\0\ta9_0\0\mvns_0\29000037\mvks_0\1\mvns_0\29000113\mvks_0\1\mvns_0\29000069\mvks_0\1\mvns_0\29000081\mvks_0\2\mvns_0\29000108\mvks_0\1\mvns_0\29000080\mvks_0\1\mvns_0\29000089\mvks_0\1\mvns_0\29000041\mvks_0\1\tv0_0\278\tv1_0\0\tv2_0\0\tv3_0\532\tv4_0\227\tv5_0\0\tv6_0\0\tvp_0\17\kv0_0\5\kv1_0\0\kv2_0\0\kv3_0\0\kv4_0\0\kv5_0\0\kv6_0\0\bv0_0\3\bv1_0\0\bv2_0\0\bv3_0\0\bv4_0\0\bv5_0\0\bv6_0\0\kvr0_0\1\kvr1_0\0\kvr2_0\0\kvr3_0\0\kvr4_0\0\kvr5_0\0\kvr6_0\0\tk0_0\736\tk1_0\20\tk2_0\311\tk3_0\0\tk4_0\320\tk5_0\84\tk6_0\29\kk0_0\8\kk1_0\0\kk2_0\0\kk3_0\0\kk4_0\1\kk5_0\0\kk6_0\0\dk0_0\10\dk1_0\1\dk2_0\2\dk3_0\0\dk4_0\2\dk5_0\1\dk6_0\1\tw0_0\11\tw1_0\0\tw2_0\49\tw3_0\28\tw4_0\10\tw5_0\5\tw6_0\54\tw7_0\382\tw8_0\47\te0_0\2\te1_0\0\te3_0\48\te2_0\0\te4_0\0\te5_0\0\te6_0\0\te7_0\0\te8_0\0\kw0_0\0\kw1_0\0\kw2_0\0\kw3_0\0\kw4_0\0\kw5_0\1\kw6_0\0\kw7_0\1\kw8_0\0\ke0_0\1\ke1_0\0\ke3_0\0\ke2_0\0\ke4_0\0\ke5_0\0\bw0_0\1\bw1_0\0\bw2_0\2\bw3_0\1\bw4_0\0\bw5_0\0\bw6_0\3\bw7_0\5\bw8_0\1\be0_0\0\be1_0\0\be3_0\1\be2_0\0\be4_0\0\be5_0\0\be8_0\0\be9_0\0\de6_0\0\de7_0\0\de8_0\0\sw0_0\0\sw1_0\0\sw2_0\26\sw3_0\0\sw4_0\0\sw5_0\15\sw6_0\4\sw7_0\53\sw8_0\0\se0_0\2\se1_0\0\se2_0\0\se3_0\6\se4_0\0\se5_0\0\hw0_0\0\hw1_0\0\hw2_0\3\hw3_0\0\hw4_0\0\hw5_0\5\hw6_0\1\hw7_0\8\hw8_0\0\he0_0\1\he1_0\0\he2_0\0\he3_0\3\he4_0\0\he5_0\0\EOF\1';
	$tst_pid = 999;
	$tst_mapid = 999;

	$cfg = new Config();
	$chk = new Validator();

	showLog("Testing Config...");
	// Check Config File Write Access
	showLog(" > Checking Config File...");
	if (!$chk->is_sane('_config.php')) 
	{
		showLog("\t - Config File Writable: ".__FAIL);
	} 
	else 
	{
		showLog("\t - Config File Writable: ".__PASS);
	}
	
	// Check Log File Write Access
	showLog(" > Checking Log Files...");
	if (!$chk->is_sane($cfg->get('debug_log'))) 
	{
		showLog("\t - Error Log File Writable: ".__WARN);
	} 
	else 
	{
		showLog("\t - Error Log File Writable: ".__PASS);
	}
	
	if (!$chk->is_sane($cfg->get('admin_log'))) 
	{
		showLog("\t - Admin Log File Writable: ".__WARN);
	} 
	else 
	{
		showLog("\t - Admin Log File Writable: ".__PASS);
	}
	
	// DB Host Access
	showLog(" > Checking Database Host...");
	if ($chk->is_ipaddress($cfg->get('db_host'))) 
	{
		showLog("\t - Database host (".$cfg->get('db_host').") IP Address valid: ".__PASS);
	} 
	elseif ($chk->is_hostname($cfg->get('db_host'))) 
	{
		showLog("\t - Database host (".$cfg->get('db_host').") appears valid: ".__PASS);
		if(PHP_OS == 'WINNT')
		{
			showLog("\t - Database host (".$cfg->get('db_host').") resolves: ".__WARN);
		} 
		elseif(!checkdnsrr($cfg->get('db_host'),"ANY")) 
		{
			showLog("\t - Database host (".$cfg->get('db_host').") resolves: ".__PASS);
		} 
		else 
		{
			showLog("\t - Database host (".$cfg->get('db_host').") resolves: ".__FAIL);
		}
	} 
	else 
	{
		if ($cfg->get('db_host') == 'localhost') 
		{
			showLog("\t - Database host (".$cfg->get('db_host').") valid: ".__PASS);
		} 
		elseif(PHP_OS == 'WINNT')
		{
			showLog("\t - Database host (".$cfg->get('db_host').") resolves: ".__WARN);
		} 
		elseif(!checkdnsrr($cfg->get('db_host'),"ANY")) 
		{
			showLog("\t - Database host (".$cfg->get('db_host').") valid: ".__PASS);
		} 
		else 
		{
			showLog("\t - Database host (".$cfg->get('db_host').") valid: ".__FAIL);
		}
	}
	
	// DB MySQL Access
	showLog(" > Checking Database Config...");
	$connection = @mysql_connect($cfg->get('db_host'), $cfg->get('db_user'), $cfg->get('db_pass'));
	if (!$connection) 
	{
		showLog("\t - Database host (".$cfg->get('db_host').") access: ".__FAIL);
	} 
	else 
	{
		showLog("\t - Database host (".$cfg->get('db_host').") access: ".__PASS);
		
		// DB Access
		$db_selected = @mysql_select_db($cfg->get('db_name'), $connection);
		if (!$db_selected) 
		{
			showLog("\t - Database (".$cfg->get('db_name').") access: ".__FAIL);
		} 
		else 
		{
			showLog("\t - Database (".$cfg->get('db_name').") access: ".__PASS);
			
			// DB Version
			if (getDbVer() != $cfg->get('db_expected_ver')) 
			{
				showLog("\t - Database version (".$cfg->get('db_expected_ver')."): ".__FAIL);
			} 
			else 
			{
				showLog("\t - Database version (".$cfg->get('db_expected_ver')."): ".__PASS);
			}
		}
	}
	
	// Check SNAPSHOT Storage Write Access
	showLog(" > Checking SNAPSHOT Storage Path...");
	if (!$chk->is_sane_dir($cfg->get('stats_logs'))) 
	{
		showLog("\t - SNAPSHOT Path Writable: ".__FAIL);
	} 
	else 
	{
		showLog("\t - SNAPSHOT Path Writable: ".__PASS);
	}
	
	// Check SNAPSHOT Archive Write Access
	showLog(" > Checking SNAPSHOT Archive Storage Path...");
	if (!$chk->is_sane_dir($cfg->get('stats_logs_store'))) 
	{
		showLog("\t - SNAPSHOT Archive Path Writable: ".__FAIL);
	} 
	else 
	{
		showLog("\t - SNAPSHOT Archive Path Writable: ".__PASS);
	}
	
	// Check Admin Backup Write Access
	showLog(" > Checking Backup Storage Path...");
	if (!$chk->is_sane_dir($cfg->get('admin_backup_path'))) 
	{
		showLog("\t - Backup Path Writable: ".__FAIL);
	} 
	else 
	{
		showLog("\t - Backup Path Writable: ".__PASS);
	}
	
	// Check For Required Functions
	showLog(" > Checking Remote URL Functions...");
	if( function_exists('file') && function_exists('fopen') && ini_get('allow_url_fopen') ) 
	{
		showLog("\t - Remote URL Function Exist ('FOPEN'): ".__PASS);
		$doURLChecks = true;
	} 
	elseif( (function_exists('curl_exec')) ) 
	{
		showLog("\t - Remote URL Function Exist ('CURL'): ".__PASS);
		$doURLChecks = true;
	} 
	else 
	{
		showLog("\t - Remote URL Function Exist: ".__WARN);
		$doURLChecks = false;
	}
	
	// Close database connection
	@mysql_close($connection);
	
	if ($doURLChecks) 
	{
		// Check bf2statistics.php Processing
		showLog(" > Checking BF2Statistics Processing...");
		$fh = @fsockopen($_SERVER['HTTP_HOST'], 80);
		if ($fh) 
		{
			fwrite($fh, "POST /ASP/bf2statistics.php HTTP/1.1\r\n");
			fwrite($fh, "HOST: ".$_SERVER['HTTP_HOST']."\r\n");
			fwrite($fh, "User-Agent: GameSpyHTTP/1.0\r\n");
			fwrite($fh, "Content-Type: application/x-www-form-urlencoded\r\n");
			fwrite($fh, "Content-Length: " . strlen($tst_snapshot) . "\r\n\r\n");
			fwrite($fh, $tst_snapshot . "\r\n");
			
			$buffer = "";
			while (!feof($fh)) 
			{
				$buffer .= fgets($fh, 4096);
			}
			fclose($fh);
			
			// Check Response Buffer
			if (preg_match("%^HTTP/1.[01]\s*(\d+) *([^\n\r]*)(.*?)$%is", $buffer, $matches)) 
			{
				$responsecode = $matches[1];
			}
			if ($responsecode != '200') 
			{
				showLog("\t - BF2Statistics Processing Check: ".__FAIL);
			} 
			else 
			{
				showLog("\t - BF2Statistics Processing Check: ".__PASS);
			}
		} 
		else 
		{
			showLog("\t - BF2Statistics Processing Check: ".__FAIL);
		}
		
		// Check .aspx Page Responses
		showLog(" > Checking Gamespy (.aspx) File Basic Response...");
		$url = "http://".$_SERVER['HTTP_HOST']."/ASP/getbackendinfo.aspx";
		$response = getPageContents($url);
		if (trim($response[0]) != 'O') 
		{
			showLog("\t - Gamespy (.aspx) Basic Response: ".__FAIL);
		} 
		else 
		{
			showLog("\t - Gamespy (.aspx) Basic Response: ".__PASS);
		}
		
		showLog(" > Checking Gamespy (.aspx) File Advanced Responses...");
		$url = "http://".$_SERVER['HTTP_HOST']."/ASP/getawardsinfo.aspx?pid={$tst_pid}";
		$response = getPageContents($url);
		if (trim($response[0]) != 'O') 
		{
			showLog("\t - Gamespy (.aspx) Advanced (1) Response: ".__FAIL);
		} 
		else 
		{
			showLog("\t - Gamespy (.aspx) Advanced (1) Response: ".__PASS);
		}
		
		$url = "http://".$_SERVER['HTTP_HOST']."/ASP/getrankinfo.aspx?pid={$tst_pid}";
		$response = getPageContents($url);
		if (trim($response[0]) != 'O') 
		{
			showLog("\t - Gamespy (.aspx) Advanced (2) Response: ".__FAIL);
		} 
		else 
		{
			showLog("\t - Gamespy (.aspx) Advanced (2) Response: ".__PASS);
		}
		
		$url = "http://".$_SERVER['HTTP_HOST']."/ASP/getunlocksinfo.aspx?pid={$tst_pid}";
		$response = getPageContents($url);
		if (trim($response[0]) != 'O') 
		{
			showLog("\t - Gamespy (.aspx) Advanced (3) Response: ".__FAIL);
		} 
		else 
		{
			showLog("\t - Gamespy (.aspx) Advanced (3) Response: ".__PASS);
		}
	}
	
	// Re-Connect to DB (The above scripts sometimes removes it)
	$connection = @mysql_connect($cfg->get('db_host'), $cfg->get('db_user'), $cfg->get('db_pass'));
	@mysql_select_db($cfg->get('db_name'), $connection) or die("Database Error: " . mysql_error());
	
	// Remove Test Server Data
	$query = "DELETE FROM `servers` WHERE prefix = '{$tst_prefix}';";
	$result = mysql_query($query);
	if ($result) 
	{
		showLog(" -> Server Info ({$tst_prefix}) removed from Table (servers).");
	} 
	else 
	{
		showLog(" -> <font color='red'>ERROR:</font> Server Info ({$tst_prefix}) removed from Table (servers)!\n".mysql_error());
	}
	
	// Remove Test Map Data
	$query = "DELETE FROM `mapinfo` WHERE id = {$tst_mapid};";
	$result = mysql_query($query);
	if ($result) 
	{
		showLog(" -> Map Info ({$tst_mapid}) removed from Table (mapinfo).");
	} 
	else 
	{
		showLog(" -> <font color='red'>ERROR:</font> Map Info ({$tst_mapid}) removed from Table (mapinfo)!\n".mysql_error());
	}
	
	$query = "DELETE FROM `round_history` WHERE mapid = {$tst_mapid};";
	$result = mysql_query($query);
	if ($result) 
	{
		showLog(" -> Map Info ({$tst_mapid}) removed from Table (round_history).");
	} 
	else 
	{
		showLog(" -> <font color='red'>ERROR:</font> Map Info ({$tst_mapid}) removed from Table (round_history)!\n".mysql_error());
	}
	showLog("Done! :)");
	showLog("");
	
	// Close database connection
	@mysql_close($connection);
	
	// Remove Test Player Data
	$_POST['selitems'] = array($tst_pid);
	processDeletePlayers();
}

function processChangeLeaders() 
{
	// This script will edits your characters rank and assign awards
	global $cfg, $DB;
	
	$bco = $_POST['b_commander'];
	$oco = $_POST['o_commander'];
	$bdco = $_POST['b_d_commander'];
	$odco = $_POST['o_d_commander'];
	$bdcor = $_POST['b_d_rank'];
	$odcor = $_POST['o_d_rank'];
	$tour = $_POST['tour'];
	
	$check = $DB->query("SELECT tour FROM tour_leaders WHERE tour='$tour' LIMIT 1");
	if(count($check) > 0)
	{
		$howmany = $check;
		$DB->query("UPDATE `tour_leaders` SET `b_commander`='$bco',`o_commander`='$oco',`b_d_commander`='$bdco',`bdrank`='$bdcor',`o_d_commander`='$odco',`odrank`='$odcor' WHERE (tour='$tour')");
	}
	else
	{
		$DB->query("INSERT INTO tour_leaders(tour, b_commander, o_commander, b_d_commander, bdrank, o_d_commander, odrank) VALUES('$tour', '$bco', '$oco', '$bdco', '$bdcor', '$odco', '$odcor') LIMIT 1");
	}
	showLog("Done! :)");
}

function processChangeRank() 
{
	// This script will edits your characters rank and assign awards
	global $cfg, $DB;
	
	$rank = $_POST['selected_rank'];
	$id = $_POST['player'];
	
	$changerank = $DB->query("UPDATE player SET rank='$rank' WHERE id LIKE '$id'");
	showLog("Done! :)");
}

function processAddCustomBadge() 
{
	// This script will edits your characters rank and assign awards
	global $cfg, $DB;
	
	$award = $_POST['selected_badge'];
	$id = $_POST['player'];
	
	$addaward = $DB->query("INSERT INTO custom_awards(pid, award, count, type) VALUES($id, $award, 1, 1)");
	showLog("Done! :)");
}

function processAddCustomMedal() 
{
	// This script will edits your characters rank and assign awards
	global $cfg, $DB;
	
	$award = $_POST['medal'];
	$id = $_POST['player'];
	$count = $_POST['count'];
	
	$check = $DB->query("SELECT count FROM custom_awards WHERE pid='$id' AND award='$award' LIMIT 1");
	if(count($check) > 0)
	{
		$howmany = $check;
		$DB->query("UPDATE `custom_awards` SET `count`=(`count` + ".$count.") WHERE pid='$id' AND award='$award'");
	}
	else
	{
		$DB->query("INSERT INTO custom_awards(pid, award, count, type) VALUES($id, $award, $count, 2)");
	}
	showLog("Done! :)");
}

function processScoreManager() 
{
	// This script will edits your characters rank and assign awards
	global $cfg, $DB;
	
	$a = $_POST['tour'];
	$b = $_POST['mapid'];
	$c = $_POST['mapname'];
	$d = $_POST['team'];
	$e = $_POST['tickets'];
	$f = $_POST['ntp'];
	$g = $_POST['oftp'];
	$h = $_POST['ntpp'];
	$i = $_POST['ofpp'];
	
	$DB->query("INSERT INTO scoreboard(tour, number, mapname, winner, tickets, natotp, opfortp, ntpPoints, otpPoints) VALUES('$a', '$b', '$c', '$d', '$e', '$f', '$g', '$h', '$i')");
	showLog("Done! :)");
}

function processBanPlayers() 
{
	// This script will validate the BF2 Gamespy Ranks
	global $cfg;
	$connection = @mysql_connect($cfg->get('db_host'), $cfg->get('db_user'), $cfg->get('db_pass'));
	@mysql_select_db($cfg->get('db_name'), $connection) or die("Database Error: " . mysql_error());
	
	$pids_arr = $_POST['selitems'];
	
	showLog("Banning Players...");
	$query = "SELECT id FROM player";
	if ($pids_arr) 
	{
		$pids = implode(", ", $pids_arr);
		$query .= " WHERE id IN ({$pids})";
	} 
	else 
	{
		showLog("ERROR:<font color='red'>ERROR:</font> No Players Selected!");
		return;
	}

	$result = mysql_query($query);
	if (mysql_num_rows($result)) 
	{
		while ($row = mysql_fetch_array($result)) 
		{
			$pid = $row['id'];
			$query = "UPDATE player SET permban = 1 WHERE (id = {$pid})";
			$updateresult = mysql_query($query);
			if ($updateresult) 
			{
				showLog(" -> Player ({$pid}) Banned");
			} 
			else 
			{
				showLog(" -> <font color='red'>ERROR:</font> Player ({$pid}) *NOT* Banned: ".mysql_error());
			}
		}
	} 
	else 
	{
		showLog("<font color='red'>ERROR:</font>  No Data Found!");
	}

	// Close database connection
	@mysql_close($connection);
	showLog("Done! :)");
}

function processUnBanPlayers() 
{
	// This script will validate the BF2 Gamespy Ranks
	global $cfg;
	$connection = @mysql_connect($cfg->get('db_host'), $cfg->get('db_user'), $cfg->get('db_pass'));
	@mysql_select_db($cfg->get('db_name'), $connection) or die("Database Error: " . mysql_error());
	
	$pids_arr = $_POST['selitems'];
	
	showLog("Un-Banning Players...");
	$query = "SELECT id FROM player";
	if ($pids_arr) 
	{
		$pids = implode(", ", $pids_arr);
		$query .= " WHERE id IN ({$pids})";
	} 
	else 
	{
		showLog("ERROR:<font color='red'>ERROR:</font> No Players Selected!");
		return;
	}

	$result = mysql_query($query);
	if (mysql_num_rows($result)) 
	{
		while ($row = mysql_fetch_array($result)) 
		{
			$pid = $row['id'];
			$query = "UPDATE player SET permban = 0 WHERE (id = {$pid})";
			$updateresult = mysql_query($query);
			if ($updateresult) 
			{
				showLog(" -> Player ({$pid}) Un-Banned");
			} 
			else 
			{
				showLog(" -> <font color='red'>ERROR:</font> Player ({$pid}) *NOT* Un-Banned: ".mysql_error());
			}
		}
	} 
	else 
	{
		showLog("<font color='red'>ERROR:</font>  No Data Found!");
	}

	// Close database connection
	@mysql_close($connection);
	showLog("Done! :)");
}

function processResetUnlocks() 
{
	// This script will validate the BF2 Gamespy Ranks
	global $cfg;
	$connection = @mysql_connect($cfg->get('db_host'), $cfg->get('db_user'), $cfg->get('db_pass'));
	@mysql_select_db($cfg->get('db_name'), $connection) or die("Database Error: " . mysql_error());
	
	$pids_arr = $_POST['selitems'];
	
	showLog("Reseting Player Unlocks...");
	$query = "SELECT id, availunlocks, usedunlocks FROM player";
	if ($pids_arr) 
	{
		$pids = implode(", ", $pids_arr);
		$query .= " WHERE id IN ({$pids})";
	} 
	else 
	{
		showLog("ERROR:<font color='red'>ERROR:</font> No Players Selected!");
		return;
	}

	$result = mysql_query($query);
	if (mysql_num_rows($result)) 
	{
		while ($row = mysql_fetch_array($result)) 
		{
			$pid = $row['id'];
			$unlocks = $row['availunlocks'] + $row['usedunlocks'];
			$used = 0;
			showLog(" -> Found Player ({$pid}) : $unlocks : $used");
			showLog(" -> Reseting Unlocks for Player ({$pid})");
			
			$query = "UPDATE unlocks SET state = 'n' WHERE (id = {$pid})";
			mysql_query($query) or die(mysql_error());
			
			$query = "UPDATE player SET availunlocks = {$unlocks}, usedunlocks = {$used} WHERE id = {$pid}";
			mysql_query($query) or die(mysql_error());
			
			showLog("Done! :)");
		}
	} 
	else 
	{
		showLog("<font color='red'>ERROR:</font>  No Data Found!");
	}

	// Close database connection
	@mysql_close($connection);
	showLog("Done! :)");
}

function processDeletePlayers() 
{
	// This script will permantly delete player data.  Use with EXTREME cation!!
	global $cfg;
	$connection = @mysql_connect($cfg->get('db_host'), $cfg->get('db_user'), $cfg->get('db_pass'));
	@mysql_select_db($cfg->get('db_name'), $connection) or die("Database Error: " . mysql_error());
	
	// Build Data Table Array
	$DataTables = array('army','awards','kills','kits','mapinfo','maps','player','player_history','unlocks','vehicles','weapons');

	$pids_arr = $_POST['selitems'];
	
	if ($pids_arr) 
	{
		$pids = implode(", ", $pids_arr);
	} 
	else 
	{
		showLog("<font color='red'>ERROR:</font> No Players Selected!");
		return;
	}
	
	showLog("Delete Player Data ({$pids})...");
	foreach ($DataTables as $DataTable) 
	{
		// Check Table Exists
		$query = "SHOW TABLES LIKE '" . $DataTable . "'";
		$result = mysql_query($query);
		if (mysql_num_rows($result)==1) 
		{
			// Table Exists, lets clear it
			$query = "DELETE FROM `" . $DataTable . "` ";
			if ($DataTable == 'kills') 
			{
				$query .= "WHERE ((attacker IN ({$pids})) OR (victim IN ({$pids})));";
			} 
			else 
			{
				$query .= "WHERE id IN ({$pids});";
			}

			$result = mysql_query($query);
			if ($result) 
			{
				showLog(" -> Player(s) removed from Table (" . $DataTable . ").");
			} 
			else 
			{
				showLog(" -> <font color='red'>ERROR:</font> Player(s)  *NOT* removed from Table (" . $DataTable . ")!\n".mysql_error());
			}
		}
	}

	// Close database connection
	@mysql_close($connection);
	showLog("Done! :)");
}

function processMergePlayers() 
{
	// This script will merge 2 player ID's, setting the first one as .  Use with EXTREME cation!!
	global $cfg;
	$connection = @mysql_connect($cfg->get('db_host'), $cfg->get('db_user'), $cfg->get('db_pass'));
	@mysql_select_db($cfg->get('db_name'), $connection) or die("Database Error: " . mysql_error());
	
	// Get PlayerID's
	$pids = array();
	$pids[0] = ($_POST['target_pid'])?$_POST['target_pid']:0;		// Target PID
	$pids[1] = ($_POST['source_pid'])?$_POST['source_pid']:0;		// Source PID
	
	// Check PID Values
	
	if ($pids[0]==0 || $pids[1]==0) 
	{
		showLog("<font color='red'>ERROR:</font> Data input missing!");
		return;
	}
	
	if($pids[0] == $pids[1]) 
	{
		showLog("<font color='red'>ERROR:</font> Target &amp; Source are Identical!!");
		return;
	}
	
	// Check Players Exist
	foreach ($pids as $pid) 
	{
		if (!is_numeric($pid)) 
		{
			showLog("<font color='red'>ERROR:</font> PID ({$pid}) is not a valid player!");
			return;
		}
		
		$query = "SELECT id FROM player WHERE id = {$pid}";
		$result = mysql_query($query);
		if (mysql_num_rows($result)==1) 
		{
			$pids_exist = true;
		} 
		else 
		{
			showLog("<<font color='red'>ERROR:</font> PID ({$pid}) is not a valid player!");
			return;
		}
	}
	
	// We are still here, so everything must have checked out
	showLog("Merging Player Data...");
	
	// Note: PID1 -->> PID0.  PID0 becomes the primary and PID1 is removed!  Make sure you get this RIGHT!!!!
	
	// Merge Single-line data tables
	$DataTables = array('army','kits','vehicles','weapons','player');
	foreach ($DataTables as $DataTable) 
	{
		showLog(" -> Merging {$DataTable} table...");
		$query = "SELECT * FROM {$DataTable} WHERE id = {$pids[1]}";
		$result = mysql_query($query);
		if (mysql_num_rows($result)==1) 
		{
			$fieldCount = mysql_num_fields($result);
			$row = mysql_fetch_row($result);
			
			// Build Update Query
			$query = "UPDATE {$DataTable} SET ";
			for( $i = 1; $i < $fieldCount; $i++ ) 
			{
				if (mysql_field_type($result, $i)=='int') 
				{
					if ($DataTable == 'player' && mysql_field_name($result, $i) == 'joined') 
					{
						$query .= "`" . mysql_field_name($result, $i) . "` = " . $row[$i] . ",\n";
					} 
					elseif ($DataTable == 'player' &&  mysql_field_name($result, $i) == 'lastonline') 
					{
						$query .= "`" . mysql_field_name($result, $i) . "` = `" . mysql_field_name($result, $i) . "`,\n";
					} 
					elseif ($DataTable == 'player' &&  mysql_field_name($result, $i) == 'rndscore') 
					{
						$query .= "`" . mysql_field_name($result, $i) . "` = (SELECT IF(" . $row[$i] . " > `" . mysql_field_name($result, $i) . "`, " . $row[$i] . ", `" . mysql_field_name($result, $i) . "`)),\n";
					} 
					else 
					{
						$query .= "`" . mysql_field_name($result, $i) . "` = `" . mysql_field_name($result, $i) . "` + " . $row[$i] . ",\n";
					}
				}
			}
			$query = rtrim($query, ",\n") . "\nWHERE id = {$pids[0]};";
			
			// Update Data
			if (mysql_query($query)) 
			{
				showLog("\t\tSuccess!");
				// Remove Old Data
				$query="DELETE FROM `{$DataTable}` WHERE id = {$pids[1]};";
				if (mysql_query($query)) 
				{
					showLog(" -> Old Player Data ({$DataTable}) Removed.");
				} 
				else 
				{
					showLog("\t\t<font color='red'>ERROR:</font> ".mysql_error());
				}
			} 
			else 
			{
				showLog("\t\t<font color='red'>ERROR:</font> ".mysql_error());
				return;
			}
		} 
		else 
		{
			showLog("\t\tNo Data");
		}
	}
	
	// Reset Unlocks
	showLog(" -> Reseting Unlocks for Player ({$pids[0]})...");
	$query = "UPDATE unlocks SET state = 'n' WHERE (id = {$pids[0]})";
	if (mysql_query($query)) 
	{
		$query = "UPDATE player SET availunlocks = 0, usedunlocks = 0 WHERE id = {$pids[0]}";
		mysql_query($query) or die(mysql_error());
		showLog("\t\tSuccess!");
		
		// Remove Old Unlocks Data
		showLog(" -> Removing Old Unlocks for Player ({$pids[1]})...");
		$query = "DELETE FROM unlocks WHERE (id = {$pids[1]})";
		if (mysql_query($query)) 
		{
			showLog("\t\tUnlocks Removed!");
		} 
		else 
		{
			showLog("\t\t<font color='red'>ERROR:</font> Unlocks Removal Failed!".mysql_error());
		}
	} 
	else 
	{
		showLog("\t\t<font color='red'>ERROR:</font> ".mysql_error());
		return;
	}
	
	// Merge Awards Data
	showLog(" -> Merging Awards table...");
	$query = "SELECT * FROM awards WHERE id = {$pids[1]};";
	$result = mysql_query($query);
	if( mysql_num_rows( $result ) )	
	{
		while( $rowsrc = mysql_fetch_array( $result ) ) 
		{
			// Check Awards exist
			if ($rowsrc['awd']) 
			{
				$query = "SELECT * FROM awards WHERE id = {$pids[0]} AND awd = " . $rowsrc['awd'] . ";";
				$chkresult = mysql_query($query);
				if( mysql_num_rows( $chkresult ) ) 
				{
					// Update Award
					$rowdest = mysql_fetch_array( $chkresult );
					$query = "UPDATE `awards` SET\n";

					switch ($rowsrc['awd'])
					{
						case 2051902:	// Gold
						case 2051907:	// Silver
						case 2051919:	// Bronze
							$query .= "`level` = `level` + " . $rowsrc['level'] . ",\n";
							break;
						default:
							$query .= "level = " . MAX($rowsrc['level'],$rowdest['level']) . ",\n";
					}

					$query .= "earned = " . MIN($rowsrc['earned'],$rowdest['earned']) . ",\n";
					$query .= "first = " . MIN($rowsrc['first'],$rowdest['first']) . "\n";
					$query .= "WHERE id = {$pids[0]} AND `awd` = " . $rowsrc['awd'] . ";";
					if (mysql_query($query)) 
					{
						showLog("\t\tAward {$rowsrc[awd]} Update Success!");
					} 
					else 
					{
						showLog("\t\t<font color='red'>ERROR:</font> Award {$rowsrc[awd]} Update Failed: ".mysql_error());
					}
				} 
				else 
				{
					// Insert Award
					$query  = "INSERT INTO `awards` SET\n";
					$query .= "`id` = {$pids[0]},\n";
					$query .= "`awd` = " . $rowsrc['awd'] . ",\n";
					$query .= "`level` = " . $rowsrc['level'] . ",\n";
					$query .= "`earned` = " . $rowsrc['earned'] . ",\n";
					$query .= "`first` = " . $rowsrc['first'] . ";";
					if (mysql_query($query)) 
					{
						showLog("\t\tAward {$rowsrc[awd]} Insert Success!");
					} 
					else 
					{
						showLog("\t\t<font color='red'>ERROR:</font> Award {$rowsrc[awd]} Insert Failed: ".mysql_error());
					}
				}
			} 
			else 
			{
				showLog("\t\t<font color='red'>ERROR:</font> Err, that shouldn't have happend! :(");
			}
		}
		showLog("\t\tAwards Table Merged!");
		
		// Remove Old Awards Data
		showLog(" -> Removing Old Awards for Player ({$pids[1]})...");
		$query = "DELETE FROM awards WHERE (id = {$pids[1]})";
		if (mysql_query($query)) 
		{
			showLog("\t\tSuccess!");
		} 
		else 
		{
			showLog("\t\t<font color='red'>ERROR:</font> ".mysql_error());
		}
	}
	
	// Merge Maps Data
	showLog(" -> Merging Maps table...");
	$query = "SELECT * FROM `maps` WHERE `id` = {$pids[1]};";
	$result = mysql_query($query);
	if( mysql_num_rows( $result ) )	
	{
		while( $rowsrc = mysql_fetch_array( $result ) ) 
		{
			// Check Map exist
			if ($rowsrc['mapid']>=0) 
			{
				$query = "SELECT * FROM `maps` WHERE `id`= {$pids[0]} AND `mapid` = " . $rowsrc['mapid'] . ";";
				$chkresult = mysql_query($query);
				if( mysql_num_rows( $chkresult ) ) 
				{
					// Update Map Data
					$rowdest = mysql_fetch_array( $chkresult );
					$query = "UPDATE `maps` SET\n";
					$query .= "`time` = `time` + " . $rowsrc['time'] . ",\n";
					$query .= "`win` = `win` + " . $rowsrc['win'] . ",\n";
					$query .= "`loss` = `loss` + " . $rowsrc['loss'] . ",\n";
					if ($rowsrc['best'] > $rowdest['best']) 
					{
						$query .= "`best` = " . $rowsrc['best'] . ",\n";
					}
					if ($rowsrc['worst'] < $rowdest['worst']) 
					{
						$query .= "`worst` = `worst` + " . $rowsrc['worst'] . "\n";
					}
					
					$query .= "WHERE id = {$pids[0]} AND `mapid` = " . $rowsrc['mapid'] . ";";
					if (mysql_query($query)) 
					{
						showLog("\t\tMap {$rowsrc[mapid]} Update Success!");
					} 
					else 
					{
						showLog("\t\t<font color='red'>ERROR:</font> Map {$rowsrc[mapid]} Update Failed: ".mysql_error());
					}
				} 
				else 
				{
					// Insert Map Data
					$query  = "INSERT INTO `maps` SET\n";
					$query .= "`id` = {$pids[0]},\n";
					$query .= "`mapid` = " . $rowsrc['mapid'] . ",\n";
					$query .= "`time` = " . $rowsrc['time'] . ",\n";
					$query .= "`win` = " . $rowsrc['win'] . ",\n";
					$query .= "`loss` = " . $rowsrc['loss'] . ",\n";
					$query .= "`best` = " . $rowsrc['best'] . ",\n";
					$query .= "`worst` = " . $rowsrc['worst'] . ";";
					if (mysql_query($query)) 
					{
						showLog("\t\tMap {$rowsrc[mapid]} Insert Success!");
					} 
					else 
					{
						showLog("\t\t<font color='red'>ERROR:</font> Map {$rowsrc[mapid]} Insert Failed: ".mysql_error());
					}
				}
			} 
			else 
			{
				showLog("\t\t<font color='red'>ERROR:</font> MapID Invalid!");
			}
		}
		showLog("\t\tDone!");
		
		// Remove Old Maps Data
		showLog(" -> Removing Old Maps for Player ({$pids[1]})...");
		$query = "DELETE FROM maps WHERE (id = {$pids[1]})";
		if (mysql_query($query)) 
		{
			showLog("\t\tSuccess!");
		} 
		else 
		{
			showLog("\t\t<font color='red'>ERROR:</font> ".mysql_error());
		}
	}
	
	// Update Kills Data
	showLog(" -> Updating Kills Data...");
	$query = "SELECT * FROM kills WHERE attacker = {$pids[1]};";
	$result = mysql_query($query);
	if( mysql_num_rows( $result ) )	
	{
		while( $rowsrc = mysql_fetch_array( $result ) ) 
		{
			// Check Kills exist
			if ($rowsrc['victim']) 
			{
				$query = "SELECT * FROM kills WHERE attacker = {$pids[0]} AND victim = " . $rowsrc['victim'] . ";";
				$chkresult = mysql_query($query);
				if( mysql_num_rows( $chkresult ) ) 
				{
					// Update Existing record
					$query = "UPDATE `kills` SET\n";
					$query .= "`count` = `count` + " . $rowsrc['count'] . "\n";
					$query .= "WHERE attacker = {$pids[0]} AND victim = " . $rowsrc['victim'] . ";";
					if (mysql_query($query)) 
					{
						// Success
					} 
					else 
					{
						showLog("\t\t<font color='red'>ERROR:</font> Kills data not updated: ".mysql_error());
					}
				} 
				else 
				{
					// Insert Kills
					$query  = "INSERT INTO `kills` SET\n";
					$query .= "attacker = {$pids[0]},\n";
					$query .= "victim = " . $rowsrc['victim'] . ",\n";
					$query .= "`count` = " . $rowsrc['count'] . ";";
					if (mysql_query($query)) 
					{
						// Success
					} 
					else 
					{
						showLog("\t\t<font color='red'>ERROR:</font> Kills data not inserted: ".mysql_error());
					}
				}
			} 
			else 
			{
				showLog("<font color='red'>#</font>");
			}
		}
		showLog("\t\tKills Done!");
		
		// Remove Old Kills Data
		showLog(" -> Removing Old Kills for Player ({$pids[1]})...");
		$query = "DELETE FROM kills WHERE (attacker = {$pids[1]})";
		if (mysql_query($query)) 
		{
			showLog("\t\tSuccess!");
		} 
		else 
		{
			showLog("\t\t<font color='red'>ERROR:</font> ".mysql_error());
		}
	}
	
	// Update Deaths Data
	showLog(" -> Updating Deaths Data...");
	$query = "SELECT * FROM kills WHERE victim = {$pids[1]};";
	$result = mysql_query($query);
	if( mysql_num_rows( $result ) )	
	{
		while( $rowsrc = mysql_fetch_array( $result ) ) 
		{
			// Check Deaths exist
			if ($rowsrc['attacker']) 
			{
				$query = "SELECT * FROM kills WHERE attacker = " . $rowsrc['attacker'] . " AND victim = {$pids[0]};;";
				$chkresult = mysql_query($query);
				if( mysql_num_rows( $chkresult ) ) 
				{
					// Update Existing record
					$query = "UPDATE `kills` SET\n";
					$query .= "`count` = `count` + " . $rowsrc['count'] . "\n";
					$query .= "WHERE attacker = " . $rowsrc['attacker'] . " AND victim = {$pids[0]};";
					if (mysql_query($query)) 
					{
						// Success
					} 
					else 
					{
						showLog("\t\t<font color='red'>ERROR:</font> Kills data not updated: ".mysql_error());
					}
				} 
				else 
				{
					// Insert Deaths
					$query  = "INSERT INTO `kills` SET\n";
					$query .= "attacker = " . $rowsrc['attacker'] . ",\n";
					$query .= "victim = {$pids[0]},\n";
					$query .= "`count` = " . $rowsrc['count'] . ";";
					if (mysql_query($query)) 
					{
						// Success
					} 
					else 
					{
						showLog("\t\t<font color='red'>ERROR:</font> Kills data not inserted: ".mysql_error());
					}
				}
			} 
			else 
			{
				showLog("<font color='red'>#</font>");
			}
		}
		showLog("\t\tDeaths Done!");
		
		// Remove Old Deaths Data
		showLog(" -> Removing Old Deaths for Player ({$pids[1]})...");
		$query = "DELETE FROM kills WHERE (victim = {$pids[1]})";
		if (mysql_query($query)) 
		{
			showLog("\t\tSuccess!");
		} 
		else 
		{
			showLog("\t\t<font color='red'>ERROR:</font> ".mysql_error());
		}
	}

	showLog("Done! :)\n");

	// Close database connection
	@mysql_close($connection);
	
	//Validating Rank
	$_POST['selitems'] = array($pids[0]);
	processValidateRanks();
	
	// Ensure Old player does not exist
	$_POST['selitems'] = array($pids[1]);
	processDeletePlayers();
	
}

function processInstallDB() 
{
	ini_set('max_execution_time', 0);
	
	// Install Default DB Schema
	$cfg = new Config();

	// Store New/Changed config items
	showLog("Saving Config...");
	foreach ($_POST as $item => $val) 
	{
		$key = explode('__', $item);
		if ($key[0] == 'cfg') 
		{
			showLog(" -> Found Key: '{$key[1]}' => '".((is_array($cfg->get($key[1])))?str_replace("\r\n", ",",$val):$val)."' (Old: ".((is_array($cfg->get($key[1])))?implode(',',$cfg->get($key[1])):$cfg->get($key[1])).")...");
			$cfg->set($key[1],$val);
		}
	}
	$cfg->Save();
	
	$connection = @mysql_connect($cfg->get('db_host'), $cfg->get('db_user'), $cfg->get('db_pass'));
	@mysql_select_db($cfg->get('db_name'), $connection) or die("Database Error: " . mysql_error());
	
	// Import Schema
	require('includes/db/sql.dbschema.php');
	
	showLog("Installing Database Schema");
	foreach ($sqlschema as $query) 
	{
		if (mysql_query($query[1])) 
		{
			showLog(" -> ".$query[0]." Installed");
		} 
		else 
		{
			showLog(" -> <font color='red'>ERROR:</font> ".$query[0]." *NOT* Installed: ".mysql_error());
		}
	}
	
	// Import Defaut Data
	require('includes/db/sql.dbdata.php');
	
	$i = 0;
	showLog("Loading Default Database Data");
	foreach ($sqldata as $query) 
	{
		if (mysql_query($query[1])) 
		{
			if ($query[0] == _IPN) 
			{
				if (($i % 100) == 0) {showLog(" -> ".$query[0]." ({$i} records) Loaded");}
				$i++;
			} 
			else 
			{
				showLog(" -> ".$query[0]." Loaded");
			}
		} 
		else 
		{
			showLog(" -> <font color='red'>ERROR:</font> ".$query[0]." *NOT* Loaded: ".mysql_error());
		}
	}

	// Close database connection
	@mysql_close($connection);
	showLog("Done! :)");
}

function processUpgradeDB() 
{
	// Update Scheme to Include EF & AF Booster Pack Info
	$cfg = new Config();

	// Store New/Changed config items
	showLog("Saving Config...");
	foreach ($_POST as $item => $val) 
	{
		$key = explode('__', $item);
		if ($key[0] == 'cfg') 
		{
			showLog(" -> Found Key: '{$key[1]}' => '".((is_array($cfg->get($key[1])))?str_replace("\r\n", ",",$val):$val)."' (Old: ".((is_array($cfg->get($key[1])))?implode(',',$cfg->get($key[1])):$cfg->get($key[1])).")...");
			$cfg->set($key[1],$val);
		}
	}
	$cfg->Save();
	
	// Get DB Version
	$curdbver = verCmp(getDbVer());
	
	// Open Database connection
	$connection = @mysql_connect($cfg->get('db_host'), $cfg->get('db_user'), $cfg->get('db_pass'));
	@mysql_select_db($cfg->get('db_name'), $connection) or die("Database Error: " . mysql_error());
	
	// Upgrade Schema
	require('includes/db/sql.dbupgrade.php');
	
	showLog("Upgrading Database Schema");
	foreach ($sqlupgrade as $query) 
	{
		if ($curdbver < verCmp($query[1])) 
		{
			if (mysql_query($query[2])) 
			{
				showLog(" -> ".$query[0]." Success");
			} 
			else 
			{
				showLog(" -> <font color='red'>ERROR:</font> ".$query[0]." *FAILED*: ".mysql_error());
			}
		} 
		else 
		{
			showLog(" -> <font color='blue'>Skipping:</font> ".$query[0]);
		}
	}

	// Close database connection
	@mysql_close($connection);
	showLog("Done! :)");
}

function processClearDB() 
{
	// This script will clear the Gamespy tables.  Use with EXTREME cation!!
	global $cfg, $DataTables;
	$connection = @mysql_connect($cfg->get('db_host'), $cfg->get('db_user'), $cfg->get('db_pass'));
	@mysql_select_db($cfg->get('db_name'), $connection) or die("Database Error: " . mysql_error());
	
	showLog("Clearing Data Tables");
	foreach ($DataTables as $DataTable) 
	{
		// Check Table Exists
		$query = "SHOW TABLES LIKE '" . $DataTable . "'";
		$result = mysql_query($query);
		if (mysql_num_rows($result)) 
		{
			// Table Exists, lets clear it
			$query="TRUNCATE TABLE `" . $DataTable . "`;";
			$result = mysql_query($query);
			if ($result) 
			{
				showLog(" -> Table (" . $DataTable . ") Cleared.");
			} 
			else 
			{
				showLog(" -> <font color='red'>ERROR:</font> Table (" . $DataTable . ") NOT Cleared!\n".mysql_error());
			}
		}
	}

	// Close database connection
	@mysql_close($connection);
	showLog("Done! :)");
}

function processBackupDB() 
{
	// This script will backup the "Gamespy" Data Tabels
	global $cfg, $DataTables;
	$connection = @mysql_connect($cfg->get('db_host'), $cfg->get('db_user'), $cfg->get('db_pass'));
	@mysql_select_db($cfg->get('db_name'), $connection) or die("Database Error: " . mysql_error());
		
	showLog("Backing Up Data Tables");
	
	// Create Backup Folder
	$backupPath  = chkPath($cfg->get('admin_backup_path'));
	$backupPath .= "bak_".date('Ymd_Hi');
	mkdir($backupPath, 0666);	// Read and write for owner, nothing for everybody else
	showLog(" -> Created Backup Directory: {$backupPath}");
	
	foreach ($DataTables as $DataTable) 
	{
		// Check Table Exists
		$query = "SHOW TABLES LIKE '" . $DataTable . "'";
		$result = mysql_query($query);
		if (mysql_num_rows($result) == 1) 
		{
			// Table Exists, lets back it up
			$backupFile = $backupPath ."/". $DataTable . $cfg->get('admin_backup_ext');
			$query = "SELECT * INTO OUTFILE '{$backupFile}' FROM {$DataTable};";
			$result = mysql_query($query);
			if ($result) 
			{
				showLog(" -> Table (" . $DataTable . ") Backed Up.");
			} 
			else 
			{
				showLog(" -> <font color='red'>ERROR:</font> Table (" . $DataTable . ") *NOT* Backed Up: ".mysql_error());
			}
		}
	}
	// Close database connection
	@mysql_close($connection);
	showLog("Done! :)");
}

function processRestoreDB() 
{
	// This script will backup the "Gamespy" Data Tabels
	global $cfg, $DataTables;
	$connection = @mysql_connect($cfg->get('db_host'), $cfg->get('db_user'), $cfg->get('db_pass'));
	@mysql_select_db($cfg->get('db_name'), $connection) or die("Database Error: " . mysql_error());

	showLog("Restore Data Tables");
	
	// Check Backup Folder
	$backupPath  = chkPath($cfg->get('admin_backup_path'));
	$backupPath .= $_POST["backupname"];
	showLog(" -> Loaded Backup Directory: {$backupPath}");

	foreach ($DataTables as $DataTable) 
	{
		// Check Table Exists
		$query = "SHOW TABLES LIKE '" . $DataTable . "'";
		$result = mysql_query($query);
		if (mysql_num_rows($result) == 1) 
		{
			// Table Exists
			$backupFile = $backupPath ."/". $DataTable.$cfg->get('admin_backup_ext');
			if (file_exists($backupFile)) 
			{
				// File Exists, lets restore it
				$query="LOAD DATA INFILE '{$backupFile}' INTO TABLE {$DataTable};";
				$result = mysql_query($query);
				if ($result) 
				{
					showLog(" -> Table (" . $DataTable . ") Restored.");
				} 
				else 
				{
					showLog(" -> <font color='red'>ERROR:</font> Table (" . $DataTable . ") *NOT* Restored: ".mysql_error());
				}
			} 
			else 
			{
				showLog(" -> <font color='red'>ERROR:</font> Data File (" . $backupFile . ") does *NOT* Exist!!");
			}
		}
	}
	// Close database connection
	@mysql_close($connection);
	showLog("Done! :)");
}

function processValidateRanks() 
{
	// This script will validate the BF2 Gamespy Ranks
	global $cfg;
	$connection = @mysql_connect($cfg->get('db_host'), $cfg->get('db_user'), $cfg->get('db_pass'));
	@mysql_select_db($cfg->get('db_name'), $connection) or die("Database Error: " . mysql_error());

	$pids_arr = $_POST['selitems'];
	
	showLog("Validating Player Ranks");
	$query = "SELECT `id`, score, rank FROM player";
	if ($pids_arr) 
	{
		$pids = implode(", ", $pids_arr);
		$query .= " WHERE id IN ({$pids})";
	} 
	else 
	{
		showLog("<font color='red'>ERROR:</font> No Players Selected!");
		return;
	}

	$result = mysql_query($query);
	if (mysql_num_rows($result) > 0) 
	{
		$expRank = array();
		while ($row = mysql_fetch_array($result)) 
		{
			$pid = $row['id'];
			$score = $row['score'];
			$rank  = $row['rank'];
			showLog(" -> Found Player ({$pid}) : Score:{$score} : Rank:{$rank}");
			
			// Verify/Correct Rank
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
			if ($rank < $expRank[0] || $rank > $expRank[1])
			{
				// Rank seems to be messed up, will reset to minimum rank for this level
				showLog(" -> Rank Correction (".$row['id']."):");
				showLog("      Score: ".$score);
				showLog("      Expected: ".$expRank[0]."-".$expRank[1]);
				showLog("      Found: ".$row['rank']);
				showLog("      New Rank: ".$expRank[0]);
				
				// Update Database
				$query = "UPDATE player SET rank = ".$expRank[0]." WHERE id = ".$row['id'];
				if (mysql_query($query)) 
				{
					showLog(" -> Rank Correction: Success!");
				} 
				else 
				{
					showLog(" -> Rank Correction: Fail! (".mysql_error().")");
				}
			}
		}
		showLog("Done! :)");
	} 
	else 
	{
		showLog("<font color='red'>ERROR:</font>  No Data Found!");
	}
	// Close database connection
	@mysql_close($connection);
}

function processCheckAwards() 
{
	// This script will check for Backend awards
	global $cfg;
	$connection = @mysql_connect($cfg->get('db_host'), $cfg->get('db_user'), $cfg->get('db_pass'));
	@mysql_select_db($cfg->get('db_name'), $connection) or die("Database Error: " . mysql_error());
	
	// Import Backend Awards Data
	require('includes/data.awards.php');
	
	// Check for Backend Awards Data
	if (!isset($backendawardsdata)) 
	{
		$backendawardsdata = array_merge(buildBackendAwardsData('bf2'), buildBackendAwardsData('xpack'));
	}
	
	$startTime = microtime_float();
	
	$pids_arr = $_POST['selitems'];
	
	showLog("Checking Player Backend Awards");
	$query = "SELECT `id` FROM player";
	if ($pids_arr) 
	{
		$pids = implode(", ", $pids_arr);
		$query .= " WHERE id IN ({$pids})";
	} 
	else 
	{
		showLog("ERROR:<font color='red'>ERROR:</font> No Players Selected!");
		return;
	}

	$result = mysql_query($query);
	if (mysql_num_rows($result) > 0) 
	{
		while ($rowp = mysql_fetch_array($result)) 
		{
			$pid = $rowp['id'];
			showLog(" -> Found Player ({$pid})");
			
			$playerStartTime = microtime_float();
			
			// Calculate Awards
			foreach ($backendawardsdata as $award) 
			{
				// Check if Player already has Award
				$query = "SELECT awd, level FROM awards WHERE (id = " . $pid . ") AND (awd = {$award[0]})";
				$awdresult = mysql_query($query);
				if (!$awdresult) 
				{
					showLog("<font color='red'>ERROR:</font> " . mysql_error());
					showLog("Query String: " . $query);
				}
				// Check if player has award
				if (mysql_num_rows($awdresult)>0) 
				{
					$rowawd = mysql_fetch_array($awdresult);
				}
				
				// Check Criteria
				$chkcriteria = false;
				foreach ($award[3] as $criteria) 
				{
					if ($award[2] == 2) 
					{
						// Can receive multiple times
						if (mysql_num_rows($awdresult)>0) 
						{
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
						"WHERE (id = " . $pid . ") AND ({$where})\n" .
						"ORDER BY id;";
					$chkresult = mysql_query($query);
					if (!$chkresult) 
					{
						showLog("<font color='red'>ERROR:</font>  ERROR: " . mysql_error() . "\n");
						showLog("Query String: " . $query);
					}
					if (mysql_num_rows($chkresult) > 0) 
					{
						$rowchk = mysql_fetch_array($chkresult);
						if ($rowchk['checkval'] >= $criteria[2]) 
						{
							$chkcriteria = true;
							break;
						} 
						else 
						{
							$chkcriteria = false;
							break;
						}
					}
				}
				
				if ($chkcriteria && mysql_num_rows($awdresult) == 0) 
				{
					showLog("    - Award Missing ({$award[0]})");
					// Insert information
					$query = "INSERT INTO awards SET
						id = " . $pid . ",
						awd = {$award[0]},
						level = 1,
						earned = " . time() . ",
						first = 0;";
					if (!mysql_query($query)) 
					{
						showLog("<font color='red'>ERROR:</font> " . mysql_error());
						showLog("Query String: " . $query);
					}
				} 
				elseif (!$chkcriteria && mysql_num_rows($awdresult)>0) 
				{
					if ($rowawd['awd'] == $award[0]) 
					{
						showLog("    - Has Award ({$award[0]}), but does not meet requirements!");
						// Delete information
						$query = "DELETE FROM awards WHERE (id = " . $pid . " AND awd = {$award[0]});";
						if (!mysql_query($query)) 
						{
							showLog("<font color='red'>ERROR:</font> " . mysql_error());
							showLog("Query String: " . $query);
						}
					}
				} 
				elseif ($chkcriteria) 
				{
					showLog("    - Has Award ({$award[0]}), requirements met!");
				}
			}
			showLog("    Processing Time ({$pid}): " . (microtime_float() - $playerStartTime));
		}
		showLog("Done! :)");
	} 
	else 
	{
		showLog("<font color='red'>ERROR:</font>  No Data Found!");
	}

	// Close database connection
	@mysql_close($connection);
	showLog("Total Processing Time: " . (microtime_float() - $startTime));
}

function processImportLogs() 
{
	// This function will import all existing log files.  This is useful for rebuilding an empty Gamespy database
	global $cfg;

	// Make Sure Script doesn't timeout
	set_time_limit(0);
	
	// Find Log Files
	showLog("Importing Log Files");
	$regex = '([0-9]{4})([0-9]{2})([0-9]{2})_([0-9]{4})';

	$dir = opendir(chkPath($cfg->get('stats_logs')));
	chdir(chkPath($cfg->get('stats_logs')));
	while(($file = readdir($dir)) !== false)
	{
		if (strpos($file, $cfg->get('stats_ext')))
		{
			ereg($regex,$file,$sort);
			$files[] = $sort[0] . "|" . $file;
		}
	}
	
	// Sort Files
	sort($files, SORT_STRING);

	// Re-post existing log data to bf2statistics
	$total = 0;
	for ($x = 0; $x < count($files); $x++)
	{
		$file = explode("|",$files[$x]);
		$fh = fsockopen($_SERVER['HTTP_HOST'], 80);
		
		fwrite($fh, "POST /ASP/bf2statistics.php HTTP/1.1\r\n");
		fwrite($fh, "HOST: localhost\r\n");
		fwrite($fh, "User-Agent: GameSpyHTTP/1.0\r\n");
		fwrite($fh, "Content-Type: application/x-www-form-urlencoded\r\n");
		
		$filename = @fopen($file[1], 'r');
		$data = fread($filename, filesize($file[1]));
		@fclose($filename);
		
		if (strpos($data, '\EOF\1') === false) 
		{
			// Older SNAPSHOT.  Insert EOF to ensure bf2statiscs.php processes this...
			$data .= '\EOF\1';
		}

		if (strpos($file[1], "importdata") === false) 
		{
			// Make sure we know this is an import of existing log data
			$data .= '\import\1';
		}
		fwrite($fh, "Content-Length: " . strlen($data) . "\r\n\r\n");
		fwrite($fh, $data . "\r\n");
		fclose($fh);
		showLog(" -> Importing $file[1]...done!");
		$total++;
	}
	
	showLog("Total files imported: $total");
}

function microtime_float() 
{
	list($usec, $sec) = explode(" ", microtime());
	return ((float)$usec + (float)$sec);
}
?>