<?php
	$WHERE = '';
	if (LEADERBOARD_HIDE_BOTS) {
		$WHERE .= ' AND isbot = 0';
	}
	if (LEADERBOARD_HIDE_HIDDEN_PLAYERS) {
		$WHERE .= ' AND hidden = 0';
	}

	$query = "SELECT rank FROM player WHERE id = $PID $WHERE;";
?>
