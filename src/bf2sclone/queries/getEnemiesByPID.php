<?php
	$WHERE = '';
	if (RANKING_HIDE_BOTS) {
		$WHERE .= ' AND player.isbot = 0';
	}
	if (RANKING_HIDE_HIDDEN_PLAYERS) {
		$WHERE .= ' AND player.hidden = 0';
	}

	$query = "SELECT attacker, count FROM kills INNER JOIN player ON kills.attacker = player.id WHERE victim = $PID $WHERE ORDER BY count DESC LIMIT 11;";
?>

