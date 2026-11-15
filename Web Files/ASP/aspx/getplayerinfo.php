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

	----------------------------------------------------------------------------------------

	URL: http://bf2tech.org/index.php/BF2_Statistics
*/

// Namespace
namespace System;

// No direct access
defined("BF2_ADMIN") || die("No Direct Access");

// Grab game constants
defined("NUM_ARMIES") || include SYSTEM_PATH . DIRECTORY_SEPARATOR . "GameConstants.php";

// Prepare output
$Response = new AspResponse();

// Set response format
$format = (isset($_GET['format'])) ? min(2, abs((int)$_GET['format'])) : 0;
$Response->setResponseFormat($format);

// Get database connection
$connection = Database::GetConnection("stats");

// Make sure we have the required Params and they are valid!
$pid = (isset($_GET['pid'])) ? (int)$_GET['pid'] : 0;
$info = $_GET['info'] ?? '';

// Ensure we have the required url parameters
if ($pid == 0 || empty($info)) {
    $Response->responseError(true, 107);
    $Response->writeHeaderLine("asof", "err");
    $Response->writeDataLine(time(), "Invalid Syntax!");
    $Response->send();
} elseif (stringStartsWith($info, "per*,cmb*")) {
    /**
     * BFHQ Request
     * @request too long!
     */
    // Fetch Player Data
    $row = $connection->query("SELECT * FROM player WHERE id = {$pid}")->fetch();
    if (empty($row))
    {
        $Response->responseError(true);
        $Response->writeHeaderLine("asof", "err");
        $Response->writeDataLine(time(), "Player Not Found!");
        $Response->send();
    }
    else
    {
        // Initialize response
        $Response->writeHeaderLine("asof");
        $Response->writeDataLine(time());

        // Build initial header
        $Output = [
            'pid' => $row['id'],
            'nick' => trim((string) $row['name']),
            'scor' => $row['score'],
            'jond' => $row['joined'],
            'wins' => $row['wins'],
            'loss' => $row['losses'],
            'mode0' => $row['mode0'],
            'mode1' => $row['mode1'],
            'mode2' => $row['mode2'],
            'time' => $row['time'],
            'smoc' => (($row['rank_id']) == 11 ? 1 : 0),
            'cmsc' => $row['skillscore'],
            'osaa' => '0.00', // Overall small arms accuracy
            'kill' => $row['kills'],
            'kila' => $row['damageassists'],
            'deth' => $row['deaths'],
            'suic' => $row['suicides'],
            'bksk' => $row['killstreak'],
            'wdsk' => $row['deathstreak'],
            'tvcr' => 0, // Top Victim
            'topr' => 0, // Top Opponent
            'klpm' => @number_format(60 * ($row['kills'] / max(1, $row['time'])), 2, '.', ''), // Kills per min
            'dtpm' => @number_format(60 * ($row['deaths'] / max(1, $row['time'])), 2, '.', ''), // Deaths per min
            'ospm' => @number_format(60 * ($row['score'] / max(1, $row['time'])), 2, '.', ''), // Score per min
            'klpr' => @number_format($row['kills'] / max(1, $row['rounds']), 2, '.', ''), // Kill per round
            'dtpr' => @number_format($row['deaths'] / max(1, $row['rounds']), 2, '.', ''), // Deaths per round
            'twsc' => $row['teamscore'],
            'cpcp' => $row['captures'],
            'cacp' => $row['captureassists'],
            'dfcp' => $row['defends'],
            'heal' => $row['heals'],
            'rviv' => $row['revives'],
            'rsup' => $row['resupplies'],
            'rpar' => $row['repairs'],
            'tgte' => $row['targetassists'],
            'dkas' => $row['driverassists'],
            'dsab' => $row['driverspecials'],
            'cdsc' => $row['cmdscore'],
            'rank' => $row['rank_id'],
            'kick' => $row['kicked'],
            'bbrs' => $row['bestscore'],
            'tcdr' => $row['cmdtime'],
            'ban' => $row['banned'],
            'lbtl' => $row['lastonline'],
            'vrk' => 0, // Vehicle Road Kills
            'tsql' => $row['sqltime'],
            'tsqm' => $row['sqmtime'],
            'tlwf' => $row['lwtime'],
            'mvks' => 0, // Top Victim kills
            'vmks' => 0, // Top Opponent Kills
            'mvns' => "-", // Top Victim name
            'mvrs' => 0, // Top Victim rank
            'vmns' => "-", // Top opponent name
            'vmrs' => 0, // Top opponent rank
            'fkit' => 0, // Fav Kit
            'fmap' => getFavMap($pid), // Fav Map
            'fveh' => 0, // Fav vehicle
            'fwea' => 0, // Fav Weapon
            'tnv' => 0, // NIGHT VISION GOGGLES Time - NOT USED
            'tgm' => 0 // GAS MASK TIME - NOT USED
        ];

        // Add Weapon data
        addWeaponData($Output, $pid);

        // Define our KD ratio callback
        /**
         * @param $val
         * @param $row
         *
         * @return string
         */
        $kdRatio = function($val, array $row)
        {
            $kills = (int)$row["kills"];
            $deaths = (int)$row["deaths"];
            return getKDRatioString($kills, $deaths);
        };

        // Add Vehicle Data
        addData($Output, $pid, 'player_vehicle', NUM_VEHICLES, 'fveh', [
            'vtm' => ['col' => 'time', 'dv' => 0],
            'vkl' => ['col' => 'kills', 'dv' => 0],
            'vdt' => ['col' => 'deaths', 'dv' => 0],
            'vkd' => ['dv' => '0:0', 'f' => $kdRatio],
            'vkr' => ['col' => 'roadkills', 'dv' => 0, 'inc' => 'vrk']
        ]);

        // Add Army Data
        addData($Output, $pid, 'player_army', 10, null, [
            'atm' => ['col' => 'time', 'dv' => 0],
            'awn' => ['col' => 'wins', 'dv' => 0],
            'alo' => ['col' => 'losses', 'dv' => 0],
            'abr' => ['col' => 'best', 'dv' => 0],
        ]);

        // Add kit data
        addData($Output, $pid, 'player_kit', NUM_KITS, 'fkit', [
            'ktm' => ['col' => 'time', 'dv' => 0],
            'kkl' => ['col' => 'kills', 'dv' => 0],
            'kdt' => ['col' => 'deaths', 'dv' => 0],
            'kkd' => ['dv' => '0:0', 'f' => $kdRatio],
        ]);

        // Add Tactical data
        addTacticalData($Output, $pid);

        // Add Fav Victim and Opponent data
        addPlayerTopVictimAndOpp($Output, $pid);

        // Add data and spit out the response
        $Response->writeHeaderDataArray($Output);
        $Response->send();
    }
} elseif (stringStartsWith($info, "rank") && stringEndsWith($info, "vac-"))
{
    // NOTE: xpack and bf2 have same return
    $query = <<<SQL
SELECT id, name, score, rank_id, defends, repairs, heals, resupplies, driverspecials, cmdscore, cmdtime, sqltime, 
  sqmtime, wins, losses, teamscore, killstreak, deathstreak, time, kills
FROM `player` 
WHERE `id` = {$pid}
SQL;

    // Make sure the Player exists
    $row = $connection->query($query)->fetch();
    if (empty($row))
    {
        $Response->responseError(true);
        $Response->writeHeaderLine("asof", "err");
        $Response->writeDataLine(time(), "Player Not Found!");
        $Response->send();
    }

    // Load stats data
    StatsData::Load();

    // Build initial player data
    $Output = [
        'pid' => $row['id'],
        'name' => $row['name'],
        'scor' => $row['score'],
        'rank' => $row['rank_id'],
        'dfcp' => $row['defends'],
        'rpar' => $row['repairs'],
        'heal' => $row['heals'],
        'rsup' => $row['resupplies'],
        'dsab' => $row['driverspecials'],
        'cdsc' => $row['cmdscore'],
        'tcdr' => $row['cmdtime'],
        'tsql' => $row['sqltime'],
        'tsqm' => $row['sqmtime'],
        'wins' => $row['wins'],
        'loss' => $row['losses'],
        'twsc' => $row['teamscore'],
        'bksk' => $row['killstreak'],
        'wdsk' => $row['deathstreak'],
        'time' => $row['time'],
        'kill' => $row['kills']
    ];

    // Weapons
    $includeTacticals = str_contains((string) $info, 'de-');

    // Add default data
    for ($i = 0; $i < StatsData::$NumWeapons; $i++)
    {
        // Skip tactical equipment and explosives (Leave 11 index for combined explosives)
        if ($i > 12 && $i < 18) {
            continue;
        }

        $Output["wkl-{$i}"] = 0;
    }

    // Grappling hook, Tactical, and Zipline
    if ($includeTacticals)
    {
        $Output['de-6'] = 0;
        $Output['de-7'] = 0;
        $Output['de-8'] = 0;
    }

    $result = $connection->query("SELECT * FROM `player_weapon` WHERE `player_id` = {$pid}");
    while ($roww = $result->fetch())
    {
        $i = (int)$roww['weapon_id'];

        // Tactical weapons
        if ($i > 14 && $i < 18 && $includeTacticals) {
            switch ($i)
            {
                case 17:
                    $Output['de-6'] = $roww['deployed'];
                    break;
                case 16:
                    $Output['de-8'] = $roww['deployed'];
                    break;
                case 15:
                    $Output['de-7'] = $roww['deployed'];
                    break;
            }
        } elseif (in_array($i, EXPLOSIVE_IDS)) {
            $Output["wkl-11"] += (int)$roww['kills'];
        } else
        {
            $Output["wkl-{$i}"] = $roww['kills'];
        }
    }

    // Kits
    for ($i = 0; $i < StatsData::$NumKits; $i++)
    {
        $Output["ktm-$i"] = 0; // Time
        $Output["kkl-$i"] = 0; // Kills
    }

    $result = $connection->query("SELECT * FROM `player_kit` WHERE `player_id` = {$pid}");
    while ($rowk = $result->fetch())
    {
        $i = $rowk["kit_id"];
        $Output["ktm-$i"] = $rowk["time"]; // Time
        $Output["kkl-$i"] = $rowk["kills"]; // Kills
    }

    // Vehicles
    for ($i = 0; $i < StatsData::$NumVehicles; $i++)
    {
        $Output["vtm-$i"] = 0; // Time
        $Output["vkl-$i"] = 0; // Kills
        $Output["vac-{$i}"] = 0; // Vehicle accuracy? Always zero
    }

    $result = $connection->query("SELECT * FROM `player_vehicle` WHERE `player_id` = {$pid}");
    while ($rowv = $result->fetch())
    {
        $i = $rowv["vehicle_id"];
        $Output["vtm-$i"] = $rowv["time"]; // Time
        $Output["vkl-$i"] = $rowv["kills"]; // Kills
    }

    // Army
    for ($i = 0; $i < StatsData::$NumArmies; $i++)
    {
        $Output["atm-{$i}"] = 0;
        $Output["abr-{$i}"] = 0;
        $Output["awn-{$i}"] = 0;
    }

    $result = $connection->query("SELECT * FROM `player_army` WHERE player_id = {$pid}");
    while ($rowa = $result->fetch())
    {
        $i = $rowa["army_id"];
        $Output["atm-{$i}"] = $rowa["time"];
        $Output["abr-{$i}"] = $rowa["best"];
        $Output["awn-{$i}"] = $rowa["wins"];
    }

    // Send response
    $Response->writeHeaderLine("asof");
    $Response->writeDataLine(time());
    $Response->writeHeaderDataArray($Output);
    $Response->send();
} elseif ($info == 'ktm-,vtm-,wtm-,mtm-')
{
    $kit = isset($_GET['kit']) ? (int)$_GET['kit'] : 0;
    $vehicle = isset($_GET['vehicle']) ? (int)$_GET['vehicle'] : 0;
    $weapon = isset($_GET['weapon']) ? (int)$_GET['weapon'] : 0;
    $map = isset($_GET['map']) ? (int)$_GET['map'] : 0;
    $name = null;

    // Fetch Player, make sure he exists
    $query = "SELECT `name` FROM `player` WHERE `id` = {$pid}";
    $result = $connection->query($query);
    if (!($name = $result->fetchColumn(0)))
    {
        $Response->responseError(true);
        $Response->writeHeaderLine("asof", "err");
        $Response->writeDataLine(time(), "Player Not Found!");
        $Response->send(); // script ends here
    }

    // Prepare response
    $Response->writeHeaderLine("asof");
    $Response->writeDataLine(time());
    $Response->writeHeaderLine("pid", "nick", "ktm-{$kit}", "vtm-{$vehicle}", "wtm-{$weapon}", "mtm-{$map}");

    // Kits
    $query = "SELECT `time` FROM `player_kit` WHERE `player_id`={$pid} AND `kit_id`={$kit}";
    $result = $connection->query($query);
		$kitt = (int)$result->fetchColumn(0);

    // Vehicles
    $query = "SELECT `time` FROM `player_vehicle` WHERE `player_id` = {$pid} AND `vehicle_id`={$vehicle}";
    $result = $connection->query($query);
		$vehiclet = (int)$result->fetchColumn(0);

    // Weapons
    $query = "SELECT `time` FROM `player_weapon` WHERE `player_id` = {$pid} AND `weapon_id`={$weapon}";
    $result = $connection->query($query);
    $weapont = (int)$result->fetchColumn(0);

    // Maps
    $query = "SELECT `time` FROM `player_map` WHERE (`player_id` = {$pid}) AND (`map_id` = {$map})";
    $result = $connection->query($query);
		$mapt = (int)$result->fetchColumn(0);

		// Write and send response
    $Response->writeDataLine($pid, $name, $kitt, $vehiclet, $weapont, $mapt);
    $Response->send();
} elseif (stringStartsWith($info, 'mtm-,mwn-,mls-'))
{
    // Make sure Player exists
    $query = "SELECT `name` FROM `player` WHERE `id` = {$pid}";
    $result = $connection->query($query);
    $name = $result->fetchColumn(0);
    if (empty($name))
    {
        $Response->responseError(true);
        $Response->writeHeaderLine("asof", "err");
        $Response->writeDataLine(time(), "Player Not Found!");
        $Response->send();
    }

    // Prepare response
    $Response->writeHeaderLine("asof");
    $Response->writeDataLine(time());

    // Add default map data
    $Output = ['pid' => $pid, 'nick' => $name];

    // Build individual headers, so they can group together in response
    $mtm = $mwn = $mls = $mbs = $mws = [];

    // Extended data?
    $Extended = (str_contains((string) $info, "mbs-"));

    // Vanilla BF2 Maps (Middle East Theatre)
    for ($i = 0; $i < 7; $i++)
    {
        $mtm[$i] = $mwn[$i] = $mls[$i] = 0;
        if ($Extended) {
            $mbs[$i] = $mws[$i] = 0;
        }
    }

    // Vanilla BF2 Maps (Asian Theatre)
    for ($i = 100; $i < 106; $i++)
    {
        $mtm[$i] = $mwn[$i] = $mls[$i] = 0;
        if ($Extended) {
            $mbs[$i] = $mws[$i] = 0;
        }
    }

    // Special BF2 Maps (Wake Island, Highway tampa)
    for ($i = 601; $i < 603; $i++)
    {
        $mtm[$i] = $mwn[$i] = $mls[$i] = 0;
        if ($Extended) {
            $mbs[$i] = $mws[$i] = 0;
        }
    }

    // Special forces maps
    for ($i = 300; $i < 308; $i++)
    {
        $mtm[$i] = $mwn[$i] = $mls[$i] = 0;
        if ($Extended) {
            $mbs[$i] = $mws[$i] = 0;
        }
    }

    // More Maps (smoke screen, Taraba Quarry, Jalalabad)
    for ($i = 10; $i < 13; $i++)
    {
        $mtm[$i] = $mwn[$i] = $mls[$i] = 0;
        if ($Extended) {
            $mbs[$i] = $mws[$i] = 0;
        }
    }

    // Armored Fury Maps
    for ($i = 200; $i < 203; $i++)
    {
        $mtm[$i] = $mwn[$i] = $mls[$i] = 0;
        if ($Extended) {
            $mbs[$i] = $mws[$i] = 0;
        }
    }

    // Great wall, Operation Blue Pearl
    for ($i = 110; $i < 130; $i += 10)
    {
        $mtm[$i] = $mwn[$i] = $mls[$i] = 0;
        if ($Extended) {
            $mbs[$i] = $mws[$i] = 0;
        }
    }

    // Prepare where statement
    $where = (str_contains((string) $info, "cmap-"))
        ? "`player_id` = {$pid}"
        : "`player_id` = {$pid} AND `map_id` < 700";

    // Fetch map data from DB
    $result = $connection->query("SELECT * FROM `player_map` WHERE {$where}");
    while ($row = $result->fetch())
    {
        $i = (int)$row['map_id'];
        $mtm[$i] = $row['time'];
        $mwn[$i] = $row['wins'];
        $mls[$i] = $row['losses'];
        if ($Extended)
        {
            $mbs[$i] = $row['best'];
            $mws[$i] = $row['worst'];
        }
    }

    /**
     * If the headers aren't all grouped together, the map info wont parse in the bf2 client,
     * Therefor was must do this in a non-efficient way
     */
    foreach ($mtm as $i => $value)
        $Output["mtm-{$i}"] = $value;

    foreach ($mwn as $i => $value)
        $Output["mwn-{$i}"] = $value;

    foreach ($mls as $i => $value)
        $Output["mls-{$i}"] = $value;

    if ($Extended)
    {
        foreach ($mbs as $i => $value)
            $Output["mbs-{$i}"] = $value;

        foreach ($mws as $i => $value)
            $Output["mws-{$i}"] = $value;
    }

    // Output map data
    $Response->writeHeaderDataArray($Output);
    $Response->send();
} elseif ($info == 'rank')
{
    $query = "SELECT `id`, `name`, `rank_id`, `chng`, `decr` FROM `player` WHERE `id` = {$pid}";
    $row = $connection->query($query)->fetch();
    if (empty($row))
    {
        $Response->responseError(true);
        $Response->writeHeaderLine("asof", "err");
        $Response->writeDataLine(time(), "Player Not Found!");
        $Response->send();
    }
    else
    {
        // Update
        if ($row['chng'] != 0 || $row['decr'] != 0) {
            $connection->exec("UPDATE `player` SET `chng` = 0, `decr` = 0 WHERE `id` = {$pid}");
        }

        $Response->writeHeaderLine("pid", "nick", "rank", "chng", "decr");
        $Response->writeDataLine($row['id'], $row['name'], $row['rank_id'], $row['chng'], $row['decr']);
        $Response->send();
    }
} else
{
    $Response->responseError(true);
    $Response->writeHeaderLine("asof", "err");
    $Response->writeDataLine(time(), "Parameter Error!");
    $Response->send();
}

/**
 * Adds an objects data to the output array using the given parameters.
 *
 * @param array $Output [Reference Variable]
 * @param int $pid The player ID
 * @param string $table The table we are fetching data from
 * @param int $limit The limit of rows
 * @param string $favKey The favorite object id Key in the output, or null if none
 * @param array $keyvals An array of [ out-key => $options ]. See Example
 *
 * @example:
 *  $options = [
 *      'outkey' => [
 *          'col' => The column name for this output key
 *          'dv' => The default value to output
 *          'f' => A callback function to format the value. Receives 2 parameters: (value of 'col', $row)
 *          'inc' => Increments the specified $Output key by the value returned by 'col' or 'f'
 *      ]
 * ];
 */
function addData(array &$Output, $pid, $table, $limit, $favKey, $keyvals): void
{
    /**
     * Add Defaults
     *
     * ORDER IS IMPORTANT HERE FOR BFHQ!
     */
    foreach ($keyvals as $key => $val)
    {
        for ($i = 0; $i < $limit; $i++)
            $Output["$key-$i"] = $val['dv'];
    }

    // Fetch DB connection
    $connection = Database::GetConnection("stats");
    $favTime = 0;
    $addFav = !empty($favKey);
    $idcol = str_replace('player_', '', $table) . "_id";

    $result = $connection->query("SELECT * FROM {$table} WHERE player_id = {$pid} AND {$idcol} < {$limit} ORDER BY {$idcol}");
    while ($row = $result->fetch())
    {
        $i = (int)$row[$idcol];
        foreach ($keyvals as $key => $value)
        {
            $val = (isset($value['col'])) ? $row[$value['col']] : 0;
            if (isset($value['f']))
            {
                $formatter = $value['f'];
                $val = $formatter($val, $row);
            }

            // Increment?
            if (isset($value['inc'])) {
                $Output[$value['inc']] += $val;
            }

            // Set output value
            $Output["$key-$i"] = $val;
        }

        // Favorite
        if ($addFav)
        {
            $time = (int)$row["time"];
            if ($time > $favTime)
            {
                $Output[$favKey] = $i;
                $favTime = $time;
            }
        }
    }
}

/**
 * Adds the weapon data to the current output
 *
 * @param mixed[] $Output [Reference Variable]
 * @param string|int $pid The player ID
 */
function addWeaponData(array &$Output, $pid): void
{
    // Fetch DB connection
    $connection = Database::GetConnection("stats");
    $limit = NUM_WEAPONS - 1;

    // Assign some vars
    $favTime = 0;
    $totalHits = 0;
    $totalFired = 0;

    // Explosives sum variables
    $eKills = $eDeaths = 0;

    /**
     * Add Headers and Default Values
     *
     * ORDER IS IMPORTANT HERE FOR BFHQ!
     */
    addHeaderRange($Output, 0, $limit, ['wtm' => 0, 'wkl' => 0, 'wdt' => 0, 'wac' => 0, 'wkd' => '0:0']);

    // Weapons
    $result = $connection->query("SELECT * FROM player_weapon WHERE player_id = {$pid} ORDER BY weapon_id");
    while ($row = $result->fetch())
    {
        $i = (int)$row['weapon_id'];

        // Exclude Tactical weapons
        if ($i < NUM_WEAPONS)
        {
            // Define whether this weapon is an explosive
            $isExplosive = in_array($i, EXPLOSIVE_IDS);
            if ($isExplosive) {
                $i = 11;
            }

            // Convert weapon stats to integers
            $time = (int)$row["time"];
            $kills = (int)$row["kills"];
            $deaths = (int)$row["deaths"];
            $hits = (int)$row["hits"];
            $fired = (int)$row["fired"];
            $acc = ($fired != 0 && $hits != 0) ? round(($hits / $fired) * 100, 0) : 0;

            // Update totals for later
            $totalFired += $fired;
            $totalHits += $hits;

            // Define favorite based on Time Played
            if ($time > $favTime)
            {
                $Output['fwea'] = $i;
                $favTime = $time;
            }

            // Set weapon data
            $Output["wtm-$i"] += $time;      // Time
            $Output["wkl-$i"] += $kills;     // Kills
            $Output["wdt-$i"] += $deaths;    // Deaths
            $Output["wac-$i"] += $acc;       // Accuracy

            // check for explosive, which are all combined into wkl-11
            if ($isExplosive)
            {
                $eKills += $kills;
                $eDeaths += $deaths;
            }
            else
            {
                // K/D Ratio
                $Output["wkd-$i"] = getKDRatioString($kills, $deaths);
            }
        }
    }

    // K/D Ratio for explosives
    $Output["wkd-11"] = getKDRatioString($eKills, $eDeaths);

    // Set favorite data's
    $acc = ($totalHits != 0) ? round($totalHits / $totalFired, 2) * 100 : 0;
    $Output['osaa'] = number_format((float)$acc, 2, '.', '');
}

/**
 * Adds the tactical data to the current output
 *
 * @param mixed[] $Output [Reference Variable]
 * @param string|int $pid The player ID
 */
function addTacticalData(array &$Output, $pid): void
{
    // Fetch DB connection
    $connection = Database::GetConnection("stats");

    /**
     * Add Defaults
     *
     * ORDER IS IMPORTANT HERE FOR BFHQ!
     */
    for ($i = 6; $i < 9; $i++)
        $Output["de-$i"] = 0;

    // Weapons
    $result = $connection->query("SELECT * FROM player_weapon WHERE player_id = {$pid} AND weapon_id BETWEEN 16 AND 18");
    while ($row = $result->fetch())
    {
        $i = ((int)$row['weapon_id']) - 10;
        $Output["de-$i"] = (int)$row['deployed'];
    }
}

/**
 * Adds the favorite victim and opponent data to the current output
 *
 * @param mixed[] $Output [Reference Variable]
 * @param string|int $pid The player ID
 */
function addPlayerTopVictimAndOpp(array &$Output, $pid): void
{
    // Fetch DB connection
    $connection = Database::GetConnection("stats");

    // Fetch Fav Victim
    $query = "SELECT victim, `count` FROM player_kill WHERE attacker={$pid} ORDER BY `count` DESC LIMIT 1";
    $row = $connection->query($query)->fetch();
    if (!empty($row))
    {
        $victim = $row['victim'];
        $count = $row['count'];
        $row = $connection->query("SELECT name, rank_id FROM player WHERE id={$victim}")->fetch();
        if (!empty($row))
        {
            $Output['tvcr'] = $victim;
            $Output['mvks'] = $count;
            $Output['mvns'] = $row['name'];
            $Output['mvrs'] = $row['rank_id'];
        }
    }

    // Fetch Fav Opponent
    $query = "SELECT attacker, `count` FROM player_kill WHERE victim={$pid} ORDER BY `count` DESC LIMIT 1";
    $row = $connection->query($query)->fetch();
    if (!empty($row))
    {
        $attacker = $row['attacker'];
        $count = $row['count'];
        $row = $connection->query("SELECT name, rank_id FROM player WHERE id={$attacker}")->fetch();
        if (!empty($row))
        {
            $Output['topr'] = $attacker;
            $Output['vmks'] = $count;
            $Output['vmns'] = $row['name'];
            $Output['vmrs'] = $row['rank_id'];
        }
    }
}

/**
 * Returns the favorite map ID for the specified player
 *
 * @param int $pid The player ID
 */
function getFavMap($pid): int
{
    // Fetch DB connection
    $connection = Database::GetConnection("stats");

    // Fetch Fav Victim
    $query = "SELECT map_id FROM player_map WHERE player_id={$pid} AND map_id < 700 ORDER BY `time` DESC LIMIT 1";
    $result = $connection->query($query)->fetchColumn(0);
    return ($result !== false) ? (int)$result : 0;
}

/**
 * Calculate greatest common divisor of x and y. The result is always positive even
 * if either of, or both, input operands are negative.
 *
 * @param number $x
 * @param number $y
 *
 * @return number A positive number that divides into both x and y
 */
function denominator($x, $y): float|int
{
    while ($y != 0)
    {
        $remainder = $x % $y;
        $x = $y;
        $y = $remainder;
    }

    return abs($x);
}

function getKDRatioString(string $kills, $deaths): string
{
    // K/D Ratio
    if ($deaths != 0)
    {
        $den = denominator($kills, $deaths);
        return ($kills / $den) . ':' . ($deaths / $den);
    }
    else {
        return $kills . ':0';
    }
}

function addHeaderRange(array &$Output, $offset, $limit, $headers): void
{
    foreach ($headers as $key => $value)
    {
        for ($i = $offset; $i < $limit; $i++)
        {
            $Output["$key-$i"] = $value;
        }
    }
}

/**
 * Determines whether the end of a string matches the specified string
 */
function stringEndsWith( $string, $sub ): bool
{
    $len = strlen( (string) $sub );
    return substr_compare( (string) $string, (string) $sub, -$len, $len ) === 0;
}

/**
 * Determines whether the beginning of a string matches a specified string
 */
function stringStartsWith( $string, $sub ): bool
{
    return substr_compare( (string) $string, (string) $sub, 0, strlen( (string) $sub ) ) === 0;
}