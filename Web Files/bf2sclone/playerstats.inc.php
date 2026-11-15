<?php
/* file based queries */
include_once(ROOT . DS . 'queries'. DS .'getRankByID.php');
include_once(ROOT . DS . 'queries'. DS .'getArmyByID.php');
include_once(ROOT . DS . 'queries'. DS .'getUnlockByID.php');
include_once(ROOT . DS . 'queries'. DS .'getVehicleByID.php');
include_once(ROOT . DS . 'queries'. DS .'getKitByID.php');
include_once(ROOT . DS . 'queries'. DS .'getCountryByCode.php');
include_once(ROOT . DS . 'queries'. DS .'getMapByID.php');
include_once(ROOT . DS . 'queries'. DS .'getUnlockID.php');

/*
| --------------------------------------------------------------
| General player info functions
| --------------------------------------------------------------
*/
function getPIDFromNick($NICK)
{
	global $link;
	include(ROOT . DS . 'queries'. DS . 'getPID.php'); // imports the correct sql statement
	$result = mysqli_query($link, $query) or die('Query failed: ' . mysqli_error($link));	
	$PID = mysqli_fetch_assoc($result);
	 	
	mysqli_free_result($result);
	return $PID['id'];
}

function getRankFromPID($PID)
{
	global $link;
	include(ROOT . DS . 'queries'. DS .'getRankFromPID.php'); // imports the correct sql statement
	$result = mysqli_query($link, $query) or die('Query failed: ' . mysqli_error($link));	
	$Rank = mysqli_fetch_assoc($result);
	
	mysqli_free_result($result);
	return $Rank['rank'];
}

function getNickFromPID($PID)
{
	global $link;
	// Performing SQL query
	include(ROOT . DS . 'queries'. DS .'getNameFromPID.php'); // imports the correct sql statement
	$result = mysqli_query($link, $query) or die('Query failed: ' . mysqli_error($link));
	
	$player = [];
	$player['name'] = 'N/A'; # if player is not found
	while ($row = mysqli_fetch_assoc($result)) {
		$player = $row;
		if ($player['name'] == '') {
            $player['name'] = 'N/A';
        } # if player is not found
	}	 

	// Free resultset
	mysqli_free_result($result);
	return $player['name'];
}

function getPlayerDataFromPID($PID)
{
	global $link;
	// Performing SQL query
	include(ROOT . DS . 'queries'. DS .'getPlayerData.php'); // imports the correct sql statement
	$result = mysqli_query($link, $query) or die('Query failed: ' . mysqli_error($link));
	$player = mysqli_fetch_assoc($result); 

	// Free resultset
	mysqli_free_result($result);
	return $player;
}

/*
| --------------------------------------------------------------
| Favorite enemy & Victims
| --------------------------------------------------------------
*/
/**
 * @return list<mixed>
 */
function getFavouriteVictims($PID): array
{
	global $link;
	include(ROOT . DS . 'queries'. DS .'getVictimsByPID.php'); // imports the correct sql statement
	$result = mysqli_query($link, $query) or die('Query failed: ' . mysqli_error($link));
	
	$players = [];
	while ($row = mysqli_fetch_assoc($result)) 
	{
		$players[] = $row;
	}

	// Free resultset
	mysqli_free_result($result);
	return $players;
}

/**
 * @return list<mixed>
 */
function getFavouriteEnemies($PID): array
{
	global $link;
	include(ROOT . DS . 'queries'. DS .'getEnemiesByPID.php'); // imports the correct sql statement
	$result = mysqli_query($link, $query) or die('Query failed: ' . mysqli_error($link));
	
	$players = [];
	while ($row = mysqli_fetch_assoc($result)) 
	{
		$players[] = $row;
	}
	// Free resultset
	mysqli_free_result($result);
	return $players;
}

/*
| --------------------------------------------------------------
| Army data & Summary
| --------------------------------------------------------------
*/
/**
 * @return list<mixed>
 */
function getArmyData($PID): array
{
	global $link;
	include(ROOT . DS . 'queries'. DS .'getArmiesByPID.php'); // imports the correct sql statement
	$result = mysqli_query($link, $query) or die('Query failed: ' . mysqli_error($link));
	
	$armies = [];
	while ($row = mysqli_fetch_assoc($result)) 
	{
		$armies[] = $row;
	}

	// Free resultset
	mysqli_free_result($result);
	return $armies;
}

function getArmySummaries($armies)
{
	global $link;
	$armyCount = getArmyCount();
	
	$summary['total']['time'] = 0;
	$summary['total']['win'] = 0;
	$summary['total']['loss'] = 0;
	$summary['total']['score'] = 0;
	$summary['total']['best'] = 0;
	$summary['total']['worst'] = 0;
	$summary['total']['brnd'] = 0;
	
	for ($i=0; $i<$armyCount; $i++)
	{
		$summary['total']['time'] 	+= $armies[0]['time'.$i];
		$summary['total']['win'] 	+= $armies[0]['win'.$i];
		$summary['total']['loss'] 	+= $armies[0]['loss'.$i];
		$summary['total']['score'] 	+= $armies[0]['score'.$i];
		$summary['total']['best'] 	+= $armies[0]['best'.$i];
		$summary['total']['worst'] 	+= $armies[0]['worst'.$i];
		$summary['total']['brnd'] 	+= $armies[0]['brnd'.$i];
	}
	
	$summary['average']['time'] 	= round($summary['total']['time'] / $armyCount, 2);
	$summary['average']['win'] 		= round($summary['total']['win'] / $armyCount, 2);
	$summary['average']['loss'] 	= round($summary['total']['loss'] / $armyCount, 2);
	$summary['average']['score'] 	= round($summary['total']['score'] / $armyCount, 2);
	$summary['average']['best'] 	= round($summary['total']['best'] / $armyCount, 2);
	$summary['average']['worst'] 	= round($summary['total']['worst'] / $armyCount, 2);
	$summary['average']['brnd'] 	= round($summary['total']['brnd'] / $armyCount, 2);
	
	return $summary;
}

/*
| --------------------------------------------------------------
| Vehicle data & Summary
| --------------------------------------------------------------
*/
/**
 * @return list<mixed>
 */
function getVehicleData($PID): array
{
	global $link;
	include(ROOT . DS . 'queries'. DS .'getVehicleDataByID.php'); // imports the correct sql statement
	$result = mysqli_query($link, $query) or die('Query failed: ' . mysqli_error($link));
	
	$vehicles = [];
	while ($row = mysqli_fetch_assoc($result)) 
	{
		$vehicles[] = $row;
	}
	// Free resultset
	mysqli_free_result($result);
	return $vehicles;
}

function getVehicleSummaries($vehicles)
{
	$vehicleCount = getVehicleCount();
	
	$summary['total']['time'] = 0;
	$summary['total']['kills'] = 0;
	$summary['total']['deaths'] = 0;
	$summary['total']['ratio'] = 0;
	$summary['total']['rk'] = 0;
	
	for ($i=0; $i<$vehicleCount; $i++)
	{
		$summary['total']['time'] 	+= $vehicles[0]['time'.$i];
		$summary['total']['kills'] 	+= $vehicles[0]['kills'.$i];
		$summary['total']['deaths'] += $vehicles[0]['deaths'.$i];
		$summary['total']['rk'] 	+= $vehicles[0]['rk'.$i];
	}
	
	$summary['average']['time'] 	= round($summary['total']['time'] / $vehicleCount, 2);
	$summary['average']['kills'] 	= round($summary['total']['kills'] / $vehicleCount, 2);
	$summary['average']['deaths'] 	= round($summary['total']['deaths'] / $vehicleCount, 2);

	if ($summary['total']['kills']) {
        $summary['average']['ratio'] = round(($summary['total']['kills']/$summary['total']['kills']) / $vehicleCount, 2);
    } else {
        $summary['average']['ratio'] = ($summary['total']['kills'] / $vehicleCount);
    }

	$summary['average']['rk'] = round($summary['total']['rk'] / $vehicleCount, 2);

	return $summary;
}

/*
| --------------------------------------------------------------
| Weapon & Equipment data & Summary
| --------------------------------------------------------------
*/
function getWeaponData($PID, array $player): array
{
	global $link;
	include(ROOT . DS . 'queries'. DS .'getWeaponDataByID.php'); // imports the correct sql statement
	$result = mysqli_query($link, $query) or die('Query failed: ' . mysqli_error($link));
	
	$SQLweapons = [];
	$weapons = [];
	while ($row = mysqli_fetch_assoc($result)) 
	{
		$SQLweapons[] = $row;
	}
	
	$weapons[0]['name'] = 'Assault Rifles';
	$weapons[1]['name'] = 'Grenade Launcher Attachment';
	$weapons[2]['name'] = 'Carbines';
	$weapons[3]['name'] = 'Light Machine Guns';
	$weapons[4]['name'] = 'Sniper Rifles';
	$weapons[5]['name'] = 'Pistols';
	$weapons[6]['name'] = 'AT/AA';
	$weapons[7]['name'] = 'Submachine Guns';
	$weapons[8]['name'] = 'Shotguns';

	for ($i=0; $i<=8; $i++)	
	{
		$weapons[$i]['time'] 	= $SQLweapons[0]['time'.$i];
		$weapons[$i]['kills'] 	= $SQLweapons[0]['kills'.$i];

		if ($SQLweapons[0]['kills'.$i]) {
            $weapons[$i]['totalkills'] = (100 * round($SQLweapons[0]['kills'.$i] / $player['kills'], 2));
        } else {
            $weapons[$i]['totalkills'] = 0;
        }
		$weapons[$i]['deaths'] = $SQLweapons[0]['deaths'.$i];
		$weapons[$i]['fired'] = $SQLweapons[0]['fired'.$i];
		$weapons[$i]['hit'] = $SQLweapons[0]['hit'.$i];
	}
	
	$weapons[9]['name'] = 'Knife';
	$weapons[9]['time'] = $SQLweapons[0]['knifetime'];
	$weapons[9]['kills'] = $SQLweapons[0]['knifekills'];
	if ($SQLweapons[0]['knifekills']) {
        $weapons[9]['totalkills'] = (100 * round($SQLweapons[0]['knifekills'] / $player['kills'], 2));
    } else {
        $weapons[9]['totalkills'] = 0;
    }	
	$weapons[9]['deaths'] = $SQLweapons[0]['knifedeaths'];
	$weapons[9]['fired'] = $SQLweapons[0]['knifefired'];
	$weapons[9]['hit'] = $SQLweapons[0]['knifehit'];			
	#$weapons[9]['deployed'] = 0;
	
	$weapons[10]['name'] = 'Defibrillator';
	$weapons[10]['time'] = $SQLweapons[0]['shockpadtime'];
	$weapons[10]['kills'] = $SQLweapons[0]['shockpadkills'];
	if ($SQLweapons[0]['shockpadkills']) {
        $weapons[10]['totalkills'] = (100 * round($SQLweapons[0]['shockpadkills'] / $player['kills'], 2));
    } else {
        $weapons[10]['totalkills'] = 0;
    }	
	$weapons[10]['deaths'] = $SQLweapons[0]['shockpaddeaths'];
	$weapons[10]['fired'] = $SQLweapons[0]['shockpadfired'];
	$weapons[10]['hit'] = $SQLweapons[0]['shockpadhit'];		
	#$weapons[10]['deployed'] = 0;
	
	$weapons[11]['name'] = 'Claymore';
	$weapons[11]['time'] = $SQLweapons[0]['claymoretime'];
	$weapons[11]['kills'] = $SQLweapons[0]['claymorekills'];
	if ($SQLweapons[0]['claymorekills']) {
        $weapons[11]['totalkills'] = (100 * round($SQLweapons[0]['claymorekills'] / $player['kills'], 2));
    } else {
        $weapons[11]['totalkills'] = 0;
    }		
	$weapons[11]['deaths'] = $SQLweapons[0]['claymoredeaths'];
	$weapons[11]['fired'] = $SQLweapons[0]['claymorefired'];
	$weapons[11]['hit'] = $SQLweapons[0]['claymorehit'];	
	#$weapons[11]['deployed'] = 0;
		
	$weapons[12]['name'] = 'Hand Grenade';
	$weapons[12]['time'] = $SQLweapons[0]['handgrenadetime'];
	$weapons[12]['kills'] = $SQLweapons[0]['handgrenadekills'];
	if ($SQLweapons[0]['handgrenadekills']) {
        $weapons[12]['totalkills'] = (100 * round($SQLweapons[0]['handgrenadekills'] / $player['kills'], 2));
    } else {
        $weapons[12]['totalkills'] = 0;
    }		
	$weapons[12]['deaths'] = $SQLweapons[0]['handgrenadedeaths'];
	$weapons[12]['fired'] = $SQLweapons[0]['handgrenadefired'];
	$weapons[12]['hit'] = $SQLweapons[0]['handgrenadehit'];	
	#$weapons[12]['deployed'] = 0;
	
	$weapons[13]['name'] = 'AT Mine';
	$weapons[13]['time'] = $SQLweapons[0]['atminetime'];
	$weapons[13]['kills'] = $SQLweapons[0]['atminekills'];
	if ($SQLweapons[0]['atminekills']) {
        $weapons[13]['totalkills'] = (100 * round($SQLweapons[0]['atminekills'] / $player['kills'], 2));
    } else {
        $weapons[13]['totalkills'] = 0;
    }		
	$weapons[13]['deaths'] = $SQLweapons[0]['atminedeaths'];
	$weapons[13]['fired'] = $SQLweapons[0]['atminefired'];
	$weapons[13]['hit'] = $SQLweapons[0]['atminehit'];		
	#$weapons[13]['deployed'] = 0;

	$weapons[14]['name'] = 'C4';
	$weapons[14]['time'] = $SQLweapons[0]['c4time'];
	$weapons[14]['kills'] = $SQLweapons[0]['c4kills'];
	if ($SQLweapons[0]['c4kills']) {
        $weapons[14]['totalkills'] = (100 * round($SQLweapons[0]['c4kills'] / $player['kills'], 2));
    } else {
        $weapons[14]['totalkills'] = 0;
    }		
	$weapons[14]['deaths'] = $SQLweapons[0]['c4deaths'];
	$weapons[14]['fired'] = $SQLweapons[0]['c4fired'];
	$weapons[14]['hit'] = $SQLweapons[0]['c4hit'];	
	$weapons[14]['fired'] = 0;
	
	$weapons[15]['name'] = 'Tactical (Flash, Smoke)';
	$weapons[15]['time'] = $SQLweapons[0]['tacticaltime'];
	$weapons[15]['kills'] = 0;
	$weapons[15]['deaths'] = 0;
	$weapons[15]['fired'] = $SQLweapons[0]['tacticaldeployed'];
	$weapons[15]['totalkills'] = 0;	

	$weapons[16]['name'] = 'Grappling Hook';
	$weapons[16]['time'] = $SQLweapons[0]['grapplinghooktime'];
	$weapons[16]['kills'] = 0;
	$weapons[16]['deaths'] = $SQLweapons[0]['grapplinghookdeaths'];
	$weapons[16]['fired'] = $SQLweapons[0]['grapplinghookdeployed'];
	$weapons[16]['totalkills'] = 0;	

	$weapons[17]['name'] = 'Zipline';
	$weapons[17]['time'] = $SQLweapons[0]['ziplinetime'];
	$weapons[17]['kills'] = 0;
	$weapons[17]['deaths'] = $SQLweapons[0]['ziplinedeaths'];
	$weapons[17]['fired'] = $SQLweapons[0]['ziplinedeployed'];
	$weapons[17]['totalkills'] = 0;	
	
	
	// Free resultset
	mysqli_free_result($result);

	return $weapons;
}

function getWeaponSummary($weapons, array $player): array
{
	$summary = [];
	$summary['total'] = [];
	$summary['average'] = [];
	$summary['total']['time'] = 0;
	$summary['total']['kills'] = 0;
	$summary['total']['totalkills'] = 0;
	$summary['total']['deaths'] = 0;
	$summary['total']['ratio'] = 0; 
	$summary['total']['acc'] = 0; 
	$summary['total']['fired'] = 0;
	$summary['total']['hit'] = 0;
	
	for ($i=0; $i<=12; $i++)	
	{
		$summary['total']['time'] 	+= $weapons[$i]['time'];
		$summary['total']['kills'] 	+= $weapons[$i]['kills'];
		if ($weapons[$i]['kills']) {
            $summary['total']['totalkills'] += $weapons[$i]['kills'];
        }
		
		$summary['total']['deaths'] += $weapons[$i]['deaths'];
		$summary['total']['fired'] 	+= $weapons[$i]['fired'];
		$summary['total']['hit'] 	+= $weapons[$i]['hit'];

		if ($weapons[$i]['deaths']) {
            $summary['total']['ratio'] += ($weapons[$i]['kills'] / $weapons[$i]['deaths']);
        } else {
            $summary['total']['ratio'] += $weapons[$i]['kills'];
        }
			
		if ($weapons[$i]['hit']) {
            $summary['total']['acc'] += ($weapons[$i]['fired'] / $weapons[$i]['hit']);
        }			
	}

	if ($summary['total']['totalkills'] > 0) {
        $summary['total']['totalkills'] = (100 * ($summary['total']['totalkills'] / $player['kills']));
    } else {
        $summary['total']['totalkills'] = ($player['kills']);
    }

	$summary['average']['time'] 	= ($summary['total']['time'] / 13);
	$summary['average']['kills'] 	= ($summary['total']['kills'] / 13);
	$summary['average']['deaths'] 	= ($summary['total']['deaths'] / 13);
	$summary['average']['ratio'] 	= ($summary['total']['ratio'] / 13);
	$summary['average']['acc'] 		= ($summary['total']['acc'] / 13);
	$summary['average']['fired'] 	= ($summary['total']['fired'] / 13);
	$summary['average']['hit'] 		= ($summary['total']['hit'] / 13);
	
	return $summary;
}

function getWeaponID($weapons, $weaponname)
{
	foreach ($weapons as $key => $value)
	{
		if (strcasecmp((string) $value, (string) $weaponname) == 0) {
            return $key;
        } // same but not case sensitive!
	}
    return null;
}

/**
 * @return mixed[]
 */
function getUnlocksByPID($PID): array
{
	global $link;
	include(ROOT . DS . 'queries'. DS .'getUnlocksByPID.php'); // imports the correct sql statement
	$result = mysqli_query($link, $query) or die('Query failed: ' . mysqli_error($link));
	
	$unlocks = [];
	while ($row = mysqli_fetch_assoc($result)) 
	{
		$unlocks[$row['kit']] = $row['state'];
	}

	mysqli_free_result($result);
	return $unlocks;
}

function getEquipmentSummary($weapons, array $player): array
{
	$summary = [];
	$summary['total'] = [];
	$summary['average'] = [];
	$summary['total']['time'] = 0;
	$summary['total']['kills'] = 0;
	$summary['total']['totalkills'] = 0;
	$summary['total']['deaths'] = 0;
	$summary['total']['ratio'] = 0; 
	$summary['total']['fired'] = 0; 
	
	for ($i=9; $i<=16; $i++)	// equipment = 9-16
	{
		$summary['total']['time'] 	+= $weapons[$i]['time'];
		$summary['total']['kills'] 	+= $weapons[$i]['kills'];

		if ($weapons[$i]['kills']) {
            $summary['total']['totalkills'] += $weapons[$i]['kills'];
        }
		if ($summary['total']['deaths']) {
            $summary['total']['ratio'] = $summary['total']['kills']/$summary['total']['deaths'];
        }

		$summary['total']['deaths'] += $weapons[$i]['deaths'];
		$summary['total']['fired'] 	+= $weapons[$i]['fired'];
	}
	
	if ($summary['total']['totalkills'] > 0) {
        $summary['total']['totalkills'] = (100 * ($summary['total']['totalkills'] / $player['kills']));
    }
	
	$summary['average']['time'] 	= ($summary['total']['time'] / 13);
	$summary['average']['kills'] 	= ($summary['total']['kills'] / 13);
	$summary['average']['deaths'] 	= ($summary['total']['deaths'] / 13);
	$summary['average']['ratio'] 	= ($summary['total']['ratio'] / 13);
	$summary['average']['fired'] 	= ($summary['total']['fired'] / 13);
	
	return $summary;
}

/*
| --------------------------------------------------------------
| Kit data & Summary
| --------------------------------------------------------------
*/
/**
 * @return list<mixed>
 */
function getKitData($PID): array
{
	global $link;
	include(ROOT . DS . 'queries'. DS .'getKitDataByPID.php'); // imports the correct sql statement
	$result = mysqli_query($link, $query) or die('Query failed: ' . mysqli_error($link));
	
	$kits = [];
	while ($row = mysqli_fetch_assoc($result)) 
	{
		$kits[] = $row;
	}
	// Free resultset
	mysqli_free_result($result);
	return $kits;
}

function getKitSummary($kits, array $player)
{
	$summary['total']['time'] = 0;
	$summary['total']['kills'] = 0;
	$summary['total']['deaths'] = 0;
	$summary['total']['totalkills'] = 0; 
	
	$count = getKitCount();
	for ($i=0; $i < $count; $i++)
	{
		$summary['total']['time'] 	+= $kits[0]['time'.$i];
		$summary['total']['kills'] 	+= $kits[0]['kills'.$i];
		$summary['total']['totalkills'] += $kits[0]['kills'.$i];
		$summary['total']['deaths'] += $kits[0]['deaths'.$i];
	}
	if ($summary['total']['totalkills']) {
        $summary['total']['totalkills'] = ($player['kills'] / $summary['total']['totalkills']);
    } else {
        $summary['total']['totalkills'] = $player['kills'];
    }
	
	$summary['average']['time'] 	= ($summary['total']['time'] / $count);
	$summary['average']['kills'] 	= ($summary['total']['kills'] / $count);
	$summary['average']['deaths'] 	= ($summary['total']['deaths'] / $count);

	if ($summary['total']['deaths']) {
        $summary['average']['ratio'] = ($summary['total']['kills'] / $summary['total']['deaths']);
    } else {
        $summary['average']['ratio'] = ($summary['total']['kills'] / 13);
    }
	return $summary;	
}

/*
| --------------------------------------------------------------
| Map data & Summary
| --------------------------------------------------------------
|
| NOTE: you will only see the maps the player has already played 
|	-> good for booster packs etc...
|
*/
/**
 * @return list<mixed>
 */
function getMapData($PID): array
{
	global $link;
	include(ROOT . DS . 'queries'. DS .'getMapDataByPID.php'); // imports the correct sql statement
	$result = mysqli_query($link, $query) or die('Query failed: ' . mysqli_error($link));
	
	$maps = [];
	while ($row = mysqli_fetch_assoc($result)) 
	{
		$maps[] = $row;
	}
	// Free resultset
	mysqli_free_result($result);
	return $maps;
}

function getMapSummary($maps): array
{
	$summary = [];
	$summary['total']['time'] = 0;
	$summary['total']['win'] = 0;
	$summary['total']['loss'] = 0;
	$summary['total']['ratio'] = 0;
	$summary['average']['time'] = 0;
	$summary['average']['win'] = 0;
	$summary['average']['loss'] = 0;
	$summary['average']['br'] = 0;

	$count = count($maps);
	for ($i=0; $i < $count; $i++)
	{
		$summary['total']['time'] 	+= $maps[$i]['time'];
		$summary['total']['win'] 	+= $maps[$i]['win'];
		$summary['total']['loss'] 	+= $maps[$i]['loss'];
		if ($maps[$i]['loss'])
		{
			$summary['total']['ratio'] += ($maps[$i]['win'] / $maps[$i]['loss']);
		}
		else
		{
			$summary['total']['ratio'] += $maps[$i]['win'];
		}

		$summary['average']['br'] += $maps[$i]['best'];	
	}
	$summary['average']['time'] 	= $summary['total']['time'] / $count;
	$summary['average']['loss'] 	= round($summary['total']['loss'] / $count, 2);
	$summary['average']['br'] 		= round($summary['average']['br'] / $count, 0);
	$summary['average']['ratio'] 	= round($summary['total']['ratio'] / $count, 0);
	return $summary;
}

/*
| --------------------------------------------------------------
| Player Favorites function
| --------------------------------------------------------------
*/
function getPlayerFavorites($weapons, $vehicles, $kits, $armies, $maps, $theatres): array
{
	$summary = [
		'weapon' => 0,
		'equipment' => 0,
		'vehicle' => 0,
		'kit' => 0,
		'army' => 0,
		'map' => 0,
		'theatre' => 0
	];

	// Fav weapon
	$max = -1;
	for ($i=0; $i < 11; $i++)
	{
		if ($weapons[$i]['time'] > $max)
		{
			$summary['weapon'] = $i;
			$max = $weapons[$i]['time'];
		}
	}
	if (($weapons[11]['time'] + $weapons[13]['time'] + $weapons[14]['time']) > $max)
	{
		$summary['weapon'] = 11;
		$max = ($weapons[11]['time'] + $weapons[13]['time'] + $weapons[14]['time']);
	}
	if ($weapons[12]['time'] > $max)
	{
		$summary['weapon'] = 12;
	}
	
	// Fav equipment
	$max = -1;
	for ($i=9; $i <= 17; $i++)
	{
		if ($weapons[$i]['time'] > $max)
		{
			$summary['equipment'] = $i;
			$max = $weapons[$i]['time'];
		}
	}

	// Fave kit
	$max = -1;
	$count = getKitCount();
	for ($i=0; $i < $count; $i++)
	{
		if ($kits[0]['time'.$i] > $max)
		{
			$summary['kit'] = $i;
			$max = $kits[0]['time'.$i];
		}
	}

	// Fav vehicle
	$max = -1;
	$count = getVehicleCount();
	for ($i=0; $i < $count; $i++)
	{
		if ($vehicles[0]['time'.$i] > $max)
		{
			$summary['vehicle'] = $i;
			$max = $vehicles[0]['time'.$i];
		}
	}
	
	// Fav army
	$max = -1;
	$count = getArmyCount();
	for ($i=0; $i <= $count; $i++)
	{
		if ($armies[0]['time'.$i] > $max)
		{
			$summary['army'] = $i;
			$max = $armies[0]['time'.$i];
		}
	}
	
	// Fav Map
	$max = -1;
	foreach($maps as $map)
	{
		if ($map['time'] > $max)
		{
			$summary['map'] = $map['mapid'];
			$max = $map['time'];
		}
	}
	
	// Fav Theatre
	$max = -1;
	foreach($theatres as $t)
	{
		if ($t['time'] >= $max)
		{
			$summary['theatre'] = $t['name'];
			$max = $t['time'];
		}
	}
	
	return $summary;
}

/*
| --------------------------------------------------------------
| Old Favorite Functions (not used, but convienient)
| --------------------------------------------------------------
*/
function getFavouriteMap($PID)
{
	global $link;
	include(ROOT . DS . 'queries'. DS .'getFavMap.php'); // imports the correct sql statement
	$result = mysqli_query($link, $query) or die('Query failed: ' . mysqli_error($link));
	
	$favmap = [];
	while ($row = mysqli_fetch_assoc($result)) 
	{
		$favmap[] = $row;
	}

	// Free resultset
	mysqli_free_result($result);
	return $favmap[0]['mapid'];
}

function getFavouriteKit($PID): string|array
{
	global $link;
	include(ROOT . DS . 'queries'. DS .'getFavKit.php'); // imports the correct sql statement
	$result = mysqli_query($link, $query) or die('Query failed: ' . mysqli_error($link));
	$kits = mysqli_fetch_row($result);

	arsort($kits);
	$kit = str_replace('time', '', key($kits));
	
	// Free resultset
	mysqli_free_result($result);
	return $kit;
}

function getFavouriteArmy($PID): string|array
{
	global $link;
	include(ROOT . DS . 'queries'. DS .'getFavArmy.php'); // imports the correct sql statement
	$result = mysqli_query($link, $query) or die('Query failed: ' . mysqli_error($link));
	$arms = mysqli_fetch_row($result);
	
	arsort($arms);
	$arm = str_replace('time', '', key($arms));
	
	// Free resultset
	mysqli_free_result($result);
	return $arm;
}

function getFavouriteVehicle($PID): string|array
{
	global $link;
	include(ROOT . DS . 'queries'. DS .'getFavVehicle.php'); // imports the correct sql statement
	$result = mysqli_query($link, $query) or die('Query failed: ' . mysqli_error($link));
	$vs = mysqli_fetch_row($result);
	
	arsort($vs);
	$v = str_replace('time', '', key($vs));
	
	// Free resultset
	mysqli_free_result($result);
	return $v;
}

/*
| --------------------------------------------------------------
| Next Ranks Functions
| --------------------------------------------------------------
*/
/**
 * @return array{rank: mixed, title: mixed, rank_points: mixed, points_needed: (float | int), percent: (100 | float), days: mixed, time_straight: mixed}[]
 */
function getNextRankInfo($PID): array
{
	// Get player data
	$player = getPlayerDataFromPID($PID);
	
	// Read the lines from the ranks list,a dn assign a key to each rank
	$lines  = file( ROOT . DS . 'queries'. DS .'ranks.list' );
	foreach($lines as $key => $value)
	{
		$rank[$key] = $value;
	}
	unset($lines);
	
	// Read the lines from the ranks points list to assign needed points for each rank
	$lines  = file( ROOT . DS . 'queries'. DS .'rank_points.list' );
	foreach($lines as $key => $value)
	{
		$points[$key] = $value;
	}
	unset($lines);
	
	// Lets get our SPM, very important
	$SPM = round(($player['score'] / intToMins($player['time'])), 1);
	
	// Init a return array
	$return = [];
	
	// Include the requirements for special ranks, and get the next 3
	include(ROOT . DS . 'queries'. DS .'nextRankReqs.php' );
	foreach(getNextRanks($PID, $player['rank'], 3) as $id)
	{
		// Next rank
		$return[] = [
			'rank' => $id,
			'title' => $rank[$id], 
			'rank_points' => $points[$id],
			'points_needed' => $points[$id] - $player['score'],
			'percent' => @round(($player['score'] / $points[$id]) * 100, 2),
			'days' => getNextRankDayCount($player['joined'], $player['lastonline'], $player['score'], $points[$id]),
			'time_straight' => getNextRankTime($player['score'], $points[$id], $SPM)
		];
	}
	
	// Make sure our percents are not over 100!
	foreach($return as $key => $value)
	{
		if ($value['percent'] > 100) {
            $return[$key]['percent'] = 100;
        }
	}
	return $return;
}

function getNextRankTime($score, $points_needed, $spm): string
{
	
	$temp = ($points_needed - $score) / ($spm  / 60);
	if ($temp < 0) {
        return "0 Seconds";
    }
	
	// Convert into a fancy little time
	$temp = intToTime($temp, 0, 0, 0);
	
	// Explode the time
	$time = explode(':', (string) $temp);
	
	// Hour corrections
	$return = '';
	if($time[0] > 0)
	{
		$return .= ($time[0] > 1) ? $time[0] .' Hours, and ' : $time[0] .' Hour, and ';
	}
	if($time[1] > 0)
	{
		$return .= ($time[1] > 1) ? $time[1] .' Minutes ' : $time[1] .' Minute ';
	}
	
	return $return;
}

function getNextRankDayCount($joined, $last, $score, $points_needed): float|int
{
	$temp = $last - $joined;
	$days = round(($temp / 86400), 0);
	
	// Score Per Day
	$spd = @round(($score / $days), 0);
	
	// Get how many points you need
	$needs = $points_needed - $score;
	$total = @round(($needs / $spd), 0);
	return ($total > 0) ? $total : 0;	
}
?>