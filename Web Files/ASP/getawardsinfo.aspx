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

/*****************************************
 * 11/13/05 v0.0.1 - ALPHA build         *
 * 11/15/05 v0.0.2 - Rearranged output   *
 * 11/20/05 v0.0.3 - Changed id handling *
 * 11/28/05 v0.0.4 - Updated for SF      *
 * 01/03/06 v0.1 - BETA release          *
 * 02/04/12 v1.0 - Release	             *
 *****************************************/

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
	
	$head = "O\n" .
		"H\tpid\tasof\n" .
		"D\t$pid\t" . time() . "\n" .
		"H\taward\tlevel\twhen\tfirst\n";
	
	$query = "SELECT awd, level, earned, first FROM awards WHERE id = {$pid} ORDER BY id";
	$result = mysql_query($query) or die(mysql_error());

	$count = 0;
	$out = "";
	
	if (mysql_num_rows($result))
	{
		while ($row = mysql_fetch_array($result))
		{
			if (($row['awd'] > 2000000) && ($row['awd'] < 3000000)) {$first = $row['first'];} #medals
			else {$first = 0;}
			
			$out .= "D\t$row[awd]\t$row[level]\t$row[earned]\t$first\n";
		}
	}
	
	$num = strlen(preg_replace('/[\t\n]/','',$head));
	$num += strlen(preg_replace('/[\t\n]/','',$out));
	print $head;
	print $out . "$\t" . $num . "\t$";
	
	// Close database connection
	@mysql_close($connection);
}
?>