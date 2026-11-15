<?php
function getRankByID($rank_id): string
{
	$lines  = file(getcwd()."/queries/ranks.list");
	return $lines [$rank_id];
}
?>