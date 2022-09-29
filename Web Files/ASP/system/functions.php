<?php
/*
| ---------------------------------------------------------------
| Method: getDataTables()
| ---------------------------------------------------------------
|
| This function returns an array of all bf2stats table names
|
*/	
	function getDataTables()
	{
		return array(
			'army',
			'awards',
			'kills',
			'kits',
			'mapinfo',
			'maps',
			'player',
			'player_history',
			'round_history',
			'servers',
			'unlocks',
			'vehicles',
			'weapons',
		);
	}
	
/*
| ---------------------------------------------------------------
| Function: sec2hms()
| ---------------------------------------------------------------
|
| Converts a timestamp to how many days, hours, mintues left
| Thanks to: http://www.laughing-buddha.net/php/lib/sec2hms/
|
| @Param: (Int) $sec - The timestamp
| @Return (String) The array of data
|
*/
	function sec2hms($sec, $padHours = true) 
	{
		// start with a blank string
		$hms = "";

		// do the hours first: there are 3600 seconds in an hour, so if we divide
		// the total number of seconds by 3600 and throw away the remainder, we're
		// left with the number of hours in those seconds
		$hours = intval(intval($sec) / 3600); 

		// add hours to $hms (with a leading 0 if asked for)
		$hms .= ($padHours) ? str_pad($hours, 2, "0", STR_PAD_LEFT). ":" : $hours. ":";

		// dividing the total seconds by 60 will give us the number of minutes
		// in total, but we're interested in *minutes past the hour* and to get
		// this, we have to divide by 60 again and then use the remainder
		$minutes = intval(($sec / 60) % 60); 

		// add minutes to $hms (with a leading 0 if needed)
		$hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT). ":";

		// seconds past the minute are found by dividing the total number of seconds
		// by 60 and using the remainder
		$seconds = intval($sec % 60); 

		// add seconds to $hms (with a leading 0 if needed)
		$hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);

		// done!
		return $hms;
	}
	
/*
| ---------------------------------------------------------------
| Function: formatTime()
| ---------------------------------------------------------------
|
| Converts a timestamp to a human readable time format
|
| @Param: (Int) $sec - The timestamp
| @Return (String) The array of data
|
*/
	function formatTime($seconds)
	{
		// Get our seconds to hours:minutes:seconds
		$time = sec2hms($seconds, false);
		
		// Explode the time
		$time = explode(':', $time);
		
		// Hour corrections
		$set = '';
		if($time[0] > 0)
		{
			// Set days if viable
			if($time[0] > 23)
			{
				$days = floor($time[0] / 24);
				$time[0] = $time[0] - ($days * 24);
				$set .= ($days > 1) ? $days .' Days' : $days .' Day';
				if($time[0] > 0) $set .= ',';
			}
			$set .= ($time[0] > 1) ? $time[0] .' Hours' : $time[0] .' Hour';
		}
		if($time[1] > 0)
		{
			$set .= ($time[0] > 0) ? ', ' : '';
			$set .= ($time[1] > 1) ? $time[1] .' Minutes' : $time[1] .' Minute';
		}
		
		return $set;
	}

/*
| ---------------------------------------------------------------
| Method: redirect()
| ---------------------------------------------------------------
|
| This function is used to easily redirect and refresh pages
|
| @Param: (String) $url - Where were going
| @Param: (Int) $wait - How many sec's we wait till the redirect.
| @Return: (None)
|
*/
	function redirect($url, $wait = 0)
	{
		// Format for ASP if need be
		if(preg_match("/^[A-Za-z0-9]+$/", $url))
			$url = 'index.php?task='. $url;
		
		// Check for refresh or straight redirect
		if($wait >= 1)
		{
			header("Refresh:". $wait .";url=". $url);
		}
		else
		{
			header("Location: ".$url);
			die();
		}
	}
	
/**
 * Combines several string arguments into a file path.
 *
 * @param string|string[] $parts The pieces of the path, passed as 
 *   individual arguments. Each argument can be a single dimmensional 
 *   array of paths, a string folder / filename, or a mixture of the two.
 *   Dots may also be passed ( . & .. ) to change directory levels
 *
 * @return string Returns the full path using the correct system 
 *   directory separater
 */
	function path($parts = null)
	{
		// Get our path parts
		$args = func_get_args();
		$parts = array();
		
		// Trim our paths to remvove spaces and new lines
		foreach($args as $part)
		{
			// If part is array, then implode and continue
			if(is_array($part))
			{
				// Remove empty entries
				$part = array_filter($part, 'strlen');
				$parts[] = implode(DS, $part);
				continue;
			}
			
			// String
			$part = trim($part);
			if($part == '.' || empty($part))
				continue;
			elseif($part == '..')
				array_pop($parts);
			else
				$parts[] = $part;
		}

		// Get our cleaned path into a variable with the correct directory seperator
		return implode( DS, $parts );
	}

/*
| ---------------------------------------------------------------
| Method: isIPInNet()
| ---------------------------------------------------------------
|
| Notes:
|		Host address and subnets are supported, use x.x.x.x/y standard notation.
|		Addresses without subnet (ie, x.x.x.x) are assumed to be a single HOST
|		An address of 0.0.0.0/0 matches ALL HOSTS (ie, disbales check)
|		
|	$auth_hosts = array(
|		"127.0.0.1",
|		"10.0.0.0/8",
|		"172.16.0.0/12",
|		"192.168.0.0/16"
|	);
|
*/

	function isIPInNet($ip, $net, $mask) 
	{
		$lnet = ip2long($net);
		$lip = ip2long($ip);
		$binnet = str_pad( decbin($lnet), 32, "0", STR_PAD_LEFT );
		$firstpart = substr($binnet, 0, $mask);
		$binip = str_pad( decbin($lip), 32, "0", STR_PAD_LEFT );
		$firstip = substr($binip, 0, $mask);
		
		return( strcmp($firstpart, $firstip) == 0 );
	}

/*
| ---------------------------------------------------------------
| Method: isIPInNetArray()
| ---------------------------------------------------------------
|
| This function checks if an ip is in an array of nets (ip and mask)
|
*/
	function isIpInNetArray($theip,$thearray) 
	{
		$exit_c = false;
		
		if(is_array($thearray)) 
		{
			foreach($thearray as $subnet) 
			{
				// Match all
				if($subnet == '0.0.0.0' || $subnet == '0.0.0.0/0') 
				{
					$exit_c = true;
					break;
				}
				
				if(strpos($subnet, "/") === false)
				{
					$subnet .= "/32";
				}
				
				list($net,$mask) = explode("/",$subnet);
				if(isIPInNet($theip,$net,$mask))
				{
					$exit_c = true;
					break;
				}
			}
		}
		return $exit_c;
	}

/*
| ---------------------------------------------------------------
| Method: checkPrivateIp()
| ---------------------------------------------------------------
|
| Checks if the givin IP is a Private (local) IP
|
*/
	function isPrivateIp($ip_s) 
	{
		// Define Private IPs
		$privateIPs = array();
		$privateIPs[] = '10.0.0.0/8';
		$privateIPs[] = '127.0.0.0/8';
		$privateIPs[] = '172.16.0.0/12';
		$privateIPs[] = '192.168.0.0/16';
		
		if ($ip_s != "" && isIPInNetArray($ip_s, $privateIPs))
		{
			return 1;	// Private IP
		}
		else
		{
			return 0;	// Public/Other IP
		}
	}

/**
 * Checks an IP address, returning whether its a valid IP.
 *
 * @param string $ip The ip address to check.
 *
 * @return bool Returns true if the given IP address is a valid IP, false otherwise
 */
	function isValidIp($_ip)
	{
		// Setup reserved IP ranges
		static $reserved_ips = array();
		if(empty($reserved_ips))
		{
			// array(min, max)
			$reserved_ips = array(
				array(sprintf("%u", ip2long('0.0.0.0')), sprintf("%u", ip2long('2.255.255.255'))),
				array(sprintf("%u", ip2long('10.0.0.0')), sprintf("%u", ip2long('10.255.255.255'))),
				array(sprintf("%u", ip2long('127.0.0.0')), sprintf("%u", ip2long('127.255.255.255'))),
				array(sprintf("%u", ip2long('169.254.0.0')), sprintf("%u", ip2long('169.254.255.255'))),
				array(sprintf("%u", ip2long('172.16.0.0')), sprintf("%u", ip2long('172.31.255.255'))),
				array(sprintf("%u", ip2long('192.0.2.0')), sprintf("%u", ip2long('192.0.2.255'))),
				array(sprintf("%u", ip2long('192.168.0.0')), sprintf("%u", ip2long('192.168.255.255'))),
				array(sprintf("%u", ip2long('255.255.255.0')), sprintf("%u", ip2long('255.255.255.255')))
			);
		}
		
		// Trim the ip address
		$ip = sprintf("%u", ip2long( trim($_ip) ));
		if(!empty($_ip) && $ip != -1)
		{
			foreach($reserved_ips as $r)
			{
				$min = $r[0];
				$max = $r[1];
				if(($ip >= $min) && ($ip <= $max))
					return false;
			}
			return true;
		}
		return false;
	}

/*
| ---------------------------------------------------------------
| Method: verCmp()
| ---------------------------------------------------------------
|
| Converts the DB version from a float to INT for comparison
|
*/
	function verCmp($ver)
	{
		$ver_arr = explode(".", $ver);
		
		$i = 1;
		$result = 0;
		foreach($ver_arr as $vbit) 
		{
			$result += $vbit * $i;
			$i = $i / 100;
		}
		return $result;
	}

/*
| ---------------------------------------------------------------
| Method: ErrorLog()
| ---------------------------------------------------------------
|
| Logs stats errors
|
*/
	function ErrorLog($msg, $lvl)
	{
		switch($lvl) 
		{
			case -1:
				$lvl_txt = 'INFO: ';
				break;
			case 0:
				$lvl_txt = 'SECURITY: ';
				break;
			case 1:
				$lvl_txt = 'ERROR: ';
				break;
			case 2:
				$lvl_txt = 'WARNING: ';
				break;
			default:
				$lvl_txt = 'NOTICE: ';
				break;
		}
		
		if($lvl <= Config::Get('debug_lvl'))
		{
			$err_msg = date('Y-m-d H:i:s')." -- ".$lvl_txt.$msg."\n";
			$log = SYSTEM_PATH . DS . 'logs' . DS . 'stats_debug.log';
			$file = @fopen($log, 'a');
			@fwrite($file, $err_msg);
			@fclose($file);
		}
	}
	
/*
| ---------------------------------------------------------------
| Method: checkQueryResult()
| ---------------------------------------------------------------
|
| Logs failed queries from snapshot proccessing
|
*/
	function checkQueryResult($result, $query, $DB)
	{
		if($result === false || $DB->errorCode() != 0) 
		{
			$error = $DB->errorInfo();
			$msg  = 'Database ERROR: ' . preg_replace("/[\n|\r|\n\r]+/", "", $error[2]) . PHP_EOL;
			$msg .= 'Query String: '. $query . PHP_EOL;
			//ErrorLog($msg, 1);
			throw new Exception($msg);
		}
	}

/*
| ---------------------------------------------------------------
| Method: getPageContents()
| ---------------------------------------------------------------
|
| Uses either file() or CURL to get the contents of a page
|
*/
	function getPageContents($url)
	{	
		// Try file() first
		if( function_exists('file') && function_exists('fopen') && ini_get('allow_url_fopen') ) 
		{
			ini_set("user_agent", "GameSpyHTTP/1.0");
			$results = @file($url);
		}
		
		// either there was no function, or it failed -- try curl
		if( !$results && function_exists('curl_exec') ) 
		{
			$curl_handle = curl_init();
			curl_setopt($curl_handle, CURLOPT_URL, $url);
			curl_setopt($curl_handle, CURLOPT_USERAGENT, "GameSpyHTTP/1.0");
			curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 1);
			curl_setopt($curl_handle, CURLOPT_TIMEOUT, 10);
			$results = curl_exec($curl_handle);
			$err = curl_error($curl_handle);
			if( $err != '' ) 
				return false;
			$results = explode("\n",trim($results));
			curl_close($curl_handle);
		}
		
		// still nothing, forgetd a'bout it
		if( !$results ) return false;
		return $results;
	}
?>