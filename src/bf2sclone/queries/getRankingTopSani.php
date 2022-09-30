<?php
	$query = "SELECT id,name,rank,heals+revives as sani ,country FROM player WHERE 1=1 ORDER BY sani DESC LIMIT 5;";
?>