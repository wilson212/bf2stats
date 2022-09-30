<?php
function getKitByID($id)
{
	$lines  = file(getcwd()."/queries/kits.list");
	return $lines[$id];
}

function getKitCount()
{
	$lines  = file(getcwd()."/queries/kits.list");
	return count($lines);	
}

?>