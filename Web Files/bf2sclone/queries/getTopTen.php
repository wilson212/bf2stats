<?php
	$query = "SELECT id, rank_id, country, name, score FROM player ORDER BY score DESC LIMIT ". LEADERBOARD_COUNT .";";
?>