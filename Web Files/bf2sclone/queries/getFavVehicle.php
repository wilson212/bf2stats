<?php
	$count = getVehicleCount();
	$count--;
	$string = '';
	for ($i=0; $i <= $count; $i++)
	{
		$string .= 'time'. $i;
		if ($i != $count) {
            $string .=', ';
        }
	}
	$query = "SELECT $string FROM vehicles WHERE id = $PID LIMIT 1;";
?>
