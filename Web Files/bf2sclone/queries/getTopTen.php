<?php
	$query = "SELECT id, rank, country, name, score FROM player ORDER BY score DESC LIMIT ". LEADERBOARD_COUNT .";";
?>