<?php
function getSFBadgeByID($id): ?string
{
	$id_lines  = file(getcwd()."/queries/sfbadge-id.list");
	$name_lines  = file(getcwd()."/queries/sfbadges.list");
	foreach ($id_lines as $key => $value)
	{
		if ($id == intval($value)) {
            return $name_lines [$key];
        }
	}
    return null;
}

function getSFBadgeCount(): int
{
	$lines  = file(getcwd()."/queries/sfbadges.list");
	return count($lines);	
}

function getSFBadge($id): string
{
	$lines  = file(getcwd()."/queries/sfbadge-id.list");
	return $lines[$id];	
}
?>