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
 * Class StatsData
 * @package System
 */
class StatsData
{
    /**
     * @var int The number of army types in the database
     */
    public static $NumArmies = 0;

    /**
     * @var int The number of kit types in the database
     */
    public static $NumKits = 0;

    /**
     * @var int The number of vehicle types in the database
     */
    public static $NumVehicles = 0;

    /**
     * @var int The number of weapon types in the database
     */
    public static $NumWeapons = 0;

    /**
     * @var int The number of gamemodes in the database
     */
    public static $NumGamemodes = 0;

    /**
     * @var int The number of ranks in the database
     */
    public static $NumRanks = 0;

    /**
     * @var string[] An array of ArmyId => Army String Name
     */
    public static $ArmyNames = [];

    /**
     * @var string[] An array of KitId => Kit String Name
     */
    public static $KitNames = [];

    /**
     * @var string[] An array of VehicleId => Vehicle String Name
     */
    public static $VehicleNames = [];

    /**
     * @var string[] An array of WeaponId => Weapon String Name
     */
    public static $WeaponNames = [];

    /**
     * @var string[] An array of GamemodeId => Game Mode String Name
     */
    public static $GameModes = [];

    /**
     * @var string[] An array of RankId => Rank String Name
     */
    public static $RankNames = [];

    /**
     * Loads the stats data from the database if it has not been previously
     * called
     */
    public static function Load(): void
    {
        // Only load data if it has not been loaded yet
        if (self::$NumArmies == 0)
        {
            $pdo = Database::GetConnection('stats');

            // Load armies
            $result = $pdo->query("SELECT name FROM army ORDER BY id ASC");
            while ($row = $result->fetch())
            {
                self::$ArmyNames[] = $row['name'];
                self::$NumArmies++;
            }

            // Load kits
            $result = $pdo->query("SELECT name FROM kit ORDER BY id ASC");
            while ($row = $result->fetch())
            {
                self::$KitNames[] = $row['name'];
                self::$NumKits++;
            }

            // Load vehicles
            $result = $pdo->query("SELECT name FROM vehicle ORDER BY id ASC");
            while ($row = $result->fetch())
            {
                self::$VehicleNames[] = $row['name'];
                self::$NumVehicles++;
            }

            // Load weapons
            $result = $pdo->query("SELECT name FROM weapon ORDER BY id ASC");
            while ($row = $result->fetch())
            {
                self::$WeaponNames[] = $row['name'];
                self::$NumWeapons++;
            }

            // Load Game Modes
            $result = $pdo->query("SELECT name FROM game_mode ORDER BY id ASC");
            while ($row = $result->fetch())
            {
                self::$GameModes[] = $row['name'];
                self::$NumGamemodes++;
            }

            // Load Ranks
            $result = $pdo->query("SELECT name FROM `rank` ORDER BY id ASC");
            while ($row = $result->fetch())
            {
                self::$RankNames[] = $row['name'];
                self::$NumRanks++;
            }
        }
    }

    /**
     * Fetches the army name by Id, or false if it does not exist
     *
     * @param int $id The item id
     *
     * @return bool|string The name of the army, or false if it does not exist
     */
    public static function GetArmyNameById($id)
    {
        // Only load data if it has not been loaded yet
        if (self::$NumArmies == 0) {
            self::Load();
        }

        // Get max index, to prevent an index out of bounds error
        $maxIndex = self::$NumArmies - 1;
        return ($id > $maxIndex) ? false : self::$ArmyNames[$id];
    }

    /**
     * Fetches the kit name by Id, or false if it does not exist
     *
     * @param int $id The item id
     *
     * @return bool|string The name of the kit, or false if it does not exist
     */
    public static function GetKitNameById($id)
    {
        // Only load data if it has not been loaded yet
        if (self::$NumArmies == 0) {
            self::Load();
        }

        // Get max index, to prevent an index out of bounds error
        $maxIndex = self::$NumKits - 1;
        return ($id > $maxIndex) ? false : self::$KitNames[$id];
    }

    /**
     * Fetches the weapon name by Id, or false if it does not exist
     *
     * @param int $id The item id
     *
     * @return bool|string The name of the weapon, or false if it does not exist
     */
    public static function GetWeaponNameById($id)
    {
        // Only load data if it has not been loaded yet
        if (self::$NumArmies == 0) {
            self::Load();
        }

        // Get max index, to prevent an index out of bounds error
        $maxIndex = self::$NumWeapons - 1;
        return ($id > $maxIndex) ? false : self::$WeaponNames[$id];
    }

    /**
     * Fetches the vehicle name by Id, or false if it does not exist
     *
     * @param int $id The item id
     *
     * @return bool|string The name of the vehicle, or false if it does not exist
     */
    public static function GetVehicleNameById($id)
    {
        // Only load data if it has not been loaded yet
        if (self::$NumVehicles == 0) {
            self::Load();
        }

        // Get max index, to prevent an index out of bounds error
        $maxIndex = self::$NumVehicles - 1;
        return ($id > $maxIndex) ? false : self::$VehicleNames[$id];
    }
}