<?php
function getSFRibbonByID($id)
{
	$id_lines  = file(getcwd()."/queries/sfribbon-id.list");
	$name_lines  = file(getcwd()."/queries/sfribbons.list");
	foreach ($id_lines as $key => $value)
	{
		if ($id == intval($value))
			return $name_lines [$key];
	}
}

function getSFRibbonCount()
{
	$lines  = file(getcwd()."/queries/sfribbons.list");
	return count($lines);	
}

function getSFRibbon($id)
{
	$lines  = file(getcwd()."/queries/sfribbon-id.list");
	return $lines[$id];	
}
?>