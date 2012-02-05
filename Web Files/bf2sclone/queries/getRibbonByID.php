<?php
function getRibbonByID($id)
{
	$id_lines  = file(getcwd()."/queries/ribbon-id.list");
	$name_lines  = file(getcwd()."/queries/ribbons.list");
	foreach ($id_lines as $key => $value)
	{
		if ($id == intval($value))
			return $name_lines [$key];
	}
}

function getRibbonCount()
{
	$lines  = file(getcwd()."/queries/ribbons.list");
	return count($lines);	
}

function getRibbon($id)
{
	$lines  = file(getcwd()."/queries/ribbon-id.list");
	return $lines[$id];	
}
?>