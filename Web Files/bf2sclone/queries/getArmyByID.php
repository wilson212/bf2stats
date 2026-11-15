<?php
function getArmyByID($id): string
{
	$lines  = file(getcwd()."/queries/armies.list");
	return $lines [$id];
}

function getArmyCount(): int
{
	$lines  = file(getcwd()."/queries/armies.list");
	return count($lines);	
}

?>