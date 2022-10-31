<?php
	$WHERE = '';
	if (RANKING_HIDE_BOTS) {
		$WHERE .= ' AND isbot = 0';
	}
	if (RANKING_HIDE_HIDDEN_PLAYERS) {
		$WHERE .= ' AND hidden = 0';
	}
	
	#NOTE: minimum 1 death
	$query = "SELECT id,name,rank, wins/losses as wlr,country FROM player WHERE 1=1 $WHERE ORDER BY wlr DESC LIMIT 5;";
?>
