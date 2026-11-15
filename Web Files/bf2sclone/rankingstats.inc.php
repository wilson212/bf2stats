<?php

function getRankingCollection()
{
	$i = 0;
	$full[$i]['name'] = 'Captures';
	$full[$i]['data'] = getTopCaptures();
	$i++;
	$full[$i]['name'] = 'Relative Command Score';
	$full[$i]['data'] = getTopCMD();	
	$i++;
	$full[$i]['name'] = 'Command Score';
	$full[$i]['data'] = getTopCMDScore();
	$i++;
	$full[$i]['name'] = 'Best Round Score';
	$full[$i]['data'] = getTopRndScore();
	$i++;
	$full[$i]['name'] = 'Flag work (Defend, Capture, etc...)';
	$full[$i]['data'] = getTopFlagwork();
	$i++;
	$full[$i]['name'] = 'Kill/Death Ratio<br>(minimum 1 death)';
	$full[$i]['data'] = getTopKDR();
	$i++;
	$full[$i]['name'] = 'Top Killer';
	$full[$i]['data'] = getTopKills();
	$i++;
	$full[$i]['name'] = 'Best Medic (revives, heals)';
	$full[$i]['data'] = getTopSani();
	$i++;
	$full[$i]['name'] = 'Score';
	$full[$i]['data'] = getTopScore();
	$i++;
	$full[$i]['name'] = 'Score Per Minute';
	$full[$i]['data'] = getTopSPM();
	$i++;
	$full[$i]['name'] = 'Best Teamworkers';
	$full[$i]['data'] = getTopTeamwork();
	$i++;
	$full[$i]['name'] = 'Win/Loss Ratio<br>(minimum 1 loss)';
	$full[$i]['data'] = getTopWLR();
	return $full;	
}

/**
 * @return list<mixed>
 */
function getTopTen(): array
{
	global $link;
	include(ROOT . DS . 'queries'. DS .'getTopTen.php'); // imports the correct sql statement
	$result = mysqli_query($link, $query) or die('Query failed: ' . mysqli_error($ink));	
	$data = [];
	
	while ($row = mysqli_fetch_assoc($result)) 
	{
		$data[] = $row;
	}	 	
	mysqli_free_result($result);
	return $data;
}

/**
 * @return array{id: mixed, name: mixed, rank: mixed, value: mixed, country: mixed}[]
 */
function getTopCaptures(): array
{
	global $link;
	include(ROOT . DS . 'queries'. DS .'getRankingTopCaptures.php'); // imports the correct sql statement
	$result = mysqli_query($link, $query) or die('Query failed: ' . mysqli_error($link));	
	$data = [];
	
	$idx = 0;
	while ($row = mysqli_fetch_assoc($result)) 
	{
		$data[$idx]['id'] = $row['id'];
		$data[$idx]['name'] = $row['name'];
		$data[$idx]['rank'] = $row['rank'];
		$data[$idx]['value'] = $row['captures'];
		$data[$idx]['country'] = $row['country'];
		$idx++;
	}	 	
	mysqli_free_result($result);
	return $data;
}

/**
 * @return array{id: mixed, name: mixed, rank: mixed, value: mixed, country: mixed}[]
 */
function getTopCMD(): array
{
	global $link;
	include( ROOT . DS . 'queries'. DS .'getRankingTopCMD.php' ); // imports the correct sql statement
	$result = mysqli_query($link, $query) or die('Query failed: ' . mysqli_error($link));	
	$data = [];
	
	$idx = 0;
	while ($row = mysqli_fetch_assoc($result)) 
	{
		$data[$idx]['id'] = $row['id'];
		$data[$idx]['name'] = $row['name'];
		$data[$idx]['rank'] = $row['rank'];
		$data[$idx]['value'] = $row['cmd'];
		$data[$idx]['country'] = $row['country'];
		$idx++;
	}	 	
	mysqli_free_result($result);
	return $data;
}

/**
 * @return array{id: mixed, name: mixed, rank: mixed, value: mixed, country: mixed}[]
 */
function getTopCMDScore(): array
{
	global $link;
	include( ROOT . DS . 'queries'. DS .'getRankingTopCmdScore.php' ); // imports the correct sql statement
	$result = mysqli_query($link, $query) or die('Query failed: ' . mysqli_error($link));	
	$data = [];
	
	$idx = 0;
	while ($row = mysqli_fetch_assoc($result)) 
	{
		$data[$idx]['id'] = $row['id'];
		$data[$idx]['name'] = $row['name'];
		$data[$idx]['rank'] = $row['rank'];
		$data[$idx]['value'] = $row['cmdscore'];
		$data[$idx]['country'] = $row['country'];
		$idx++;
	}	 	
	mysqli_free_result($result);
	return $data;
}

/**
 * @return array{id: mixed, name: mixed, rank: mixed, value: mixed, country: mixed}[]
 */
function getTopRndScore(): array
{
	global $link;
	include( ROOT . DS . 'queries'. DS .'getRankingTopRndScore.php' ); // imports the correct sql statement
	$result = mysqli_query($link, $query) or die('Query failed: ' . mysqli_error($link));	
	$data = [];
	
	$idx = 0;
	while ($row = mysqli_fetch_assoc($result)) 
	{
		$data[$idx]['id'] = $row['id'];
		$data[$idx]['name'] = $row['name'];
		$data[$idx]['rank'] = $row['rank'];
		$data[$idx]['value'] = $row['bestscore'];
		$data[$idx]['country'] = $row['country'];
		$idx++;
	}	 	
	mysqli_free_result($result);
	return $data;
}

/**
 * @return array{id: mixed, name: mixed, rank: mixed, value: mixed, country: mixed}[]
 */
function getTopFlagwork(): array
{
	global $link;
	include( ROOT . DS . 'queries'. DS .'getRankingTopFlagwork.php' ); // imports the correct sql statement
	$result = mysqli_query($link, $query) or die('Query failed: ' . mysqli_error($link));	
	$data = [];
	
	$idx = 0;
	while ($row = mysqli_fetch_assoc($result)) 
	{
		$data[$idx]['id'] = $row['id'];
		$data[$idx]['name'] = $row['name'];
		$data[$idx]['rank'] = $row['rank'];
		$data[$idx]['value'] = $row['flagwork'];
		$data[$idx]['country'] = $row['country'];
		$idx++;
	}	 	
	mysqli_free_result($result);
	return $data;
}

/**
 * @return array{id: mixed, name: mixed, rank: mixed, value: mixed, country: mixed}[]
 */
function getTopKDR(): array
{
	global $link;
	include( ROOT . DS . 'queries'. DS .'getRankingTopKDR.php' ); // imports the correct sql statement
	$result = mysqli_query($link, $query) or die('Query failed: ' . mysqli_error($link));	
	$data = [];
	
	$idx = 0;
	while ($row = mysqli_fetch_assoc($result)) {
		$data[$idx]['id'] = $row['id'];
		$data[$idx]['name'] = $row['name'];
		$data[$idx]['rank'] = $row['rank'];
		$data[$idx]['value'] = $row['kdr'];
		$data[$idx]['country'] = $row['country'];
		$idx++;
	}	 	
	mysqli_free_result($result);
	return $data;
}

/**
 * @return array{id: mixed, name: mixed, rank: mixed, value: mixed, country: mixed}[]
 */
function getTopSani(): array
{
	global $link;
	include(ROOT . DS . 'queries'. DS .'getRankingTopSani.php'); // imports the correct sql statement
	$result = mysqli_query($link, $query) or die('Query failed: ' . mysqli_error($link));	
	$data = [];
	
	$idx = 0;
	while ($row = mysqli_fetch_assoc($result)) 
	{
		$data[$idx]['id'] = $row['id'];
		$data[$idx]['name'] = $row['name'];
		$data[$idx]['rank'] = $row['rank'];
		$data[$idx]['value'] = $row['sani'];
		$data[$idx]['country'] = $row['country'];
		$idx++;
	}	 	
	mysqli_free_result($result);
	return $data;
}

/**
 * @return array{id: mixed, name: mixed, rank: mixed, value: mixed, country: mixed}[]
 */
function getTopWLR(): array
{
	global $link;
	include(ROOT . DS . 'queries'. DS .'getRankingTopWLR.php'); // imports the correct sql statement
	$result = mysqli_query($link, $query) or die('Query failed: ' . mysqli_error($link));	
	$data = [];
	
	$idx = 0;
	while ($row = mysqli_fetch_assoc($result)) 
	{
		$data[$idx]['id'] = $row['id'];
		$data[$idx]['name'] = $row['name'];
		$data[$idx]['rank'] = $row['rank'];
		$data[$idx]['value'] = $row['wlr'];
		$data[$idx]['country'] = $row['country'];
		$idx++;
	}	 	
	mysqli_free_result($result);
	return $data;
}

/**
 * @return array{id: mixed, name: mixed, rank: mixed, value: mixed, country: mixed}[]
 */
function getTopSPM(): array
{
	global $link;
	include(ROOT . DS . 'queries'. DS .'getRankingTopSPM.php'); // imports the correct sql statement
	$result = mysqli_query($link, $query) or die('Query failed: ' . mysqli_error($link));	
	$data = [];
	
	$idx = 0;
	while ($row = mysqli_fetch_assoc($result)) 
	{
		$data[$idx]['id'] = $row['id'];
		$data[$idx]['name'] = $row['name'];
		$data[$idx]['rank'] = $row['rank'];
		$data[$idx]['value'] = $row['spm'];
		$data[$idx]['country'] = $row['country'];
		$idx++;
	}	 	
	mysqli_free_result($result);
	return $data;
}

/**
 * @return array{id: mixed, name: mixed, rank: mixed, value: mixed, country: mixed}[]
 */
function getTopScore(): array
{
	global $link;
	include(ROOT . DS . 'queries'. DS .'getRankingTopScore.php'); // imports the correct sql statement
	$result = mysqli_query($link, $query) or die('Query failed: ' . mysqli_error($link));	
	$data = [];
	
	$idx = 0;
	while ($row = mysqli_fetch_assoc($result)) 
	{
		$data[$idx]['id'] = $row['id'];
		$data[$idx]['name'] = $row['name'];
		$data[$idx]['rank'] = $row['rank'];
		$data[$idx]['value'] = $row['score'];
		$data[$idx]['country'] = $row['country'];
		$idx++;
	}	 	
	mysqli_free_result($result);
	return $data;
}

/**
 * @return array{id: mixed, name: mixed, rank: mixed, value: mixed, country: mixed}[]
 */
function getTopKills(): array
{
	global $link;
	include(ROOT . DS . 'queries'. DS .'getRankingTopKills.php'); // imports the correct sql statement
	$result = mysqli_query($link, $query) or die('Query failed: ' . mysqli_error($link));	
	$data = [];
	
	$idx = 0;
	while ($row = mysqli_fetch_assoc($result)) 
	{
		$data[$idx]['id'] = $row['id'];
		$data[$idx]['name'] = $row['name'];
		$data[$idx]['rank'] = $row['rank'];
		$data[$idx]['value'] = $row['kills'];
		$data[$idx]['country'] = $row['country'];
		$idx++;
	}	 	
	mysqli_free_result($result);
	return $data;
}

/**
 * @return array{id: mixed, name: mixed, rank: mixed, value: mixed, country: mixed}[]
 */
function getTopTeamwork(): array
{
	global $link;
	include(ROOT . DS . 'queries'. DS .'getRankingTopTeamwork.php'); // imports the correct sql statement
	$result = mysqli_query($link, $query) or die('Query failed: ' . mysqli_error($link));	
	$data = [];
	
	$idx = 0;
	while ($row = mysqli_fetch_assoc($result)) 
	{
		$data[$idx]['id'] = $row['id'];
		$data[$idx]['name'] = $row['name'];
		$data[$idx]['rank'] = $row['rank'];
		$data[$idx]['value'] = $row['teamwork'];
		$data[$idx]['country'] = $row['country'];
		$idx++;
	}	 	
	mysqli_free_result($result);
	return $data;
}
?>