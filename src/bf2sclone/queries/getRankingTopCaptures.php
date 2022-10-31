<?php
	$WHERE = '';
	if (RANKING_HIDE_BOTS) {
		$WHERE .= ' AND isbot = 0';
	}
	if (RANKING_HIDE_HIDDEN_PLAYERS) {
		$WHERE .= ' AND hidden = 0';
	}

	#WHERE captures>0 
	$query = "SELECT id,name,rank, captures,country FROM player WHERE 1=1 $WHERE ORDER BY captures DESC LIMIT 5;";
?>
