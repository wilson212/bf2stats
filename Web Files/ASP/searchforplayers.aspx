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

/*********************************
 * 11/13/05 v0.0.1 - ALPHA build *
 * 01/03/06 v0.1 - BETA release  *
 * 02/04/12 v1.0 - Release       *
 *********************************/
 
//Disable Zlib Compression
ini_set('zlib.output_compression', '0');

$nick = (isset($_GET['nick'])) ? $_GET['nick'] : false;

if (!$nick || $nick == '')
{
    print 'Invalid syntax!';
}
else
{
	$head = "O\n" .
		"H\tasof\n" .
		"D\t" . time() . "\n" .
		"H\tn\tpid\tnick\tscore\n";
	
	$num = strlen(preg_replace('/[\t\n]/','',$head));
	print $head;

	// Import configuration
	require('includes/utils.php');
	$cfg = new Config();
	
	$connection = @mysql_connect($cfg->get('db_host'), $cfg->get('db_user'), $cfg->get('db_pass'));
	@mysql_select_db($cfg->get('db_name'), $connection);
	
	$query = "SELECT id, name, score FROM player WHERE name LIKE '%".quote_smart($nick)."%'";
	$result = mysql_query($query) or die(mysql_error());

	$num = 1;
	$count = 31;
	$out = "";

	if (mysql_num_rows($result))
	{
		while ($row = mysql_fetch_array($result))
		{
			$count += strlen($num) + strlen($row['id']) + $row['name'] + $row['score'];
			$out .= "D\t" . $num++ . "\t" . $row['id'] . "\t" . $row['name'] . "\t" . $row['score'] . "\n";
		}
	}

	$num += strlen(preg_replace('/[\t\n]/','',$out));
	print $out . "$\t" . $num . "\t$";
	
	@mysql_close($connection);
}
?>