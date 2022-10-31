<?php
	$WHERE = '';
	if (LEADERBOARD_HIDE_BOTS) {
		$WHERE .= ' AND isbot = 0';
	}
	if (LEADERBOARD_HIDE_HIDDEN_PLAYERS) {
		$WHERE .= ' AND hidden = 0';
	}

	#NOTE: minimum 1 death
	$query = "SELECT id,name,rank, kills/deaths as kdr,country FROM player WHERE 1=1 $WHERE ORDER BY kdr DESC LIMIT 5;";
?>
