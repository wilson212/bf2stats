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

/**
 * This class provides common methods for fetching Battlefield 2 related information
 *
 * @package System
 */
class Battlefield2
{
    /**
     * @var array
     */
    protected static $Ranks = [];

    /**
     * Converts a badge level to its string name
     *
     * @param int $level The badge level
     */
    public static function GetBadgePrefix($level): string
    {
        if ($level == 3) {
            return 'Gold';
        } elseif ($level == 2) {
            return 'Silver';
        } else {
            return 'Bronze';
        }
    }

    /**
     * Gets the name of a game mode by its id
     *
     * @param int $gamemode
     *
     * @return string
     */
    public static function GetGameModeString($gamemode)
    {
        $pdo = Database::GetConnection('stats');
        $result = $pdo->query("SELECT `name` FROM `game_mode` WHERE id=". (int)$gamemode);
        $name = $result->fetchColumn(0);
        return ($name === false) ? 'Unknown' : $name;
    }

    /**
     * Fetches the name of a rank by ID
     *
     * @param int $rank
     *
     * @return string
     */
    public static function GetRankName($rank)
    {
        $pdo = Database::GetConnection('stats');
        $result = $pdo->query("SELECT `name` FROM `rank` WHERE id=". (int)$rank);
        $name = $result->fetchColumn(0);
        return ($name === false) ? 'Unknown' : $name;
    }

    /**
     * Fetches the name of an award by ID
     *
     * @param int $awardId
     * @param int $level
     *
     * @return string
     */
    public static function GetAwardName($awardId, $level = 0)
    {
        $pdo = Database::GetConnection('stats');
        $result = $pdo->query("SELECT `name`, `type` FROM `award` WHERE id=". (int)$awardId);
        $award = $result->fetch();
        if (empty($award)) {
            return "Unknown Award";
        }

        // If award is a badge, add the level
        if ($award['type'] == 1)
        {
            $prefix = self::GetBadgePrefix($level);
            return $prefix . ' ' . $award['name'];
        }
        else
        {
            // Store award name
            return $award['name'];
        }
    }
}