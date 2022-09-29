<?php
function getBadgeByID($id)
{
	$id_lines  = file(getcwd()."/queries/badge-id.list");
	$name_lines  = file(getcwd()."/queries/badges.list");
	foreach ($id_lines as $key => $value)
	{
		if ($id == intval($value))
			return $name_lines [$key];
	}
}

function getBadgeCount()
{
	$lines  = file(getcwd()."/queries/badges.list");
	return count($lines);	
}

function getBadge($id)
{
	$lines  = file(getcwd()."/queries/badge-id.list");
	return $lines[$id];	
}

?>