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
 * Home Model
 *
 * @package Models
 * @subpackage Home
 */
class HomeModel
{
    /**
     * @var DbConnection The stats database connection
     */
    private $pdo;

    /**
     * @var int Time stamp of one week ago
     */
    private static int $OneWeekAgo = 0;

    /**
     * @var int Time stamp of two weeks ago
     */
    private static int $TwoWeekAgo = 0;

    /**
     * HomeModel constructor.
     */
    public function __construct()
    {
        // Fetch database connection
        $this->pdo = Database::GetConnection('stats');

        // Set static vars
        if (self::$OneWeekAgo == 0)
        {
            self::$OneWeekAgo = time() - (86400 * 7);
            self::$TwoWeekAgo = time() - (86400 * 14);
        }
    }

    /**
     * Credits to MrNiceGuy for providing this method of fetching the Apache version of the server
     *
     * @return string Returns the apache server version
     */
    public function getApacheVersion()
    {
        if(!function_exists('apache_get_version'))
        {
            if(!isset($_SERVER['SERVER_SOFTWARE']) || strlen((string) $_SERVER['SERVER_SOFTWARE']) == 0)
            {
                return 'Unknown';
            }

            return $_SERVER["SERVER_SOFTWARE"];
        }

        return apache_get_version();
    }

    /**
     * @return int Returns the database size in bytes
     */
    public function getStatsDataSize(): float|int
    {
        // Get database size
        $size = 0;
        $q = $this->pdo->query("SHOW TABLE STATUS");
        while ($row = $q->fetch())
        {
            // Views will return null here
            if (!isset($row["Data_length"]) || $row["Data_length"] == null) {
                continue;
            }

            $size += $row["Data_length"] + $row["Index_length"];
        }
        return $size;
    }

    /**
     * @return string Returns the database version
     */
    public function getDatabaseVersion()
    {
        return $this->pdo->query('SELECT version()')->fetchColumn(0);
    }

    /**
     * @return int Returns the total number of games processed
     */
    public function getNumGamesProcessed(): int
    {
        return (int)$this->pdo->query('SELECT COUNT(id) FROM round')->fetchColumn(0);
    }

    /**
     * @return int Returns the total number of games that failed to process
     */
    public function getNumGamesFailed(): int
    {
        return (int)$this->pdo->query('SELECT COUNT(id) FROM `failed_snapshot`')->fetchColumn(0);
    }

    /**
     * @return int Returns the total number of players
     */
    public function getNumPlayers(): int
    {
        return (int)$this->pdo->query("SELECT COUNT(id) FROM player")->fetchColumn(0);
    }

    /**
     * @return int Returns the total number of players who have been active this week
     */
    public function getNumActivePlayersThisWeek(): int
    {
        $query = "SELECT COUNT(id) FROM player WHERE lastonline > " . self::$OneWeekAgo;
        return (int)$this->pdo->query($query)->fetchColumn(0);
    }

    /**
     * @return int Returns the total number of servers
     */
    public function getNumActivePlayersLastWeek(): int
    {
        $a = self::$OneWeekAgo;
        $b = self::$TwoWeekAgo;

        $query = "SELECT COUNT(id) FROM player WHERE lastonline BETWEEN $a AND $b";
        return (int)$this->pdo->query($query)->fetchColumn(0);
    }

    /**
     * @return int Returns the total number of players who registered this week
     */
    public function getNumNewPlayersThisWeek(): int
    {
        $query = "SELECT COUNT(id) FROM player WHERE joined > " . self::$OneWeekAgo;
        return (int)$this->pdo->query($query)->fetchColumn(0);
    }

    /**
     * @return int Returns the total number of players who registered last week
     */
    public function getNumNewPlayersLastWeek(): int
    {
        $a = self::$OneWeekAgo;
        $b = self::$TwoWeekAgo;

        $query = "SELECT COUNT(id) FROM player WHERE joined BETWEEN $a AND $b";
        return (int)$this->pdo->query($query)->fetchColumn(0);
    }

    /**
     * @return int Returns the total number of servers
     */
    public function getNumServers(): int
    {
        return (int)$this->pdo->query("SELECT COUNT(id) FROM server")->fetchColumn(0);
    }

    /**
     * @return int Returns the total number of servers who have been active this week
     */
    public function getNumActiveServersThisWeek(): int
    {
        $query = "SELECT COUNT(id) FROM server WHERE lastupdate > " . self::$OneWeekAgo;
        return (int)$this->pdo->query($query)->fetchColumn(0);
    }

    /**
     * @return int Returns the total number of servers who were active between this week and last
     */
    public function getNumActiveServersLastWeek(): int
    {
        $a = self::$OneWeekAgo;
        $b = self::$TwoWeekAgo;

        $query = "SELECT COUNT(id) FROM server WHERE lastupdate BETWEEN $a AND $b";
        return (int)$this->pdo->query($query)->fetchColumn(0);
    }

    /**
     * Fetches the chart data for the Home page
     */
    public function getRankDistChartData(): array
    {
        $this->getNumPlayers();
        $output = ['ranks' => ['name' => [], 'count' => []]];
        $query = "SELECT id, name FROM rank ORDER BY id ASC";

        $i = 0;
        $result = $this->pdo->query(trim($query));
        while ($row = $result->fetch())
        {
            $query2 = "SELECT COUNT(rank_id) FROM player WHERE rank_id=". $row['id'];
            $count = $this->pdo->query($query2)->fetchColumn();
            $output['ranks']['name'][] = [$i, $row['name']];
            $output['ranks']['count'][] = [$i++, $count];
        }

        // return chart data
        return $output;
    }

    /**
     * Fetches the chart data for the Home page
     */
    public function getGamesPlayedChartData(): array
    {
        $output = [
            'week' => ['y' => [], 'x' => []],
            'month' => ['y' => [], 'x' => []],
            'year' => ['y' => [], 'x' => []]
        ];

        /* -------------------------------------------------------
         * WEEK
         * -------------------------------------------------------
         */
        $todayStart = new DateTime('6 days ago midnight');
        $timestamp = $todayStart->getTimestamp();

        // Build array
        $temp = [];
        for ($iDay = 6; $iDay >= 0; $iDay--)
        {
            $key = date('l (m/d)', time() - ($iDay * 86400));
            $temp[$key] = 0;
        }

        $query = "SELECT `time_imported` FROM round WHERE `time_imported` > $timestamp";
        $result = $this->pdo->query($query);
        while ($row = $result->fetch())
        {
            $key = date("l (m/d)", (int)$row['time_imported']);
            $temp[$key] += 1;
        }

        $i = 0;
        foreach ($temp as $key => $value)
        {
            $output['week']['y'][] = [$i, $value];
            $output['week']['x'][] = [$i++, $key];
        }

        /* -------------------------------------------------------
         * MONTH
         * -------------------------------------------------------
         */

        $temp = [];

        $start = new DateTime('6 weeks ago');
        $end = new DateTime('now');
        $interval = DateInterval::createFromDateString('1 week');

        $period = new DatePeriod($start, $interval, $end);
        $timeArrays = [];

        foreach ($period as $p)
        {
            // Start
            /* @var $p DateTime */
            $p->modify('+1 minute');
            $key1 = $p->format('M d');
            $timestamp = $p->getTimestamp();

            // End
            $p->modify('+7 days');
            $key2 = $p->format('M d');

            // Append
            $timeArrays[$timestamp] = $p->getTimestamp();
            $temp[] = $key1 . ' - ' . $key2;
        }

        $i = 0;
        foreach ($timeArrays as $start => $finish)
        {
            $query = "SELECT COUNT(*) FROM round WHERE `time_imported` BETWEEN $start AND $finish";
            $result = (int)$this->pdo->query($query)->fetchColumn(0);

            $output['month']['y'][] = [$i, $result];
            $output['month']['x'][] = [$i, $temp[$i]];
            $i++;
        }

        /* -------------------------------------------------------
         * YEAR
         * -------------------------------------------------------
         */

        $temp = [];

        // Yep, php DateTime using strings is BadAss!!
        $start = new DateTime('first day of 11 months ago');
        $end = new DateTime('last day of this month');
        $interval = DateInterval::createFromDateString('1 month');

        $period = new DatePeriod($start, $interval, $end);
        $timeArrays = [];

        foreach ($period as $p)
        {
            // Start
            $key1 = $p->format('M Y');
            $timestamp = $p->getTimestamp();

            // End
            $p->modify('+1 month');

            // Append
            $timeArrays[$timestamp] = $p->getTimestamp();
            $temp[] = $key1;
        }

        $i = 0;
        foreach ($timeArrays as $start => $finish)
        {
            $query = "SELECT COUNT(*) FROM round WHERE `time_imported` BETWEEN $start AND $finish";
            $result = (int)$this->pdo->query($query)->fetchColumn(0);

            $output['year']['y'][] = [$i, $result];
            $output['year']['x'][] = [$i, $temp[$i]];
            $i++;
        }

        // return chart data
        return $output;
    }
}
