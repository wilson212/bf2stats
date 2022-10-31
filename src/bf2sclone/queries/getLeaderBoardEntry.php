<?php
	$WHERE = '';
	if (LEADERBOARD_HIDE_BOTS) {
		$WHERE .= ' AND isbot = 0';
	}
	if (LEADERBOARD_HIDE_HIDDEN_PLAYERS) {
		$WHERE .= ' AND hidden = 0';
	}
	
	$query = 'SELECT id,name,kills,rank,score,(score/(time/60)) as spm, (kills/deaths) as kdr, time, country FROM player WHERE (';
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
		$query .= ") $WHERE ORDER BY SCORE DESC LIMIT 50;";
	}
	else
		$query = "SELECT id,name,rank,kills,score,(score/(time/60)) as spm, (kills/deaths) as kdr, time, country FROM player WHERE score > 0 $WHERE ORDER BY SCORE DESC LIMIT 10;";
?>
