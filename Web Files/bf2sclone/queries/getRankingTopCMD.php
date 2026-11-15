<?php
	$query = "SELECT id,name,rank_id,cmdscore/cmdtime as cmd ,country FROM player WHERE 1=1 ORDER BY cmd DESC LIMIT 5;";
?>