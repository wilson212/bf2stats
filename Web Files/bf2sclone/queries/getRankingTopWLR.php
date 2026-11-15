<?php
	#NOTE: minimum 1 death
	$query = "SELECT id,name,rank_id, wins/losses as wlr,country FROM player WHERE 1=1 ORDER BY wlr DESC LIMIT 5;";
?>