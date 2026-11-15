<?php
function getUnlockID($id): ?int
{
	$lines  = file(getcwd()."/queries/unlock-id.list");
	foreach ($lines as $key => $value)
	{
		if ($value == $id) {
            return $key;
        }
	}
    return null;
}
?>