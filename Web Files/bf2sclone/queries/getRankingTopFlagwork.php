<?php
	$query = "SELECT id,name,rank_id,captureassists+captures+neutralizes+defends as flagwork,country FROM player WHERE 1=1 ORDER BY flagwork DESC LIMIT 5;";
?>