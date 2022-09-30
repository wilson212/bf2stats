<?php

function getSearchResults($SEARCHVALUE)
{
	include(ROOT . DS . 'queries'. DS .'getPIDList.php'); // imports the correct sql statement
	$result = mysqli_query($GLOBALS['link'], $query) or die('Query failed: ' . mysqli_error($GLOBALS['link']));
	$data = array();

	while ($row = mysqli_fetch_assoc($result))
	{
		$data[] = $row;
	}
	mysqli_free_result($result);
	return $data;
}
?>
