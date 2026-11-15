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
 * Simply provides a list of the weapon unlocks.
 *
 * Accepted URL Parameters: None
 */

// Namespace
namespace System;
use System\Cache\CacheManager;

// No direct access
defined("BF2_ADMIN") || die("No Direct Access");

// Connect to the database
$connection = Database::GetConnection("stats");

$isRanked = false;
if (Request::IsLocalHost())
{
    $serverIp = $_SERVER['SERVER_ADDR'];
    $stmt = $connection->query("SELECT EXISTS(SELECT * FROM `stats_provider_auth_ip` WHERE `address` IN ('{$serverIp}', '127.0.0.1', '::1') LIMIT 1)", \PDO::FETCH_NUM);
    $isRanked = $stmt->fetch()[0] == '1';
}
else
{
    $ipAddr = Request::ClientIp();
    $stmt = $connection->query("SELECT EXISTS(SELECT * FROM `stats_provider_auth_ip` WHERE `address`='{$ipAddr}' LIMIT 1)", \PDO::FETCH_NUM);
    $isRanked = $stmt->fetch()[0] == '1';
}

// Check file cache for cached response?
$item = null;
$expireTime = Config::Get('stats_aspx_cache_time');
if ($expireTime > 0)
{
    // Fetch cached response
    $cache = CacheManager::GetInstance('FileCache');
    $item = $cache->getItem("getbackendinfo{$isRanked}.aspx");
    $response = $item->get();

    // Check if response is empty (expired)
    if (!empty($response)) {
        die($response);
    }

    // Set expire time of new cached response
    $item->expiresAfter($expireTime);
}

// Prepare response
$Response = new AspResponse();

if ($isRanked)
{
    $Response->writeHeaderLine("ver", "rnk", "now");
    $Response->writeDataLine("0.1", "1", time());
}
else
{
    $Response->writeHeaderLine("ver", "now");
    $Response->writeDataLine("0.1", time());
}
$Response->writeHeaderLine("id", "kit", "name", "descr");

// fetch all unlocks from the database
$stmt = $connection->query("SELECT `id`, `kit_id`, `name`, `desc` FROM `unlock` ORDER BY `id`");
while ($row = $stmt->fetch())
{
    $Response->writeDataLine($row['id'], $row['kit_id'], $row['name'], $row['desc']);
}

$Response->send($item);