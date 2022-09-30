<?php
function getCountryByCode($id)
{
	$lines  = file(getcwd()."/queries/country.list");
	$i = 0;
	while ($i<getCountryCount())
	{
		if (strncasecmp( $lines[$i], $id, 2 ) == 0)
			return substr($lines[$i],3);
		$i++;
	}
	return 'N/A';
}

function getCountryCount()
{
	$lines  = file(getcwd()."/queries/country.list");
	return count($lines);	
}

?>