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

/****************************************
* 11/18/05 v0.0.1 - ALPHA build        *
* 11/28/05 v0.0.2 - Updated for SF     *
* 11/30/05 v0.0.3 - Fixed query        *
* 12/01/05 v0.0.4 - Fixed return       *
* 12/26/05 v0.0.5 - Updated risingstar *
* 12/27/05 v0.0.6 - Updated query      *
* 01/03/06 v0.1 - BETA release         *
* 02/14/06 v0.1.1 - Fixed skillscore   *
****************************************/

/***************************************
* 06/12/10 - Fixed vehicle leaderboard *
* 06/12/10 - Fixed missed player       *
* 06/12/10 - Add trim                  *
* 06/12/10 - Tweaked jump to player    *
****************************************/
//Disable Zlib Compression
ini_set('zlib.output_compression', '0');

$type = (isset($_GET['type'])) ? $_GET['type'] : false;

if (!$type) 
{
    print 'Invalid syntax!';
}
else
{
	// Import configuration
	require('includes/utils.php');
	$cfg = new Config();
	
	$connection = @mysql_connect($cfg->get('db_host'), $cfg->get('db_user'), $cfg->get('db_pass'));
	@mysql_select_db($cfg->get('db_name'), $connection);

	$head = "O\n" .
		"H\tsize\tasof\n";
	
	$num = strlen(preg_replace('/[\t\n]/','',$head));
	print $head;

	$id = $_GET['id'];
	$pid = $_GET['pid'] ? $_GET['pid'] : false;

	// Optional parameters
	$after = $_GET['after'] ? $_GET['after'] : 0;
	$before = $_GET['before'] ? $_GET['before'] : 0;
	$pos = $_GET['pos'] ? $_GET['pos'] : 1;
	$min = ($pos - 1) - $before;
	$max = $after + 1;
	$out = "";
	
	if ($type == 'score')
	{
		if ($id == 'overall')
		{
			$query = "SELECT COUNT(id) FROM player WHERE score > 0";
			$result = mysql_query($query) or die(mysql_error());
			$row = mysql_fetch_array($result);
			$out .= "D\t$row[0]\t" . time() . "\n";
			$out .= "H\tn\tpid\tnick\tscore\ttotaltime\tplayerrank\tcountrycode\n";
			
			if (!$pid)
			{
				$query = "SELECT id, name, rank, country, time, score FROM player WHERE score > 0 ORDER BY score DESC, name DESC LIMIT {$min}, {$max}";
				$result = mysql_query($query) or die(mysql_error());
				while ($row = mysql_fetch_array($result))
				{
					$plpid = $row['id'];
					$name = trim($row['name']);
					$rank = $row['rank'];
					$country = strtoupper($row['country']);
					$time = $row['time'];
					$score = $row['score'];
					$out .= "D\t" . $pos++ . "\t$plpid\t$name\t$score\t$time\t$rank\t$country\n";
				}
			}
			else
			{
				$query = "SELECT id, name, rank, country, time, score FROM player WHERE score > 0 ORDER BY score DESC, name DESC";
				$result = mysql_query($query) or die(mysql_error());
				while ($row = mysql_fetch_array($result))
				{
					$plpid = $row['id'];
					if ($plpid == $pid) {
						$name = trim($row['name']);
						$rank = $row['rank'];
						$country = strtoupper($row['country']);
						$time = $row['time'];
						$score = $row['score'];
						$out .= "D\t" . $pos++ . "\t$plpid\t$name\t$score\t$time\t$rank\t$country\n";
						break;
					}
					$pos++;
				}
			}
		}
		elseif ($id == 'commander')
		{
			$query = "SELECT COUNT(id) FROM player WHERE cmdscore > 0";
			$result = mysql_query($query) or die(mysql_error());
			$row = mysql_fetch_array($result);
			
			$out .= "D\t$row[0]\t" . time() . "\n";
			$out .= "H\tn\tpid\tnick\tcoscore\tcotime\tplayerrank\tcountrycode\n";
			
			if (!$pid)
			{
				$query = "SELECT id, name, rank, country, cmdtime, cmdscore FROM player WHERE cmdscore > 0 ORDER BY cmdscore DESC, name DESC LIMIT {$min}, {$max}";
				$result = mysql_query($query) or die(mysql_error());
				while ($row = mysql_fetch_array($result))
				{
					$plpid = $row['id'];
					$name = trim($row['name']);
					$rank = $row['rank'];
					$country = strtoupper($row['country']);
					$cmdtime = $row['cmdtime'];
					$cmdscore = $row['cmdscore'];
					$out .= "D\t" . $pos++ . "\t$plpid\t$name\t$cmdscore\t$cmdtime\t$rank\t$country\n";
				}
			}
			else
			{
				$query = "SELECT id, name, rank, country, cmdtime, cmdscore FROM player WHERE cmdscore > 0 ORDER BY cmdscore DESC, name DESC";
				$result = mysql_query($query) or die(mysql_error());
				while ($row = mysql_fetch_array($result))
				{
					$plpid = $row['id'];
					if ($plpid == $pid) {
						$name = trim($row['name']);
						$rank = $row['rank'];
						$country = strtoupper($row['country']);
						$cmdtime = $row['cmdtime'];
						$cmdscore = $row['cmdscore'];
						$out .= "D\t" . $pos++ . "\t$pid\t$name\t$cmdscore\t$cmdtime\t$rank\t$country\n";
						break;
					}
					$pos++;
				}
			}
		}
		elseif ($id ==  'team')
		{
			$query = "SELECT COUNT(id) FROM player WHERE teamscore > 0";
			$result = mysql_query($query) or die(mysql_error());
			$row = mysql_fetch_array($result);
			
			$out .= "D\t$row[0]\t" . time() . "\n";
			$out .= "H\tn\tpid\tnick\tteamscore\ttotaltime\tplayerrank\tcountrycode\n";
			
			if (!$pid)
			{
				$query = "SELECT id, name, rank, country, time, teamscore FROM player WHERE teamscore > 0 ORDER BY teamscore DESC, name DESC LIMIT {$min}, {$max}";
				$result = mysql_query($query) or die(mysql_error());
				while ($row = mysql_fetch_array($result))
				{
					$plpid = $row['id'];
					$name = trim($row['name']);
					$rank = $row['rank'];
					$country = strtoupper($row['country']);
					$time = $row['time'];
					$teamscore = $row['teamscore'];
					$out .= "D\t" . $pos++ . "\t$plpid\t$name\t$teamscore\t$time\t$rank\t$country\n";
				}
			}
			else
			{
				$query = "SELECT id, name, rank, country, time, teamscore FROM player WHERE teamscore > 0 ORDER BY teamscore DESC, name DESC";
				$result = mysql_query($query) or die(mysql_error());
				while ($row = mysql_fetch_array($result))
				{
					$plpid = $row['id'];
					if ($plpid == $pid) {
						$name = trim($row['name']);
						$rank = $row['rank'];
						$country = strtoupper($row['country']);
						$time = $row['time'];
						$teamscore = $row['teamscore'];
						$out .= "D\t" . $pos++ . "\t$pid\t$name\t$teamscore\t$time\t$rank\t$country\n";
						break;
					}
					$pos++;
				}
			}
		}
		elseif ($id == 'combat')
		{
			$query = "SELECT COUNT(id) FROM player WHERE skillscore > 0";
			$result = mysql_query($query) or die(mysql_error());
			$row = mysql_fetch_array($result);
			
			$out .= "D\t$row[0]\t" . time() . "\n";
			$out .= "H\tn\tpid\tnick\tscore\ttotalkills\ttotaltime\tplayerrank\tcountrycode\n";
			
			if (!$pid)
			{
				$query = "SELECT id, name, rank, country, time, kills, skillscore FROM player WHERE skillscore > 0 ORDER BY skillscore DESC, name DESC LIMIT {$min}, {$max}";
				$result = mysql_query($query) or die(mysql_error());
				while ($row = mysql_fetch_array($result))
				{
					$plpid = $row['id'];
					$name = trim($row['name']);
					$rank = $row['rank'];
					$country = strtoupper($row['country']);
					$time = $row['time'];
					$kills = $row['kills'];
					$combatscore = $row['skillscore'];
					$out .= "D\t" . $pos++ . "\t$plpid\t$name\t$combatscore\t$kills\t$time\t$rank\t$country\n";
				}
			}
			else
			{
				$query = "SELECT id, name, rank, country, time, kills, skillscore FROM player WHERE skillscore > 0 ORDER BY skillscore DESC, name DESC";
				$result = mysql_query($query) or die(mysql_error());
				while ($row = mysql_fetch_array($result))
				{
					$plpid = $row['id'];
					if ($plpid == $pid) {
						$name = trim($row['name']);
						$rank = $row['rank'];
						$country = strtoupper($row['country']);
						$time = $row['time'];
						$kills = $row['kills'];
						$combatscore = $row['skillscore'];
						$out .= "D\t" . $pos++ . "\t$pid\t$name\t$combatscore\t$kills\t$time\t$rank\t$country\n";
						break;
					}
					$pos++;
				}
			}
		}
	}
	# Need weekly score calculations!
	elseif ($type == 'risingstar')
	{
		$query = "SELECT COUNT(DISTINCT(id)) FROM player_history WHERE score > 0 AND timestamp >= (UNIX_TIMESTAMP() - (60*60*24*7))";
		$result = mysql_query($query) or die(mysql_error());
		$row = mysql_fetch_array($result);
		
		$out .= "D\t$row[0]\t" . time() . "\n";
		$out .= "H\tn\tpid\tnick\tweeklyscore\ttotaltime\tdate\tplayerrank\tcountrycode\n";
		
		if (!$pid)
		{
			$query = "SELECT p.id, p.name, p.rank, p.country, p.time, sum(h.score) as weeklyscore, p.joined
				FROM player AS p JOIN player_history AS h ON p.id = h.id
				WHERE h.score > 0 AND h.timestamp >= (UNIX_TIMESTAMP() - (60*60*24*7))
				GROUP BY p.id
				ORDER BY weeklyscore DESC, name DESC
				LIMIT {$min}, {$max}";
			$result = mysql_query($query) or die(mysql_error());
			while ($row = mysql_fetch_array($result))
			{
				$plpid = $row['id'];
				$name = trim($row['name']);
				$rank = $row['rank'];
				$country = strtoupper($row['country']);
				$time = $row['time'];
				$score = $row['weeklyscore'];
				$joined = date('m/d/y h:i:00 A', $row['joined']);
				$out .= "D\t" . $pos++ . "\t$plpid\t$name\t$score\t$time\t$joined\t$rank\t$country\n";
			}
		}
		else
		{
			$query = "SELECT p.id, p.name, p.rank, p.country, p.time, sum(h.score) as weeklyscore, p.joined
				FROM player AS p JOIN player_history AS h ON p.id = h.id
				WHERE h.score > 0 AND h.timestamp >= (UNIX_TIMESTAMP() - (60*60*24*7))
				GROUP BY p.id
				ORDER BY weeklyscore DESC, name DESC";
			$result = mysql_query($query) or die(mysql_error());
			while ($row = mysql_fetch_array($result))
			{
				$plpid = $row['id'];
				if ($plpid == $pid) {
					$name = trim($row['name']);
					$rank = $row['rank'];
					$country = strtoupper($row['country']);
					$time = $row['time'];
					$score = $row['weeklyscore'];
					$joined = date('m/d/y h:i:00 A', $row['joined']);
					$out .= "D\t" . $pos++ . "\t$pid\t$name\t$score\t$time\t$joined\t$rank\t$country\n";
					break;
				}
				$pos++;
			}
		}
	}
	elseif ($type == 'kit')
	{
		$query = "SELECT COUNT(id) FROM kits WHERE kills{$id} > 0";
		$result = mysql_query($query) or die(mysql_error());
		$row = mysql_fetch_array($result);
		
		$out .= "D\t$row[0]\t" . time() . "\n";
		$out .= "H\tn\tpid\tnick\tkillswith\tdeathsby\ttimeplayed\tplayerrank\tcountrycode\n";
		
		if (!$pid)
		{
			$query = "SELECT player.id AS plid, name, rank, country, kills{$id} AS kills, deaths{$id} AS deaths, time{$id} AS time FROM player NATURAL JOIN kits WHERE kills{$id} > 0 ORDER BY kills{$id} DESC, name DESC LIMIT {$min}, {$max}";
			$result = mysql_query($query) or die(mysql_error());
			while ($row = mysql_fetch_array($result))
			{
				$plpid = $row['plid'];
				$name = trim($row['name']);
				$rank = $row['rank'];
				$country = strtoupper($row['country']);
				$time = $row['time'];
				$kills = $row['kills'];
				$deaths = $row['deaths'];
				$out .= "D\t" . $pos++ . "\t$plpid\t$name\t$kills\t$deaths\t$time\t$rank\t$country\n";
			}
		}
		else
		{
			$query = "SELECT player.id AS plid, name, rank, country, kills{$id} AS kills, deaths{$id} AS deaths, time{$id} AS time FROM player NATURAL JOIN kits WHERE kills{$id} > 0 ORDER BY kills{$id} DESC, name DESC";
			$result = mysql_query($query) or die(mysql_error());
			while ($row = mysql_fetch_array($result))
			{
				$plpid = $row['plid'];
				if ($plpid == $pid) {
					$name = trim($row['name']);
					$rank = $row['rank'];
					$country = strtoupper($row['country']);
					$time = $row['time'];
					$kills = $row['kills'];
					$deaths = $row['deaths'];
					$out .= "D\t" . $pos++ . "\t$pid\t$name\t$kills\t$deaths\t$time\t$rank\t$country\n";
					break;
				}
				$pos++;
			}
		}
	}
	elseif ($type == 'vehicle')
	{
		$query = "SELECT COUNT(id) FROM vehicles WHERE kills{$id} > 0";
		$result = mysql_query($query) or die(mysql_error());
		$row = mysql_fetch_array($result);
		
		$out .= "D\t$row[0]\t" . time() . "\n";
		$out .= "H\tn\tpid\tnick\tkillswith\tdetahsby\ttimeused\tplayerrank\tcountrycode\n";
		
		if (!$pid)
		{
			$query = "SELECT player.id AS plid, name, rank, country, kills{$id} AS kills, deaths{$id} AS deaths, time{$id} AS time FROM player NATURAL JOIN vehicles WHERE kills{$id} > 0 ORDER BY kills{$id} DESC, name DESC LIMIT {$min}, {$max}";
			$result = mysql_query($query) or die(mysql_error());
			while ($row = mysql_fetch_array($result))
			{
				$plpid = $row['plid'];
				$name = trim($row['name']);
				$rank = $row['rank'];
				$country = strtoupper($row['country']);
				$time = $row['time'];
				$kills = $row['kills'];
				$deaths = $row['deaths'];
				$out .= "D\t" . $pos++ . "\t$plpid\t$name\t$kills\t$deaths\t$time\t$rank\t$country\n";
			}
		}
		else
		{
			$query = "SELECT player.id AS plid, name, rank, country, kills{$id} AS kills, deaths{$id} AS deaths, time{$id} AS time FROM player NATURAL JOIN vehicles WHERE kills{$id} > 0 ORDER BY kills{$id} DESC, name DESC";
			$result = mysql_query($query) or die(mysql_error());
			while ($row = mysql_fetch_array($result))
			{
				$plpid = $row['plid'];
				if ($plpid == $pid) {
					$name = trim($row['name']);
					$rank = $row['rank'];
					$country = strtoupper($row['country']);
					$time = $row['time'];
					$kills = $row['kills'];
					$deaths = $row['deaths'];
					$out .= "D\t" . $pos++ . "\t$pid\t$name\t$kills\t$deaths\t$time\t$rank\t$country\n";
					break;
				}
				$pos++;
			}	
		}
	}
	elseif ($type == 'weapon')
	{
		$query = "SELECT COUNT(id) FROM weapons WHERE kills{$id} > 0";
		$result = mysql_query($query) or die(mysql_error());
		$row = mysql_fetch_array($result);
		
		$out .= "D\t$row[0]\t" . time() . "\n";
		# NOTE: EA typo (deathsby=detahsby)
		$out .= "H\tn\tpid\tnick\tkillswith\tdetahsby\ttimeused\taccuracy\tplayerrank\tcountrycode\n";
				
		if (!$pid)
		{
			$query = "SELECT player.id AS plid, name, rank, country, kills{$id} AS kills, deaths{$id} AS deaths, time{$id} AS time, hit{$id} AS hit, fired{$id} AS fired FROM player NATURAL JOIN weapons WHERE kills{$id} > 0 ORDER BY kills{$id} DESC, name DESC LIMIT {$min}, {$max}";
			$result = mysql_query($query) or die(mysql_error());
			while ($row = mysql_fetch_array($result))
			{
				$plpid = $row['plid'];
				$name = trim($row['name']);
				$rank = $row['rank'];
				$country = strtoupper($row['country']);
				$time = $row['time'];
				$kills = $row['kills'];
				$deaths = $row['deaths'];
				$acc = @number_format(($row['hit'] / $row['fired']) * 100);
				$out .= "D\t" . $pos++ . "\t$plpid\t$name\t$kills\t$deaths\t$time\t$acc\t$rank\t$country\n";
			}
		}
		else
		{
			$query = "SELECT player.id AS plid, name, rank, country, kills{$id} AS kills, deaths{$id} AS deaths, time{$id} AS time, hit{$id} AS hit, fired{$id} AS fired FROM player NATURAL JOIN weapons WHERE kills{$id} > 0 ORDER BY kills{$id} DESC, name DESC";
			$result = mysql_query($query) or die(mysql_error());
			while ($row = mysql_fetch_array($result))
			{
				$plpid = $row['plid'];
				if ($plpid == $pid) {
					$name = trim($row['name']);
					$rank = $row['rank'];
					$country = strtoupper($row['country']);
					$time = $row['time'];
					$kills = $row['kills'];
					$deaths = $row['deaths'];
					$acc = @number_format(($row['hit'] / $row['fired']) * 100);
					$out .= "D\t" . $pos++ . "\t$pid\t$name\t$kills\t$deaths\t$time\t$acc\t$rank\t$country\n";
					break;
				}
				$pos++;
			}
		}
	}
	/*
#NOTE: found these types in EA query
	elseif ($type == 'timeplayed')
	{
http://bf2web.gamespy.com/ASP/getleaderboard.aspx?type=timeplayed&id=
O
H	size	asof
D	1032605	1133420553
H	n	pid	nick	totaltime	playerrank	countrycode
D	1	45545295	Rips01	152890067	9	DE
D	2	44144747	Frogblast	152357459	7	US
D	3	46240523	Mr.Riddle01	152202830	7	AT
	}
	elseif ($type == 'map')
	{
id=mapid
http://bf2web.gamespy.com/ASP/getleaderboard.aspx?type=map&id=100
O
H	size	asof
D	609235	1133420930
H	n	pid	nick	bestroundscore	wins	losses	completed	incomplete	fullgames	time	playerrank	countrycode
D	1	49096356	GoThiC.KiLLa	1463	60	53	77	36	0	92910	7	US
D	2	43445898	[KSK]Skeletor	1441	7	8	8	8	0	16317	6	DE
D	3	45533306	[DPS]Strike	1345	56	45	43	60	0	87516	8	US
	}
	elseif ($type == 'army')
	{
id=armyid
http://bf2web.gamespy.com/ASP/getleaderboard.aspx?type=army&id=0
O
H	size	asof
D	718173	1133420624
H	n	pid	nick	bestscore	wins	losses	timeplayed	playerrank	countrycode
D	1	45719422	{TA}FireStorm	259	2559	948	3081930	10	US
D	2	44602655	RAID|EPoX	246	2206	1697	3243239	10	FR
D	3	45006981	Phantom0701	146	2035	431	2094545	10	CN
	}
*/
	else {print 'Unknown type!';}

	$num += strlen(preg_replace('/[\t\n]/','',$out));
	print $out . "$\t" . $num . "\t$";

	// Close database connection
	@mysql_close($connection);
	
}
?>