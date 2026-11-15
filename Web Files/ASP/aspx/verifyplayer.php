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
 * The purpose of this ASPX call is to prevent players from using any kind of
 * Cross Service Exploitation. An example of this is being logged into a Gamespy
 * service provider A, and playing on a server that uses Service B's Stats system.
 *
 * Servers using our service will use this page to verify the following:
 *
 *  1. The player exists in our stats database (by PID)
 *  2. The player's name matches the name we have in the database.
 *  3. The player is not flagged as banned in our stats database
 *  4. The player is logged on using our gamespy servers.
 *
 * This page will output either "OK" or "NOK" under the "result" header.
 * If a server receives a "NOK" result, and submits a snapshot with said player in it,
 * the snapshot will be rejected during processing. The new 3.0 python contains the
 * compiled python scripts to properly use this service.
 *
 * Whether a server uses this service or not, is on the administrator. However,
 * the snapshot processor will deny processing any snapshots that detect any kind
 * of Cross Service Exploitation, which in turn will make the server "Unranked".
 */

// Namespace
namespace System;

// No direct access
defined("BF2_ADMIN") || die("No Direct Access");

// Prepare output
$Response = new AspResponse();

// Make sure we have a valid PID. Casting to int will sanitize input
$pid = (isset($_GET['pid'])) ? (int)$_GET['pid'] : 0;
$nick = (isset($_GET['nick'])) ? str_replace('%20', ' ', $_GET['nick']) : '';
$nick = trim($nick);

// Player id specified?
if ($pid == 0 || ($nick === '' || $nick === '0'))
{
    $Response->responseError(true, 107);
    $Response->writeHeaderLine("asof", "err");
    $Response->writeDataLine(time(), "Invalid Syntax!");
    $Response->send();
}
else
{
    // Get database connection
    $connection = Database::GetConnection("stats");

    // Make sure the player exists
    $query = "SELECT `name`, `permban` FROM `player` WHERE `id` = {$pid}";
    $player = $connection->query($query)->fetch();

    // Query failed or player does not exist
    if (empty($player))
    {
        $Response->writeHeaderLine("result", "message");
        $Response->writeDataLine("NOK", "Player Not Found!");
        $Response->send();
    }
    else
    {
        // Remove any spaces. This can happen when using an nick prefix!
        $split = explode(" ", $nick);
        $nick = $split[count($split)-1];

        // Ensure the player is using our services, and is not banned!
        if (strtolower($nick) !== strtolower((string) $player['name'])) {
            $Response->writeHeaderLine("result", "message");
            $Response->writeDataLine("NOK", "Player Nick Invalid!");
            $Response->send();
        } elseif ($player['permban'] != 0) {
            $Response->writeHeaderLine("result", "message");
            $Response->writeDataLine("NOK", "Player Is Banned!");
            $Response->send();
        } else
        {
            // Just echo OK for now
            $Response->writeHeaderLine("result", "message");
            $Response->writeDataLine("OK", "Player Is Valid");
            $Response->send();
        }
    }
}