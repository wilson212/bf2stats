<?php
function getMedalByID($id): ?string
{
	$id_lines  = file(getcwd()."/queries/medal-id.list");
	$name_lines  = file(getcwd()."/queries/medals.list");
	foreach ($id_lines as $key => $value)
	{
		if ($id == intval($value)) {
            return $name_lines [$key];
        }
	}
    return null;
}

function getMedalCount(): int
{
	$lines  = file(getcwd()."/queries/medals.list");
	return count($lines);	
}

function getMedal($id): string
{
	$lines  = file(getcwd()."/queries/medal-id.list");
	return $lines[$id];	
}

?>