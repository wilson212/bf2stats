<?php
function update_real_scores()
{
	$query = 'SELECT id, score, cmdscore, kills FROM player';
	$result = mysql_query($query);	

	while($player = mysql_fetch_assoc($result))
	{
		$nskill = ($player['kills'] * 5);
		$nteam = $player['score'] - $player['cmdscore'] - $nskill;
		$q = 'UPDATE player SET skillscore='. $nskill .', teamscore='.$nteam .' WHERE id='. $player['id'];
		$result2 = mysql_query($q) or die('Query failed: ' . mysql_error());	
		print( $q .': '. mysql_affected_rows() .'<br />' );
		ob_flush(); flush();
	}	 	
}

function get_pid_txt()
{
	$query = 'SELECT id, name FROM player';
	$result = mysql_query($query);	

	$text = '';
	while($player = mysql_fetch_assoc($result))
	{
		$text .= $player['name'] . PHP_EOL . $player['id'] . PHP_EOL;
	}

	file_put_contents(ROOT . DS . 'PID.txt', $text);
}

function getLeaderBoardEntries($LEADERBOARD)
{
	include( ROOT . DS . 'queries'. DS .'getLeaderBoardEntry.php' ); // imports the correct sql statement
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());	
	$data = array();
	
	while ($row = mysql_fetch_assoc($result)) 
	{
		$data[] = $row;
	}	 	
	mysql_free_result($result);
	return $data;
}

// update_real_scores(); die();
?>