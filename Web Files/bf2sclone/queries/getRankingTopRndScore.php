<?php
	#NOTE: minimum 1 death
	$query = "SELECT id,name,rank_id, bestscore,country FROM player WHERE 1=1 ORDER BY bestscore DESC LIMIT 5;";
?>