<?php

/*
    Copyright (C) 2006  BF2Statistics

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/*************************************
 * 08/03/06 - ALPHA build            *
 * 02/04/12 - Release            	 *
 *************************************/

/*
 Not sure what this does, but I suspect that is clears the chng & decr values in
 "player" table???  
 First found by "thomaskunze" on http://www.bf2statistics.com/
 
 Sample Gamespy Output:
	O
	Cleared rank notification 123123123
	$	35	$
 
 The Shadow
*/ 
 
//Disable Zlib Compression
ini_set('zlib.output_compression', '0');
 
$pid = (isset($_GET['pid'])) ? $_GET['pid'] : false;

if (!$pid || !is_numeric($pid)) 
{
    print 'Invalid syntax!';
}
else
{
	// Import configuration
	require('includes/utils.php');
	$cfg = new Config();
	
	$connection = @mysql_connect($cfg->get('db_host'), $cfg->get('db_user'), $cfg->get('db_pass'));
	@mysql_select_db($cfg->get('db_name'), $connection);

	$query = "SELECT rank FROM player WHERE id = {$pid}";
	$result = mysql_query($query) or die(mysql_error());

	if (!mysql_num_rows($result)) 
	{
		print "E\nPlayer not found!";
	}
	else
	{
		$row = mysql_fetch_array($result);
		$rank = $row['rank'];

		$query = "UPDATE player SET chng = 0, decr = 0 WHERE id = {$pid}";
		$result = mysql_query($query) or die(mysql_error());
		if(!$result) 
		{
			$out = "O\nFailed to clear rank notification {$pid}\n";
		} 
		else 
		{
			$out = "O\nCleared rank notification {$pid}\n";
		}
		
		$num = strlen(preg_replace('/[\t\n]/','',$out));
		print $out . "$\t" . $num . "\t$";
	}
	@mysql_close($connection);
}
?>