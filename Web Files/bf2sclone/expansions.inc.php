<?php
include_once(ROOT . DS . 'queries'. DS .'getExpansionTimeByName.php');
include_once(ROOT . DS . 'queries'. DS .'getTheaterTimeQueryByName.php');

function getExpasionTimeByName($PID, $ExpansionID)
{
	global $link;
	$query = getExpasionTimeQueryByName($PID, $ExpansionID);
	$result = mysqli_query($link, $query) or die('Query failed: ' . mysqli_error($link));	
	$data = [];
	
	while ($row = mysqli_fetch_assoc($result)) 
	{
		$data[] = $row;
	}	 	
	mysqli_free_result($result);
	return $data[0]['time'];
}

/**
 * @return mixed[]
 */
function getTheaterData($PID): array
{
	$theaterdata = [];
	$id_lines  = file(ROOT . DS ."queries". DS ."armies.list");
	foreach ($id_lines as $key => $value)
	{
		$theaterdata[$key] 				= getTheaterByName($PID, trim(strtolower($id_lines[$key])));
		$theaterdata[$key]['time'] ??= 0;
		$theaterdata[$key]['wins'] ??= 0;
		$theaterdata[$key]['losses'] ??= 0;
		$theaterdata[$key]['br'] ??= 0;
		$theaterdata[$key]['name'] 		= trim($id_lines[$key]);
	}
	return $theaterdata;
}


function getTheaterByName($PID, $TheaterID)
{
	global $link;
	$query = getTheaterTimeQueryByName($PID, $TheaterID);
	$result = mysqli_query($link, $query) or die('Query failed: ' . mysqli_error($link));	
	$data = [];
	
	while ($row = mysqli_fetch_assoc($result)) 
	{
		$data[] = $row;
	}	 	
	mysqli_free_result($result);
	return $data[0];
}
?>