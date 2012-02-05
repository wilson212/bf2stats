<?php

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
?>