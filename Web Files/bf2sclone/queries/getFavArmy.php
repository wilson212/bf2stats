<?php
	$count = getArmyCount()-1;
	$string = '';
	for ($i=0; $i <= $count; $i++)
	{
		$string .= 'time'. $i;
		if ($i != $count) {
            $string .=', ';
        }
	}
	$query = "SELECT $string FROM army WHERE id = $PID LIMIT 1;";
?>
