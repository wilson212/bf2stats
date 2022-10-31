<?php
	$WHERE = '';
	if (LEADERBOARD_HIDE_BOTS) {
		$WHERE .= ' AND isbot = 0';
	}
	if (LEADERBOARD_HIDE_HIDDEN_PLAYERS) {
		$WHERE .= ' AND hidden = 0';
	}

	$query = "SELECT id,name,rank,captureassists+captures+neutralizes+defends as flagwork,country FROM player WHERE 1=1 $WHERE ORDER BY flagwork DESC LIMIT 5;";
?>
