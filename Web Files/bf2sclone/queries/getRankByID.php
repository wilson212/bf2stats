<?php
function getRankByID($rank_id)
{
	$lines  = file(getcwd()."/queries/ranks.list");
	return $lines [$rank_id];
}
?>