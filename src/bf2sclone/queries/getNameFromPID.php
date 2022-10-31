<?php
	$WHERE = '';
	if (RANKING_HIDE_BOTS) {
		$WHERE .= ' AND isbot = 0';
	}
	if (RANKING_HIDE_HIDDEN_PLAYERS) {
		$WHERE .= ' AND hidden = 0';
	}
	
	$query = "SELECT name FROM player WHERE id = $PID $WHERE;";
?>
