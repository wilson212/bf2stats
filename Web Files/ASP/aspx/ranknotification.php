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

// Namespace
namespace System;

// No direct access
defined("BF2_ADMIN") || die("No Direct Access");

// Prepare output
$Response = new AspResponse();

// Make sure we have a valid PID. Casting to an (int) will sanitize input
$pid = (isset($_GET['pid'])) ? (int)$_GET['pid'] : 0;

// Player does not exist?
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

    // Fetch player rank notifications
    $row = $connection->query("SELECT `chng`, `decr` FROM `player` WHERE `id` = {$pid}")->fetch();
    if (empty($row))
    {
        $Response->responseError(true);
        $Response->writeLine("Player Not Found");
    }
    else
    {
        // DO we need to update the database, and clear notifications?
        $chng = (int)$row['chng'];
        $decr = (int)$row['decr'];
        if ($chng > 0 || $decr > 0)
        {
            $query = "UPDATE `player` SET `chng` = 0, `decr` = 0 WHERE `id` = {$pid}";
            $result = $connection->exec($query);
            if ($result === false)
            {
                $Response->responseError(true);
                $Response->writeDataLine("Failed to clear rank notification {$pid}");
            }
            else {
                $Response->writeDataLine("Cleared rank notification {$pid}");
            }
        }
        else
        {
            // We should not be here...
            $Response->writeDataLine("Stop trying to do us harm!");
        }
    }

    $Response->send();
}