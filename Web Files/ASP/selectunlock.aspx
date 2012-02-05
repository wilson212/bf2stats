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

/********************************************
 * 11/14/05 v0.0.1 - ALPHA build            *
 * 11/15/05 v0.0.2 - Added redundancy check *
 * 11/26/05 v0.0.3 - Updated for SF         *
 * 12/05/05 v0.0.4 - Fixed typo             *
 * 01/03/06 v0.1 - BETA release             *
 * 02/04/12 v1.0 - Release		            *
 ********************************************/

//Disable Zlib Compression
ini_set('zlib.output_compression', '0'); 
 
$pid = (isset($_GET['pid'])) ? $_GET['pid'] : false;
$id = (isset($_GET['id'])) ? $_GET['id'] : false;

if (!$pid || !is_numeric($pid) || !$id || !is_numeric($id)) 
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

	$query = "UPDATE unlocks SET state = 's' WHERE (id = {$pid}) AND (kit = {$id})";
	mysql_query($query) or die(mysql_error());

	$query = "SELECT availunlocks FROM player WHERE id = {$pid}";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	$unlocks = $row['availunlocks'];
	$unlocks -= 1;

	$query = "UPDATE player SET availunlocks = {$unlocks} WHERE id = {$pid}";
	mysql_query($query) or die(mysql_error());

	$query = "SELECT usedunlocks FROM player WHERE id = {$pid}";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_array($result);
	$used = $row['usedunlocks'];
	$used += 1;

	$query = "UPDATE player SET usedunlocks = {$used} WHERE id = {$pid}";
	mysql_query($query) or die(mysql_error());

	@mysql_close($connection);
}
?>