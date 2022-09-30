<?php
function getExpasionTimeQueryByName($PID, $ExpansionID)
{
	$id_lines  = file(getcwd()."/queries/maps-".$ExpansionID.".list");
	$maplist = '';
	$first = true;
	foreach ($id_lines as $key => $value)
	{
		if ($first)
		{
			$maplist .= '(mapid='.$value;
			$first = false;
		}
		else
		{
			$maplist .= ' OR mapid='.$value;
		}
	}
	return "SELECT sum(time) as time FROM maps WHERE id=$PID AND ".$maplist.');';	
}

?>