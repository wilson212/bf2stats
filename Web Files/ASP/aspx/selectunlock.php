<?php
/*
    Copyright (C) 2006-2021 BF2Statistics

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

use System\Database\DbConnection;

// No direct access
defined("BF2_ADMIN") || die("No Direct Access");

// Keep the legacy query parameters and GameSpy response framing.
$Response = new AspResponse();
$pid = filter_var($_GET['pid'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 4294967295]]);
$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 65535]]);
if ($pid === false || $id === false) {
    $Response->responseError(true, 107);
    $Response->writeHeaderLine("asof", "err");
    $Response->writeDataLine(time(), "Invalid Syntax!");
} else {
    try {
        Unlocks::select($pid, $id, Database::GetConnection('stats'));
        $Response->writeLine("OK");
    } catch (\DomainException $e) {
        $Response->responseError(true);
        $Response->writeHeaderLine("asof", "err");
        $Response->writeDataLine(time(), $e->getMessage());
    }
}
$Response->send();
