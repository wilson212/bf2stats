<?php
function getUnlockByID($id)
{
	$lines = file( ROOT . DS . 'queries'. DS .'unlocks.list' );
	foreach ($lines as $line)
	{
		$u = explode('|', $line);
		if ($u[0] == $id) return $u[1];
	}
}
?>