<?php
function getBadgeByID($id): ?string
{
	$id_lines  = file(getcwd()."/queries/badge-id.list");
	$name_lines  = file(getcwd()."/queries/badges.list");
	foreach ($id_lines as $key => $value)
	{
		if ($id == intval($value)) {
            return $name_lines [$key];
        }
	}
    return null;
}

function getBadgeCount(): int
{
	$lines  = file(getcwd()."/queries/badges.list");
	return count($lines);	
}

function getBadge($id): string
{
	$lines  = file(getcwd()."/queries/badge-id.list");
	return $lines[$id];	
}

?>