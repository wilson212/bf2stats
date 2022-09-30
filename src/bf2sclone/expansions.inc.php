<?php
include_once(ROOT . DS . 'queries'. DS .'getExpansionTimeByName.php');
include_once(ROOT . DS . 'queries'. DS .'getTheaterTimeQueryByName.php');

function getExpasionTimeByName($PID, $ExpansionID)
{
	$query = getExpasionTimeQueryByName($PID, $ExpansionID);
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());	
	$data = array();
	
	while ($row = mysql_fetch_assoc($result)) 
	{
		$data[] = $row;
	}	 	
	mysql_free_result($result);
	return $data[0]['time'];
}

function getTheaterData($PID)
{
	$theaterdata = array();
	$id_lines  = file(ROOT . DS ."queries". DS ."armies.list");
	foreach ($id_lines as $key => $value)
	{
		$theaterdata[$key] 				= getTheaterByName($PID, trim(strtolower($id_lines[$key])));
		$theaterdata[$key]['time'] 		= isset($theaterdata[$key]['time']) ? $theaterdata[$key]['time'] : 0;
		$theaterdata[$key]['wins'] 		= isset($theaterdata[$key]['wins']) ? $theaterdata[$key]['wins'] : 0;
		$theaterdata[$key]['losses'] 	= isset($theaterdata[$key]['losses']) ? $theaterdata[$key]['losses'] : 0;
		$theaterdata[$key]['br'] 		= isset($theaterdata[$key]['br']) ? $theaterdata[$key]['br'] : 0;
		$theaterdata[$key]['name'] 		= trim($id_lines[$key]);
	}
	return $theaterdata;
}


function getTheaterByName($PID, $TheaterID)
{
	$query = getTheaterTimeQueryByName($PID, $TheaterID);
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());	
	$data = array();
	
	while ($row = mysql_fetch_assoc($result)) 
	{
		$data[] = $row;
	}	 	
	mysql_free_result($result);
	return $data[0];
}
?>