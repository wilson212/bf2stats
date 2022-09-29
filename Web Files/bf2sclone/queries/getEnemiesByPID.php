<?php
	$query = "SELECT attacker, count FROM kills where victim = $PID ORDER BY count DESC LIMIT 11;";
?>

