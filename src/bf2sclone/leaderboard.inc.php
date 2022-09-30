<?php
function update_real_scores()
{
	$query = 'SELECT id, score, cmdscore, kills FROM player';
	$result = mysqli_query($GLOBALS['link'], $query);

	while($player = mysqli_fetch_assoc($result))
	{
		$nskill = ($player['kills'] * 5);
		$nteam = $player['score'] - $player['cmdscore'] - $nskill;
		$q = 'UPDATE player SET skillscore='. $nskill .', teamscore='.$nteam .' WHERE id='. $player['id'];
		$result2 = mysqli_query($q) or die('Query failed: ' . mysqli_error($GLOBALS['link']));
		print( $q .': '. mysqli_affected_rows() .'<br />' );
		ob_flush(); flush();
	}
}

function get_pid_txt()
{
	$query = 'SELECT id, name FROM player';
	$result = mysqli_query($GLOBALS['link'], $query);

	$text = '';
	while($player = mysqli_fetch_assoc($result))
	{
		$text .= $player['name'] . PHP_EOL . $player['id'] . PHP_EOL;
	}

	file_put_contents(ROOT . DS . 'PID.txt', $text);
}

function getLeaderBoardEntries($LEADERBOARD)
{
	include( ROOT . DS . 'queries'. DS .'getLeaderBoardEntry.php' ); // imports the correct sql statement
	$result = mysqli_query($GLOBALS['link'], $query) or die('Query failed: ' . mysqli_error($GLOBALS['link']));
	$data = array();

	while ($row = mysqli_fetch_assoc($result))
	{
		$data[] = $row;
	}
	mysqli_free_result($result);
	return $data;
}

// update_real_scores(); die();
?>
