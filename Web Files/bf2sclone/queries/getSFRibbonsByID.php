<?php
function getSFRibbonByID($id): ?string
{
	$id_lines  = file(getcwd()."/queries/sfribbon-id.list");
	$name_lines  = file(getcwd()."/queries/sfribbons.list");
	foreach ($id_lines as $key => $value)
	{
		if ($id == intval($value)) {
            return $name_lines [$key];
        }
	}
    return null;
}

function getSFRibbonCount(): int
{
	$lines  = file(getcwd()."/queries/sfribbons.list");
	return count($lines);	
}

function getSFRibbon($id): string
{
	$lines  = file(getcwd()."/queries/sfribbon-id.list");
	return $lines[$id];	
}
?>