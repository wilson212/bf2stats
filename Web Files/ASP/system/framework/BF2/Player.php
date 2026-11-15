<?php
/**
 * BF2Statistics ASP Framework
 *
 * Author:       Steven Wilson
 * Copyright:    Copyright (c) 2006-2021, BF2statistics.com
 * License:      GNU GPL v3
 *
 */
namespace System\BF2;
use ArrayAccess;
use Stringable;
use ArgumentOutOfRangeException;
use System\AwardData;
use System\StatsData;
use System\Collections\Dictionary;
use System\TimeHelper;

/**
 * Class Player
 * @package System\BF2
 */
class Player implements ArrayAccess, Stringable
{
    /**
     * @var string A regular expression for BF2 player name validation or sanitization
     *
     * @example $sanitized = preg_replace("/[^". Player::NAME_REGEX ."]/" '', trim($nick));
     */
    const NAME_REGEX = 'A-Za-z0-9_\.<>=\-\s\@\{\}\*\|\[\]\(\)';
    /**
     * @var int The players PID
     */
    public $id = 0;
    /**
     * @var string The players name
     */
    public $name = '';
    /**
     * @var int The players rank at the end of the round
     */
    public $rank = 0;
    /**
     * @var string The players IP Address
     */
    public $ipAddress = '';
    /**
     * @var bool Specifies if this player is an AI controlled player
     */
    public $isAi = false;
    /**
     * @var int Indicates the players overall score at the end of the round
     */
    public $roundScore = 0;
    /**
     * @var int Indicates the players round time played in seconds
     */
    public $roundTime = 0;
    /**
     * @var int Indicates the players command score at the end of the round
     */
    public $commandScore = 0;
    /**
     * @var int Indicates the players skill score at the end of the round
     */
    public $skillScore = 0;
    /**
     * @var int Indicates the players team score at the end of the round
     */
    public $teamScore = 0;
    /**
     * @var int Indicates the Army ID this player was playing as
     */
    public $armyId = 0;
    /**
     * @var int Indicates the Team the player was on
     */
    public $team = 0;
    /**
     * @var bool Returns whether or not the player was present at the end of the round
     */
    public $completedRound = false;
    /**
     * @var int Returns the Time played as a Commander this round
     */
    public $cmdTime = 0;
    /**
     * @var int Returns the Time played as a Squad Leader this round
     */
    public $sqlTime = 0;
    /**
     * @var int Returns the Time played as a Squad Member this round
     */
    public $sqmTime = 0;
    /**
     * @var int Returns the Time played as a Lone Wolf this round
     */
    public $lwTime = 0;
    /**
     * @var int Indicates the number of times this player was kicked from the server
     */
    public $timesKicked = 0;
    /**
     * @var int Indicates the number of times this player was banned
     */
    public $timesBanned = 0;
    /**
     * @var array A list of players that were killed by this player [VictimPid => KillCount]
     */
    public $victims = [];
    /**
     * @var ObjectStat[] An array of the players weapon data and scores
     */
    public $weaponData = [];
    /**
     * @var ObjectStat[] An array of the players kit data and scores
     */
    public $kitData = [];
    /**
     * @var ObjectStat[] An array of the players vehicle data and scores
     */
    public $vehicleData = [];
    /**
     * @var int[] An array containing the seconds played as each army by ID
     */
    public $timeAsArmy = [];
    /**
     * @var int The time the player spent in a parachute
     */
    public $timeParachute = 0;
    /**
     * @var int
     */
    public $kills = 0;
    /**
     * @var int
     */
    public $deaths = 0;
    /**
     * @var int
     */
    public $suicides = 0;
    /**
     * @var int
     */
    public $killStreak = 0;
    /**
     * @var int
     */
    public $deathStreak = 0;
    /**
     * @var int
     */
    public $heals = 0;
    /**
     * @var int
     */
    public $revives = 0;
    /**
     * @var int
     */
    public $resupplies = 0;
    /**
     * @var int
     */
    public $repairs = 0;
    /**
     * @var int
     */
    public $flagCaptures = 0;
    /**
     * @var int
     */
    public $flagCaptureAssists = 0;
    /**
     * @var int
     */
    public $flagNeutralizes= 0;
    /**
     * @var int
     */
    public $flagNeutralizeAssists = 0;
    /**
     * @var int
     */
    public $flagDefends = 0;
    /**
     * @var int
     */
    public $damageAssists = 0;
    /**
     * @var int
     */
    public $targetAssists = 0;
    /**
     * @var int
     */
    public $driverSpecials = 0;
    /**
     * @var int
     */
    public $teamKills = 0;
    /**
     * @var int
     */
    public $teamDamage = 0;
    /**
     * @var int
     */
    public $teamVehicleDamage = 0;
    /**
     * @var array [Id => Level]
     */
    public $earnedAwards = [];
    public $timeFormatted = '';
    /**
     * @var float
     */
    public $scorePerMin = 0.00;
    /**
     * Player constructor.
     *
     * Award data is expected to be loaded already before constructing!
     *
     * @param Dictionary $playerData the player data array from the Snapshot
     */
    public function __construct(Dictionary $playerData)
    {
        /**
         * We use a dictionary here, so that we get a detailed exception
         * thrown if a key does not exist! Helpful for debugging and
         * validating that there are no missing array keys from the snapshot
         */
        $this->id = (int)$playerData['pID'];
        $this->name = preg_replace("/[^". Player::NAME_REGEX ."]/", '', trim((string) $playerData['name']));
        $this->rank = (int)$playerData['rank'];
        $this->roundScore = (int)$playerData['rs'];
        $this->roundTime = (int)$playerData['ctime'];
        $this->commandScore = (int)$playerData['cs'];
        $this->skillScore = (int)$playerData['ss'];
        $this->teamScore = (int)$playerData['ts'];
        $this->armyId = (int)$playerData['a'];
        $this->team = (int)$playerData['t'];
        $this->completedRound = ((int)$playerData['c']) == 1;
        $this->cmdTime = (int)$playerData['tco'];
        $this->sqlTime = (int)$playerData['tsl'];
        $this->sqmTime = (int)$playerData['tsm'];
        $this->lwTime = (int)$playerData['tlw'];
        $this->timesKicked = (int)$playerData['kck'];
        $this->timesBanned = (int)$playerData['ban'];
        $this->isAi = ((int)$playerData['ai']) == 1;
        $this->ipAddress = preg_replace("/[^A-Za-z0-9.:]/", '', (string) $playerData['ip']);

        // Sometimes Squad times are negative.. idk why, but we need to fix that here
        if ($this->sqlTime < 0) {
            $this->sqlTime = 0;
        }
        if ($this->sqmTime < 0) {
            $this->sqmTime = 0;
        }
        if ($this->lwTime < 0) {
            $this->lwTime = 0;
        }

        $this->kills = (int)$playerData["kills"];
        $this->deaths = (int)$playerData["deaths"];
        $this->suicides = (int)$playerData["su"];
        $this->killStreak = (int)$playerData["ks"];
        $this->deathStreak = (int)$playerData["ds"];
        $this->heals = (int)$playerData["he"];
        $this->revives = (int)$playerData["rev"];
        $this->repairs = (int)$playerData["rep"];
        $this->resupplies = (int)$playerData["rsp"];
        $this->flagCaptures = (int)$playerData["cpc"];
        $this->flagCaptureAssists = (int)$playerData["cpa"];
        $this->flagNeutralizes = (int)$playerData->getValueOrDefault("cpn", 0);
        $this->flagNeutralizeAssists = (int)$playerData->getValueOrDefault("cpna", 0);
        $this->flagDefends = (int)$playerData["cpd"];
        $this->damageAssists = (int)$playerData["ka"];
        $this->targetAssists = (int)$playerData["tre"];
        $this->driverSpecials = (int)$playerData["drs"];
        $this->teamKills = (int)$playerData["tmkl"];
        $this->teamDamage = (int)$playerData["tmdg"];
        $this->teamVehicleDamage = (int)$playerData["tmvd"];

        $this->scorePerMin = ($this->roundTime > 0 && $this->roundScore > 0)
            ? round($this->roundScore / ($this->roundTime / 60), 2)
            : 0.00;
        $this->timeFormatted = TimeHelper::SecondsToHms($this->roundTime);

        // Add parachute
        $this->timeParachute = (int)$playerData["tvp"];

        // Extract Award Data
        foreach ($playerData['armyData'] as $obj)
        {
            if ($obj['id'] < StatsData::$NumArmies)
            {
                $this->timeAsArmy[$obj['id']] = (int)$obj['time'];
            }
        }

        // Extract Kit Data
        foreach ($playerData['kitData'] as $obj)
        {
            if ($obj['id'] < StatsData::$NumKits)
            {
                // Create new object stat
                $object = new ObjectStat();
                $object->id = (int)$obj['id'];
                $object->score = (int)$obj['score'];
                $object->time = (int)$obj['time'];
                $object->kills = (int)$obj['kills'];
                $object->deaths = (int)$obj['deaths'];

                // Add object to list
                $this->kitData[] = $object;
            }
        }

        // Extract Vehicle Data
        foreach ($playerData['vehicleData'] as $obj)
        {
            if ($obj['id'] < StatsData::$NumVehicles)
            {
                // Create new object stat
                $object = new ObjectStat();
                $object->id = (int)$obj['id'];
                $object->score = (int)$obj['score'];
                $object->time = (int)$obj['time'];
                $object->kills = (int)$obj['kills'];
                $object->deaths = (int)$obj['deaths'];
                $object->roadKills = (int)$obj['roadkills'];

                // Add object to list
                $this->vehicleData[] = $object;
            }
        }

        // Extract Vehicle Data
        foreach ($playerData['weaponData'] as $obj)
        {
            if ($obj['id'] < StatsData::$NumWeapons)
            {
                // Create new object stat
                $object = new ObjectStat();
                $object->id = (int)$obj['id'];
                $object->time = (int)$obj['time'];
                $object->kills = (int)$obj['kills'];
                $object->deaths = (int)$obj['deaths'];
                $object->fired = (int)$obj['fired'];
                $object->hits = (int)$obj['hits'];
                $object->deployed = (int)$obj['deployed'];

                // Add object to list
                $this->weaponData[] = $object;
            }
        }

        // Extract player awards
        foreach ($playerData['awards'] as $obj)
        {
            if (isset(AwardData::$PythonAwards[$obj['id']]))
            {
                $id = AwardData::$PythonAwards[$obj['id']];
                $this->earnedAwards[ $id ] = (int)$obj['level'];
            }
        }

        // Extract player kill data
        foreach ($playerData['victims'] as $player)
        {
            $this->victims[$player['id']] = (int)$player['count'];
        }
    }
    /**
     * Whether a offset exists
     *
     * @param string $offset An offset to check for.
     *
     * @return boolean true on success or false on failure.
     *
     * The return value will be casted to boolean if non-boolean was returned.
     *
     */
    public function offsetExists($offset): bool
    {
        return property_exists($this, $offset);
    }
    /**
     * Offset to retrieve
     *
     * @param string $offset The offset to retrieve.
     *
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset): mixed
    {
        if (!$this->offsetExists($offset))
        {
            throw new ArgumentOutOfRangeException("The given offset was not present in the object: {$offset}");
        }

        return $this->{$offset};
    }
    /**
     * Offset to set
     *
     * @param string $offset The offset to assign the value to.
     *
     * @param mixed $value The value to set.
     */
    public function offsetSet($offset, $value): void
    {
        if (!$this->offsetExists($offset))
        {
            throw new ArgumentOutOfRangeException("The given offset was not present in the object: {$offset}");
        }

        $this->{$offset} = $value;
    }
    /**
     * Method un-used
     *
     * @deprecated This method does not do anything
     *
     * @param string $offset The offset to unset.
     */
    public function offsetUnset($offset): void
    {

    }
    public function __toString(): string
    {
        return "PlayerClass";
    }
}