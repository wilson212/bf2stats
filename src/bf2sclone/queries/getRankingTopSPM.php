<?php
	$WHERE = '';
	if (LEADERBOARD_HIDE_BOTS) {
		$WHERE .= ' AND isbot = 0';
	}
	if (LEADERBOARD_HIDE_HIDDEN_PLAYERS) {
		$WHERE .= ' AND hidden = 0';
	}

	$query = "SELECT id,name,rank,score/(time/60) as spm,country FROM player WHERE 1=1 $WHERE ORDER BY spm DESC LIMIT 5;";
?>
