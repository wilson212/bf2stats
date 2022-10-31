<?php
	$WHERE = '';
	if (LEADERBOARD_HIDE_BOTS) {
		$WHERE .= ' AND isbot = 0';
	}
	if (LEADERBOARD_HIDE_HIDDEN_PLAYERS) {
		$WHERE .= ' AND hidden = 0';
	}

	$query = "SELECT id,name,rank,teamscore-(teamdamage+teamkills+teamvehicledamage) as teamwork,country FROM player WHERE teamscore>(teamdamage+teamkills+teamvehicledamage) $WHERE ORDER BY teamwork DESC LIMIT 5;";
?>
