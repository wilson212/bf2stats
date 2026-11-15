<?php
/**
 * BF2Statistics ASP Framework
 *
 * Author:       Steven Wilson
 * Copyright:    Copyright (c) 2006-2021, BF2statistics.com
 * License:      GNU GPL v3
 *
 */

namespace System;
use System\BF2\Player;
use System\BF2\ObjectStat;

abstract class GameResult
{
    /**
     * Epoch timestamp of when the Round Started (Python's time.time() function)
     *
     * @var int
     */
    public $roundStartTime;

    /**
     * Epoch timestamp of when the Round Ended (Python's time.time() function)
     *
     * @var int
     */
    public $roundEndTime;

    /**
     * Returns the timespan of time the round lasted from start to finish in seconds
     *
     * @var int
     */
    public $roundTime;

    /**
     * Snapshot Server prefix
     *
     * @var string
     */
    public $serverPrefix;

    /**
     * Snapshot Server name
     *
     * @var string
     */
    public $serverName;

    /**
     * Snapshot Server Port
     *
     * @var int
     */
    public $serverPort;

    /**
     * Snapshot Server Gamespy Query Port
     *
     * @var int
     */
    public $queryPort;

    /**
     * Map ID Played during this round
     *
     * @var int
     */
    public $mapId;

    /**
     * Map name Played during this round
     *
     * @var string
     */
    public $mapName;

    /**
     * Is this a custom map?
     *
     * @var bool
     */
    public $isCustomMap;

    /**
     * Total amount of kills from all players in the round
     *
     * @var int
     */
    public $mapKills;

    /**
     * Total amount of deaths from all players in the round
     *
     * @var int
     */
    public $mapDeaths;

    /**
     * Sum of all the players score in the given round
     *
     * @var int
     */
    public $mapScore;

    /**
     * The gamemode ID of this round (Cq, Coop, SP)
     *
     * @var int
     */
    public $gameMode;

    /**
     * Mod name that was played
     *
     * @var string
     */
    public $mod;

    /**
     * The winning team ID. Value is set to 0 if no team won (tie)
     *
     * @var int
     */
    public $winningTeam;

    /**
     * Winning Army ID. Value is set to -1 if no army won the round
     *
     * @var int
     */
    public $winningArmyId;

    /**
     * Team 1's Army Id
     *
     * @var int
     */
    public $team1ArmyId;

    /**
     * Team 2's Army Id
     *
     * @var int
     */
    public $team2ArmyId;

    /**
     * Remaining round tickets team 1
     *
     * @var int
     */
    public $team1Tickets;

    /**
     * Remaining round tickets team 2
     *
     * @var int
     */
    public $team2Tickets;

    /**
     * The number of team 1 players
     *
     * @var int
     */
    public $team1Players;

    /**
     * The number of team 2 players
     *
     * @var int
     */
    public $team2Players;

    /**
     * The number of team 1 players at the EOR
     *
     * @var int
     */
    public $team1PlayersEnd;

    /**
     * The number of team 2 players at the EOR
     *
     * @var int
     */
    public $team2PlayersEnd;

    /**
     * The total number of players who played in the round
     *
     * @var int
     */
    public $playersConnected;

    /**
     * The list of players and their data that played this round
     *
     * @var Player[]
     */
    public $players = [];

    /**
     * Fetches the Player object by PID, or false if the player
     * was not found in this round.
     *
     * @param int $pid The player ID
     *
     * @return bool|Player
     */
    public function getPlayerById($pid)
    {
        foreach ($this->players as $player)
        {
            if ($player->id == $pid) {
                return $player;
            }
        }

        return false;
    }

    /**
     * Gets a list of players in the snapshot, separated into a 2-dimensional array by ArmyID
     *
     * @return array [ armyID => [ System\BF2\Player ] ]
     */
    public function getPlayersByArmy()
    {
        $players = [];
        $list = $this->players;

        // Sort players by score
        usort($list, fn($a, $b): int|float => $b['roundScore'] - $a['roundScore']);

        foreach ($list as $player)
        {
            $players[$player->armyId][] = $player;
        }

        return $players;
    }

    /**
     * Fetches a list of vehicles that were used this round, and their respective top player.
     *
     * @return array [ vehicleName => [ 'id', 'pid', 'name', 'rank', 'team', 'kills', 'deaths', 'time', 'roadKills' ] ]
     */
    public function getTopVehiclePlayers()
    {
        $return = [];

        foreach ($this->players as $player)
        {
            foreach ($player->vehicleData as $data)
            {
                $name = StatsData::$VehicleNames[$data->id];
                if (!isset($return[$name]) || $this->_isPlayerBetter($data, $return[$name]))
                {
                    $return[$name] = [
                        'id' => $data->id,
                        'pid' => $player->id,
                        'name' => $player->name,
                        'rank' => $player->rank,
                        'team' => $player->armyId,
                        'kills' => $data->kills,
                        'deaths' => $data->deaths,
                        'score' => $data->score,
                        'time' => $data->time,
                        'time_string' => TimeHelper::SecondsToHms($data->time),
                        'roadKills' => $data->roadKills
                    ];
                }
            }
        }

        return $return;
    }

    /**
     * Fetches a list of kits that were played this round, and their respective top player.
     *
     * @return array [ kitName => [ 'id', 'pid', 'name', 'rank', 'team', 'kills', 'deaths', 'time' ] ]
     */
    public function getTopKitPlayers()
    {
        $return = [];

        foreach ($this->players as $player)
        {
            foreach ($player->kitData as $data)
            {
                $name = StatsData::$KitNames[$data->id];
                if (!isset($return[$name]) || $this->_isPlayerBetter($data, $return[$name]))
                {
                    $return[$name] = [
                        'id' => $data->id,
                        'pid' => $player->id,
                        'name' => $player->name,
                        'rank' => $player->rank,
                        'team' => $player->armyId,
                        'kills' => $data->kills,
                        'deaths' => $data->deaths,
                        'score' => $data->score,
                        'time' => $data->time,
                        'time_string' => TimeHelper::SecondsToHms($data->time)
                    ];
                }
            }
        }

        return $return;
    }

    /**
     * Fetches a list of specific score categories, and their respective top player.
     *
     * @return array [ categoryName => [ 'id', 'name', 'rank', 'team', 'value' ] ]
     */
    public function getTopSkillPlayers()
    {
        $categories = [
            'roundScore' => ['id' => 0, 'name' => 'N/A', 'rank' => 0, 'team' => -1, 'value' => 0],
            'skillScore' => ['id' => 0, 'name' => 'N/A', 'rank' => 0, 'team' => -1,'value' => 0],
            'teamScore' => ['id' => 0, 'name' => 'N/A', 'rank' => 0, 'team' => -1,'value' => 0],
            'commandScore' => ['id' => 0, 'name' => 'N/A', 'rank' => 0, 'team' => -1,'value' => 0],
            'heals' => ['id' => 0, 'name' => 'N/A', 'rank' => 0, 'team' => -1,'value' => 0],
            'revives' => ['id' => 0, 'name' => 'N/A', 'rank' => 0, 'team' => -1,'value' => 0],
            'resupplies' => ['id' => 0, 'name' => 'N/A', 'rank' => 0, 'team' => -1,'value' => 0],
            'repairs' => ['id' => 0, 'name' => 'N/A', 'rank' => 0, 'team' => -1,'value' => 0],
            'flagCaptures' => ['id' => 0, 'name' => 'N/A', 'rank' => 0, 'team' => -1,'value' => 0],
            'flagDefends' => ['id' => 0, 'name' => 'N/A', 'rank' => 0, 'team' => -1,'value' => 0],
            'killStreak' => ['id' => 0, 'name' => 'N/A', 'rank' => 0, 'team' => -1,'value' => 0],
            'deathStreak' => ['id' => 0, 'name' => 'N/A', 'rank' => 0, 'team' => -1,'value' => 0],
            'damageAssists' => ['id' => 0, 'name' => 'N/A', 'rank' => 0, 'team' => -1,'value' => 0],
            'driverSpecials' => ['id' => 0, 'name' => 'N/A', 'rank' => 0, 'team' => -1,'value' => 0],
            'teamKills' => ['id' => 0, 'name' => 'N/A', 'rank' => 0, 'team' => -1,'value' => 0],
            'teamDamage' => ['id' => 0, 'name' => 'N/A', 'rank' => 0, 'team' => -1,'value' => 0]
        ];

        foreach ($this->players as $player)
        {
            foreach ($categories as $key => $values)
            {
                $value = $player->{$key};
                if ($value > $values['value'])
                {
                    $categories[$key] = [
                        'id' => $player->id,
                        'name' => $player->name,
                        'rank' => $player->rank,
                        'team' => $player->armyId,
                        'value' => $value
                    ];
                }
            }
        }

        return $categories;
    }

    /**
     * Returns a list of commanders for this game, sorted by command score
     *
     * @return array [ index => [ 'id', 'name', 'rank', 'time', 'score', 'team' ] ]
     */
    public function getCommanders()
    {
        $commanders = [];
        foreach ($this->players as $player)
        {
            if ($player->cmdTime > 0)
            {
                $commanders[] = [
                    'id' => $player->id,
                    'name' => $player->name,
                    'rank' => $player->rank,
                    'time' => $player->cmdTime,
                    'time_string' => TimeHelper::SecondsToHms($player->cmdTime),
                    'score' => $player->commandScore,
                    'team' => $player->armyId
                ];
            }
        }

        usort($commanders, fn($a, $b): int|float => $b['score'] - $a['score']);
        return $commanders;
    }

    /**
     * Returns a list of earned awards in this game
     *
     * @return array [
     *      index => [ 'award_id', 'award_name', 'award_type', 'award_level', 'player_id', 'player_rank', 'player_name' ] ]
     */
    public function getEarnedAwards()
    {
        // Fetch the round awards
        $awards = [];
        foreach ($this->players as $player)
        {
            foreach ($player->earnedAwards as $id => $level)
            {
                // Get our award type.
                $isMedal = ($id > 2000000 && $id < 3000000);
                $isBadge = ($id < 2000000);

                $type = 0; // ribbon
                if ($isBadge) {
                    $type = 1;
                }
                if ($isMedal) {
                    $type = 2;
                }

                $awards[] = [
                    'award_id' => $id,
                    'award_name' => Battlefield2::GetAwardName($id, $level),
                    'award_type' => $type,
                    'award_level' => $level,
                    'player_id' => $player->id,
                    'player_name' => $player->name,
                    'player_rank' => $player->rank,
                    'player_team' => $player->armyId
                ];
            }
        }

        return $awards;
    }

    /**
     * Determines if a player ObjectStat is greater than the second
     * set of object data by comparing the kills, deaths and time played in
     * the object.
     *
     *
     */
    private function _isPlayerBetter(ObjectStat $data, array $best): bool
    {
        if ($data->score > $best['score']) {
            return true;
        } elseif ($data->score < $best['score']) {
            return false;
        }

        /** Scores Match, try kills
        if ($data->kills > $best['kills'])
            return true;
        else if ($data->kills < $best['kills'])
            return false;
         */

        /** Kills Match, try deaths */

        if ($data->deaths < $best['deaths']) {
            return true;
        } elseif ($data->deaths > $best['deaths']) {
            return false;
        }

        /** Deaths and Kills Match, try time played */

        if ($data->time > $best['time']) {
            return true;
        } elseif ($data->time < $best['time']) {
            return false;
        }

        /** It's a draw. Just say no */
        return false;
    }
}