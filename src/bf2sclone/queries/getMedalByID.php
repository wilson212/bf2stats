<?php
function getMedalByID($id)
{
	$id_lines  = file(getcwd()."/queries/medal-id.list");
	$name_lines  = file(getcwd()."/queries/medals.list");
	foreach ($id_lines as $key => $value)
	{
		if ($id == intval($value))
			return $name_lines [$key];
	}
}

function getMedalCount()
{
	$lines  = file(getcwd()."/queries/medals.list");
	return count($lines);	
}

function getMedal($id)
{
	$lines  = file(getcwd()."/queries/medal-id.list");
	return $lines[$id];	
}

?>