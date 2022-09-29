<?php
	$query = "SELECT id, rank, country, name, score, time, (score/(time/60)) as spm, (kills/deaths) as kdr FROM player ORDER BY score DESC LIMIT ". LEADERBOARD_COUNT .";";
?>