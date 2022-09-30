<?php
	#NOTE: minimum 1 death
	$query = "SELECT id,name,rank, kills/deaths as kdr,country FROM player WHERE 1=1 ORDER BY kdr DESC LIMIT 5;";
?>