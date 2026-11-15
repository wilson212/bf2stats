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
 * Used by the BF2 Server only.
 *
 * Accepted URL Parameters:
 * @param string $name Unique player Name
 */

// Namespace
namespace System;
use PDO;
use System\BF2\Player;

// No direct access
defined("BF2_ADMIN") || die("No Direct Access");

// Prepare output
$Response = new AspResponse();

// Set response format
$format = (isset($_GET['format'])) ? min(2, abs((int)$_GET['format'])) : 0;
$Response->setResponseFormat($format);

// Get our Player Nick
if (isset($_POST['nick']))
{
    $nick = $_POST['nick'];
}
else
{
    $nick = (isset($_GET['nick'])) ? str_replace('%20', ' ', $_GET['nick']) : '';
}

// Sanitize nick
$pattern = Player::NAME_REGEX;
$nick = preg_replace("/[^{$pattern}]/", '', trim((string) $nick));

// Search by name?
if (!empty($nick))
{
    // Ensure nick is not too long!
    if (strlen($nick) > 32)
    {
        $Response->responseError(true);
        $Response->writeHeaderLine("err");
        $Response->writeDataLine("Nick Specified is larger than 32 characters!");
        $Response->send();
    }

    // Get database connection
    $connection = Database::GetConnection("stats");

    // Try to fetch players id
    $result = $connection->prepare("SELECT id FROM player WHERE name = :nick LIMIT 1");
    $result->bindValue(":nick", $nick, PDO::PARAM_STR);

    // Player does not exist
    if (!$result->execute() || !($pid = $result->fetchColumn(0)))
    {
        $Response->responseError(true);
        $Response->writeHeaderLine("asof", "err");
        $Response->writeDataLine(time(), "Player Not Found!");
        $Response->send();
        die; // prevents PhpStorm IDE error below
    }

    // Send response
    $Response->writeHeaderLine("pid");
    $Response->writeDataLine($pid);
    $Response->send();
}
else
{
    $Response->responseError(true);
    $Response->writeHeaderLine("err");
    $Response->writeDataLine("No Nick Specified!");
    $Response->send();
}