<?php
function getVehicleByID($id)
{
	$lines  = file(getcwd()."/queries/vehicle.list");
	return $lines [$id];
}

function getVehicleCount()
{
	$lines  = file(getcwd()."/queries/vehicle.list");
	return count($lines);	
}

?>