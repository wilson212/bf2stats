<?php
	$query = 'SELECT id,name,kills,rank,score,(score/(time/60)) as spm, (kills/deaths) as kdr, time, country FROM player WHERE';
	if ($LEADERBOARD)
	{
		$first = true;
		foreach (explode(',', $LEADERBOARD) as $key => $value)
		{
			if ($first)
			{
				$query .= " id='$value'";
				$first = false;
			}
			else
				$query .= "or id='$value'";
		}
		$query .= ' ORDER BY SCORE DESC LIMIT 50;';
	}
	else
		$query = "SELECT id,name,rank,kills,score,(score/(time/60)) as spm, (kills/deaths) as kdr, time, country FROM player WHERE score > 0 ORDER BY SCORE DESC LIMIT 10;";
?>