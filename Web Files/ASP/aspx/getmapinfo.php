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
 * This provides a list of map data for a particular player.
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

// Get database connection
$connection = Database::GetConnection("stats");

// Make sure we have the needed params
$pid = (isset($_GET['pid'])) ? (int)$_GET['pid'] : 0;

if ($pid > 0)
{
    // Build our query
    $query = "SELECT m.*, mi.name AS mapname FROM player_map AS m JOIN map AS mi ON m.map_id = mi.id " .
        "WHERE m.player_id = {$pid} ORDER BY map_id";
    $result = $connection->query($query);
    if ($row = $result->fetch())
    {
        $Response->writeHeaderLine("mapid", "mapname", "time", "win", "loss", "best", "worst");
        do
        {
            $Response->writeDataLine(
                $row['map_id'],
                $row['mapname'],
                $row['time'],
                $row['wins'],
                $row['losses'],
                $row['bestscore'],
                $row['worstscore']
            );
        } while ($row = $result->fetch());
    }
    else
    {
        $Response->responseError(true);
        $Response->writeHeaderLine("err");
        $Response->writeDataLine("Map Data Not Found!");
    }
}
else
{

    $Response->responseError(true);
    $Response->writeHeaderLine("err");
    $Response->writeDataLine("Invalid Syntax!");
}

// Output data
$Response->send();