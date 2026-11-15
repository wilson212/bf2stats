<?php

/**
 * @return list<mixed>
 */
function getLeaderBoardEntries($LEADERBOARD): array
{
	global $link;
	include( ROOT . DS . 'queries'. DS .'getLeaderBoardEntry.php' ); // imports the correct sql statement
	$result = mysqli_query($link, $query) or die('Query failed: ' . mysqli_error($link));	
	$data = [];
	
	while ($row = mysqli_fetch_assoc($result)) 
	{
		$data[] = $row;
	}	 	
	mysqli_free_result($result);
	return $data;
}
?>