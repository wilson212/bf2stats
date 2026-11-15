<?php
function getExpasionTimeQueryByName($PID, string $ExpansionID): string
{
	$id_lines  = file(getcwd()."/queries/maps-".$ExpansionID.".list");
	$maplist = '';
	$first = true;
	foreach ($id_lines as $value)
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