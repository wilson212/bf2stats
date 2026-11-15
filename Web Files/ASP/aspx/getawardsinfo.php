<?php
/*
    Copyright (C) 2006-2021  BF2Statistics

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * This provides a list of awards for a particular player.
 *
 * Accepted URL Parameters:
 * @param int $pid Unique player ID
 */

// Namespace
namespace System;

// No direct access
defined("BF2_ADMIN") || die("No Direct Access");

// Prepare output
$Response = new AspResponse();

// Set response format
$format = (isset($_GET['format'])) ? min(2, abs((int)$_GET['format'])) : 0;
$Response->setResponseFormat($format);

// Make sure we have a valid PID. Casting to int will sanitize input
$pid = (isset($_GET['pid'])) ? (int)$_GET['pid'] : 0;

// Player id specified?
if ($pid == 0)
{
    $Response->responseError(true, 107);
    $Response->writeHeaderLine("asof", "err");
    $Response->writeDataLine(time(), "Invalid Syntax!");
    $Response->send();
}
else
{
    // Connect to the database
    $connection = Database::GetConnection("stats");

    // Prepare our output header
    $Response->writeHeaderLine("pid", "asof");
    $Response->writeDataLine($pid, time());
    $Response->writeHeaderLine("award", "level", "when", "first");

    // We will not use a view this time, performance is key
    $query = <<<SQL
-- COUNT all rows for each medal to get a single row per medal
SELECT a.award_id AS `id`, a.player_id AS `pid`, MAX(r.time_end) AS `earned`, MIN(r.time_end) AS `first`, COUNT(`level`) AS `level`
FROM player_award AS a
  LEFT JOIN round AS r ON a.round_id = r.id
WHERE a.player_id=$pid
GROUP BY a.player_id, a.award_id
UNION ALL
-- Don't GROUP badge/ribbon rows since we need to return all levels invidially (for ribbons)
-- (selecting 0 as first since neither ribbons nor badges can be awarded more than once)
SELECT a.award_id AS `id`, a.player_id AS `pid`, r.time_end AS `earned`, 0 AS `first`, `level`
FROM player_award AS a
  LEFT JOIN round AS r ON a.round_id = r.id
WHERE a.player_id=$pid AND a.award_id NOT BETWEEN 2000000 AND 2999999
ORDER BY `id`, `level`;
SQL;


    // Query and get all of the Players awards
    $stmt = $connection->query($query);
    while ($award = $stmt->fetch())
    {
        // Ribbons will get a 'first' value of 0
        $id = (int)$award['id'];
        $first = (($id > 2000000) && ($id < 3000000)) ? $award['first'] : 0;
        $Response->writeDataLine($id, $award['level'], $award['earned'], $first);
    }

    $Response->send();
}