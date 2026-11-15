<?php
	$query = "SELECT id,name,rank_id,teamscore-(teamdamage+teamkills+teamvehicledamage) as teamwork,country FROM player WHERE teamscore>(teamdamage+teamkills+teamvehicledamage) ORDER BY teamwork DESC LIMIT 5;";
?>