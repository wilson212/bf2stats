<?php

function getRankingCollection()
{
	$i = 0;
	$full[$i]['name'] = 'Score';
	$full[$i]['data'] = getTopScore();
	$i++;
	$full[$i]['name'] = 'Score Per Minute';
	$full[$i]['data'] = getTopSPM();
	$i++;
	$full[$i]['name'] = 'Win/Loss Ratio';
	$full[$i]['data'] = getTopWLR();
	$i++;
	$full[$i]['name'] = 'Kill/Death Ratio';
	$full[$i]['data'] = getTopKDR();
	$i++;
	$full[$i]['name'] = 'Best Round Score';
	$full[$i]['data'] = getTopRndScore();
	$i++;
	$full[$i]['name'] = 'Flag Captures';
	$full[$i]['data'] = getTopCaptures();
	$i++;
	$full[$i]['name'] = 'Flag work';
	$full[$i]['desc'] = '(Defend, Capture, etc...)';
	$full[$i]['data'] = getTopFlagwork();
	$i++;
	$full[$i]['name'] = 'Top Killer';
	$full[$i]['data'] = getTopKills();
	$i++;
	$full[$i]['name'] = 'Best Medic';
	$full[$i]['desc'] = '(revives, heals)';
	$full[$i]['data'] = getTopSani();
	$i++;
	$full[$i]['name'] = 'Best Teamworkers';
	$full[$i]['data'] = getTopTeamwork();
	$i++;
	$full[$i]['name'] = 'Command Score';
	$full[$i]['data'] = getTopCMDScore();
	$i++;
	$full[$i]['name'] = 'Relative Command Score';
	$full[$i]['data'] = getTopCMD();
	return $full;	
}

function getTopPlayers()
{
	include(ROOT . DS . 'queries'. DS .'getTopPlayers.php'); // imports the correct sql statement
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());	
	$data = array();
	
	while ($row = mysql_fetch_assoc($result)) 
	{
		$data[] = $row;
	}	 	
	mysql_free_result($result);
	return $data;
}

function getTopCaptures()
{
	include(ROOT . DS . 'queries'. DS .'getRankingTopCaptures.php'); // imports the correct sql statement
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());	
	$data = array();
	
	$idx = 0;
	while ($row = mysql_fetch_assoc($result)) 
	{
		$data[$idx]['id'] = $row['id'];
		$data[$idx]['name'] = $row['name'];
		$data[$idx]['rank'] = $row['rank'];
		$data[$idx]['value'] = $row['captures'];
		$data[$idx]['country'] = $row['country'];
		$idx++;
	}	 	
	mysql_free_result($result);
	return $data;
}

function getTopCMD()
{
	include( ROOT . DS . 'queries'. DS .'getRankingTopCMD.php' ); // imports the correct sql statement
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());	
	$data = array();
	
	$idx = 0;
	while ($row = mysql_fetch_assoc($result)) 
	{
		$data[$idx]['id'] = $row['id'];
		$data[$idx]['name'] = $row['name'];
		$data[$idx]['rank'] = $row['rank'];
		$data[$idx]['value'] = $row['cmd'];
		$data[$idx]['country'] = $row['country'];
		$idx++;
	}	 	
	mysql_free_result($result);
	return $data;
}

function getTopCMDScore()
{
	include( ROOT . DS . 'queries'. DS .'getRankingTopCmdScore.php' ); // imports the correct sql statement
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());	
	$data = array();
	
	$idx = 0;
	while ($row = mysql_fetch_assoc($result)) 
	{
		$data[$idx]['id'] = $row['id'];
		$data[$idx]['name'] = $row['name'];
		$data[$idx]['rank'] = $row['rank'];
		$data[$idx]['value'] = $row['cmdscore'];
		$data[$idx]['country'] = $row['country'];
		$idx++;
	}	 	
	mysql_free_result($result);
	return $data;
}

function getTopRndScore()
{
	include( ROOT . DS . 'queries'. DS .'getRankingTopRndScore.php' ); // imports the correct sql statement
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());	
	$data = array();
	
	$idx = 0;
	while ($row = mysql_fetch_assoc($result)) 
	{
		$data[$idx]['id'] = $row['id'];
		$data[$idx]['name'] = $row['name'];
		$data[$idx]['rank'] = $row['rank'];
		$data[$idx]['value'] = @number_format($row['rndscore']);
		$data[$idx]['country'] = $row['country'];
		$idx++;
	}	 	
	mysql_free_result($result);
	return $data;
}

function getTopFlagwork()
{
	include( ROOT . DS . 'queries'. DS .'getRankingTopFlagwork.php' ); // imports the correct sql statement
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());	
	$data = array();
	
	$idx = 0;
	while ($row = mysql_fetch_assoc($result)) 
	{
		$data[$idx]['id'] = $row['id'];
		$data[$idx]['name'] = $row['name'];
		$data[$idx]['rank'] = $row['rank'];
		$data[$idx]['value'] = $row['flagwork'];
		$data[$idx]['country'] = $row['country'];
		$idx++;
	}	 	
	mysql_free_result($result);
	return $data;
}

function getTopKDR()
{
	include( ROOT . DS . 'queries'. DS .'getRankingTopKDR.php' ); // imports the correct sql statement
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());	
	$data = array();
	
	$idx = 0;
	while ($row = mysql_fetch_assoc($result)) {
		$data[$idx]['id'] = $row['id'];
		$data[$idx]['name'] = $row['name'];
		$data[$idx]['rank'] = $row['rank'];
		$data[$idx]['value'] = $row['kdr'];
		$data[$idx]['country'] = $row['country'];
		$idx++;
	}	 	
	mysql_free_result($result);
	return $data;
}

function getTopSani()
{
	include(ROOT . DS . 'queries'. DS .'getRankingTopSani.php'); // imports the correct sql statement
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());	
	$data = array();
	
	$idx = 0;
	while ($row = mysql_fetch_assoc($result)) 
	{
		$data[$idx]['id'] = $row['id'];
		$data[$idx]['name'] = $row['name'];
		$data[$idx]['rank'] = $row['rank'];
		$data[$idx]['value'] = $row['sani'];
		$data[$idx]['country'] = $row['country'];
		$idx++;
	}	 	
	mysql_free_result($result);
	return $data;
}

function getTopWLR()
{
	include(ROOT . DS . 'queries'. DS .'getRankingTopWLR.php'); // imports the correct sql statement
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());	
	$data = array();
	
	$idx = 0;
	while ($row = mysql_fetch_assoc($result)) 
	{
		$data[$idx]['id'] = $row['id'];
		$data[$idx]['name'] = $row['name'];
		$data[$idx]['rank'] = $row['rank'];
		$data[$idx]['value'] = $row['wlr'];
		$data[$idx]['country'] = $row['country'];
		$idx++;
	}	 	
	mysql_free_result($result);
	return $data;
}

function getTopSPM()
{
	include(ROOT . DS . 'queries'. DS .'getRankingTopSPM.php'); // imports the correct sql statement
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());	
	$data = array();
	
	$idx = 0;
	while ($row = mysql_fetch_assoc($result)) 
	{
		$data[$idx]['id'] = $row['id'];
		$data[$idx]['name'] = $row['name'];
		$data[$idx]['rank'] = $row['rank'];
		$data[$idx]['value'] = $row['spm'];
		$data[$idx]['country'] = $row['country'];
		$idx++;
	}	 	
	mysql_free_result($result);
	return $data;
}

function getTopScore()
{
	include(ROOT . DS . 'queries'. DS .'getRankingTopScore.php'); // imports the correct sql statement
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());	
	$data = array();
	
	$idx = 0;
	while ($row = mysql_fetch_assoc($result)) 
	{
		$data[$idx]['id'] = $row['id'];
		$data[$idx]['name'] = $row['name'];
		$data[$idx]['rank'] = $row['rank'];
		$data[$idx]['value'] = @number_format($row['score']);
		$data[$idx]['country'] = $row['country'];
		$idx++;
	}	 	
	mysql_free_result($result);
	return $data;
}

function getTopKills()
{
	include(ROOT . DS . 'queries'. DS .'getRankingTopKills.php'); // imports the correct sql statement
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());	
	$data = array();
	
	$idx = 0;
	while ($row = mysql_fetch_assoc($result)) 
	{
		$data[$idx]['id'] = $row['id'];
		$data[$idx]['name'] = $row['name'];
		$data[$idx]['rank'] = $row['rank'];
		$data[$idx]['value'] = @number_format($row['kills']);
		$data[$idx]['country'] = $row['country'];
		$idx++;
	}	 	
	mysql_free_result($result);
	return $data;
}

function getTopTeamwork()
{
	include(ROOT . DS . 'queries'. DS .'getRankingTopTeamwork.php'); // imports the correct sql statement
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());	
	$data = array();
	
	$idx = 0;
	while ($row = mysql_fetch_assoc($result)) 
	{
		$data[$idx]['id'] = $row['id'];
		$data[$idx]['name'] = $row['name'];
		$data[$idx]['rank'] = $row['rank'];
		$data[$idx]['value'] = @number_format($row['teamwork']);
		$data[$idx]['country'] = $row['country'];
		$idx++;
	}	 	
	mysql_free_result($result);
	return $data;
}
?>