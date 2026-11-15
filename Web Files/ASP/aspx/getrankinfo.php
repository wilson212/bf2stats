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
 * This provides details on a particular players rank.
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

// Player does not exist?
if ($pid == 0)
{
    $Response->responseError(true, 107);
    $Response->writeHeaderLine("asof", "err");
    $Response->writeDataLine(time(), "Invalid Syntax!");
    $Response->send();
}
else // Player exists
{
    // Get database connection
    $connection = Database::GetConnection("stats");

    // Make sure the player exists
    $query = "SELECT `rank_id`, `chng`, `decr` FROM `player` WHERE `id` = {$pid}";
    $row = $connection->query($query)->fetch();

    // Query failed or player does not exist
    if (empty($row))
    {
        $Response->responseError(true);
        $Response->writeHeaderLine("asof", "err");
        $Response->writeDataLine(time(), "Player Not Found!");
        $Response->send();
    }
    else
    {
        $chng = (int)$row['chng'];
        $decr = (int)$row['decr'];

        // Do we need to reset notifications?
        if ($chng > 0 || $decr > 0) {
            $connection->exec("UPDATE `player` SET `chng` = 0, `decr` = 0 WHERE `id` = {$pid}");
        }

        // Send response
        $Response->writeHeaderLine("rank", "chng", "decr");
        $Response->writeDataLine($row['rank_id'], $chng, $decr);
        $Response->send();
    }
}