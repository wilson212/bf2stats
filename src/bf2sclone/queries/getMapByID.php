<?php
function getMapByID($id)
{
	$lines  = file(getcwd()."/queries/maps.list");
	$i = 0;
	while ($i<getMapCount())
	{
		if (strncasecmp( $lines[$i], "$id", strlen("$id") ) == 0)
			return substr($lines[$i], strlen("$id")+1);
		$i++;
	}
	return 'N/A';
}

function getMapCount()
{
	$lines  = file(getcwd()."/queries/maps.list");
	return count($lines);	
}

?>