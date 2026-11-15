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

/**
 * This provides details of the players unlocked weapons.
 *
 * Accepted URL Parameters:
 * @param int $pid Unique player ID
 */

// Namespace
namespace System;

// No direct access
use System\Database\DbConnection;
use System\Collections\Dictionary;

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
else
{
    // Connect to the database
    $connection = Database::GetConnection("stats");

    switch ((int)Config::Get('game_unlocks'))
    {
        case 0:
            // Get Player Data
            $row = $connection->query("SELECT `name`, `rank_id` FROM `player` WHERE `id` = {$pid}")->fetch();
            if (empty($row))
            {
                $Response->writeHeaderLine("pid", "nick", "asof");
                $Response->writeDataLine($pid, "No_Player", time());
                $Response->writeHeaderLine("enlisted", "officer");
                $Response->writeDataLine(0, 0);
                $Response->writeHeaderLine("id", "state");
                $Response->send();
            }
            else
            {
                // Cast to an int
                $rank = (int)$row['rank_id'];
                $name = $row['name'];

                // Determine Earned Unlocks due to Rank
                $totalPlayerUnlockCount = Unlocks::countByRank($rank);

                // Determine Bonus Unlocks due to Kit Badges
                $totalPlayerUnlockCount += Unlocks::countByBadges($pid, $rank, $connection);

                // Player used unlocks
                $usedCount = 0;

                // Get all current unlocks, and set the status to locked by default
                $unlockStatus = new Dictionary();
                $result = $connection->query("SELECT `id` FROM `unlock` ORDER BY `id` ASC");
                while ($row = $result->fetch())
                {
                    $unlockStatus->add($row['id'], 'n');
                }

                // Get all unlock requirements
                $unlockChecks = [];
                $result = $connection->query("SELECT `parent_id`, `child_id` FROM `unlock_requirement`");
                while ($row = $result->fetch())
                {
                    $unlockChecks[] = $row;
                }

                $selected = [];

                // Get players current unlocks
                $query = "SELECT unlock_id FROM `player_unlock` WHERE `player_id`={$pid} ORDER BY `unlock_id` ASC";
                $result = $connection->query($query);
                while ($row = $result->fetch())
                {
                    $usedCount++;
                    $unlockStatus[$row['unlock_id']] = 's';
                    $selected[(int)$row['unlock_id']] = true;
                }

                // Check unlock requirements
                foreach ($unlockChecks as $requirement)
                {
                    $required = $requirement['parent_id'];
                    $want = $requirement['child_id'];
                    // Need to remove unlocks that require other unlocks to
                    // be selected, otherwise BF2 will offer the unlock regardless
                    // if the required unlock has been selected
                    if (!isset($selected[(int)$required]) && !isset($selected[(int)$want]))
                    {
                        $unlockStatus->remove($want);
                    }
                }

                // Determine available unlocks count
                $available = max(0, ($totalPlayerUnlockCount - $usedCount));

                // Prepare output
                $Response->writeHeaderLine("pid", "nick", "asof");
                $Response->writeDataLine($pid, $name, time());
                $Response->writeHeaderLine("enlisted", "officer");
                $Response->writeDataLine($available, 0);
                $Response->writeHeaderLine("id", "state");

                // Append output
                foreach ($unlockStatus as $unlockId => $status)
                {
                    $Response->writeDataLine($unlockId, $status);
                }

                // Send response
                $Response->send();
            }
            break;
        case 1:
            $Response->writeHeaderLine("pid", "nick", "asof");
            $Response->writeDataLine($pid, "All_Unlocks", time());
            $Response->writeHeaderLine("enlisted", "officer");
            $Response->writeDataLine(0, 0);
            $Response->writeHeaderLine("id", "state");

            // Get all current unlocks
            $result = $connection->query("SELECT `id` FROM `unlock` ORDER BY `id` ASC");
            while ($row = $result->fetch())
                $Response->writeDataLine($row['id'], 's');

            $Response->send();
            break;
        default:
            $Response->writeHeaderLine("pid", "nick", "asof");
            $Response->writeDataLine($pid, "No_Player", time());
            $Response->writeHeaderLine("enlisted", "officer");
            $Response->writeDataLine(0, 0);
            $Response->writeHeaderLine("id", "state");

            // Get all current unlocks
            $query = "SELECT `id` FROM `unlock` ORDER BY `id` ASC";
            $result = $connection->query($query);
            while ($row = $result->fetch())
                $Response->writeDataLine($row['id'], 'n');

            $Response->send();
            break;
    }
}
