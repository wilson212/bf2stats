<?php
function getCountryByCode($id): string
{
	$lines  = file(getcwd()."/queries/country.list");
	$i = 0;
	while ($i<getCountryCount())
	{
		if (strncasecmp( $lines[$i], (string) $id, 2 ) == 0) {
            return substr($lines[$i],3);
        }
		$i++;
	}
	return 'N/A';
}

function getCountryCount(): int
{
	$lines  = file(getcwd()."/queries/country.list");
	return count($lines);	
}

?>