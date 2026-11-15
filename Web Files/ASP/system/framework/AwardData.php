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
use System\IO\Path;

/**
 * Class AwardData
 * @package System
 */
class AwardData
{
    /**
     * @var array An array of all awards that are earned in game via python.
     */
    public static $PythonAwards = [];

    /**
     *
     * @var BF2\BackendAward[] An array of backend earned awards
     */
    public static $BackendAwards = [];

    /**
     * Loads and caches the award data if not already done.
     */
    public static function Load(): void
    {
        if (self::$PythonAwards === [])
        {
            // Fetch non-backend awards
            $pdo = Database::GetConnection('stats');
            $result = $pdo->query("SELECT `id`, `code` FROM `award` WHERE `backend`=0");
            while ($row = $result->fetch())
            {
                $id = (int)$row['id'];
                self::$PythonAwards[$row['code']] = $id;
            }

            /** @noinspection PhpIncludeInspection */
            self::$BackendAwards = include Path::Combine(SYSTEM_PATH, "config", "backendAwards.php");
        }
    }
}