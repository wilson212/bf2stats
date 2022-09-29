<?php
function getUnlockID($id)
{
	$lines  = file(getcwd()."/queries/unlock-id.list");
	foreach ($lines as $key => $value)
	{
		if ($value == $id)
		return $key;
	}
}
?>