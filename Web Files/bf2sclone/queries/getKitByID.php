<?php
function getKitByID($id): string
{
	$lines  = file(getcwd()."/queries/kits.list");
	return $lines[$id];
}

function getKitCount(): int
{
	$lines  = file(getcwd()."/queries/kits.list");
	return count($lines);	
}

?>