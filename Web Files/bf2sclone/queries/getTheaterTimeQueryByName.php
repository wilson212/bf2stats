<?php
function getTheaterTimeQueryByName($PID, string $ExpansionID): string
{
	$id_lines  = file(getcwd()."/queries/theater-".$ExpansionID.".list");
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
	return "SELECT sum(time) as time, sum(win) as wins, sum(loss) as losses, max(best) as br FROM maps WHERE id = $PID AND ".$maplist.');';	
}

?>