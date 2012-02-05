<?php
function getArmyByID($id)
{
	$lines  = file(getcwd()."/queries/armies.list");
	return $lines [$id];
}

function getArmyCount()
{
	$lines  = file(getcwd()."/queries/armies.list");
	return count($lines);	
}

?>