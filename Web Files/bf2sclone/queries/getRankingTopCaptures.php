<?php
	#WHERE captures>0 
	$query = "SELECT id,name,rank, captures,country FROM player WHERE 1=1 ORDER BY captures DESC LIMIT 5;";
?>