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

/****************************************************
 * 17/08/06 v0.0.1 - ALPHA build     				*
 * 02/02/2012 v1.0 - Updated and fixed for release  *
 *	by Wilson212									*
 ****************************************************/

//Disable Zlib Compression
ini_set('zlib.output_compression', '0');

$pid 	 = (isset($_GET['pid'])) ? $_GET['pid'] : 0;
$mapid 	 = (isset($_GET['mapid'])) ? $_GET['mapid'] : 0;
$mapname = (isset($_GET['mapname'])) ? $_GET['mapname'] : '';
$limit	 = (isset($_GET['customonly'])) ? $_GET['customonly'] : 0;

// Import configuration
require('includes/utils.php');
$cfg = new Config();

if ($limit == 1) 
{
	// Limit results to custom maps ONLY
	$maplimit = " AND id >= " . $cfg->get('game_custom_mapid');
} 
else 
{
	$maplimit = "";
}

if (!is_numeric($pid) || !is_numeric($mapid) || !is_numeric($limit)) 
{
	die("Invalid Parameters!");
}

if ($mapid && !is_numeric($mapid)) 
{
	print 'Invalid syntax!';
} 
elseif ($pid && is_numeric($pid)) 
{
	$connection = @mysql_connect($cfg->get('db_host'), $cfg->get('db_user'), $cfg->get('db_pass'));
	@mysql_select_db($cfg->get('db_name'), $connection);
	
	$query = "SELECT m.*, mi.name AS mapname" .
		"\nFROM maps m JOIN mapinfo mi ON m.mapid = mi.id" .
		"\nWHERE m.id = {$pid}" .
		"\nORDER BY mapid";
	$result = mysql_query($query) or die(mysql_error());

	if (!mysql_num_rows($result)) 
	{
		$out = "E\n" .
			"H\terr\n" .
			"D\tPlayer Map Data Not Found!\n";
			
		$num = strlen(preg_replace('/[\t\n]/','',$out));
		print $out . "$\t" . $num . "\t$";
	} 
	else 
	{
		$out = "O\n" .
			"H\tmapid\tmapname\ttime\twin\tloss\tbest\tworst\n";
			
		while($row = mysql_fetch_array($result)) 
		{
			$out .= "D\t$row[mapid]\t$row[mapname]\t$row[time]\t$row[win]\t$row[loss]\t$row[best]\t$row[worst]\n";
		}
		
		$num = strlen(preg_replace('/[\t\n]/','',$out));
		print $out . "$\t" . $num . "\t$";
	}
	@mysql_close($connection);
} 
else 
{
	$connection = @mysql_connect($cfg->get('db_host'), $cfg->get('db_user'), $cfg->get('db_pass'));
	@mysql_select_db($cfg->get('db_name'), $connection);
	
	if($mapid) 
	{
		$query = "SELECT * FROM mapinfo WHERE id = {$mapid} {$maplimit}";
	}
	elseif ($mapname) 
	{
		$query = "SELECT * FROM mapinfo WHERE name = '".quote_smart($mapname)."'{$maplimit}";
	} 
	else 
	{
		$query = "SELECT * FROM mapinfo WHERE name <> ''{$maplimit} ORDER BY id";
	}
	$result = mysql_query($query) or die(mysql_error());

	if(!mysql_num_rows($result)) 
	{
		$out = "E\n" .
			"H\terr\n" .
			"D\tMap Data Not Found!\n";
			
		$num = strlen(preg_replace('/[\t\n]/','',$out));
		print $out . "$\t" . $num . "\t$";
	} 
	else 
	{
		$out = "O\n" .
			"H\tmapid\tname\tscore\ttime\ttimes\tkills\tdeaths\n";
		
		while($row = mysql_fetch_array($result)) 
		{
			$out .= "D\t$row[id]\t$row[name]\t$row[score]\t$row[time]\t$row[times]\t$row[kills]\t$row[deaths]\n";
		}
		
		$num = strlen(preg_replace('/[\t\n]/','',$out));
		print $out . "$\t" . $num . "\t$";
	}
	@mysql_close($connection);
}
?>