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

function achieved($has, $value)
{
	if ($has == $value) return 'achieved';
	else return 'notachieved';
}

function earned($date)
{
	if ($date > 0)
		return ' (<i>'.date('Y-m-d H:i:s', $date).'</i>)';
}

function getBadgeLevel($value)
{
	for ($LEVEL=3; $LEVEL>=0; $LEVEL--)
	{
		if ($value[$LEVEL][EARNED] > 0)
		{
			return $LEVEL;
		}
		if ($value[$LEVEL][EARNED] == $LEVEL) // both is zero
			return 0;
	}
}

function getRibbonLevel($value)
{
	for ($LEVEL=2; $LEVEL>=0; $LEVEL--)
	{
		if ($value[$LEVEL][EARNED] > 0)
		{
			return $LEVEL;
		}
		if ($value[$LEVEL][EARNED] == $LEVEL) // both is zero
			return 0;
	}
}

function getAwardByPID_and_AWD($PID, $AWD, $LEVEL)
{
	include(ROOT . DS .'queries'. DS .'getAwardByPID_and_AWD.php'); // imports the correct sql statement
	$result = mysqli_query($GLOBALS['link'], $query) or die('Query failed: ' . mysqli_error($GLOBALS['link']));

	$awards = array();
	while ($row = mysqli_fetch_assoc($result))
	{
		$awards[] = $row;
	}
	// Free resultset
	mysqli_free_result($result);
	return $awards;
}

function getAwardByPID_and_AWD_NOLEVEL($PID, $AWD)
{
	include(ROOT . DS .'queries'. DS .'getAwardByPID_and_AWD_NOLEVEL.php'); // imports the correct sql statement
	$result = mysqli_query($GLOBALS['link'], $query) or die('Query failed: ' . mysqli_error($GLOBALS['link']));

	$awards = array();
	while ($row = mysqli_fetch_assoc($result))
	{
		$awards[] = $row;
	}
	// Free resultset
	mysqli_free_result($result);
	return $awards;
}


function getAwardsByPID($PID)
{
	$PlayerAwards = array();
	# grab all badges
	# $PlayerAwards[$awd]
	# $PlayerAwards[$awd][$level]
	# $PlayerAwards[$awd][$level]['earned']
	# $PlayerAwards[$awd][$level]['first']



	$PlayerAwards = array();

	#get badges
	$count = getBadgeCount();
	for ($i=0; $i<$count; $i++)
	{
		$AWD = trim(getBadge($i));
		for ($LEVEL=0; $LEVEL<4; $LEVEL++) // levels!
		{
			$award = getAwardByPID_and_AWD($PID, $AWD, $LEVEL);
			$PlayerAwards[$i][$LEVEL][AWD] = isset($AWD) ? $AWD : 0;
			$PlayerAwards[$i][$LEVEL][LEVEL] = $LEVEL;
			$PlayerAwards[$i][$LEVEL][EARNED] = isset($award[0]['earned']) ? $award[0]['earned'] : 0;
			$PlayerAwards[$i][$LEVEL][FIRST] = isset($award[0]['first']) ? $award[0]['first'] : 0;
			$PlayerAwards[$i][$LEVEL][NAME] = getBadgeByID($AWD);
		}
	}

	#append next after those
	#get Medals
	$oldcount = $count;
	$count = $oldcount+getMedalCount();
	for ($i=$oldcount; $i<$count; $i++)
	{
		$AWD = trim(getMedal($i-$oldcount));
		$LEVEL=0; // levels!
		$award = getAwardByPID_and_AWD_NOLEVEL($PID, $AWD);
		$PlayerAwards[$i][$LEVEL][AWD] = isset($AWD) ? $AWD : 0;
		$PlayerAwards[$i][$LEVEL][LEVEL] = isset($award[0]['level']) ? $award[0]['level'] : 0;
		$PlayerAwards[$i][$LEVEL][EARNED] = isset($award[0]['earned']) ? $award[0]['earned'] : 0;
		$PlayerAwards[$i][$LEVEL][FIRST] = isset($award[0]['first']) ? $award[0]['first'] : 0;
		$PlayerAwards[$i][$LEVEL][NAME] = getMedalByID($AWD);
	}
	#append next after those
	#get Ribbons
	$oldcount = $count;
	$count = $oldcount+getRibbonCount();
	for ($i=$oldcount; $i<$count; $i++)
	{
		$AWD = trim(getRibbon($i-$oldcount));
		$LEVEL=0; // levels!
		$award = getAwardByPID_and_AWD_NOLEVEL($PID, $AWD);
		$PlayerAwards[$i][$LEVEL][AWD] = isset($AWD) ? $AWD : 0;
		$PlayerAwards[$i][$LEVEL][LEVEL] = isset($award[0]['level']) ? $award[0]['level'] : 0;
		$PlayerAwards[$i][$LEVEL][EARNED] = isset($award[0]['earned']) ? $award[0]['earned'] : 0;
		$PlayerAwards[$i][$LEVEL][FIRST] = isset($award[0]['first']) ? $award[0]['first'] : 0;
		$PlayerAwards[$i][$LEVEL][NAME] = getRibbonByID($AWD);
	}

	#append next after those
	#get SFbadges
	$oldcount = $count;
	$count = $oldcount+getSFBadgeCount();
	for ($i=$oldcount; $i<$count; $i++)
	{
		$AWD = trim(getSFBadge($i-$oldcount));
		for ($LEVEL=0; $LEVEL<4; $LEVEL++) // levels!
		{
			$award = getAwardByPID_and_AWD($PID, $AWD, $LEVEL);
			$PlayerAwards[$i][$LEVEL][AWD] = isset($AWD) ? $AWD : 0;
			$PlayerAwards[$i][$LEVEL][LEVEL] = $LEVEL;
			$PlayerAwards[$i][$LEVEL][EARNED] = isset($award[0]['earned']) ? $award[0]['earned'] : 0;
			$PlayerAwards[$i][$LEVEL][FIRST] = isset($award[0]['first']) ? $award[0]['first'] : 0;
			$PlayerAwards[$i][$LEVEL][NAME] = getSFBadgeByID($AWD);
		}
	}

	#append next after those
	#get SFRibbons
	$oldcount = $count;
	$count = $oldcount+getSFRibbonCount();
	for ($i=$oldcount; $i<$count; $i++)
	{
		$AWD = trim(getSFRibbon($i-$oldcount));
		$LEVEL=0; // levels!
		$award = getAwardByPID_and_AWD_NOLEVEL($PID, $AWD);
		$PlayerAwards[$i][$LEVEL][AWD] = isset($AWD) ? $AWD : 0;
		$PlayerAwards[$i][$LEVEL][LEVEL] = isset($award[0]['level']) ? $award[0]['level'] : 0;
		$PlayerAwards[$i][$LEVEL][EARNED] = isset($award[0]['earned']) ? $award[0]['earned'] : 0;
		$PlayerAwards[$i][$LEVEL][FIRST] = isset($award[0]['first']) ? $award[0]['first'] : 0;
		$PlayerAwards[$i][$LEVEL][NAME] = getSFRibbonByID($AWD);
	}

	return $PlayerAwards;
}


# returns the path to the award
#NOT FINISHED YET
function getBadgeStatus($awards, $awardID)
{
	foreach ($awards as $key => $value)
	{
		if ($awards['awd'] == $awardID)
		{
			$ROOT."game-images/awards/front/".$awardID.'_'.$awards['level'].'.png';
		}
		else
			return $ROOT."game-images/awards/locked/".$awardID.'_0.png';
	}
}

?>
