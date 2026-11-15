<?php
function getVehicleByID($id): string
{
	$lines  = file(getcwd()."/queries/vehicle.list");
	return $lines [$id];
}

function getVehicleCount(): int
{
	$lines  = file(getcwd()."/queries/vehicle.list");
	return count($lines);	
}

?>