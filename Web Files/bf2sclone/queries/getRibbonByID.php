<?php
function getRibbonByID($id): ?string
{
	$id_lines  = file(getcwd()."/queries/ribbon-id.list");
	$name_lines  = file(getcwd()."/queries/ribbons.list");
	foreach ($id_lines as $key => $value)
	{
		if ($id == intval($value)) {
            return $name_lines [$key];
        }
	}
    return null;
}

function getRibbonCount(): int
{
	$lines  = file(getcwd()."/queries/ribbons.list");
	return count($lines);	
}

function getRibbon($id): string
{
	$lines  = file(getcwd()."/queries/ribbon-id.list");
	return $lines[$id];	
}
?>