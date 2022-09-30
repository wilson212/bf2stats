<?php
	$query = "SELECT id,name,rank,cmdscore/cmdtime as cmd ,country FROM player WHERE 1=1 ORDER BY cmd DESC LIMIT 5;";
?>