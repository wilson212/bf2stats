<?php
#awards - more complicaded...
include_once(ROOT . DS .'queries'. DS .'getBadgeByID.php');
include_once(ROOT . DS .'queries'. DS .'getMedalByID.php');
include_once(ROOT . DS .'queries'. DS .'getRibbonByID.php');
include_once(ROOT . DS .'queries'. DS .'getSFRibbonsByID.php');
include_once(ROOT . DS .'queries'. DS .'getSFBadgeByID.php');

define('AWD', 0);
define('LEVEL', 1);
define('EARNED', 2);
define('FIRST', 3);
define('NAME', 4);

function achieved($has, $value): string
{
	if ($has == $value) {
        return 'achieved';
    } else {
        return 'notachieved';
    }
}

function earned($date): ?string
{
    if ($date > 0) {
        return ' (<i>'.date('Y-m-d H:i:s', $date).'</i>)';
    }
    return null;
}

function getBadgeLevel($value): ?int
{
	for ($LEVEL=3; $LEVEL>=0; $LEVEL--)
	{
		if ($value[$LEVEL][EARNED] > 0)
		{
			return $LEVEL;
		}
		if ($value[$LEVEL][EARNED] == $LEVEL) {
            // both is zero
            return 0;
        }
	}
    return null;
}

function getRibbonLevel($value): ?int
{
	for ($LEVEL=2; $LEVEL>=0; $LEVEL--)
	{
		if ($value[$LEVEL][EARNED] > 0)
		{
			return $LEVEL;
		}
		if ($value[$LEVEL][EARNED] == $LEVEL) {
            // both is zero
            return 0;
        }
	}
    return null;
}

/**
 * @return list<mixed>
 */
function getAwardByPID_and_AWD($PID, $AWD, $LEVEL): array
{
	global $link;
	include(ROOT . DS .'queries'. DS .'getAwardByPID_and_AWD.php'); // imports the correct sql statement
	$result = mysqli_query($link, $query) or die('Query failed: ' . mysqli_error($link));
	
	$awards = [];
	while ($row = mysqli_fetch_assoc($result)) 
	{
		$awards[] = $row;
	}
	// Free resultset
	mysqli_free_result($result);
	return $awards;
}

/**
 * @return list<mixed>
 */
function getAwardByPID_and_AWD_NOLEVEL($PID, $AWD): array
{
	global $link;
	include(ROOT . DS .'queries'. DS .'getAwardByPID_and_AWD_NOLEVEL.php'); // imports the correct sql statement
	$result = mysqli_query($link, $query) or die('Query failed: ' . mysqli_error($link));
	
	$awards = [];
	while ($row = mysqli_fetch_assoc($result)) 
	{
		$awards[] = $row;
	}
	// Free resultset
	mysqli_free_result($result);
	return $awards;
}


/**
 * @return non-empty-array<int<0, 3>, array{string, mixed, mixed, mixed, mixed}>[]
 */
function getAwardsByPID($PID): array
{
	# grab all badges
	# $PlayerAwards[$awd]
	# $PlayerAwards[$awd][$level]
	# $PlayerAwards[$awd][$level]['earned']
	# $PlayerAwards[$awd][$level]['first']
	


	$PlayerAwards = [];

	#get badges
	$count = getBadgeCount();
	for ($i=0; $i<$count; $i++)
	{
		$AWD = trim((string) getBadge($i));
		for ($LEVEL=0; $LEVEL<4; $LEVEL++) // levels!
		{
			$award = getAwardByPID_and_AWD($PID, $AWD, $LEVEL);
			$PlayerAwards[$i][$LEVEL][AWD] = $AWD ?? 0;
			$PlayerAwards[$i][$LEVEL][LEVEL] = $LEVEL;
			$PlayerAwards[$i][$LEVEL][EARNED] = $award[0]['earned'] ?? 0;
			$PlayerAwards[$i][$LEVEL][FIRST] = $award[0]['first'] ?? 0;
			$PlayerAwards[$i][$LEVEL][NAME] = getBadgeByID($AWD);		
		}
	}
	
	#append next after those
	#get Medals
	$oldcount = $count;
	$count = $oldcount+getMedalCount();
	for ($i=$oldcount; $i<$count; $i++)
	{
		$AWD = trim((string) getMedal($i-$oldcount));
		$LEVEL=0; // levels!
		$award = getAwardByPID_and_AWD_NOLEVEL($PID, $AWD);
		$PlayerAwards[$i][$LEVEL][AWD] = $AWD ?? 0;
		$PlayerAwards[$i][$LEVEL][LEVEL] = $award[0]['level'] ?? 0;
		$PlayerAwards[$i][$LEVEL][EARNED] = $award[0]['earned'] ?? 0;
		$PlayerAwards[$i][$LEVEL][FIRST] = $award[0]['first'] ?? 0;
		$PlayerAwards[$i][$LEVEL][NAME] = getMedalByID($AWD);		
	}	
	#append next after those
	#get Ribbons
	$oldcount = $count;
	$count = $oldcount+getRibbonCount();
	for ($i=$oldcount; $i<$count; $i++)
	{
		$AWD = trim((string) getRibbon($i-$oldcount));
		$LEVEL=0; // levels!
		$award = getAwardByPID_and_AWD_NOLEVEL($PID, $AWD);
		$PlayerAwards[$i][$LEVEL][AWD] = $AWD ?? 0;
		$PlayerAwards[$i][$LEVEL][LEVEL] = $award[0]['level'] ?? 0;
		$PlayerAwards[$i][$LEVEL][EARNED] = $award[0]['earned'] ?? 0;
		$PlayerAwards[$i][$LEVEL][FIRST] = $award[0]['first'] ?? 0;
		$PlayerAwards[$i][$LEVEL][NAME] = getRibbonByID($AWD);		
	}		
	
	#append next after those	
	#get SFbadges
	$oldcount = $count;
	$count = $oldcount+getSFBadgeCount();
	for ($i=$oldcount; $i<$count; $i++)
	{
		$AWD = trim((string) getSFBadge($i-$oldcount));
		for ($LEVEL=0; $LEVEL<4; $LEVEL++) // levels!
		{
			$award = getAwardByPID_and_AWD($PID, $AWD, $LEVEL);
			$PlayerAwards[$i][$LEVEL][AWD] = $AWD ?? 0;
			$PlayerAwards[$i][$LEVEL][LEVEL] = $LEVEL;
			$PlayerAwards[$i][$LEVEL][EARNED] = $award[0]['earned'] ?? 0;
			$PlayerAwards[$i][$LEVEL][FIRST] = $award[0]['first'] ?? 0;
			$PlayerAwards[$i][$LEVEL][NAME] = getSFBadgeByID($AWD);		
		}
	}	
	
	#append next after those
	#get SFRibbons
	$oldcount = $count;
	$count = $oldcount+getSFRibbonCount();
	for ($i=$oldcount; $i<$count; $i++)
	{
		$AWD = trim((string) getSFRibbon($i-$oldcount));
		$LEVEL=0; // levels!
		$award = getAwardByPID_and_AWD_NOLEVEL($PID, $AWD);
		$PlayerAwards[$i][$LEVEL][AWD] = $AWD ?? 0;
		$PlayerAwards[$i][$LEVEL][LEVEL] = $award[0]['level'] ?? 0;
		$PlayerAwards[$i][$LEVEL][EARNED] = $award[0]['earned'] ?? 0;
		$PlayerAwards[$i][$LEVEL][FIRST] = $award[0]['first'] ?? 0;
		$PlayerAwards[$i][$LEVEL][NAME] = getSFRibbonByID($AWD);		
	}			
	
	return $PlayerAwards;
}


# returns the path to the award
#NOT FINISHED YET
function getBadgeStatus(array $awards, string $awardID): ?string
{
	foreach ($awards as $value)
	{
		if ($awards['awd'] != $awardID)
		{
			return $ROOT."game-images/awards/locked/".$awardID.'_0.png';	
		}
	}
    return null;
}

?>