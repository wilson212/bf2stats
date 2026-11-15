<?php
// Include our config file
include(__DIR__ . '/config.inc.php');
// process page start:
$time_start = microtime(true);

// Define a smaller Directory seperater and ROOT path
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', __DIR__);
define('CACHE_PATH', ROOT . DS . 'cache' . DS);
define('TEMPLATE_PATH', ROOT . DS . 'template' . DS);

// IFF PID -> go show stats!
$PID = $_GET["pid"] ?? "0";
$GO = $_GET["go"] ?? "0";
$GET = $_POST["get"] ?? 0;
$SET = $_POST["set"] ?? 0;
$ADD = $_GET["add"] ?? 0;
$REMOVE = $_GET["remove"] ?? 0;
$LEADERBOARD = $_POST["leaderboard"] ?? "0";

// Check for leaderboard getting / setting
if($SET)
{
	setcookie("leaderboard", (string) $LEADERBOARD, ['expires' => time() + 315360000, 'path' => '/', 'domain' => (string) $DOMAIN]); // delete after 10 years ;)
	#NOTE: after setting a cookie, you must redirect!
	header("Location: ".$ROOT."?go=my-leaderboard"); // refresh for cookie
	exit();
}

if($GET)
{
	// output the nice save-url
	header("Location: ". $ROOT .'?go=my-leaderboard&pid='.urlencode((string) $LEADERBOARD));
	exit();
}

/* IMPLEMENTED FUNCTIONS */
include( ROOT . DS . 'functions.inc.php' );

/* PLAYER STATS SQL FUNCTIONS*/
include( ROOT . DS . 'playerstats.inc.php' );
include( ROOT . DS . 'awards.inc.php' );
include( ROOT . DS . 'expansions.inc.php' );

/* RANKING STATS SQL FUNCTIONS*/
include( ROOT . DS . 'rankingstats.inc.php' );

/* SEARCH SQL FUNCTIONS*/
include( ROOT . DS . 'search.inc.php' );

/* LEADERBOARD AND HOME (as home includes leaderboard) */
include( ROOT . DS . 'leaderboard.inc.php' );


/***************************************************************
 * PLAYERSTATS
 ***************************************************************/
if($GO == "0" && $PID)
{
	#$awards = getAwardsByPID($PID); // get earned awards
	if(isCached($PID))// already cached!
	{
		$template 	= getCache($PID);
		$LASTUPDATE = intToTime( getLastUpdate( CACHE_PATH . $PID .'.cache') );
		$NEXTUPDATE = intToTime( getNextUpdate( CACHE_PATH . $PID .'.cache', RANKING_REFRESH_TIME) );
		$template 	= str_replace('{:LASTUPDATE:}', $LASTUPDATE, $template);
		$template 	= str_replace('{:NEXTUPDATE:}', $NEXTUPDATE, $template);
	}
	else
	{
		// Load Player Data
		$player 		= getPlayerDataFromPID($PID); // receive player data
		$victims 		= getFavouriteVictims($PID); // receive victim data
		$enemies 		= getFavouriteEnemies($PID); // receive enemie data
		$armies 		= getArmyData($PID); // receive army data
		$armySummary 	= getArmySummaries($armies); // retrieve Army summary
		$unlocks 		= getUnlocksByPID($PID);	// retrieve unlock data
		$vehicles 		= getVehicleData($PID);	// retrieve vehivle data
		$vehicleSummary = getVehicleSummaries($vehicles); // retrieve Vehicle summary
		$weapons 		= getWeaponData($PID, $player); // retrieve Weapon data
		$weaponSummary 	= getWeaponSummary($weapons, $player); // retrieve weapon summary
		$equipmentSummary = getEquipmentSummary($weapons, $player); // retrieve equipment summary
		$kits 			= getKitData($PID); // retrieve kit data
		$kitSummary 	= getKitSummary($kits, $player); // retrieve kits summary
		$maps 			= getMapData($PID);
		$mapSummary 	= getMapSummary($maps);
		$TheaterData 	= getTheaterData($PID);  // retrueve Theater Data
		$playerFavorite = getPlayerFavorites($weapons, $vehicles, $kits, $armies, $maps, $TheaterData); // get player summary
		$PlayerAwards  	= getAwardsByPID($PID);

		// Include our template file
		include( TEMPLATE_PATH . 'playerstats.php' );
		
		// write cache file
		writeCache($PID, trim((string) $template));
		$LASTUPDATE = intToTime(0);
		$NEXTUPDATE = intToTime(RANKING_REFRESH_TIME);		
		$template 	= str_replace('{:LASTUPDATE:}', $LASTUPDATE, $template);
		$template 	= str_replace('{:NEXTUPDATE:}', $NEXTUPDATE, $template);		
	}
}

/***************************************************************
 * CURRENT RANKINGS
 ***************************************************************/
elseif(strcasecmp((string) $GO, 'currentranking') == 0)
{
	$rankings = getRankingCollection();
	$LASTUPDATE = 0;
	$NEXTUPDATE = 0;
	if(isCached('current-ranking'))// already cached!
	{
		$template 	= getCache('current-ranking');
		$LASTUPDATE = intToTime(getLastUpdate( CACHE_PATH . 'current-ranking.cache'));
		$NEXTUPDATE = intToTime(getNextUpdate( CACHE_PATH . 'current-ranking.cache', RANKING_REFRESH_TIME));
	}
	else
	{
		// Include our template file
		include( TEMPLATE_PATH .'current-ranking.php');
		
		// write cache file
		writeCache('current-ranking', $template);
		$LASTUPDATE = intToTime(0);
		$NEXTUPDATE = intToTime(RANKING_REFRESH_TIME);		
	}	
	$template = str_replace('{:LASTUPDATE:}', $LASTUPDATE, $template);
	$template = str_replace('{:NEXTUPDATE:}', $NEXTUPDATE, $template);		
	#echo $template;	
}

/***************************************************************
 * MY LEADER BOARD
 ***************************************************************/
elseif((strcasecmp((string) $GO, 'my-leaderboard') == 0))
{
	if ($ADD > 0) {
        $LEADERBOARD = $_COOKIE['leaderboard'] != '' ? $_COOKIE['leaderboard'].','.$ADD : $ADD;
        setcookie("leaderboard", (string) $LEADERBOARD, ['expires' => time()+315360000, 'path' => '/', 'domain' => (string) $DOMAIN]);
        // delete after 10 years ;)
        #NOTE: after setting a cookie, you must redirect!
        header("Location: ".$ROOT."?go=my-leaderboard");
        // refresh for cookie
        exit();
    } elseif($REMOVE > 0)
	{
		$LEADERBOARD = explode(',', (string) $_COOKIE['leaderboard']); // get array
		
		// delete "remove"
		foreach($LEADERBOARD as $i => $value) 
		{
			if($value == $REMOVE)
			{
				unset($LEADERBOARD[$i]);
			}
		}
		$LEADERBOARD = implode(',', $LEADERBOARD); // back to string ;)

		setcookie("leaderboard", $LEADERBOARD, ['expires' => time() + 315360000, 'path' => '/', 'domain' => (string) $DOMAIN]); // delete after 10 years ;)
		header("Location: ".$ROOT."?go=my-leaderboard"); // refresh for cookie
		exit();
	}
	# nothing todo -> load from cookie
	$LEADERBOARD = $_COOKIE['leaderboard'] ?? '';
	
	if($PID != 0) // a saved leaderboard
	{
		$LEADER = getLeaderBoardEntries(urldecode((string) $PID)); # query from database
	}
	else
	{
		$LEADER = getLeaderBoardEntries($LEADERBOARD); # query from database
	}

	// Include our template file
	include( TEMPLATE_PATH .'my-leaderboard.php');
}

/***************************************************************
 * SEARCH FOR PLAYERS
 ***************************************************************/
elseif(strcasecmp((string) $GO, 'search') == 0)
{
	$SEARCHVALUE = $_POST["searchvalue"] ?? "0";
	if ($SEARCHVALUE) {
        $searchresults = getSearchResults($SEARCHVALUE);
    }
	include( TEMPLATE_PATH .'search.php');
}

/***************************************************************
 * SHOW TOP TEN - default
 ***************************************************************/
else
{  // show the top ten

	$LASTUPDATE = 0;
	$NEXTUPDATE = 0;
	if(isCached('home'))// already cached!
	{
		$template = getCache('home');
		$LASTUPDATE = intToTime(getLastUpdate( CACHE_PATH .'home.cache' ));
		$NEXTUPDATE = intToTime(getNextUpdate( CACHE_PATH .'home.cache', RANKING_REFRESH_TIME ));
	}
	else
	{
		$topten = getTopTen();
		include( TEMPLATE_PATH .'home.php');

		// write cache file
		writeCache('home', $template);
		$LASTUPDATE = intToTime(0);
		$NEXTUPDATE = intToTime(RANKING_REFRESH_TIME);		
	}	
	$template = str_replace('{:LASTUPDATE:}', $LASTUPDATE, $template);
	$template = str_replace('{:NEXTUPDATE:}', $NEXTUPDATE, $template);		
	
}

// Closing connection
mysqli_close($link);

//processing page END
$time_end = microtime(true);
$time = round($time_end - $time_start,4);

$template = str_replace('{:PROCESSED:}', $time, $template);		

// Echo the template page and quit
echo $template;	
?>