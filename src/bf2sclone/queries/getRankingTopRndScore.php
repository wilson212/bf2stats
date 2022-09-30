<?php
	#NOTE: minimum 1 death
	$query = "SELECT id,name,rank, rndscore,country FROM player WHERE 1=1 ORDER BY rndscore DESC LIMIT 5;";
?>