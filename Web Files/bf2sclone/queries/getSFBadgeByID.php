<?php
function getSFBadgeByID($id)
{
	$id_lines  = file(getcwd()."/queries/sfbadge-id.list");
	$name_lines  = file(getcwd()."/queries/sfbadges.list");
	foreach ($id_lines as $key => $value)
	{
		if ($id == intval($value))
			return $name_lines [$key];
	}
}

function getSFBadgeCount()
{
	$lines  = file(getcwd()."/queries/sfbadges.list");
	return count($lines);	
}

function getSFBadge($id)
{
	$lines  = file(getcwd()."/queries/sfbadge-id.list");
	return $lines[$id];	
}
?>