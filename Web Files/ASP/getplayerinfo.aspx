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

/**************************************
* 12/01/05 v0.0.1 - ALPHA build       *
* 12/02/05 v0.0.2 - Updated query     *
* 12/03/05 v0.0.3 - Updated query     *
* 12/08/05 v0.0.4 - Updated query     *
* 12/09/05 v0.0.5 - Removed nvg/gm    *
* 12/10/05 v0.0.6 - Updated query     *
* 12/11/05 v0.0.7 - Updated query     *
* 12/12/05 v0.0.8 - Updated for MNG   *
* 12/14/05 v0.0.9 - Updated for MNG   *
*                   Added abs-        *
* 12/23/05 v0.0.10 - Added tkills     *
* 12/25/05 v0.0.11 - Updated query    *
* 12/27/05 v0.0.12 - Updated query    *
* 12/29/05 v0.0.13 - Updated for MNG  *
* 01/03/06 v0.1 - BETA release        *
* 01/05/06 v0.1.1 - Updated query     *
* 02/14/06 v0.1.2 - Fixed skillscore  *
* 06/17/06 v0.1.3 - EF and AF support *
*                  added by Wolverine *
* 06/24/06 v0.1.4 - Validated output  *
*		against gamespy	              *
* 07/26/06 v0.1.6 - Added transpose   *
*		  support                     *
*		Correct output data			  *
* 07/31/06 v0.1.7 - Corrected Best    *
*		  Round Result Data           *
*		added support for Mode0/1/2	  *
*		added error check for kit/    *
*		  weapon/vehicle/map query 	  *
* 08/02/06 v0.1.8 - Added support for * 
*		mtm- data in gamespy query   *
*		Added prelim. support for v1.4 patch *
* 08/08/06 v0.1.9 - Added support for * 
*		mbs- & mws- data in gamespy query   *
**************************************/

/****************************************************
* 06/12/10 - Add Highway tampa/Operation blue pearl *
* 06/12/10 - Fixed missed weapons                   *
* 06/12/10 - Fixed Opponent/victim                  *
* 06/12/10 - Remade Header                          *
* 06/12/10 - Fixed abr-                             *
* 02/04/12 v1.0.0 Release & Fixes					*
****************************************************/

//Disable Zlib Compression
ini_set('zlib.output_compression', '0'); 

$pid = (isset($_GET['pid'])) ? $_GET['pid'] : false;
$info = (isset($_GET['info'])) ? $_GET['info'] : '';
$transpose = (isset($_GET['transpose'])) ? $_GET['transpose'] : 0;

/*
URL:http://bf2.fun-o-matic.org/index.php/BF2_Statistics

Each function returns data in a similar standard format,
the data is simple text with tab seperated data values.
The opening line will either be an O or an E, an E signifies an error has occurred,
whereas an O is valid data.
The last line of the returned data starts with a $
followed by the number of bytes in the message (without tabs, newlines, or the length itself),
followed by a final $.
When valid data is returned, the lines consist of header and data rows,
indicated by H and D. Each header row is followed by one or more data rows.
*/

//omero, 2006-04-15
//better have this close to the to the beginning
// Import configuration
require('includes/utils.php');
$cfg = new Config();

if (!$pid || !is_numeric($pid) || !is_numeric($transpose) || $info == '') 
{
	$num = 0;
	$head = "E\nH\tasof\terr\n";
	$out  = "D\t" . time() . "\tInvalid Syntax!\n";
	$num += strlen(preg_replace('/[\t\n]/','',$head));
	$num += strlen(preg_replace('/[\t\n]/','',$out));
	
	print $head;
	print $out;
	print "$\t$num\t$";
}
else
{
	
	//omero, 2006-04-15
	//initialize message lengh counter and message header part
	$num = 0;
	$head = "O\n" .
	"H\tasof\n" .
	"D\t" . time() . "\n";
	
	//FIXME
	//omero, 2006-04-15
	//correct length of message will be calculated later
	//$num = 25;

	$connection = @mysql_connect($cfg->get('db_host'), $cfg->get('db_user'), $cfg->get('db_pass'));
	@mysql_select_db($cfg->get('db_name'), $connection);

	// Player info
	//'Reworked' for MNG stats =)
	//omero, 2006-04-15 
	//split up an otherwise long and unreadable line 
	$requiredKeys = "per*,cmb*,twsc,cpcp,cacp,dfcp,kila,heal,rviv,rsup,rpar," .
		"tgte,dkas,dsab,cdsc,rank,cmsc,kick,kill,deth,suic,ospm," .
		"klpm,klpr,dtpr,bksk,wdsk,bbrs,tcdr,ban,dtpm,lbtl,osaa," .
		"vrk,tsql,tsqm,tlwf,mvks,vmks,mvn*,vmr*,fkit,fmap,fveh,fwea," .
		"wtm-,wkl-,wdt-,wac-,wkd-,vtm-,vkl-,vdt-,vkd-,vkr-," .
		"atm-,awn-,alo-,abr-,ktm-,kkl-,kdt-,kkd-";
		
	if (strpos($info,$requiredKeys) !== false)
	{		
		$head .= "H\t" .
			"pid\tnick\tscor\tjond\twins\tloss\tmode0\tmode1\tmode2\t" .
			"time\tsmoc\tcmsc\tosaa\tkill\tkila\tdeth\tsuic\tbksk\twdsk\ttvcr\ttopr\tklpm\t" .
			"dtpm\tospm\tklpr\tdtpr\ttwsc\tcpcp\tcacp\tdfcp\theal\trviv\trsup\trpar\ttgte\t" .
			"dkas\tdsab\tcdsc\trank\tkick\tbbrs\ttcdr\tban\tlbtl\tvrk\ttsql\ttsqm\ttlwf\t" .
			"mvks\tvmks\tmvns\tmvrs\tvmns\tvmrs\tfkit\tfmap\tfveh\tfwea\ttnv\ttgm\t" .
			"wtm-0\twtm-1\twtm-2\twtm-3\twtm-4\twtm-5\twtm-6\twtm-7\twtm-8\twtm-9\twtm-10\twtm-11\twtm-12\twtm-13\t" .
			"wkl-0\twkl-1\twkl-2\twkl-3\twkl-4\twkl-5\twkl-6\twkl-7\twkl-8\twkl-9\twkl-10\twkl-11\twkl-12\twkl-13\t" .
			"wdt-0\twdt-1\twdt-2\twdt-3\twdt-4\twdt-5\twdt-6\twdt-7\twdt-8\twdt-9\twdt-10\twdt-11\twdt-12\twdt-13\t" .
			"wac-0\twac-1\twac-2\twac-3\twac-4\twac-5\twac-6\twac-7\twac-8\twac-9\twac-10\twac-11\twac-12\twac-13\t" .
			"wkd-0\twkd-1\twkd-2\twkd-3\twkd-4\twkd-5\twkd-6\twkd-7\twkd-8\twkd-9\twkd-10\twkd-11\twkd-12\twkd-13\t" .
			"vtm-0\tvtm-1\tvtm-2\tvtm-3\tvtm-4\tvtm-5\tvtm-6\t" .
			"vkl-0\tvkl-1\tvkl-2\tvkl-3\tvkl-4\tvkl-5\tvkl-6\t" .
			"vdt-0\tvdt-1\tvdt-2\tvdt-3\tvdt-4\tvdt-5\tvdt-6\t" .
			"vkd-0\tvkd-1\tvkd-2\tvkd-3\tvkd-4\tvkd-5\tvkd-6\t" .
			"vkr-0\tvkr-1\tvkr-2\tvkr-3\tvkr-4\tvkr-5\tvkr-6\t" .
			"atm-0\tatm-1\tatm-2\tatm-3\tatm-4\tatm-5\tatm-6\tatm-7\tatm-8\tatm-9\t" .
			"awn-0\tawn-1\tawn-2\tawn-3\tawn-4\tawn-5\tawn-6\tawn-7\tawn-8\tawn-9\t" .
			"alo-0\talo-1\talo-2\talo-3\talo-4\talo-5\talo-6\talo-7\talo-8\talo-9\t" .
			"abr-0\tabr-1\tabr-2\tabr-3\tabr-4\tabr-5\tabr-6\tabr-7\tabr-8\tabr-9\t" .
			"ktm-0\tktm-1\tktm-2\tktm-3\tktm-4\tktm-5\tktm-6\t" .
			"kkl-0\tkkl-1\tkkl-2\tkkl-3\tkkl-4\tkkl-5\tkkl-6\t" .
			"kdt-0\tkdt-1\tkdt-2\tkdt-3\tkdt-4\tkdt-5\tkdt-6\t" .
			"kkd-0\tkkd-1\tkkd-2\tkkd-3\tkkd-4\tkkd-5\tkkd-6\t" .
			"de-6\tde-7\tde-8";

		// Mods Extra Army Data
		if (strpos($info, 'mods-') !== false) 
        {
			$head .= "\tatm-10\tatm-11\t" .
				"awn-10\tawn-11\t" .
				"alo-10\talo-11\t" .
				"abrnd-10\tabrnd-11\t" .
				"abs-10\tabs-11\t" .
				"aws-10\taws-11";
		}
		
		# For MNG
		if (strpos($info, 'mng-') !== false)
		{
			// Added variables for EF and AF maps
			$head .= "\t" .
				"mtm-0\tmtm-1\tmtm-2\tmtm-3\tmtm-4\tmtm-5\tmtm-6\tmtm-100\tmtm-101\tmtm-102\tmtm-103\tmtm-104\tmtm-105\t" .
				"mtm-601\tmtm-602\tmtm-300\tmtm-301\tmtm-302\tmtm-303\tmtm-304\tmtm-305\tmtm-306\tmtm-307\tmtm-10\tmtm-11\tmtm-110\tmtm-120\tmtm-200\tmtm-201\tmtm-202\tmtm-12\t" .
				"mwn-0\tmwn-1\tmwn-2\tmwn-3\tmwn-4\tmwn-5\tmwn-6\tmwn-100\tmwn-101\tmwn-102\tmwn-103\tmwn-104\tmwn-105\t" .
				"mwn-601\tmwn-602\tmwn-300\tmwn-301\tmwn-302\tmwn-303\tmwn-304\tmwn-305\tmwn-306\tmwn-307\tmwn-10\tmwn-11\tmwn-110\tmwn-120\tmwn-200\tmwn-201\tmwn-202\tmwn-12\t" .
				"mls-0\tmls-1\tmls-2\tmls-3\tmls-4\tmls-5\tmls-6\tmls-100\tmls-101\tmls-102\tmls-103\tmls-104\tmls-105\t" .
				"mls-601\tmls-602\tmls-300\tmls-301\tmls-302\tmls-303\tmls-304\tmls-305\tmls-306\tmls-307\tmls-10\tmls-11\tmls-110\tmls-120\tmls-200\tmls-201\tmls-202\tmls-12\t" .
				"mbs-0\tmbs-1\tmbs-2\tmbs-3\tmbs-4\tmbs-5\tmbs-6\tmbs-100\tmbs-101\tmbs-102\tmbs-103\tmbs-104\tmbs-105\t" .
				"mbs-601\tmbs-602\tmbs-300\tmbs-301\tmbs-302\tmbs-303\tmbs-304\tmbs-305\tmbs-306\tmbs-307\tmbs-10\tmbs-11\tmbs-110\tmbs-120\tmbs-200\tmbs-201\tmbs-202\tmbs-12\t" .
				"mws-0\tmws-1\tmws-2\tmws-3\tmws-4\tmws-5\tmws-6\tmws-100\tmws-101\tmws-102\tmws-103\tmws-104\tmws-105\t" .
				"mws-601\tmws-602\tmws-300\tmws-301\tmws-302\tmws-303\tmws-304\tmws-305\tmws-306\tmws-307\tmws-10\tmws-11\tmws-110\tmws-120\tmws-200\tmws-201\tmws-202\tmws-12\t" .
				"abrnd-0\tabrnd-1\tabrnd-2\tabrnd-3\tabrnd-4\tabrnd-5\tabrnd-6\tabrnd-7\tabrnd-8\tabrnd-9\t" .
				"abs-0\tabs-1\tabs-2\tabs-3\tabs-4\tabs-5\tabs-6\tabs-7\tabs-8\tabs-9\t" .
				"aws-0\taws-1\taws-2\taws-3\taws-4\taws-5\taws-6\taws-7\taws-8\taws-9\t" .
				"mng-1\tmng-2\tmng-3\tmng-4\tmng-5\tmng-6\tmng-7\tmng-8\tmng-9\t" .
				"mng-10\tmng-11\tmng-12\tmng-13\tmng-14\tmng-15\tmng-16\tmng-17\t" .
				"mng-18\tmng-19\tmng-20\tmng-21\tmng-22\tmng-23\tmng-24\t" .
				"tkil\ttdmg\ttvdm";
			
			// Custom Map Handling (Based on idea by: THE_WUQKED)
			if (strpos($info, 'cmap-') !== false)
			{
				$query = "SELECT * FROM mapinfo WHERE id >= " . $cfg->get('game_custom_mapid');
				$resultm = mysql_query($query) or die(mysql_error());
				if (!mysql_num_rows($resultm)) {
					// No Custom Maps found, ignoring section
				} else {
					$usermaps = array();
					while ($rowum = mysql_fetch_array($resultm)) {
						$usermaps[] = $rowum['id'];
					}
					asort($usermaps);
					
					// Map Time Header
					foreach ($usermaps as $usermapid) {$head .= "\tmtm-" . $usermapid;}
					// Map Wins Header
					foreach ($usermaps as $usermapid) {$head .= "\tmwn-" . $usermapid;}
					// Map Lossess Header
					foreach ($usermaps as $usermapid) {$head .= "\tmls-" . $usermapid;}
					// Map Best Score Header
					foreach ($usermaps as $usermapid) {$head .= "\tmbs-" . $usermapid;}
					// Map Worst Score Header
					foreach ($usermaps as $usermapid) {$head .= "\tmws-" . $usermapid;}
				}
			}
		}
		
		//omero, 2006-04-15
		//header is complete now,
		//add length of message header
		$num += strlen(preg_replace('/[\t\n]/','',$head));

		// Player 
		$query = "SELECT * FROM player WHERE id = {$pid}";
		$result = mysql_query($query) or die(mysql_error());
		$row = mysql_fetch_array($result);
		if (!$row) 
		{
			$num = 0;    // This header shows that your BFHQ is working.
			$head = "O\tH\tasof\tD\t" . time() . "\tH\t" .
                 "pid\tnick\tscor\tjond\twins\tloss\tmode0\tmode1\tmode2\t" .
                 "time\tsmoc\tcmsc\tosaa\tkill\tkila\tdeth\tsuic\tbksk\twdsk\ttvcr\ttopr\tklpm\tdtpm\tospm\tklpr\t" .
                 "dtpr\ttwsc\tcpcp\tcacp\tdfcp\theal\trviv\trsup\trpar\ttgte\t" .
                 "dkas\tdsab\tcdsc\trank\tkick\tbbrs\ttcdr\tban\tlbtl\tvrk\ttsql\ttsqm\ttlwf\t" .
                 "mvks\tvmks\tmvns\tmvrs\tvmns\tvmrs\tfkit\tfmap\tfveh\tfwea\ttnv\ttgm\t" .
                 "wtm-0\twtm-1\twtm-2\twtm-3\twtm-4\twtm-5\twtm-6\twtm-7\twtm-8\twtm-9\twtm-10\twtm-11\twtm-12\twtm-13\t" .
                 "wkl-0\twkl-1\twkl-2\twkl-3\twkl-4\twkl-5\twkl-6\twkl-7\twkl-8\twkl-9\twkl-10\twkl-11\twkl-12\twkl-13\t" .
                 "wdt-0\twdt-1\twdt-2\twdt-3\twdt-4\twdt-5\twdt-6\twdt-7\twdt-8\twdt-9\twdt-10\twdt-11\twdt-12\twdt-13\t" .
                 "wac-0\twac-1\twac-2\twac-3\twac-4\twac-5\twac-6\twac-7\twac-8\twac-9\twac-10\twac-11\twac-12\twac-13\t" .
                 "wkd-0\twkd-1\twkd-2\twkd-3\twkd-4\twkd-5\twkd-6\twkd-7\twkd-8\twkd-9\twkd-10\twkd-11\twkd-12\twkd-13\t" .
                 "vtm-0\tvtm-1\tvtm-2\tvtm-3\tvtm-4\tvtm-5\tvtm-6\t" .
                 "vkl-0\tvkl-1\tvkl-2\tvkl-3\tvkl-4\tvkl-5\tvkl-6\t" .
                 "vdt-0\tvdt-1\tvdt-2\tvdt-3\tvdt-4\tvdt-5\tvdt-6\t" .
                 "vkd-0\tvkd-1\tvkd-2\tvkd-3\tvkd-4\tvkd-5\tvkd-6\t" .
                 "vkr-0\tvkr-1\tvkr-2\tvkr-3\tvkr-4\tvkr-5\tvkr-6\t" .
                 "atm-0\tatm-1\tatm-2\tatm-3\tatm-4\tatm-5\tatm-6\tatm-7\tatm-8\tatm-9\t" .
                 "awn-0\tawn-1\tawn-2\tawn-3\tawn-4\tawn-5\tawn-6\tawn-7\tawn-8\tawn-9\t" .
                 "alo-0\talo-1\talo-2\talo-3\talo-4\talo-5\talo-6\talo-7\talo-8\talo-9\t" .
                 "abr-0\tabr-1\tabr-2\tabr-3\tabr-4\tabr-5\tabr-6\tabr-7\tabr-8\tabr-9\t" .
                 "ktm-0\tktm-1\tktm-2\tktm-3\tktm-4\tktm-5\tktm-6\t" .
                 "kkl-0\tkkl-1\tkkl-2\tkkl-3\tkkl-4\tkkl-5\tkkl-6\t" .
                 "kdt-0\tkdt-1\tkdt-2\tkdt-3\tkdt-4\tkdt-5\tkdt-6\t" .
                 "kkd-0\tkkd-1\tkkd-2\tkkd-3\tkkd-4\tkkd-5\tkkd-6\tde-6\tde-7\tde-8\tD\t0\t \t0\t" . time() . "\t" .
                 "0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t \t \t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t" .
                 "0\t0\t0\t0\t0\t0\t" . time() . "\t0\t0\t0\t0\t0\t0\t \t \t \t \t-1\t-1\t-1\t-1\t0\t0\t0\t0\t0\t0\t0\t" .
                 "0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t" .
                 "0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t" .
                 "0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t" .
                 "0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t" .
                 "0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t" .
                 "0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\t\t0\t0\t0\t0\t0\t0\t0\t0\t0\t0\n";
			$out  = "";
			$num += strlen(preg_replace('/[\t\n]/','',$head));
			$num += strlen(preg_replace('/[\t\n]/','',$out));
			
			print $head;
			print $out;
		//	print "$\t$num\t$";
			die();
	/*	} 
		elseif ($row['score'] == 0) 
		{
			// This player is most likely a new player with NO data!
			$out = "D\t" . $row['id'] . "\t" . $row['name'] . "\t";
			
			// Set All Data responses to 0
			$fieldCount = substr_count($head, "\t") - 3;
			for( $i = 1; $i < $fieldCount; $i++ ) 
			{
				$out .= "0\t";
			}
			$num += strlen(preg_replace('/[\t\n]/','',$out));  */
		}
		else 
		{                                                                                     
		
			# For MNG
			$name = trim($row['name']);
			if (strpos($info, 'mng-') !== false) {$name = htmlspecialchars($name);}

			// Weapons
			$query = "SELECT * FROM weapons WHERE id = {$pid}";
			$result = mysql_query($query) or die(mysql_error());
			$roww = mysql_fetch_array($result);

			// Vehicles
			$query = "SELECT * FROM vehicles WHERE id = {$pid}";
			$result = mysql_query($query) or die(mysql_error());
			$rowv = mysql_fetch_array($result);

			// Army
			$query = "SELECT * FROM army WHERE id = {$pid}";
			$result = mysql_query($query) or die(mysql_error());
			$rowa = mysql_fetch_array($result);

			// Kits
			$query = "SELECT * FROM kits WHERE id = {$pid}";
			$result = mysql_query($query) or die(mysql_error());
			$rowk = mysql_fetch_array($result);
			
			// Overall weapon accuracy
			$acc = $count = 0;
			if ($roww['fired0'] != 0) {$acc += ($roww['hit0'] / $roww['fired0']) * 100; $count++;}
			if ($roww['fired1'] != 0) {$acc += ($roww['hit1'] / $roww['fired1']) * 100; $count++;}
			if ($roww['fired2'] != 0) {$acc += ($roww['hit2'] / $roww['fired2']) * 100; $count++;}
			if ($roww['fired3'] != 0) {$acc += ($roww['hit3'] / $roww['fired3']) * 100; $count++;}
			if ($roww['fired4'] != 0) {$acc += ($roww['hit4'] / $roww['fired4']) * 100; $count++;}
			if ($roww['fired5'] != 0) {$acc += ($roww['hit5'] / $roww['fired5']) * 100; $count++;}
			if ($roww['fired6'] != 0) {$acc += ($roww['hit6'] / $roww['fired6']) * 100; $count++;}
			if ($roww['fired7'] != 0) {$acc += ($roww['hit7'] / $roww['fired7']) * 100; $count++;}
			if ($roww['fired8'] != 0) {$acc += ($roww['hit8'] / $roww['fired8']) * 100; $count++;}
			if ($roww['knifefired'] != 0) {$acc += ($roww['knifehit'] / $roww['knifefired']) * 100; $count++;}
			if ($roww['shockpadfired'] != 0) {$acc += ($roww['shockpadhit'] / $roww['shockpadfired']) * 100; $count++;}
			if ($roww['c4fired'] != 0) {$acc += ($roww['c4hit'] / $roww['c4fired']) * 100; $count++;}
			if ($roww['claymorefired'] != 0) {$acc += ($roww['claymorehit'] / $roww['claymorefired']) * 100; $count++;}
			if ($roww['atminefired'] != 0) {$acc += ($roww['atminehit'] / $roww['atminefired']) * 100; $count++;}
			if ($roww['handgrenadefired'] != 0) {$acc += ($roww['handgrenadehit'] / $roww['handgrenadefired']) * 100; $count++;}
			if ($count) {$acc /= $count;}

			// Favorite opponent 
            $query = "SELECT attacker, count FROM kills WHERE victim = {$pid} GROUP BY count DESC LIMIT 1";
            $result = mysql_query($query) or die(mysql_error());
            if (mysql_num_rows($result)) 
			{
				$row2 = mysql_fetch_array($result);
				$favoi = $row2['attacker'];
				$favok = $row2['count'];
				$query = "SELECT name, rank FROM player WHERE id = {$favoi}";
				$result = mysql_query($query) or die(mysql_error());
				$row2 = mysql_fetch_array($result);
				$favon = trim($row2['name']);
				$favor = $row2['rank'];
            } 
			else 
			{
				$favoi = $favon = $favor = ' ';
				$favok = '0';
            } 

			# For MNG
			if (strpos($info, 'mng-') !== false) {$favon = htmlspecialchars($favon);}
			

			// Favorite victim 
			$query = "SELECT victim, count FROM kills WHERE attacker = {$pid} GROUP BY count DESC LIMIT 1";
			$result = mysql_query($query) or die(mysql_error());
			if (mysql_num_rows($result)) 
			{
				$row2 = mysql_fetch_array($result);
				$favvi = $row2['victim'];
				$favvk = $row2['count'];
				$query = "SELECT name, rank FROM player WHERE id = {$favvi}";
				$result = mysql_query($query) or die(mysql_error());
				$row2 = mysql_fetch_array($result);
				$favvn = trim($row2['name']);
				$favvr = $row2['rank'];
			} 
			else 
			{
				$favvi = $favvn = $favvr= ' ';
				$favvk = '0';
			}

			# For MNG
			if (strpos($info, 'mng-') !== false) {$favvn = htmlspecialchars($favvn);}


			// Favorite kit
			$query = "SELECT time0, time1, time2, time3, time4, time5, time6 FROM kits WHERE id = {$pid}";
			$result = mysql_query($query) or die(mysql_error());
			$row2 = mysql_fetch_array($result);
			arsort($row2);
			if (is_numeric(key($row2))) {$favk = key($row2);}
			else {next($row2); $favk = key($row2);}

			// Favorite map
			$favmap = array();
			$query = "SELECT time FROM maps WHERE id = {$pid}";
			$result = mysql_query($query) or die(mysql_error());
			while ($row2 = mysql_fetch_array($result))	{$favmap[] = $row2['time'];}
			rsort($favmap);
			$query = "SELECT mapid FROM maps WHERE (id = {$pid}) AND (time = {$favmap[0]})";
			$result = mysql_query($query) or die(mysql_error());
			$row2 = mysql_fetch_array($result);
			$favm = $row2[0];    

			// Favorite vehicle 
			$query = "SELECT time0, time1, time2, time3, time4, time5, time6 FROM vehicles WHERE id = {$pid}";
            $result = mysql_query($query) or die(mysql_error()); 
            $row2 = mysql_fetch_array($result); 
            if(empty($row2['time0']) && empty($row2['time1']) && empty($row2['time2']) && empty($row2['time3']) && empty($row2['time4']) && empty($row2['time5']) && empty($row2['time6']))
			{ 
				$favv = '-1'; 
            } 
			else 
			{ 
				arsort($row2);
				if (is_numeric(key($row2))) {$favv = key($row2);}
				else {next($row2); $favv = key($row2);}
            }         
			
			// Road Kills
			$vrk = 0;
			for ($i = 0; $i < 7; $i++) 
			{
				$vrk = $vrk + $rowv["rk$i"];
			}   

			// Favorite weapon 
			$query = "SELECT time0, time1, time2, time3, time4, time5, time6, time7, time8, knifetime, shockpadtime, (c4time+claymoretime+atminetime), handgrenadetime FROM weapons WHERE id = {$pid}";
			$result = mysql_query($query) or die(mysql_error());
			$row2 = mysql_fetch_array($result);
			arsort($row2);
			if (is_numeric(key($row2))) {$favw = key($row2);}
			else {next($row2); $favw = key($row2);}    
            
			
			// Sergeant Major Of The Corps
			$smoc = $row['rank'] == 11 ? 1 : 0;

			// Weapon accuracy
			$a0 = $a1 = $a2 = $a3 = $a4 = $a5 = $a6 = $a7 = $a8 = $a9 = $a10 = $a11 = $a12 = 0;
			if ($roww['fired0'] != 0) {$a0 = ($roww['hit0'] / $roww['fired0']) * 100;}
			if ($roww['fired1'] != 0) {$a1 = ($roww['hit1'] / $roww['fired1']) * 100;}
			if ($roww['fired2'] != 0) {$a2 = ($roww['hit2'] / $roww['fired2']) * 100;}
			if ($roww['fired3'] != 0) {$a3 = ($roww['hit3'] / $roww['fired3']) * 100;}
			if ($roww['fired4'] != 0) {$a4 = ($roww['hit4'] / $roww['fired4']) * 100;}
			if ($roww['fired5'] != 0) {$a5 = ($roww['hit5'] / $roww['fired5']) * 100;}
			if ($roww['fired6'] != 0) {$a6 = ($roww['hit6'] / $roww['fired6']) * 100;}
			if ($roww['fired7'] != 0) {$a7 = ($roww['hit7'] / $roww['fired7']) * 100;}
			if ($roww['fired8'] != 0) {$a8 = ($roww['hit8'] / $roww['fired8']) * 100;}
			if ($roww['knifefired'] != 0) {$a9 = ($roww['knifehit'] / $roww['knifefired']) * 100;}
			if ($roww['shockpadfired'] != 0) {$a10 = ($roww['shockpadhit'] / $roww['shockpadfired']) * 100;}

			if (($roww['c4fired'] + $roww['claymorefired'] + $roww['atminefired']) != 0)
			{

				$exphts = $roww['c4hit'] + $roww['claymorehit'] + $roww['atminehit'];
				$expfrd = $roww['c4fired'] + $roww['claymorefired'] + $roww['atminefired'];
				$a11 = (($exphts) / ($expfrd)) * 100;
			}

			if ($roww['handgrenadefired'] != 0) {$a12 = ($roww['handgrenadehit'] / $roww['handgrenadefired']) * 100;}

			
			// Weapon ratio 
			if(empty($roww['time0']))
			{
				$w0 = '0';
			}
			elseif ($roww['deaths0'] != 0)
			{
				$den = denominator($roww['kills0'], $roww['deaths0']);
				$w0 = $roww['kills0']/$den . ':' . $roww['deaths0']/$den;
			}
			else 
			{
				$w0 = $roww['kills0'] . ':0';
			}

			if(empty($roww['time1']))
			{
				$w1 = '0';
			}
			elseif ($roww['deaths1'] != 0)
			{
				$den = denominator($roww['kills1'], $roww['deaths1']);
				$w1 = $roww['kills1']/$den . ':' . $roww['deaths1']/$den;
			}
			else 
			{
				$w1 = $roww['kills1'] . ':0';
			}
	
			if(empty($roww['time2']))
			{
				$w2 = '0';
			}
			elseif ($roww['deaths2'] != 0)
			{
				$den = denominator($roww['kills2'], $roww['deaths2']);
				$w2 = $roww['kills2']/$den . ':' . $roww['deaths2']/$den;
			}
			else 
			{
				$w2 = $roww['kills2'] . ':0';
			}
		
			if(empty($roww['time3']))
			{
				$w3 = '0';
			}
			elseif ($roww['deaths3'] != 0)
			{
				$den = denominator($roww['kills3'], $roww['deaths3']);
				$w3 = $roww['kills3']/$den . ':' . $roww['deaths3']/$den;
			}
			else 
			{
				$w3 = $roww['kills3'] . ':0';
			}
		
			if(empty($roww['time4']))
			{
				$w4 = '0';
			}
			elseif ($roww['deaths4'] != 0)
			{
				$den = denominator($roww['kills4'], $roww['deaths4']);
				$w4 = $roww['kills4']/$den . ':' . $roww['deaths4']/$den;
			}
			else 
			{
				$w4 = $roww['kills4'] . ':0';
			}
		
			if(empty($roww['time5']))
			{
				$w5 = '0';
			}
			elseif ($roww['deaths5'] != 0)
			{
				$den = denominator($roww['kills5'], $roww['deaths5']);
				$w5 = $roww['kills5']/$den . ':' . $roww['deaths5']/$den;
			}
			else 
			{
				$w5 = $roww['kills5'] . ':0';
			}
		
			if(empty($roww['time6']))
			{
				$w6 = '0';
			}
			elseif ($roww['deaths6'] != 0)
			{
				$den = denominator($roww['kills6'], $roww['deaths6']);
				$w6 = $roww['kills6']/$den . ':' . $roww['deaths6']/$den;
			}
			else 
			{
				$w6 = $roww['kills6'] . ':0';
			}
		
			if(empty($roww['time7']))
			{
				$w7 = '0';
			}
			elseif ($roww['deaths7'] != 0)
			{
				$den = denominator($roww['kills7'], $roww['deaths7']);
				$w7 = $roww['kills7']/$den . ':' . $roww['deaths7']/$den;
			}
			else 
			{
				$w7 = $roww['kills7'] . ':0';
			}
		
			if(empty($roww['time8']))
			{
				$w8 = '0';
			}
			elseif ($roww['deaths8'] != 0)
			{
				$den = denominator($roww['kills8'], $roww['deaths8']);
				$w8 = $roww['kills8']/$den . ':' . $roww['deaths8']/$den;
			}
			else 
			{
				$w8 = $roww['kills8'] . ':0';
			}
		
			if(empty($roww['knifetime']))
			{
				$w9 = '0';
			}
			elseif ($roww['knifedeaths'] != 0)
			{
				$den = denominator($roww['knifekills'], $roww['knifedeaths']);
				$w9 = $roww['knifekills']/$den . ':' . $roww['knifedeaths']/$den;
			}
			else 
			{
				$w9 = $roww['knifekills'] . ':0';
			}
			
			if(empty($roww['shockpadtime']))
			{
				$w10 = '0';
			}
			elseif ($roww['shockpaddeaths'] != 0)
			{
				$den = denominator($roww['shockpadkills'], $roww['shockpaddeaths']);
				$w10 = $roww['shockpadkills']/$den . ':' . $roww['shockpaddeaths']/$den;
			}
			else 
			{
				$w10 = $roww['shockpadkills'] . ':0';
			}
			
			//omero, 2006-04-16
			//pre-calculate both deaths and kills with explosives
			$expdeth = $roww['c4deaths'] + $roww['claymoredeaths'] + $roww['atminedeaths'];
			$expklls = $roww['c4kills'] + $roww['claymorekills'] + $roww['atminekills'];
			
			if(empty($roww['c4time']) && empty($roww['claymoretime']) && empty($roww['atminetime']))
			{
				$w11 = '0';
			}
			elseif ($expdeth != 0)
			{
				$den = denominator( $expklls, $expdeth );
				$w11 = $expklls/$den . ':' . $expdeth/$den;
			}
			else 
			{
				$w11 = $expklls . ':0';
			}  
			
			if(empty($roww['handgrenadetime']))
			{
				$w12 = '0';
			}
			elseif ($roww['handgrenadedeaths'] != 0)
			{
				$den = denominator($roww['handgrenadekills'], $roww['handgrenadedeaths']);
				$w12 = $roww['handgrenadekills']/$den . ':' . $roww['handgrenadedeaths']/$den;
			}
			else 
			{
				$w12 = $roww['handgrenadekills'] . ':0';
			}      
    
	
			// Vehicle ratio 
			if(empty($rowv['time0']))
			{
				$v0 = '0';
			}
            elseif ($rowv['deaths0'] != 0)
			{
				$den = denominator($rowv['kills0'], $rowv['deaths0']);
				$v0 = $rowv['kills0']/$den . ':' . $rowv['deaths0']/$den;
			}
			else 
			{
				$v0 = $rowv['kills0'] . ':0';
			} 
			
	        if(empty($rowv['time1']))
			{
				$v1 = '0';
			}
			elseif ($rowv['deaths1'] != 0)
			{ 
				$den = denominator($rowv['kills1'], $rowv['deaths1']);
				$v1 = $rowv['kills1']/$den . ':' . $rowv['deaths1']/$den;
			}
			else 
			{
				$v1 = $rowv['kills1'] . ':0';
			}
			
			if(empty($rowv['time2']))
			{
				$v2 = '0';
			}
			elseif ($rowv['deaths2'] != 0)
			{
				$den = denominator($rowv['kills2'], $rowv['deaths2']);
				$v2 = $rowv['kills2']/$den . ':' . $rowv['deaths2']/$den;
			}
			else 
			{
				$v2 = $rowv['kills2'] . ':0';
			}
			
			if(empty($rowv['time3']))
			{
				$v3 = '0';
			}
			elseif ($rowv['deaths3'] != 0)
			{
				$den = denominator($rowv['kills3'], $rowv['deaths3']);
				$v3 = $rowv['kills3']/$den . ':' . $rowv['deaths3']/$den;
			}
			else 
			{
				$v3 = $rowv['kills3'] . ':0';
			}
			
			if(empty($rowv['time4']))
			{
				$v4 = '0';
			}
			elseif ($rowv['deaths4'] != 0)
			{
				$den = denominator($rowv['kills4'], $rowv['deaths4']);
				$v4 = $rowv['kills4']/$den . ':' . $rowv['deaths4']/$den;
			}
			else 
			{
				$v4 = $rowv['kills4'] . ':0';
			}
			
			if(empty($rowv['time5']))
			{
				$v5 = '0';
			}
			elseif ($rowv['deaths5'] != 0)
			{
				$den = denominator($rowv['kills5'], $rowv['deaths5']);
				$v5 = $rowv['kills5']/$den . ':' . $rowv['deaths5']/$den;
			}
			else 
			{
				$v5 = $rowv['kills5'] . ':0';
			}
			
			if(empty($rowv['time6']))
			{
				$v6 = '0';
			}
			elseif ($rowv['deaths6'] != 0)
			{
				$den = denominator($rowv['kills6'], $rowv['deaths6']);
				$v6 = $rowv['kills6']/$den . ':' . $rowv['deaths6']/$den;
			}
			else 
			{
				$v6 = $rowv['kills6'] . ':0';
			}  

			
			// Kit ratio 
			if(empty($rowk['time0']))
			{
				$k0 = '0';
			}
			elseif ($rowk['deaths0'] != 0)
			{
				$den = denominator($rowk['kills0'], $rowk['deaths0']);
				$k0 = $rowk['kills0']/$den . ':' . $rowk['deaths0']/$den;
			}
			else 
			{
				$k0 = $rowk['kills0'] . ':0';
			}
				
		    if(empty($rowk['time1']))
			{
				$k1 = '0';
			}
			elseif ($rowk['deaths1'] != 0)
			{
				$den = denominator($rowk['kills1'], $rowk['deaths1']);
				$k1 = $rowk['kills1']/$den . ':' . $rowk['deaths1']/$den;
			}
			else 
			{
				$k1 = $rowk['kills1'] . ':0';
			}
			
			if(empty($rowk['time2']))
			{
				$k2 = '0';
			}
			elseif ($rowk['deaths2'] != 0)
			{
				$den = denominator($rowk['kills2'], $rowk['deaths2']);
				$k2 = $rowk['kills2']/$den . ':' . $rowk['deaths2']/$den;
			}
			else 
			{
				$k2 = $rowk['kills2'] . ':0';
			}
			
			if(empty($rowk['time3']))
			{
				$k3 = '0';
			}
			elseif ($rowk['deaths3'] != 0)
			{
				$den = denominator($rowk['kills3'], $rowk['deaths3']);
				$k3 = $rowk['kills3']/$den . ':' . $rowk['deaths3']/$den;
			}
			else 
			{
				$k3 = $rowk['kills3'] . ':0';
			}
			
			if(empty($rowk['time4']))
			{
				$k4 = '0';
			}
			elseif ($rowk['deaths4'] != 0)
			{
				$den = denominator($rowk['kills4'], $rowk['deaths4']);
				$k4 = $rowk['kills4']/$den . ':' . $rowk['deaths4']/$den;
			}
			else 
			{
				$k4 = $rowk['kills4'] . ':0';
			}
			
			if(empty($rowk['time5']))
			{
				$k5 = '0';
			}
			elseif ($rowk['deaths5'] != 0)
			{
				$den = denominator($rowk['kills5'], $rowk['deaths5']);
				$k5 = $rowk['kills5']/$den . ':' . $rowk['deaths5']/$den;
			}
			else 
			{
				$k5 = $rowk['kills5'] . ':0';
			}
			
			if(empty($rowk['time6']))
			{
				$k6 = '0';
			}
			elseif ($rowk['deaths6'] != 0)
			{
				$den = denominator($rowk['kills6'], $rowk['deaths6']);
				$k6 = $rowk['kills6']/$den . ':' . $rowk['deaths6']/$den;
			}
			else 
			{
				$k6 = $rowk['kills6'] . ':0';
			}  

			// Output
			$out = "D\t" .
				$row['id'] . "\t" .
				$name . "\t" .
				$row['score'] . "\t" .
				$row['joined'] . "\t" .
				$row['wins'] . "\t" .
				$row['losses'] . "\t" .
				$row['mode0'] . "\t" .
				$row['mode1'] . "\t" .
				$row['mode2'] . "\t" .
				$row['time'] . "\t" .
				$smoc . "\t" .
				$row['skillscore'] . "\t" .
				@number_format($acc, 2) . "\t" .
				$row['kills'] . "\t" .
				$row['damageassists'] . "\t" .
				$row['deaths'] . "\t" .
				$row['suicides'] . "\t" .
				$row['killstreak'] . "\t" .
				$row['deathstreak'] . "\t" .
				#top victim/opponent in _round_ 
				$favvi . "\t" .
				$favoi . "\t" .
				@number_format(60 * ($row['kills'] / $row['time']), 2, '.', '') . "\t" .
				@number_format(60 * ($row['deaths'] / $row['time']), 2, '.', '') . "\t" .
				@number_format(60 * ($row['score'] / $row['time']), 2, '.', '') . "\t" .
				@number_format($row['kills'] / $row['rounds'], 2, '.', '') . "\t" .
				@number_format($row['deaths'] / $row['rounds'], 2, '.', '') . "\t" .   
				$row['teamscore'] . "\t" .
				$row['captures'] . "\t" .
				$row['captureassists'] . "\t" .
				$row['defends'] . "\t" .
				$row['heals'] . "\t" .
				$row['revives'] . "\t" .
				$row['ammos'] . "\t" .
				$row['repairs'] . "\t" .
				$row['targetassists'] . "\t" .
				$row['driverassists'] . "\t" .
				$row['driverspecials'] . "\t" .
				$row['cmdscore'] . "\t" .
				$row['rank'] . "\t" .
				$row['kicked'] . "\t" .
				$row['rndscore'] . "\t" .
				$row['cmdtime'] . "\t" .
				$row['banned'] . "\t" .
				$row['lastonline'] . "\t" .
				$vrk . "\t" .
				$row['sqltime'] . "\t" .
				$row['sqmtime'] . "\t" .
				$row['lwtime'] . "\t" .
				$favvk . "\t" .
				$favok . "\t" .
				$favvn . "\t" .
				$favvr . "\t" .
				$favon . "\t" .
				$favor . "\t" .
				$favk . "\t" .
				$favm . "\t" .
				$favv . "\t" .
				$favw . "\t" .
				#nvg/gas time
				"0\t0\t" .
				$roww['time0'] . "\t" .
				$roww['time1'] . "\t" .
				$roww['time2'] . "\t" .
				$roww['time3'] . "\t" .
				$roww['time4'] . "\t" .
				$roww['time5'] . "\t" .
				$roww['time6'] . "\t" .
				$roww['time7'] . "\t" .
				$roww['time8'] . "\t" .
				$roww['knifetime'] . "\t" .
				$roww['shockpadtime'] . "\t" .
				($roww['c4time'] + $roww['claymoretime'] + $roww['atminetime']). "\t" .
				$roww['handgrenadetime'] . "\t" .
				#wtm-13
				"0\t" .
				$roww['kills0'] . "\t" .
				$roww['kills1'] . "\t" .
				$roww['kills2'] . "\t" .
				$roww['kills3'] . "\t" .
				$roww['kills4'] . "\t" .
				$roww['kills5'] . "\t" .
				$roww['kills6'] . "\t" .
				$roww['kills7'] . "\t" .
				$roww['kills8'] . "\t" .
				$roww['knifekills'] . "\t" .
				$roww['shockpadkills'] . "\t" .
				($roww['c4kills'] + $roww['claymorekills'] + $roww['atminekills']) . "\t" .
				$roww['handgrenadekills'] . "\t" .
				#wkl-13
				"0\t" .
				$roww['deaths0'] . "\t" .
				$roww['deaths1'] . "\t" .
				$roww['deaths2'] . "\t" .
				$roww['deaths3'] . "\t" .
				$roww['deaths4'] . "\t" .
				$roww['deaths5'] . "\t" .
				$roww['deaths6'] . "\t" .
				$roww['deaths7'] . "\t" .
				$roww['deaths8'] . "\t" .
				$roww['knifedeaths'] . "\t" .
				$roww['shockpaddeaths'] . "\t" .
				($roww['c4deaths'] + $roww['claymoredeaths'] + $roww['atminedeaths']) . "\t" .
				$roww['handgrenadedeaths'] . "\t" .
				$roww['ziplinedeaths'] . "\t" .
				(int)$a0 . "\t" .
				(int)$a1 . "\t" .
				(int)$a2 . "\t" .
				(int)$a3 . "\t" .
				(int)$a4 . "\t" .
				(int)$a5 . "\t" .
				(int)$a6 . "\t" .
				(int)$a7 . "\t" .
				(int)$a8 . "\t" .
				(int)$a9 . "\t" .
				(int)$a10 . "\t" .
				(int)$a11 . "\t" .
				(int)$a12 . "\t" .
				#wac-13
				"0\t" .
				$w0 . "\t" .
				$w1 . "\t" .
				$w2 . "\t" .
				$w3 . "\t" .
				$w4 . "\t" .
				$w5 . "\t" .
				$w6 . "\t" .
				$w7 . "\t" .
				$w8 . "\t" .
				$w9 . "\t" .
				$w10 . "\t" .
				$w11 . "\t" .
				$w12 . "\t" .
				#wkd-13 
				"0:0\t" .
				$rowv['time0'] . "\t" .
				$rowv['time1'] . "\t" .
				$rowv['time2'] . "\t" .
				$rowv['time3'] . "\t" .
				$rowv['time4'] . "\t" .
				$rowv['time5'] . "\t" .
				$rowv['time6'] . "\t" .
				$rowv['kills0'] . "\t" .
				$rowv['kills1'] . "\t" .
				$rowv['kills2'] . "\t" .
				$rowv['kills3'] . "\t" .
				$rowv['kills4'] . "\t" .
				$rowv['kills5'] . "\t" .
				$rowv['kills6'] . "\t" .
				$rowv['deaths0'] . "\t" .
				$rowv['deaths1'] . "\t" .
				$rowv['deaths2'] . "\t" .
				$rowv['deaths3'] . "\t" .
				$rowv['deaths4'] . "\t" .
				$rowv['deaths5'] . "\t" .
				$rowv['deaths6'] . "\t" .
				$v0 . "\t" .
				$v1 . "\t" .
				$v2 . "\t" .
				$v3 . "\t" .
				$v4 . "\t" .
				$v5 . "\t" .
				$v6 . "\t" .
				$rowv['rk0'] . "\t" .
				$rowv['rk1'] . "\t" .
				$rowv['rk2'] . "\t" .
				$rowv['rk3'] . "\t" .
				$rowv['rk4'] . "\t" .
				$rowv['rk5'] . "\t" .
				$rowv['rk6'] . "\t" .
				$rowa['time0'] . "\t" .
				$rowa['time1'] . "\t" .
				$rowa['time2'] . "\t" .
				$rowa['time3'] . "\t" .
				$rowa['time4'] . "\t" .
				$rowa['time5'] . "\t" .
				$rowa['time6'] . "\t" .
				$rowa['time7'] . "\t" .
				$rowa['time8'] . "\t" .
				$rowa['time9'] . "\t" .
				$rowa['win0'] . "\t" .
				$rowa['win1'] . "\t" .
				$rowa['win2'] . "\t" .
				$rowa['win3'] . "\t" .
				$rowa['win4'] . "\t" .
				$rowa['win5'] . "\t" .
				$rowa['win6'] . "\t" .
				$rowa['win7'] . "\t" .
				$rowa['win8'] . "\t" .
				$rowa['win9'] . "\t" .
				$rowa['loss0'] . "\t" .
				$rowa['loss1'] . "\t" .
				$rowa['loss2'] . "\t" .
				$rowa['loss3'] . "\t" .
				$rowa['loss4'] . "\t" .
				$rowa['loss5'] . "\t" .
				$rowa['loss6'] . "\t" .
				$rowa['loss7'] . "\t" .
				$rowa['loss8'] . "\t" .
				$rowa['loss9'] . "\t" .
				$rowa['best0'] . "\t" .
				$rowa['best1'] . "\t" .
				$rowa['best2'] . "\t" .
				$rowa['best3'] . "\t" .
				$rowa['best4'] . "\t" .
				$rowa['best5'] . "\t" .
				$rowa['best6'] . "\t" .
				$rowa['best7'] . "\t" .
				$rowa['best8'] . "\t" .
				$rowa['best9'] . "\t" .
				$rowk['time0'] . "\t" .
				$rowk['time1'] . "\t" .
				$rowk['time2'] . "\t" .
				$rowk['time3'] . "\t" .
				$rowk['time4'] . "\t" .
				$rowk['time5'] . "\t" .
				$rowk['time6'] . "\t" .
				$rowk['kills0'] . "\t" .
				$rowk['kills1'] . "\t" .
				$rowk['kills2'] . "\t" .
				$rowk['kills3'] . "\t" .
				$rowk['kills4'] . "\t" .
				$rowk['kills5'] . "\t" .
				$rowk['kills6'] . "\t" .
				$rowk['deaths0'] . "\t" .
				$rowk['deaths1'] . "\t" .
				$rowk['deaths2'] . "\t" .
				$rowk['deaths3'] . "\t" .
				$rowk['deaths4'] . "\t" .
				$rowk['deaths5'] . "\t" .
				$rowk['deaths6'] . "\t" .
				$k0 . "\t" .
				$k1 . "\t" .
				$k2 . "\t" .
				$k3 . "\t" .
				$k4 . "\t" .
				$k5 . "\t" .
				$k6 . "\t" .
				#de-6/7/8
				$roww['tacticaldeployed'] . "\t" .
				$roww['grapplinghookdeployed'] . "\t" .
				$roww['ziplinedeployed'] . "\t";
			
			// Mods Extra Army Data
			if (strpos($info, 'mods-') !== false) 
			{
				$out .= $rowa['time10'] . "\t" .
					$rowa['time11'] . "\t" .
					$rowa['time12'] . "\t" .
					$rowa['time13'] . "\t" .
					$rowa['win10'] . "\t" .
					$rowa['win11'] . "\t" .
					$rowa['win12'] . "\t" .
					$rowa['win13'] . "\t" .
					$rowa['loss10'] . "\t" .
					$rowa['loss11'] . "\t" .
					$rowa['loss12'] . "\t" .
					$rowa['loss13'] . "\t" .
					$rowa['brnd10'] . "\t" .
					$rowa['brnd11'] . "\t" .
					$rowa['brnd12'] . "\t" .
					$rowa['brnd13'] . "\t" .
					$rowa['best10'] . "\t" .
					$rowa['best11'] . "\t" .
					$rowa['best12'] . "\t" .
					$rowa['best13'] . "\t" .
					$rowa['worst10'] . "\t" .
					$rowa['worst11'] . "\t" .
					$rowa['worst12'] . "\t" .
					$rowa['worst13'] . "\t";
			}
			
			# For MNG
			if (strpos($info, 'mng-') !== false)
			{
				// Maps 
				if (isset($usermaps)) 
				{
					$time = $win = $loss = $best = $worst = array_fill(0, end($usermaps)+1, '0');
				} 
				else 
				{
					$time = $win = $loss = $best = $worst = array_fill(0, 308, '0');
					$time[601] = $win[601] = $loss[601] = $best[601] = $worst[601] = 0;
					$time[602] = $win[602] = $loss[602] = $best[602] = $worst[602] = 0;
				}

				$querym = "SELECT * FROM maps WHERE id = {$pid}";
				$resultm = mysql_query($querym) or die(mysql_error());
				while ($rowm = mysql_fetch_array($resultm))
				{
					$time[$rowm['mapid']] = $rowm['time'];
					$win[$rowm['mapid']] = $rowm['win'];
					$loss[$rowm['mapid']] = $rowm['loss'];
					$best[$rowm['mapid']] = $rowm['best'];
					$worst[$rowm['mapid']] = $rowm['worst'];
				}
				
				$out = rtrim($out) . "\t" . 
					$time[0] . "\t" .
					$time[1] . "\t" .
					$time[2] . "\t" .
					$time[3] . "\t" .
					$time[4] . "\t" .
					$time[5] . "\t" .
					$time[6] . "\t" .
					$time[100] . "\t" .
					$time[101] . "\t" .
					$time[102] . "\t" .
					$time[103] . "\t" .
					$time[104] . "\t" .
					$time[105] . "\t" .
					$time[601] . "\t" .
					$time[602] . "\t" .
					$time[300] . "\t" .
					$time[301] . "\t" .
					$time[302] . "\t" .
					$time[303] . "\t" .
					$time[304] . "\t" .
					$time[305] . "\t" .
					$time[306] . "\t" .
					$time[307] . "\t" .
					$time[10] . "\t" .
					$time[11] . "\t" .
					$time[110] . "\t" .
					$time[120] . "\t" .
					$time[200] . "\t" .
					$time[201] . "\t" .
					$time[202] . "\t" .
					$time[12] . "\t" .
					$win[0] . "\t" .
					$win[1] . "\t" .
					$win[2] . "\t" .
					$win[3] . "\t" .
					$win[4] . "\t" .
					$win[5] . "\t" .
					$win[6] . "\t" .
					$win[100] . "\t" .
					$win[101] . "\t" .
					$win[102] . "\t" .
					$win[103] . "\t" .
					$win[104] . "\t" .
					$win[105] . "\t" .
					$win[601] . "\t" .
					$win[602] . "\t" .
					$win[300] . "\t" .
					$win[301] . "\t" .
					$win[302] . "\t" .
					$win[303] . "\t" .
					$win[304] . "\t" .
					$win[305] . "\t" .
					$win[306] . "\t" .
					$win[307] . "\t" .
					$win[10] . "\t" .
					$win[11] . "\t" .
					$win[110] . "\t" .
					$win[120] . "\t" .
					$win[200] . "\t" .
					$win[201] . "\t" .
					$win[202] . "\t" .
					$win[12] . "\t" .
					$loss[0] . "\t" .
					$loss[1] . "\t" .
					$loss[2] . "\t" .
					$loss[3] . "\t" .
					$loss[4] . "\t" .
					$loss[5] . "\t" .
					$loss[6] . "\t" .
					$loss[100] . "\t" .
					$loss[101] . "\t" .
					$loss[102] . "\t" .
					$loss[103] . "\t" .
					$loss[104] . "\t" .
					$loss[105] . "\t" .
					$loss[601] . "\t" .
					$loss[602] . "\t" .
					$loss[300] . "\t" .
					$loss[301] . "\t" .
					$loss[302] . "\t" .
					$loss[303] . "\t" .
					$loss[304] . "\t" .
					$loss[305] . "\t" .
					$loss[306] . "\t" .
					$loss[307] . "\t" .
					$loss[10] . "\t" .
					$loss[11] . "\t" .
					$loss[110] . "\t" .
					$loss[120] . "\t" .
					$loss[200] . "\t" .
					$loss[201] . "\t" .
					$loss[202] . "\t" .
					$loss[12] . "\t" .
					$best[0] . "\t" .
					$best[1] . "\t" .
					$best[2] . "\t" .
					$best[3] . "\t" .
					$best[4] . "\t" .
					$best[5] . "\t" .
					$best[6] . "\t" .
					$best[100] . "\t" .
					$best[101] . "\t" .
					$best[102] . "\t" .
					$best[103] . "\t" .
					$best[104] . "\t" .
					$best[105] . "\t" .
					$best[601] . "\t" .
					$best[602] . "\t" .
					$best[300] . "\t" .
					$best[301] . "\t" .
					$best[302] . "\t" .
					$best[303] . "\t" .
					$best[304] . "\t" .
					$best[305] . "\t" .
					$best[306] . "\t" .
					$best[307] . "\t" .
					$best[10] . "\t" .
					$best[11] . "\t" .
					$best[110] . "\t" .
					$best[120] . "\t" .
					$best[200] . "\t" .
					$best[201] . "\t" .
					$best[202] . "\t" .
					$best[12] . "\t" .
					$worst[0] . "\t" .
					$worst[1] . "\t" .
					$worst[2] . "\t" .
					$worst[3] . "\t" .
					$worst[4] . "\t" .
					$worst[5] . "\t" .
					$worst[6] . "\t" .
					$worst[100] . "\t" .
					$worst[101] . "\t" .
					$worst[102] . "\t" .
					$worst[103] . "\t" .
					$worst[104] . "\t" .
					$worst[105] . "\t" .
					$worst[601] . "\t" .
					$worst[602] . "\t" .
					$worst[300] . "\t" .
					$worst[301] . "\t" .
					$worst[302] . "\t" .
					$worst[303] . "\t" .
					$worst[304] . "\t" .
					$worst[305] . "\t" .
					$worst[306] . "\t" .
					$worst[307] . "\t" .
					$worst[10] . "\t" .
					$worst[11] . "\t" .
					$worst[110] . "\t" .
					$worst[120] . "\t" .
					$worst[200] . "\t" .
					$worst[201] . "\t" .
					$worst[202] . "\t" .
					$worst[12] . "\t" .
					$rowa['brnd0'] . "\t" .
					$rowa['brnd1'] . "\t" .
					$rowa['brnd2'] . "\t" .
					$rowa['brnd3'] . "\t" .
					$rowa['brnd4'] . "\t" .
					$rowa['brnd5'] . "\t" .
					$rowa['brnd6'] . "\t" .
					$rowa['brnd7'] . "\t" .
					$rowa['brnd8'] . "\t" .
					$rowa['brnd9'] . "\t" .
					$rowa['best0'] . "\t" .
					$rowa['best1'] . "\t" .
					$rowa['best2'] . "\t" .
					$rowa['best3'] . "\t" .
					$rowa['best4'] . "\t" .
					$rowa['best5'] . "\t" .
					$rowa['best6'] . "\t" .
					$rowa['best7'] . "\t" .
					$rowa['best8'] . "\t" .
					$rowa['best9'] . "\t" .
					$rowa['n/a'] . "\t" .
					$rowa['n/a'] . "\t" .
					$rowa['n/a'] . "\t" .
					$rowa['n/a'] . "\t" .
					$rowa['n/a'] . "\t" .
					$rowa['n/a'] . "\t" .
					$rowa['n/a'] . "\t" .
					$rowa['n/a'] . "\t" .
					$rowa['n/a'] . "\t" .
					$rowa['n/a'] . "\t" .
					$roww['c4time'] . "\t" .
					$roww['claymoretime'] . "\t" .
					$roww['atminetime'] . "\t" .
					$roww['tacticaltime'] . "\t" .
					$roww['grapplinghooktime'] . "\t" .
					$roww['ziplinetime'] . "\t" .
					$roww['c4kills'] . "\t" .
					$roww['claymorekills'] . "\t" .
					$roww['atminekills'] . "\t" .
					$roww['c4deaths'] . "\t" .
					$roww['claymoredeaths'] . "\t" .
					$roww['atminedeaths'] . "\t" .
					$roww['ziplinedeaths'] . "\t" .
					$roww['grapplinghookdeaths'] . "\t" .
					$roww['tacticaldeployed'] . "\t" .
					$roww['grapplinghookdeployed'] . "\t" .
					$roww['ziplinedeployed'] . "\t" .
					$roww['c4fired'] . "\t" .
					$roww['claymorefired'] . "\t" .
					$roww['atminefired'] . "\t" .
					$roww['c4hit'] . "\t" .
					$roww['claymorehit'] . "\t" .
					$roww['atminehit'] . "\t" .
					$row['country'] . "\t" .
					$row['teamkills'] . "\t" .
					$row['teamdamage'] . "\t" .
					$row['teamvehicledamage'] . "\t";
				
				// Custom Map Handling (Based on idea by: THE_WUQKED)
				if ((strpos($info, 'cmap-') !== false) && (isset($usermaps))) 
				{
					// Map Time Output
					foreach ($usermaps as $usermapid) {$out .= $time[$usermapid] . "\t";}
					// Map Wins Output
					foreach ($usermaps as $usermapid) {$out .= $win[$usermapid] . "\t";}
					// Map Lossess Output
					foreach ($usermaps as $usermapid) {$out .= $loss[$usermapid] . "\t";}
					// Map Best Score Output
					foreach ($usermaps as $usermapid) {$out .= $best[$usermapid] . "\t";}
					// Map Worst Score Output
					foreach ($usermaps as $usermapid) {$out .= $worst[$usermapid] . "\t";}
				}
			}
		}
	}
	// Time info
  	elseif ($info == 'ktm-,vtm-,wtm-,mtm-')
	{
		$kit = ($_GET['kit']) ? $_GET['kit'] : 0;
		$vehicle = ($_GET['vehicle']) ? $_GET['vehicle'] : 0;
		$weapon = ($_GET['weapon']) ? $_GET['weapon'] : 0;
		$map = ($_GET['map']) ? $_GET['map'] : 0;
		
		$head .= "H\tpid\tnick\tktm-$kit\tvtm-$vehicle\twtm-$weapon\tmtm-$map\n";
		$num += strlen(preg_replace('/[\t\n]/','',$head));

		
		// Player
		$query = "SELECT name FROM player WHERE id = {$pid}";
		$result = mysql_query($query) or die(mysql_error());
		$row = mysql_fetch_array($result);
		if (!$row) 
		{
			$num = 0;
			$head = "E\nH\tasof\terr\n";
			$out  = "D\t" . time() . "\tPlayer Not Found!\n";
			$num += strlen(preg_replace('/[\t\n]/','',$head));
			$num += strlen(preg_replace('/[\t\n]/','',$out));
			
			print $head;
			print $out;
			print "$\t$num\t$";
			die();
		}
		$name = $row['name'];  

		// Kits
		$query = "SELECT time{$kit} FROM kits WHERE id = {$pid}";
		$result = mysql_query($query) or die(mysql_error());
		if (mysql_num_rows($result))
		{
			$row = mysql_fetch_array($result);
			$kitt = $row["time$kit"];
		}
		else 
		{
			$kitt = 0;
		}

		// Vehicles
		$query = "SELECT time{$vehicle} FROM vehicles WHERE id = {$pid}";
		$result = mysql_query($query) or die(mysql_error());
		if (mysql_num_rows($result))
		{
			$row = mysql_fetch_array($result);
			$vehiclet = $row["time$vehicle"];
		}
		else 
		{
			$vehiclet = 0;
		}       
	
	    // Weapons 
		$query = "SELECT GREATEST(time0, time1, time2, time3, time4, time5, time6, time7, time8, knifetime, shockpadtime, (c4time+claymoretime+atminetime), handgrenadetime) FROM weapons WHERE id = {$pid}";
	    $result = mysql_query($query) or die(mysql_error());		
	    if (mysql_num_rows($result))
		{
			$row = mysql_fetch_array($result);
		    $weapont = $row[0]; 
		}
		else 
		{
			$weapont = 0;
		}       
   
		// Maps
		$query = "SELECT time FROM maps WHERE (id = {$pid}) AND (mapid = {$map})";
		$result = mysql_query($query) or die(mysql_error());
		if (mysql_num_rows($result))
		{
			$row = mysql_fetch_array($result);
			$mapt = $row['time'];
		}
		else 
		{
			$mapt = 0;
		}     

		$out = "D\t" .
			$pid . "\t" .
			$name . "\t" .
			$kitt . "\t" .
			$vehiclet . "\t" .
			$weapont . "\t" .
			$mapt . "\n";
		
	}
	// Map info (added support for mbs- & mws-)
	elseif ($info == 'mtm-,mwn-,mls-' || $info == 'mtm-,mwn-,mls-,mbs-,mws-' || $info == 'mtm-,mwn-,mls-,cmap-' || $info == 'mtm-,mwn-,mls-,mbs-,mws-,cmap-')
	{
		// Added EF and AF variables
		// Added Patch 1.4 info
		
		$head .= "H\tpid\tnick\t".
            "mtm-0\tmtm-1\tmtm-2\tmtm-3\tmtm-4\tmtm-5\tmtm-6\tmtm-100\tmtm-101\tmtm-102\tmtm-103\tmtm-104\tmtm-105\tmtm-601\t".
			"mtm-602\tmtm-300\tmtm-301\tmtm-302\tmtm-303\tmtm-304\tmtm-305\tmtm-306\tmtm-307\tmtm-10\tmtm-11\tmtm-110\tmtm-120\tmtm-200\tmtm-201\tmtm-202\tmtm-12\t".
            "mwn-0\tmwn-1\tmwn-2\tmwn-3\tmwn-4\tmwn-5\tmwn-6\tmwn-100\tmwn-101\tmwn-102\tmwn-103\tmwn-104\tmwn-105\tmwn-601\t".
			"mwn-602\tmwn-300\tmwn-301\tmwn-302\tmwn-303\tmwn-304\tmwn-305\tmwn-306\tmwn-307\tmwn-10\tmwn-11\tmwn-110\tmwn-120\tmwn-200\tmwn-201\tmwn-202\tmwn-12\t".
            "mls-0\tmls-1\tmls-2\tmls-3\tmls-4\tmls-5\tmls-6\tmls-100\tmls-101\tmls-102\tmls-103\tmls-104\tmls-105\tmls-601\t".
			"mls-602\tmls-300\tmls-301\tmls-302\tmls-303\tmls-304\tmls-305\tmls-306\tmls-307\tmls-10\tmls-11\tmls-110\tmls-120\tmls-200\tmls-201\tmls-202\tmls-12";
			
		# For mbs-,mws- 
		if (strpos($info, 'mbs-,mws-') !== false)
		{
			$head .= "\t".
                "mbs-0\tmbs-1\tmbs-2\tmbs-3\tmbs-4\tmbs-5\tmbs-6\tmbs-100\tmbs-101\tmbs-102\tmbs-103\tmbs-104\tmbs-105\tmbs-601\t".
                "mbs-602\tmbs-300\tmbs-301\tmbs-302\tmbs-303\tmbs-304\tmbs-305\tmbs-306\tmbs-307\tmbs-10\tmbs-11\tmbs-110\tmbs-120\tmbs-200\tmbs-201\tmbs-202\tmbs-12\t".
                "mws-0\tmws-1\tmws-2\tmws-3\tmws-4\tmws-5\tmws-6\tmws-100\tmws-101\tmws-102\tmws-103\tmws-104\tmws-105\tmws-601\t".
                "mws-602\tmws-300\tmws-301\tmws-302\tmws-303\tmws-304\tmws-305\tmws-306\tmws-307\tmws-10\tmws-11\tmws-110\tmws-120\tmws-200\tmws-201\tmws-202\tmws-12";
		}
		
		// Custom Map Handling (Based on idea by: THE_WUQKED)
		if (strpos($info, 'cmap-') !== false)
		{
			$query = "SELECT * FROM mapinfo WHERE id >= " . $cfg->get('game_custom_mapid');
			$resultm = mysql_query($query) or die(mysql_error());
			if (!mysql_num_rows($resultm)) 
			{
				// No Custom Maps found, ignoring section
			} 
			else 
			{
				$usermaps = array();
				while ($rowum = mysql_fetch_array($resultm)) 
				{
					$usermaps[] = $rowum['id'];
				}
				asort($usermaps);
				
				// Map Time Header
				foreach ($usermaps as $usermapid) {$head .= "\tmtm-" . $usermapid;}
				// Map Wins Header
				foreach ($usermaps as $usermapid) {$head .= "\tmwn-" . $usermapid;}
				// Map Lossess Header
				foreach ($usermaps as $usermapid) {$head .= "\tmls-" . $usermapid;}
				
				if (strpos($info, 'mbs-,mws-') !== false)
				{
					// Map Best Score Header
					foreach ($usermaps as $usermapid) {$head .= "\tmbs-" . $usermapid;}
					// Map Worst Score Header
					foreach ($usermaps as $usermapid) {$head .= "\tmws-" . $usermapid;}
				}
			}
		}

		$num += strlen(preg_replace('/[\t\n]/','',$head));

		// Player
		$query = "SELECT name FROM player WHERE id = {$pid}";
		$result = mysql_query($query) or die(mysql_error());
		$row = mysql_fetch_array($result);
		if (!$row) 
		{
			$num = 0;
			$head = "E\nH\tasof\terr\n";
			$out  = "D\t" . time() . "\tPlayer Not Found!\n";
			$num += strlen(preg_replace('/[\t\n]/','',$head));
			$num += strlen(preg_replace('/[\t\n]/','',$out));
			
			print $head;
			print $out;
			print "$\t$num\t$";
			die();
		}
		$name = trim($row['name']);

		// Maps (Added extra for mbs-,mws-) 
		if (isset($usermaps)) 
		{
			$time = $win = $loss = $best = $worst = array_fill(0, end($usermaps)+1, '0');
		} 
		else 
		{
			$time = $win = $loss = $best = $worst = array_fill(0, 308, '0');
			$time[601] = $win[601] = $loss[601] = $best[601] = $worst[601] = 0;
			$time[602] = $win[602] = $loss[602] = $best[602] = $worst[602] = 0;
		}

		$query = "SELECT * FROM maps WHERE id = {$pid}";
		$result = mysql_query($query) or die(mysql_error());
		while ($row = mysql_fetch_array($result))
		{
			$time[$row['mapid']] = $row['time'];
			$win[$row['mapid']] = $row['win'];
			$loss[$row['mapid']] = $row['loss'];
			$best[$row['mapid']] = $row['best'];
			$worst[$row['mapid']] = $row['worst'];
		}

		$out = "D\t" .
			$pid . "\t" .
			$name . "\t" .
			$time[0] . "\t" .
			$time[1] . "\t" .
			$time[2] . "\t" .
			$time[3] . "\t" .
			$time[4] . "\t" .
			$time[5] . "\t" .
			$time[6] . "\t" .
			$time[100] . "\t" .
			$time[101] . "\t" .
			$time[102] . "\t" .
			$time[103] . "\t" .
			$time[104] . "\t" .
			$time[105] . "\t" .
			$time[601] . "\t" .
			$time[602] . "\t" .
			$time[300] . "\t" .
			$time[301] . "\t" .
			$time[302] . "\t" .
			$time[303] . "\t" .
			$time[304] . "\t" .
			$time[305] . "\t" .
			$time[306] . "\t" .
			$time[307] . "\t" .
			$time[10] . "\t" .
			$time[11] . "\t" .
			$time[110] . "\t" .
			$time[120] . "\t" .
			$time[200] . "\t" .
			$time[201] . "\t" .
			$time[202] . "\t" .
			$time[12] . "\t" .
			$win[0] . "\t" .
			$win[1] . "\t" .
			$win[2] . "\t" .
			$win[3] . "\t" .
			$win[4] . "\t" .
			$win[5] . "\t" .
			$win[6] . "\t" .
			$win[100] . "\t" .
			$win[101] . "\t" .
			$win[102] . "\t" .
			$win[103] . "\t" .
			$win[104] . "\t" .
			$win[105] . "\t" .
			$win[601] . "\t" .
			$win[602] . "\t" .
			$win[300] . "\t" .
			$win[301] . "\t" .
			$win[302] . "\t" .
			$win[303] . "\t" .
			$win[304] . "\t" .
			$win[305] . "\t" .
			$win[306] . "\t" .
			$win[307] . "\t" .
			$win[10] . "\t" .
			$win[11] . "\t" .
			$win[110] . "\t" .
			$win[120] . "\t" .
			$win[200] . "\t" .
			$win[201] . "\t" .
			$win[202] . "\t" .
			$win[12] . "\t" .
			$loss[0] . "\t" .
			$loss[1] . "\t" .
			$loss[2] . "\t" .
			$loss[3] . "\t" .
			$loss[4] . "\t" .
			$loss[5] . "\t" .
			$loss[6] . "\t" .
			$loss[100] . "\t" .
			$loss[101] . "\t" .
			$loss[102] . "\t" .
			$loss[103] . "\t" .
			$loss[104] . "\t" .
			$loss[105] . "\t" .
			$loss[601] . "\t" .
			$loss[602] . "\t" .
			$loss[300] . "\t" .
			$loss[301] . "\t" .
			$loss[302] . "\t" .
			$loss[303] . "\t" .
			$loss[304] . "\t" .
			$loss[305] . "\t" .
			$loss[306] . "\t" .
			$loss[307] . "\t" .
			$loss[10] . "\t" .
			$loss[11] . "\t" .
			$loss[110] . "\t" .
			$loss[120] . "\t" .
			$loss[200] . "\t" .
			$loss[201] . "\t" .
			$loss[202] . "\t" .
			$loss[12] . "\t";
		
		# For mbs-,mws- 
		if (strpos($info, 'mbs-,mws-') !== false)
		{
			$out .=	$best[0] . "\t" .
				$best[1] . "\t" .
				$best[2] . "\t" .
				$best[3] . "\t" .
				$best[4] . "\t" .
				$best[5] . "\t" .
				$best[6] . "\t" .
				$best[100] . "\t" .
				$best[101] . "\t" .
				$best[102] . "\t" .
				$best[103] . "\t" .
				$best[104] . "\t" .
				$best[105] . "\t" .
				$best[601] . "\t" .
				$best[602] . "\t" .
				$best[300] . "\t" .
				$best[301] . "\t" .
				$best[302] . "\t" .
				$best[303] . "\t" .
				$best[304] . "\t" .
				$best[305] . "\t" .
				$best[306] . "\t" .
				$best[307] . "\t" .
				$best[10] . "\t" .
				$best[11] . "\t" .
				$best[110] . "\t" .
				$best[120] . "\t" .
				$best[200] . "\t" .
				$best[201] . "\t" .
				$best[202] . "\t" .
				$best[12] . "\t" .
				$worst[0] . "\t" .
				$worst[1] . "\t" .
				$worst[2] . "\t" .
				$worst[3] . "\t" .
				$worst[4] . "\t" .
				$worst[5] . "\t" .
				$worst[6] . "\t" .
				$worst[100] . "\t" .
				$worst[101] . "\t" .
				$worst[102] . "\t" .
				$worst[103] . "\t" .
				$worst[104] . "\t" .
				$worst[105] . "\t" .
				$worst[601] . "\t" .
				$worst[602] . "\t" .
				$worst[300] . "\t" .
				$worst[301] . "\t" .
				$worst[302] . "\t" .
				$worst[303] . "\t" .
				$worst[304] . "\t" .
				$worst[305] . "\t" .
				$worst[306] . "\t" .
				$worst[307] . "\t" .
				$worst[10] . "\t" .
				$worst[11] . "\t" .
				$worst[110] . "\t" .
				$worst[120] . "\t" .
				$worst[200] . "\t" .
				$worst[201] . "\t" .
				$worst[202] . "\t" .
				$worst[12] . "\t";
		}
		
		// Custom Map Handling (Based on idea by: THE_WUQKED)
		if ((strpos($info, 'cmap-') !== false) && (isset($usermaps))) 
		{
			// Map Time Output
			foreach ($usermaps as $usermapid) {$out .= $time[$usermapid] . "\t";}
			// Map Wins Output
			foreach ($usermaps as $usermapid) {$out .= $win[$usermapid] . "\t";}
			// Map Lossess Output
			foreach ($usermaps as $usermapid) {$out .= $loss[$usermapid] . "\t";}
			if (strpos($info, 'mbs-,mws-') !== false)
			{
				// Map Best Score Output
				foreach ($usermaps as $usermapid) {$out .= $best[$usermapid] . "\t";}
				// Map Worst Score Output
				foreach ($usermaps as $usermapid) {$out .= $worst[$usermapid] . "\t";}
			}
		}
	}
	elseif ($info == 'rank')
	{
		$query = "SELECT rank FROM player WHERE id = {$pid}";
		$result = mysql_query($query) or die(mysql_error());

		if (!mysql_num_rows($result)) 
		{
			print 'Player not found!';
		}
		else
		{
			$row = mysql_fetch_array($result);
			$rank = $row['rank'];
			
			$query = "SELECT id, name, chng, decr FROM player WHERE id = {$pid}";
			$result = mysql_query($query) or die(mysql_error());
			$row = mysql_fetch_array($result);
			
			$head .= "H\tpid\tnick\trank\tchng\tdecr\n";
			$num += strlen(preg_replace('/[\t\n]/','',$head));
			
			$out .= "D\t$row[id]\t$row[name]\t$rank\t$row[chng]\t$row[decr]\n";
			
			$query = "UPDATE player SET chng = 0, decr = 0 WHERE id = {$pid}";
			mysql_query($query) or die(mysql_error());
		}
	}
	elseif (checkGameServerRequest($info))
	{
		// NOTE: xpack and bf2 have same return
		// Added support for MODs (POE2)
		$head .= "H\t" .
			"pid\tnick\trank\t" . 
			"ktm-0\tktm-1\tktm-2\tktm-3\tktm-4\tktm-5\tktm-6\tdfcp\t" .
			"atm-0\tatm-1\tatm-2\tatm-3\tatm-4\tatm-5\tatm-6\tatm-7\tatm-8\tatm-9\tatm-10\tatm-11\trpar\t" .
			"vtm-0\tvtm-1\tvtm-2\tvtm-3\tvtm-4\tvtm-5\tvtm-6\tscor\twdsk\t" .
			"wkl-0\twkl-1\twkl-2\twkl-3\twkl-4\twkl-5\twkl-6\twkl-7\twkl-8\twkl-9\twkl-10\twkl-11\twkl-12\twkl-13\theal\t" .
			"abr-0\tabr-1\tabr-2\tabr-3\tabr-4\tabr-5\tabr-6\tabr-7\tabr-8\tabr-9\tabr-10\tabr-11\tdsab\tcdsc\ttsql\ttsqm\tloss\t" .
			"awn-0\tawn-1\tawn-2\tawn-3\tawn-4\tawn-5\tawn-6\tawn-7\tawn-8\tawn-9\tawn-10\tawn-11\twins\t" .
			"vkl-0\tvkl-1\tvkl-2\tvkl-3\tvkl-4\tvkl-5\tvkl-6\ttwsc\tbksk\ttime\tkill\trsup\ttcdr\t";
			
		if (strpos($info, 'mtm-') !== false) 
		{
			// Add Map Time Data for New Medals Data
			$head .= "mtm-0\tmtm-1\tmtm-2\tmtm-3\tmtm-4\tmtm-5\tmtm-6\tmtm-100\tmtm-101\tmtm-102\tmtm-103\tmtm-104\tmtm-105\t" .
				"mtm-601\tmtm-300\tmtm-301\tmtm-302\tmtm-303\tmtm-304\tmtm-305\tmtm-306\tmtm-307\tmtm-10\tmtm-11\tmtm-110\tmtm-200\tmtm-201\tmtm-202\t";
		}
		
		$head .= "vac-0\tvac-1\tvac-2\tvac-3\tvac-4\tvac-5\tvac-6\n";
			
		$num += strlen(preg_replace('/[\t\n]/','',$head));
		
		// Player
		$query = "SELECT * FROM player WHERE id = {$pid}";
		$result = mysql_query($query) or die(mysql_error());
		$row = mysql_fetch_array($result);
		if (!$row) 
		{
			$num = 0;
			$head = "E\nH\tasof\terr\n";
			$out  = "D\t" . time() . "\tPlayer Not Found!\n";
			$num += strlen(preg_replace('/[\t\n]/','',$head));
			$num += strlen(preg_replace('/[\t\n]/','',$out));
			
			print $head;
			print $out;
			print "$\t$num\t$";
			die();
		}

		// Weapons
		$query = "SELECT * FROM weapons WHERE id = {$pid}";
		$result = mysql_query($query) or die(mysql_error());
		if (mysql_num_rows($result)) 
		{
			$roww = mysql_fetch_array($result);
		} 
		else 
		{
			$roww = array();
			for ($i = 0; $i <= 8; $i++) {
				$roww["kills$i"] = '0';
			}
			$roww['knifekills'] = $roww['shockpadkills'] = $roww['c4kills'] = $roww['claymorekills'] = '0';
			$roww['atminekills'] = $roww['handgrenadekills'] = '0';
		}

		// Kits
		$query = "SELECT * FROM kits WHERE id = {$pid}";
		$result = mysql_query($query) or die(mysql_error());
		if (mysql_num_rows($result)) 
		{
			$rowk = mysql_fetch_array($result);
		} 
		else 
		{
			$rowk = array();
			for ($i = 0; $i <= 6; $i++) 
			{
				$rowk["time$i"] = '0';
				$rowk["kills$i"] = '0';
			}
		}
		
		// Vehicles
		$query = "SELECT * FROM vehicles WHERE id = {$pid}";
		$result = mysql_query($query) or die(mysql_error());
		if (mysql_num_rows($result)) 
		{
			$rowv = mysql_fetch_array($result);
		} 
		else 
		{
			$rowv = array();
			for ($i = 0; $i <= 6; $i++) 
			{
				$rowv["time$i"] = '0';
				$rowv["kills$i"] = '0';
			}
		}  
		
		// Army
		$query = "SELECT * FROM army WHERE id = {$pid}";
		$result = mysql_query($query) or die(mysql_error());
		if (mysql_num_rows($result)) 
		{
			$rowa = mysql_fetch_array($result);
		} 
		else 
		{
			$rowa = array();
			for ($i = 0; $i <= 13; $i++) 
			{
				$rowa["time$i"] = '0';
				$rowa["best$i"] = '0';
				$rowa["win$i"] = '0';
			}
		}

		if (strpos($info, 'mtm-') !== false) 
		{
			// Add Map Time Data for New Medals Data 
			$maptime = array_fill(0, 308, '0');
			$maptime[601] = 0;
			$maptime[602] = 0;
			$query = "SELECT * FROM maps WHERE id = {$pid}";
			$result = mysql_query($query) or die(mysql_error());
			while ($rowm = mysql_fetch_array($result))
			{
				$maptime[$rowm['mapid']] = $rowm['time'];
			}
		}
		
		// Output
		$out = "D\t" .
			$row['id'] . "\t" .
			$row['name'] . "\t" .
			$row['rank'] . "\t" .
			$rowk['time0'] . "\t" .
			$rowk['time1'] . "\t" .
			$rowk['time2'] . "\t" .
			$rowk['time3'] . "\t" .
			$rowk['time4'] . "\t" .
			$rowk['time5'] . "\t" .
			$rowk['time6'] . "\t" .
			$row['defends'] . "\t" .
			$rowa['time0'] . "\t" .
			$rowa['time1'] . "\t" .
			$rowa['time2'] . "\t" .
			$rowa['time3'] . "\t" .
			$rowa['time4'] . "\t" .
			$rowa['time5'] . "\t" .
			$rowa['time6'] . "\t" .
			$rowa['time7'] . "\t" .
			$rowa['time8'] . "\t" .
			$rowa['time9'] . "\t" .
			$rowa['time10'] . "\t" .
			$rowa['time11'] . "\t" .
			$row['repairs'] . "\t" .
			$rowv['time0'] . "\t" .
			$rowv['time1'] . "\t" .
			$rowv['time2'] . "\t" .
			$rowv['time3'] . "\t" .
			$rowv['time4'] . "\t" .
			$rowv['time5'] . "\t" .
			$rowv['time6'] . "\t" .
			$row['score'] . "\t" .
			$row['deathstreak'] . "\t" .
			$roww['kills0'] . "\t" .
			$roww['kills1'] . "\t" .
			$roww['kills2'] . "\t" .
			$roww['kills3'] . "\t" .
			$roww['kills4'] . "\t" .
			$roww['kills5'] . "\t" .
			$roww['kills6'] . "\t" .
			$roww['kills7'] . "\t" .
			$roww['kills8'] . "\t" .
			$roww['knifekills'] . "\t" .
			$roww['shockpadkills'] . "\t" .
			($roww['c4kills'] + $roww['claymorekills'] + $roww['atminekills']) . "\t" .
			$roww['handgrenadekills'] . "\t" .
			#wkl-13
			"0\t" .
			$row['heals'] . "\t" .
			$rowa['best0'] . "\t" .
			$rowa['best1'] . "\t" .
			$rowa['best2'] . "\t" .
			$rowa['best3'] . "\t" .
			$rowa['best4'] . "\t" .
			$rowa['best5'] . "\t" .
			$rowa['best6'] . "\t" .
			$rowa['best7'] . "\t" .
			$rowa['best8'] . "\t" .
			$rowa['best9'] . "\t" .
			$rowa['best10'] . "\t" .
			$rowa['best11'] . "\t" .
			$row['driverspecials'] . "\t" .
			$row['cmdscore'] . "\t" .
			$row['sqltime'] . "\t" .
			$row['sqmtime'] . "\t" .
			$row['losses'] . "\t" .
			$rowa['win0'] . "\t" .
			$rowa['win1'] . "\t" .
			$rowa['win2'] . "\t" .
			$rowa['win3'] . "\t" .
			$rowa['win4'] . "\t" .
			$rowa['win5'] . "\t" .
			$rowa['win6'] . "\t" .
			$rowa['win7'] . "\t" .
			$rowa['win8'] . "\t" .
			$rowa['win9'] . "\t" .
			$rowa['win10'] . "\t" .
			$rowa['win11'] . "\t" .
			$row['wins'] . "\t" .
			$rowv['kills0'] . "\t" .
			$rowv['kills1'] . "\t" .
			$rowv['kills2'] . "\t" .
			$rowv['kills3'] . "\t" .
			$rowv['kills4'] . "\t" .
			$rowv['kills5'] . "\t" .
			$rowv['kills6'] . "\t" .
			$row['teamscore'] . "\t" .
			$row['killstreak'] . "\t" .
			$row['time'] . "\t" .
			$row['kills'] . "\t" .
			$row['ammos'] . "\t" .
			$row['cmdtime'] . "\t";
		
		if (strpos($info, 'mtm-') !== false) 
		{
			// Add Map Time Data for New Medals Data 
			$out .= $maptime[0] . "\t" .
				$maptime[1] . "\t" .
				$maptime[2] . "\t" .
				$maptime[3] . "\t" .
				$maptime[4] . "\t" .
				$maptime[5] . "\t" .
				$maptime[6] . "\t" .
				$maptime[100] . "\t" .
				$maptime[101] . "\t" .
				$maptime[102] . "\t" .
				$maptime[103] . "\t" .
				$maptime[104] . "\t" .
				$maptime[105] . "\t" .
				$maptime[601] . "\t" .
				$maptime[602] . "\t" .
				$maptime[300] . "\t" .
				$maptime[301] . "\t" .
				$maptime[302] . "\t" .
				$maptime[303] . "\t" .
				$maptime[304] . "\t" .
				$maptime[305] . "\t" .
				$maptime[306] . "\t" .
				$maptime[307] . "\t" .
				$maptime[10] . "\t" .
				$maptime[11] . "\t" .
				$maptime[110] . "\t" .
				$maptime[120] . "\t" .
				$maptime[200] . "\t" .
				$maptime[201] . "\t" .
				$maptime[202] . "\t";
		}
		
		#vac-
		$out .=	"0\t0\t0\t0\t0\t0\t0\n";
	} 
	else
	{
		$num = 0;
		$head = "E\nH\tasof\terr\n";
		$out  = "D\t" . time() . "\tParameter Error!\n";
		$num += strlen(preg_replace('/[\t\n]/','',$head));
		$num += strlen(preg_replace('/[\t\n]/','',$out));
	
		print rtrim($head) . "\n";
		print rtrim($out) . "\n";
		print "$\t$num\t$";
		die();
	}
	
	if ($transpose) 
	{
		// Display in Alternate Format
		$num = 0;
		$transout = "O\n" .
			"H\tD\n" .
			"asof\t" . time() . "\n";
			
		$arrdata = split("\n",rtrim($head)."\n".rtrim($out));
		$keys = split("\t", $arrdata[3]);
		$vals = split("\t", $arrdata[4]);
		
		$i = 0;
		foreach ($keys as $key=>$keyval) 
		{
			$transout .= $keyval . "\t" . $vals[$i] . "\n";
			$i++;
		}
		
		print rtrim($transout) . "\n";
		$num += strlen(preg_replace('/[\t\n]/','',$transout));
	} 
	else 
	{
		print rtrim($head) . "\n";
		print rtrim($out) . "\n";
		$num += strlen(preg_replace('/[\t\n]/','',$out));
	}
	print "$\t$num\t$";
	
	// Close database connection
	@mysql_close($connection);
}

function checkGameServerRequest($info) 
{
	// Checks Game Server Query String
	$complete = true;
	//$arr = array('rank','ktm-','dfcp','rpar','vtm-','scor','atm-','wdsk','wkl-','abr-','heal','dsab','cdsc','tsql','tsqm','awn-','wins','vkl-','twsc','bksk','time','kill','rsup','tcdr','vac-');
	$arr = array('rank','ktm-','dfcp','rpar','vtm-','bksk','scor','wdsk','wkl-','heal','dsab','cdsc','tsql','tsqm','wins','vkl-','twsc','time','kill','rsup','tcdr','vac-');

	for($a = 0; $a < count($arr); $a++)
	{
		if (strpos( $info, $arr[$a]) === false )
		{
			$complete = false;
		}
	}
	return $complete;
}

function denominator($x, $y)
{
	while($y != 0)
	{
		$remainder = $x % $y;
		$x = $y;
		$y = $remainder;
	}
	return abs($x);
}
?>