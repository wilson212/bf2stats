<?php

use System\Database;
use System\Database\DbConnection;

/**
 * BF2Statistics ASP Framework
 *
 * Author:       Steven Wilson
 * Copyright:    Copyright (c) 2006-2021, BF2statistics.com
 * License:      GNU GPL v3
 *
 */
/**
 * Stats Model
 *
 * @package Stats
 * @subpackage Models
 */
class StatsModel
{
    /**
     * @var DbConnection The stats database connection
     */
    protected $pdo;

    /**
     * StatsModel constructor.
     */
    public function __construct()
    {
        // Fetch database connection
        $this->pdo = Database::GetConnection('stats');
    }

    /**
     * Fetches the list of army types from the database
     *
     * @return array
     */
    public function getArmies()
    {
        $query = "SELECT `id`, `name` FROM `army`";
        return $this->pdo->query($query)->fetchAll();
    }

    /**
     * Fetches the list of kit types from the database
     *
     * @return array
     */
    public function getKits()
    {
        $query = "SELECT `id`, `name` FROM `kit`";
        return $this->pdo->query($query)->fetchAll();
    }

    /**
     * Fetches the list of weapon types from the database
     *
     * @return array
     */
    public function getWeapons()
    {
        $query = "SELECT `id`, `name` FROM `weapon`";
        return $this->pdo->query($query)->fetchAll();
    }

    /**
     * Fetches the list of kit types from the database
     *
     * @return array
     */
    public function getVehicles()
    {
        $query = "SELECT `id`, `name` FROM `vehicle`";
        return $this->pdo->query($query)->fetchAll();
    }
}
