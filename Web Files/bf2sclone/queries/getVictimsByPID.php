<?php
	$query = "SELECT victim, count FROM kills WHERE attacker = $PID ORDER BY count DESC LIMIT 11;";
?>