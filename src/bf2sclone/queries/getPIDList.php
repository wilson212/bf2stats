<?php
	$WHERE = '';
	if (RANKING_HIDE_BOTS) {
		$WHERE .= ' AND isbot = 0';
	}
	if (RANKING_HIDE_HIDDEN_PLAYERS) {
		$WHERE .= ' AND hidden = 0';
	}

	$query = "SELECT id,name,rank,score,(score/(time/60)) as spm, (kills/deaths) as kdr, time, country FROM player WHERE (name LIKE '$SEARCHVALUE' OR name LIKE ' $SEARCHVALUE' OR id = '$SEARCHVALUE') $WHERE ORDER BY score DESC LIMIT 30;";
?>
