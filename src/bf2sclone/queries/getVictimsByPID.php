<?php
	$WHERE = '';
	if (RANKING_HIDE_BOTS) {
		$WHERE .= ' AND player.isbot = 0';
	}
	if (RANKING_HIDE_HIDDEN_PLAYERS) {
		$WHERE .= ' AND player.hidden = 0';
	}

	$query = "SELECT victim, count FROM kills INNER JOIN player ON kills.victim = player.id WHERE attacker = $PID $WHERE ORDER BY count DESC LIMIT 11;";
?>
