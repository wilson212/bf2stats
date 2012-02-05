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

/****************************************
 * 08/02/05 v0.0.1 - ALPHA build        *
 * 02/04/12 v1.0.0 - Release	        *
****************************************/

//Disable Zlib Compression
ini_set('zlib.output_compression', '0');

$listtype = (isset($_GET['type'])) ? $_GET['type'] : 0;

if (!is_numeric($listtype)) 
{
	$num = 0;
	$head = "E\nH\tasof\terr\n";
	$out  = "D\t" . time() . "\tInvalid Syntax!\n";
	$num += strlen(preg_replace('/[\t\n]/','',$head));
	$num += strlen(preg_replace('/[\t\n]/','',$out));
	
	print $head;
	print $out;
	print "$\t$num\t$";
} 
else 
{
	$where = "";
	switch ($listtype) 
	{
		case 0:		#Blacklist
			$banlimit = ((isset($_GET['banned'])) && (is_numeric($_GET['banned']))) ? $_GET['banned'] : 100;	// Default Ban Limit is 100
			$where .= " AND (`banned` >= {$banlimit} OR `permban` = 1)";
			break;
		case 1:		#Whitelist
			if ($_GET['clantag']) 
			{
				$paramLen = strlen($_GET['clantag']);
				$where .= " AND `clantag` = '" . $_GET['clantag'] . "'  AND `permban` = 0";
			}
			break;
		case 2:		#Greylist
			// Get Criteria
			$criteria = array('score','rank','time','kdratio','country','banned');
			$where = "";
			foreach ($criteria as $param) 
			{
				if ($_GET[$param]) 
				{
					switch ($param) 
					{
						case 'id':
							if (is_numeric($_GET[$param])) { $where .= " AND `{$param}` = " . $_GET[$param] . ""; }
							break;
						case 'score':
						case 'rank':
						case 'time':
							if (is_numeric($_GET[$param])) { $where .= " AND `{$param}` >= " . $_GET[$param] . ""; }
							break;
						case 'kdratio':
							if (is_numeric($_GET[$param])) { $where .= " AND (kills/deaths) >= " . $_GET[$param] . ""; }
							break;
						case 'country':
							$paramArray = str_replace (",", "','", $_GET[$param]);
							$where .= " AND `{$param}` IN ('" . $paramArray . "')";
							break;
						case 'banned':
							if (is_numeric($_GET[$param])) { $where .= " AND `banned` < " . $_GET[$param] . " AND `permban` = 0"; }
							break;
					}
				}
			}
			break;
	}

	// Import configuration
	require('includes/utils.php');
	$cfg = new Config();

	$connection = @mysql_connect($cfg->get('db_host'), $cfg->get('db_user'), $cfg->get('db_pass'));
	@mysql_select_db($cfg->get('db_name'), $connection);

		$out = "O\n" .
			"H\tsize\tasof\n";

		// Return List of Players that match criteria
		$query = "SELECT id, name FROM player WHERE ip != '0.0.0.0'" . $where . " ORDER BY id ASC";
		$result = mysql_query($query) or die(mysql_error());
		$numrows = mysql_num_rows($result);
		
		$out .= "D\t{$numrows}\t" . time() . "\n" .
			"H\tpid\tnick\n";
			
			while ($row = mysql_fetch_array($result))
			{
				$pid = $row['id'];
				$name = $row['name'];
				$out .= "D\t$pid\t$name\n";
			}

		$num = strlen(preg_replace('/[\t\n]/','',$out));
		print $out . "$\t" . $num . "\t$";

	// Close database connection
	@mysql_close($connection);
}
?>