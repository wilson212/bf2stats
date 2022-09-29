<?php

/*
	Copyright (C) 2006-2012  BF2Statistics

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

/*
| ---------------------------------------------------------------
| Define ROOT and system paths
| ---------------------------------------------------------------
*/
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(__FILE__));
define('SYSTEM_PATH', ROOT . DS . 'system');

/*
| ---------------------------------------------------------------
| Require the needed scripts to launch the system
| ---------------------------------------------------------------
*/
require(SYSTEM_PATH . DS . 'core'. DS .'Database.php');
require(SYSTEM_PATH . DS . 'core'. DS .'Config.php');
require(SYSTEM_PATH . DS . 'functions.php');

// Set Error Reporting
error_reporting(E_ALL);
ini_set("log_errors", "1");
ini_set("error_log", SYSTEM_PATH . DS . 'logs' . DS . 'php_errors.log');
ini_set("display_errors", "0");

//Disable Zlib Compression
ini_set('zlib.output_compression', '0');

// Make sure we have a listtype and it valid
$listtype = (isset($_GET['type'])) ? $_GET['type'] : 0;
if (!is_numeric($listtype)) 
{
	$out = "E\nH\tasof\terr\n" .
        "D\t" . time() . "\tInvalid Syntax!\n";
	$num = strlen(preg_replace('/[\t\n]/','',$out));
	print $out ."$\t$num\t$";
}
else 
{
	// Connect to the database
    $connection = null;
    try {
        $connection = Database::Connect('bf2stats',
            array(
                'driver' => 'mysql',
                'host' => Config::Get('db_host'), 
                'port' => Config::Get('db_port'), 
                'database' => Config::Get('db_name'), 
                'username' => Config::Get('db_user'), 
                'password' => Config::Get('db_pass')
            )
        );
    }
    catch( Exception $e ) {
        $out = "E\nH\tasof\terr\n" . 
            "D\t" . time() . "\tDatabase Connect Error\n";
        $num = strlen(preg_replace('/[\t\n]/', '', $out));
        $out .= "$\t$num\t$";
        die($out);
    }
	
	// Build our criteria based on $_GET['type']
	$where = "";
    $binds = array();
	switch ($listtype) 
	{
		case 0:		#Blacklist
			$banlimit = ((isset($_GET['banned'])) && (is_numeric($_GET['banned']))) ? $_GET['banned'] : 100;	// Default Ban Limit is 100
			$where .= " AND (`banned` >= :banlimit OR `permban` = 1)";
            $binds[':banlimit'] = intval($banlimit);
			break;
		case 1:		#Whitelist
			if ($_GET['clantag']) 
			{
				$where .= " AND `clantag` = :clantag  AND `permban` = 0";
                $binds[':clantag'] = stripslashes($_GET['clantag']);
			}
			break;
		case 2:		#Greylist
			// Get Criteria
			$criteria = array('score','rank','time','kdratio','country','banned');
			$where = "";
			foreach($criteria as $param) 
			{
				if(isset($_GET[$param])) 
				{
					switch ($param) 
					{
						case 'id':
							if (is_numeric($_GET['id'])) 
                            { 
                                $where .= " AND `id` = :id";
                                $binds[':id'] = intval($_GET['id']);
                            }
							break;
						case 'score':
						case 'rank':
						case 'time':
                            if (is_numeric($_GET[$param])) 
                            { 
                                $where .= " AND `{$param}` >= :".$param;
                                $binds[':'.$param] = intval($_GET[$param]);
                            }
							break;
						case 'kdratio':
                            if (is_numeric($_GET['kdratio']) || is_float($_GET['kdratio'])) 
                            { 
                                $where .= " AND (`kills` / `deaths`) >= :kdratio";
                                $binds[':kdratio'] = floatval($_GET['kdratio']);
                            }
							break;
						case 'country':
							$paramArray = str_replace (",", "','", $_GET[$param]);
							$where .= " AND `{$param}` IN ('" . $paramArray . "')";
							break;
						case 'banned':
							if(is_numeric($_GET['banned']))
                            {
                                $where .= " AND (`banned` < :banned AND `permban` = 0)";
                                $binds[':banned'] = intval($_GET['banned']);
                            }
							break;
					}
				}
			}
			break;
	}

	// Prepare output header
	$out = "O\n" .
		"H\tsize\tasof\n";

	// Prepare our statement query
	$query = "SELECT `id`, `name` FROM `player` WHERE `ip` != '0.0.0.0'" . $where . " ORDER BY `id` ASC";
	$stmt = $connection->prepare($query);
	foreach($binds as $k => $v)
    {
        $type = (is_int($v)) ? PDO::PARAM_INT : PDO::PARAM_STR;
        $stmt->bindValue($k, $v, $type);
    }
    
    // Execute the statement
    $result = false;
    try {
        $result = $stmt->execute();
    }
    catch( Exception $e ) {var_dump($stmt->errorInfo());}
	if($result)
	{
        $buffer = '';
        $i = 0;
        while($row = $stmt->fetch())
        {
            $pid = $row['id'];
            $name = $row['name'];
            $buffer .= "D\t$pid\t$name\n";
            ++$i;
        }
        
        $out .= "D\t". $i ."\t" . time() . "\n" .
            "H\tpid\tnick\n" .
            $buffer;
	}
    else
    {
        $out .= "D\t0\t" . time() . "\n" .
            "H\tpid\tnick\n";
    }
    
    $num = strlen(preg_replace('/[\t\n]/', '', $out));
    print $out . "$\t" . $num . "\t$";
}
?>