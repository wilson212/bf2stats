<?php
function getUnlockByID($id): ?string
{
	$lines = file( ROOT . DS . 'queries'. DS .'unlocks.list' );
	foreach ($lines as $line)
	{
		$u = explode('|', $line);
		if ($u[0] == $id) {
            return $u[1];
        }
	}
    return null;
}
?>