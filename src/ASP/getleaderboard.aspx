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

// Make sure we have a type, and its valid
$type = (isset($_GET['type'])) ? $_GET['type'] : false;
if (!$type) 
{
    $out = "E\nH\tasof\terr\n" .
        "D\t" . time() . "\tInvalid Syntax!\n";
	$num = strlen(preg_replace('/[\t\n]/', '', $out));
	echo "$\t$num\t$";
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

	// Prepare our output header
	$head = "O\n" .
		"H\tsize\tasof\n";
	
	$num = strlen(preg_replace('/[\t\n]/','',$head));
	print $head;

	$id  = (isset($_GET['id'])) ? $_GET['id'] : false;
	$pid = (isset($_GET['pid'])) ? intval($_GET['pid']) : false;

	// Optional parameters
	$after  = (isset($_GET['after'])) ? intval($_GET['after']) : 0;
	$before = (isset($_GET['before'])) ? intval($_GET['before']) : 0;
	$pos    = (isset($_GET['pos'])) ? intval($_GET['pos']) : 1;
	$min    = ($pos - 1) - $before;
	$max    = $after + 1;
	$out    = "";
	
	if ($type == 'score')
	{
		if ($id == 'overall')
		{
			$result = $connection->query("SELECT COUNT(`id`) FROM `player` WHERE `hidden` = 0 AND `score` > 0");
			$count = $result->fetchColumn();
			$out .= "D\t{$count}\t" . time() . "\n";
			$out .= "H\tn\tpid\tnick\tscore\ttotaltime\tplayerrank\tcountrycode\n";
			
			if(!$pid)
			{
				$query = "SELECT `id`, `name`, `rank`, `country`, `time`, `score` FROM `player` WHERE `hidden` = 0 AND `score` > 0 ORDER BY `score` DESC, `name` DESC LIMIT {$min}, {$max}";
				$result = $connection->query($query);
                if($result instanceof PDOStatement)
                {
                    while($row = $result->fetch())
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
			}
			else
			{
				$query = "SELECT `id`, `name`, `rank`, `country`, `time`, `score` FROM `player` WHERE `hidden` = 0 AND `score` > 0 ORDER BY `score` DESC, `name` DESC";
				$result = $connection->query($query);
                if($result instanceof PDOStatement)
                {
                    while($row = $result->fetch())
                    {
                        $plpid = $row['id'];
                        if($plpid == $pid) 
                        {
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
		}
		elseif ($id == 'commander')
		{
			$result = $connection->query("SELECT COUNT(`id`) FROM `player` WHERE `hidden` = 0 AND `cmdscore` > 0");
			$count = $result->fetchColumn();
			$out .= "D\t{$count}\t" . time() . "\n";
			$out .= "H\tn\tpid\tnick\tcoscore\tcotime\tplayerrank\tcountrycode\n";
			
			if(!$pid)
			{
				$query = "SELECT `id`, `name`, `rank`, `country`, `cmdtime`, `cmdscore` FROM `player` WHERE `hidden` = 0 AND `cmdscore` > 0 ORDER BY `cmdscore` DESC, `name` DESC LIMIT {$min}, {$max}";
				$result = $connection->query($query);
                if($result instanceof PDOStatement)
                {
                    while($row = $result->fetch())
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
			}
			else
			{
				$query = "SELECT `id`, `name`, `rank`, `country`, `cmdtime`, `cmdscore` FROM `player` WHERE `hidden` = 0 AND `cmdscore` > 0 ORDER BY `cmdscore` DESC, `name` DESC";
				$result = $connection->query($query);
                if($result instanceof PDOStatement)
                {
                    while($row = $result->fetch())
                    {
                        $plpid = $row['id'];
                        if($plpid == $pid) 
                        {
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
		}
		elseif ($id ==  'team')
		{
			$result = $connection->query("SELECT COUNT(`id`) FROM `player` WHERE `hidden` = 0 AND `teamscore` > 0");
			$count = $result->fetchColumn();
			$out .= "D\t{$count}\t" . time() . "\n";
			$out .= "H\tn\tpid\tnick\tteamscore\ttotaltime\tplayerrank\tcountrycode\n";
			
			if(!$pid)
			{
				$query = "SELECT `id`, `name`, `rank`, `country`, `time`, `teamscore` FROM `player` WHERE `hidden` = 0 AND `teamscore` > 0 ORDER BY `teamscore` DESC, `name` DESC LIMIT {$min}, {$max}";
				$result = $connection->query($query);
                if($result instanceof PDOStatement)
                {
                    while($row = $result->fetch())
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
			}
			else
			{
				$query = "SELECT `id`, `name`, `rank`, `country`, `time`, `teamscore` FROM `player` WHERE `hidden` = 0 AND `teamscore` > 0 ORDER BY `teamscore` DESC, `name` DESC";
				$result = $connection->query($query);
                if($result instanceof PDOStatement)
                {
                    while($row = $result->fetch())
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
		}
		elseif ($id == 'combat')
		{
			$result = $connection->query("SELECT COUNT(`id`) FROM `player` WHERE `hidden` = 0 AND `skillscore` > 0");
			$count = $result->fetchColumn();
			$out .= "D\t{$count}\t" . time() . "\n";
			$out .= "H\tn\tpid\tnick\tscore\ttotalkills\ttotaltime\tplayerrank\tcountrycode\n";
			
			if(!$pid)
			{
				$query = "SELECT `id`, `name`, `rank`, `country`, `time`, `kills`, `skillscore` FROM `player` WHERE `hidden` = 0 AND `skillscore` > 0 ORDER BY `skillscore` DESC, `name` DESC LIMIT {$min}, {$max}";
				$result = $connection->query($query);
                if($result instanceof PDOStatement)
                {
                    while($row = $result->fetch())
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
			}
			else
			{
				$query = "SELECT `id`, `name`, `rank`, `country`, `time`, `kills`, `skillscore` FROM `player` WHERE `hidden` = 0 AND `skillscore` > 0 ORDER BY `skillscore` DESC, `name` DESC";
				$result = $connection->query($query);
                if($result instanceof PDOStatement)
                {
                    while($row = $result->fetch())
                    {
                        $plpid = $row['id'];
                        if($plpid == $pid) 
                        {
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
	}
	# Need weekly score calculations!
	elseif ($type == 'risingstar')
	{
		$query = "SELECT COUNT(DISTINCT(h.`id`)) FROM `player_history` h INNER JOIN player p ON p.id = h.id WHERE p.hidden = 0 AND h.`score` > 0 AND h.`timestamp` >= (UNIX_TIMESTAMP() - (60*60*24*7))";
		$result = $connection->query($query);
        $count = $result->fetchColumn();
		$out .= "D\t{$count}\t" . time() . "\n";
		$out .= "H\tn\tpid\tnick\tweeklyscore\ttotaltime\tdate\tplayerrank\tcountrycode\n";
		
		if(!$pid)
		{
			$query = "SELECT p.id, p.name, p.rank, p.country, p.time, sum(h.score) as weeklyscore, p.joined
				FROM player AS p JOIN player_history AS h ON p.id = h.id
				WHERE p.hidden = 0 AND h.score > 0 AND h.timestamp >= (UNIX_TIMESTAMP() - (60*60*24*7))
				GROUP BY p.id
				ORDER BY weeklyscore DESC, name DESC
				LIMIT {$min}, {$max}";
			$result = $connection->query($query);
            if($result instanceof PDOStatement)
            {
                while($row = $result->fetch())
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
		}
		else
		{
			$query = "SELECT p.id, p.name, p.rank, p.country, p.time, sum(h.score) as weeklyscore, p.joined
				FROM player AS p JOIN player_history AS h ON p.id = h.id
				WHERE p.hidden = 0 AND h.score > 0 AND h.timestamp >= (UNIX_TIMESTAMP() - (60*60*24*7))
				GROUP BY p.id
				ORDER BY weeklyscore DESC, name DESC";
			$result = $connection->query($query);
            if($result instanceof PDOStatement)
            {
                while($row = $result->fetch())
                {
                    $plpid = $row['id'];
                    if($plpid == $pid) 
                    {
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
	}
	elseif ($type == 'kit')
	{
		$result = $connection->query("SELECT COUNT(k.id) FROM kits k INNER JOIN player p ON p.id = k.id WHERE p.hidden = 0 AND `kills{$id}` > 0");
        $count = $result->fetchColumn();
		$out .= "D\t{$count}\t" . time() . "\n";
		$out .= "H\tn\tpid\tnick\tkillswith\tdeathsby\ttimeplayed\tplayerrank\tcountrycode\n";
		
		if(!$pid)
		{
			$query = "SELECT player.id AS plid, name, rank, country, kills{$id} AS kills, deaths{$id} AS deaths, time{$id} AS time FROM player NATURAL JOIN kits WHERE hidden = 0 AND kills{$id} > 0 ORDER BY kills{$id} DESC, name DESC LIMIT {$min}, {$max}";
			$result = $connection->query($query);
            if($result instanceof PDOStatement)
            {
                while($row = $result->fetch())
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
		}
		else
		{
			$query = "SELECT player.id AS plid, name, rank, country, kills{$id} AS kills, deaths{$id} AS deaths, time{$id} AS time FROM player NATURAL JOIN kits WHERE hidden = 0 AND kills{$id} > 0 ORDER BY kills{$id} DESC, name DESC";
			$result = $connection->query($query);
            if($result instanceof PDOStatement)
            {
                while($row = $result->fetch())
                {
                    $plpid = $row['plid'];
                    if($plpid == $pid) 
                    {
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
	}
	elseif ($type == 'vehicle')
	{
		$result = $connection->query("SELECT COUNT(v.id) FROM vehicles v INNER JOIN player p ON p.id = v.id WHERE p.hidden = 0 AND `kills{$id}` > 0");
        $count = $result->fetchColumn();
		$out .= "D\t{$count}\t" . time() . "\n";
		$out .= "H\tn\tpid\tnick\tkillswith\tdetahsby\ttimeused\tplayerrank\tcountrycode\n";
		
		if(!$pid)
		{
			$query = "SELECT player.id AS plid, name, rank, country, kills{$id} AS kills, deaths{$id} AS deaths, time{$id} AS time FROM player NATURAL JOIN vehicles WHERE hidden = 0 AND kills{$id} > 0 ORDER BY kills{$id} DESC, name DESC LIMIT {$min}, {$max}";
			$result = $connection->query($query);
            if($result instanceof PDOStatement)
            {
                while($row = $result->fetch())
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
		}
		else
		{
			$query = "SELECT player.id AS plid, name, rank, country, kills{$id} AS kills, deaths{$id} AS deaths, time{$id} AS time FROM player NATURAL JOIN vehicles WHERE hidden = 0 AND kills{$id} > 0 ORDER BY kills{$id} DESC, name DESC";
			$result = $connection->query($query);
            if($result instanceof PDOStatement)
            {
                while($row = $result->fetch())
                {
                    $plpid = $row['plid'];
                    if($plpid == $pid) 
                    {
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
	}
	elseif ($type == 'weapon')
	{
		$result = $connection->query("SELECT COUNT(w.id) FROM weapons w INNER JOIN player p ON p.id = w.id WHERE p.hidden = 0 AND `kills{$id}` > 0");
        $count = $result->fetchColumn();
		$out .= "D\t{$count}\t" . time() . "\n";
		# NOTE: EA typo (deathsby=detahsby)
		$out .= "H\tn\tpid\tnick\tkillswith\tdetahsby\ttimeused\taccuracy\tplayerrank\tcountrycode\n";
        
		if (!$pid)
		{
			$query = "SELECT player.id AS plid, name, rank, country, kills{$id} AS kills, deaths{$id} AS deaths, time{$id} AS time, hit{$id} AS hit, fired{$id} AS fired FROM player NATURAL JOIN weapons WHERE hidden = 0 AND kills{$id} > 0 ORDER BY kills{$id} DESC, name DESC LIMIT {$min}, {$max}";
			$result = $connection->query($query);
            if($result instanceof PDOStatement)
            {
                while($row = $result->fetch())
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
		}
		else
		{
			$query = "SELECT player.id AS plid, name, rank, country, kills{$id} AS kills, deaths{$id} AS deaths, time{$id} AS time, hit{$id} AS hit, fired{$id} AS fired FROM player NATURAL JOIN weapons WHERE hidden = 0 AND kills{$id} > 0 ORDER BY kills{$id} DESC, name DESC";
			$result = $connection->query($query);
            if($result instanceof PDOStatement)
            {
                while($row = $result->fetch())
                {
                    $plpid = $row['plid'];
                    if($plpid == $pid) 
                    {
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
	}
	else 
	{
		print 'Unknown type!';
	}

	$num += strlen(preg_replace('/[\t\n]/','',$out));
	print $out . "$\t" . $num . "\t$";
}
?>
