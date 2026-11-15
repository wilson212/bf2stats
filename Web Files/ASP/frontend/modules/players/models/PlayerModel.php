<?php
/**
 * BF2Statistics ASP Framework
 *
 * Author:       Steven Wilson
 * Copyright:    Copyright (c) 2006-2021, BF2statistics.com
 * License:      GNU GPL v3
 *
 */
use System\Database;
use System\Database\DbConnection;
use System\Battlefield2;
use System\BF2\Player;
use System\BF2\RankCalculator;
use System\Collections\Dictionary;
use System\Database\UpdateOrInsertQuery;
use System\IO\File;
use System\TimeHelper;
use System\TimeSpan;
use System\View;

/**
 * Player Model
 *
 * @package Players
 * @subpackage Models
 */
class PlayerModel
{
    /**
     * @var DbConnection The stats database connection
     */
    protected $pdo;

    /**
     * PlayerModel constructor.
     */
    public function __construct()
    {
        // Fetch database connection
        $this->pdo = Database::GetConnection('stats');
    }

    /**
     * Creates a new player record in the player table
     *
     * @param string $name The player unique nick
     * @param string $password The player password, or null if creating an offline account
     * @param string $email The player email address
     * @param string $iso The player country ISO code.
     * @param int $rank The starting player rank
     *
     * @return bool true on success, otherwise false
     *
     * @throws ArgumentException thrown if any of the parameters are invalid.
     */
    public function createPlayer($name, $password, $email, $iso, $rank = 0)
    {
        // Check for valid ISO
        $iso = trim($iso);
        if (strlen($iso) != 2) {
            throw new ArgumentException('Invalid country ISO passed: '. $iso, 'iso');
        }

        // Check length of player name
        $name = preg_replace("/[^". Player::NAME_REGEX ."]/", '', trim($name));
        if (empty($name)) {
            throw new ArgumentException('Empty player name passed', 'name');
        } elseif (strlen($name) > 32) {
            throw new ArgumentException('Player name cannot be longer than 32 characters!', 'name');
        }

        // Prepare statement

        return $this->pdo->insert('player', [
            'name' => $name,
            'password' => md5(trim($password)),
            'rank_id' => (int)$rank,
            'email' => trim($email),
            'country' => $iso
        ]);
    }

    /**
     * Updates the specified player fields in the player table. Data is not filtered.
     *
     * @param int $id The player id
     * @param array $cols An array of [name => value] to set for the player
     *
     * @return bool true on success, otherwise false
     */
    public function updatePlayer($id, $cols)
    {
        return $this->pdo->update('player', $cols, ['id' => $id]);
    }

    /**
     * Deletes a player record from the player table, and all associated
     * records from the other stats tables
     *
     * @param int $id The player id
     *
     * @return int The number of rows affected by the last SQL statement

    public function deletePlayer($id)
    {
        // Prepare statement
        $stmt = $this->pdo->prepare("DELETE FROM player WHERE id=:id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }
     */

    /**
     * Deletes all bot records from the player table that have a play time of 0, 
	 * and all associated records from the other stats tables.
     *
     * @return int The number of rows affected by the last SQL statement
     */
    public function deleteBotPlayers(): int|false
    {
        // Prepare statement
        return $this->pdo->exec("DELETE FROM player WHERE password='' AND `time`=0");
    }

    /**
     * Fetches player data for the Players controller
     *
     * @param int $id the player ID
     *
     * @return bool|array
     */
    public function fetchPlayer($id)
    {
        // Sanitize
        $id = (int)$id;

        // Fetch player
        $query = "SELECT * FROM player WHERE `id`={$id}";
        $player = $this->pdo->query($query)->fetch();
        if (empty($player)) {
            return false;
        }

        // Get round counts
        $query = "SELECT COUNT(round_id) FROM player_round_history WHERE player_id={$id}";
        $player['total_rounds'] = (int)$this->pdo->query($query)->fetchColumn(0);

        // Remove password
        unset($player['password']);
        return $player;
    }

    /**
     * Resets a players stats, and removes all awards, unlocks, and rank histories
     *
     * @param int $id The player ID
     *
     * @throws Exception
     */
    public function resetPlayerStats($id): void
    {
        // Cast id to an integer, to be safe
        $id = (int)$id;

        try
        {
            // Start transaction
            $this->pdo->beginTransaction();

            // Delete all records from the following tables for this player
            $tables = [
                'player_kit', 'player_army', 'player_award', 'player_rank_history',
                'player_map', 'player_vehicle', 'player_weapon', 'player_unlock'
            ];
            foreach ($tables as $table)
            {
                $this->pdo->exec("DELETE FROM {$table} WHERE player_id={$id}");
            }

            // Reset all player stats
            $query = new UpdateOrInsertQuery($this->pdo, 'player');
            $query->set('time', '=', 0);
            $query->set('rounds', '=', 0);
            $query->set('rank_id', '=', 0);
            $query->set('score', '=', 0);
            $query->set('cmdscore', '=', 0);
            $query->set('skillscore', '=', 0);
            $query->set('teamscore', '=', 0);
            $query->set('kills', '=', 0);
            $query->set('wins', '=', 0);
            $query->set('losses', '=', 0);
            $query->set('deaths', '=', 0);
            $query->set('captures', '=', 0);
            $query->set('captureassists', '=', 0);
            $query->set('neutralizes', '=', 0);
            $query->set('neutralizeassists', '=', 0);
            $query->set('defends', '=', 0);
            $query->set('damageassists', '=', 0);
            $query->set('heals', '=', 0);
            $query->set('revives', '=', 0);
            $query->set('resupplies', '=', 0);
            $query->set('repairs', '=', 0);
            $query->set('targetassists', '=', 0);
            $query->set('driverspecials', '=', 0);
            $query->set('teamkills', '=', 0);
            $query->set('teamdamage', '=', 0);
            $query->set('teamvehicledamage', '=', 0);
            $query->set('suicides', '=', 0);
            $query->set('cmdtime', '=', 0);
            $query->set('sqltime', '=', 0);
            $query->set('sqmtime', '=', 0);
            $query->set('lwtime', '=', 0);
            $query->set('timepara', '=', 0);
            $query->set('mode0', '=', 0);
            $query->set('mode1', '=', 0);
            $query->set('mode2', '=', 0);
            $query->set('bestscore', '=', 0);
            $query->set('deathstreak', '=', 0);
            $query->set('killstreak', '=', 0);
            $query->where('id', '=', $id);
            $query->executeUpdate();

            // Commit changes
            $this->pdo->commit();
        }
        catch (Exception $e)
        {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Removes all awards for the specified player
     *
     * @param int $id the player id
     *
     * @return int the number of rows that were deleted
     */
    public function resetPlayerAwards($id): int|false
    {
        // Cast id to an integer, to be safe
        $id = (int)$id;
        return $this->pdo->exec("DELETE FROM player_award WHERE player_id={$id}");
    }

    /**
     * Removes all weapon unlocks for the specified player
     *
     * @param int $id the player id
     *
     * @return int the number of rows that were deleted
     */
    public function resetPlayerUnlocks($id): int|false
    {
        // Cast id to an integer, to be safe
        $id = (int)$id;
        return $this->pdo->exec("DELETE FROM player_unlock WHERE player_id={$id}");
    }

    /**
     * Bans or UnBans a player
     *
     * @param int $id The player id
     * @param bool $banned
     *
     * @return int the number of rows affected by the last SQL statement
     */
    public function setPlayerBanned($id, $banned): int
    {
        $time = ($banned) ? time() : 0;
        $mode = ($banned) ? 1 : 0;

        // Prepare statement
        $stmt = $this->pdo->prepare("UPDATE player SET permban=:mode, bantime=:time WHERE id=:id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':mode', $mode, PDO::PARAM_INT);
        $stmt->bindValue(':time', $time, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }

    /**
     * Appends a players Army data to a view
     *
     * @param int $id
     */
    public function attachArmyData($id, View $view): void
    {
        // Insure $id is an integer
        $id = (int)$id;

        // Prepare return data
        $data = [];
        $totals = [
            'time' => 0,
            'wins' => 0,
            'losses' => 0,
            'ratio' => 0.00,
            'best' => 0
        ];
        $averages = [
            'time' => 0,
            'wins' => 0,
            'losses' => 0,
            'ratio' => 0.00,
            'best' => 0
        ];

        // fetch player kit data
        $query = "SELECT * FROM `player_army` AS pk JOIN army ON pk.army_id = army.id WHERE `player_id`=".$id;
        $result = $this->pdo->query($query);

        // iterate through the results
        while ($row = $result->fetch())
        {
            $wins = (int)$row['wins'];
            $losses = (int)$row['losses'];
            $time = (int)$row['time'];
            $best = (int)$row['best'];
            $ratio = ($losses > 0) ? round($wins / $losses, 3) : (float)$wins;

            // Add kit data
            $data[] = [
                'name' => $row['name'],
                'wins' => $wins,
                'losses' => $losses,
                'best' => $best,
                'time' => TimeHelper::SecondsToHms($time),
                'ratio' => number_format($ratio, 3)
            ];

            // Totals
            $totals['wins'] += $wins;
            $totals['losses'] += $losses;
            $totals['time'] += $time;
            $totals['best'] += $best;
            $totals['ratio'] += $ratio;
        }

        // Do averages
        $length = count($data);
        if ($length > 0)
        {
            $averages['wins'] = ($totals['wins'] > 0)
                ? number_format($totals['wins'] / $length, 0)
                : 0;
            $averages['losses'] = ($totals['losses'] > 0)
                ? number_format($totals['losses'] / $length, 0)
                : 0;
            $averages['time'] = ($totals['time'] > 0)
                ? (int)round($totals['time'] / $length, 0)
                : 0;
            $averages['best'] = ($totals['best'] > 0)
                ? number_format($totals['best'] / $length, 0)
                : 0;
            $averages['ratio'] = ($totals['ratio'] > 0)
                ? round($totals['ratio'] / $length, 3)
                : "0.00";
        }

        // Finalize totals
        $totals['wins'] = number_format($totals['wins']);
        $totals['losses'] = number_format($totals['losses']);
        $totals['time'] = TimeHelper::SecondsToHms($totals['time']);
        $averages['time'] = TimeHelper::SecondsToHms($averages['time']);

        // attach data
        $view->set("armyData", $data);
        $view->set("armyTotals", $totals);
        $view->set("armyAverage", $averages);
    }

    /**
     * Appends a players Kit data to a view
     *
     * @param int $id
     */
    public function attachKitData($id, View $view): void
    {
        // ensure $id is an integer
        $id = (int)$id;

        // Prepare return data
        $data = [];
        $totals = [
            'kills' => 0,
            'deaths' => 0,
            'time' => 0,
            'ratio' => 0.00
        ];
        $averages = [
            'kills' => 0,
            'deaths' => 0,
            'time' => 0,
            'ratio' => 0.00
        ];

        // fetch player kit data
        $query = "SELECT * FROM `player_kit` AS pk JOIN kit ON pk.kit_id = kit.id WHERE `player_id`=".$id;
        $result = $this->pdo->query($query);

        // iterate through the results
        while ($row = $result->fetch())
        {
            $kills = (int)$row['kills'];
            $deths = (int)$row['deaths'];
            $time = (int)$row['time'];
            $ratio = ($deths > 0) ? round($kills / $deths, 3) : (float)$kills;

            // Add kit data
            $data[] = [
                'name' => $row['name'],
                'kills' => $kills,
                'deaths' => $deths,
                'time' => TimeHelper::SecondsToHms($time),
                'ratio' => number_format($ratio, 3)
            ];

            // Totals
            $totals['kills'] += $kills;
            $totals['deaths'] += $deths;
            $totals['time'] += $time;
            $totals['ratio'] += $ratio;
        }

        // Do averages
        $length = count($data);
        if ($length > 0)
        {
            $averages['kills'] = ($totals['kills'] > 0)
                ? number_format($totals['kills'] / $length, 0)
                : 0;
            $averages['deaths'] = ($totals['deaths'] > 0)
                ? number_format($totals['deaths'] / $length, 0)
                : 0;
            $averages['time'] = ($totals['time'] > 0)
                ? (int)round($totals['time'] / $length, 0)
                : 0;
            $averages['ratio'] = ($totals['ratio'] > 0)
                ? round($totals['ratio'] / $length, 3)
                : "0.00";
        }

        // Finalize totals
        $totals['kills'] = number_format($totals['kills']);
        $totals['deaths'] = number_format($totals['deaths']);
        $totals['time'] = TimeHelper::SecondsToHms($totals['time']);
        $averages['time'] = TimeHelper::SecondsToHms($averages['time']);

        // attach data
        $view->set("kitData", $data);
        $view->set("kitTotals", $totals);
        $view->set("kitAverage", $averages);
    }

    /**
     * Appends a players Vehicle data to a view
     *
     * @param int $id
     */
    public function attachVehicleData($id, View $view): void
    {
        // Insure $id is an integer
        $id = (int)$id;

        // Prepare return data
        $data = [];
        $totals = [
            'kills' => 0,
            'deaths' => 0,
            'time' => 0,
            'roadKills' => 0,
            'ratio' => 0.00
        ];
        $averages = [
            'kills' => 0,
            'deaths' => 0,
            'time' => 0,
            'roadKills' => 0,
            'ratio' => 0.00
        ];

        // fetch player kit data
        $query = "SELECT * FROM `player_vehicle` AS pk JOIN vehicle ON pk.vehicle_id = vehicle.id WHERE `player_id`=".$id;
        $result = $this->pdo->query($query);

        // iterate through the results
        while ($row = $result->fetch())
        {
            $kills = (int)$row['kills'];
            $deths = (int)$row['deaths'];
            $time = (int)$row['time'];
            $rKill = (int)$row['roadkills'];
            $ratio = ($deths > 0) ? round($kills / $deths, 3) : (float)$kills;

            // Add kit data
            $data[] = [
                'name' => $row['name'],
                'kills' => $kills,
                'deaths' => $deths,
                'roadKills' => $rKill,
                'time' => TimeHelper::SecondsToHms($time),
                'ratio' => number_format($ratio, 3)
            ];

            // Totals
            $totals['kills'] += $kills;
            $totals['deaths'] += $deths;
            $totals['time'] += $time;
            $totals['roadKills'] += $rKill;
            $totals['ratio'] += $ratio;
        }

        // Do averages
        $length = count($data);
        if ($length > 0)
        {
            $averages['kills'] = ($totals['kills'] > 0)
                ? number_format($totals['kills'] / $length, 0)
                : 0;
            $averages['deaths'] = ($totals['deaths'] > 0)
                ? number_format($totals['deaths'] / $length, 0)
                : 0;
            $averages['time'] = ($totals['time'] > 0)
                ? (int)round($totals['time'] / $length, 0)
                : 0;
            $averages['roadKills'] = ($totals['roadKills'] > 0)
                ? number_format($totals['roadKills'] / $length, 0)
                : 0;
            $averages['ratio'] = ($totals['ratio'] > 0)
                ? round($totals['ratio'] / $length, 3)
                : "0.00";
        }

        // Finalize totals
        $totals['kills'] = number_format($totals['kills']);
        $totals['deaths'] = number_format($totals['deaths']);
        $totals['roadKills'] = number_format($totals['roadKills']);
        $totals['time'] = TimeHelper::SecondsToHms($totals['time']);
        $averages['time'] = TimeHelper::SecondsToHms($averages['time']);

        // attach data
        $view->set("vehicleData", $data);
        $view->set("vehicleTotals", $totals);
        $view->set("vehicleAverage", $averages);
    }

    /**
     * Appends a players Weapon data to a view
     *
     * @param int $id
     */
    public function attachWeaponData($id, View $view): void
    {
        // Insure $id is an integer
        $id = (int)$id;

        // Prepare return data
        $data = [];
        $totals = [
            'kills' => 0,
            'deaths' => 0,
            'time' => 0,
            'ratio' => 0.00,
            'accuracy' => 0.00,
            'fired' => 0,
            'hits' => 0
        ];
        $averages = [
            'kills' => 0,
            'deaths' => 0,
            'time' => 0,
            'ratio' => 0.00,
            'accuracy' => 0.00,
            'fired' => 0,
            'hits' => 0
        ];

        // fetch player kit data
        $query = "SELECT * FROM `player_weapon_view` AS pk JOIN weapon ON pk.weapon_id = weapon.id WHERE `player_id`=".$id;
        $result = $this->pdo->query($query);

        // iterate through the results
        while ($row = $result->fetch())
        {
            $kills = (int)$row['kills'];
            $deths = (int)$row['deaths'];
            $time = (int)$row['time'];
            $fired = (int)$row['fired'];
            $acc = ((float)$row['accuracy']) * 100;
            $ratio = ($deths > 0) ? round($kills / $deths, 3) : (float)$kills;

            // Add kit data
            $data[] = [
                'name' => $row['name'],
                'kills' => $kills,
                'deaths' => $deths,
                'fired' => $fired,
                'hits' =>(int)$row['hits'],
                'time' => TimeHelper::SecondsToHms($time),
                'ratio' => number_format($ratio, 3),
                'accuracy' => round($acc, 2)
            ];

            // Totals
            $totals['kills'] += $kills;
            $totals['deaths'] += $deths;
            $totals['time'] += $time;
            $totals['fired'] += $fired;
            $totals['hits'] += (int)$row['hits'];
            $totals['accuracy'] += $acc;
            $totals['ratio'] += $ratio;
        }

        // Do averages
        $length = count($data);
        if ($length > 0)
        {
            $averages['kills'] = ($totals['kills'] > 0)
                ? number_format($totals['kills'] / $length, 0)
                : 0;
            $averages['deaths'] = ($totals['deaths'] > 0)
                ? number_format($totals['deaths'] / $length, 0)
                : 0;
            $averages['time'] = ($totals['time'] > 0)
                ? (int)round( $totals['time'] / $length, 0 )
                : 0;
            $averages['fired'] = ($totals['fired'] > 0)
                ? number_format($totals['fired'] / $length, 0)
                : 0;
            $averages['ratio'] = ($totals['ratio'] > 0)
                ? round($totals['ratio'] / $length, 3)
                : "0.00";
            $averages['accuracy'] = ($totals['fired'] > 0)
                ? round((($totals['hits'] / $totals['fired']) * 100), 2)
                : "0.00";
        }

        // Finalize totals
        $totals['kills'] = number_format($totals['kills']);
        $totals['deaths'] = number_format($totals['deaths']);
        $totals['deaths'] = number_format($totals['fired']);
        $totals['time'] = TimeHelper::SecondsToHms($totals['time']);
        $averages['time'] = TimeHelper::SecondsToHms($averages['time']);

        // attach data
        $view->set("weaponData", $data);
        $view->set("weaponTotals", $totals);
        $view->set("weaponAverage", $averages);
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
    public function getDenominator($x, $y): float|int
    {
        while ($y != 0)
        {
            $remainder = $x % $y;
            $x = $y;
            $y = $remainder;
        }

        return abs($x);
    }

    /**
     * Formats player scores and times
     *
     *
     */
    public function formatPlayerData(array $player): array
    {
        $data = [];
        $time = 0;
        $score = 0;
        $wins = 0;
        $losses = 0;

        foreach ($player as $key => $value)
        {
            switch ($key)
            {
                case 'time':
                    $time = (int)$value;
                    $span = TimeSpan::FromSeconds($time);
                    $data['time'] = $time;
                    if ($time < 86400) {
                        $data['timeplayed'] = $span->format("%y Hours, %j Mins, %w Seconds");
                    } else {
                        $data['timeplayed'] = $span->format("%d Days, %y Hours, %j Mins");
                    }
                    break;
                case 'joined':
                    $value = (int)$value;
                    $data['joined'] = ($value == 0) ? "Never" : date('F jS, Y g:i A T', $value);
                    break;
                case 'lastonline':
                    $value = (int)$value;
                    $data['lastonline'] = ($value == 0) ? "Never" : date('F jS, Y g:i A T', $value);
                    break;
                case 'cmdtime':
                case 'sqmtime':
                case 'sqltime':
                case 'lwtime':
                    $data[$key] = TimeHelper::SecondsToHms((int)$value);
                    break;
                case 'kills':
                case 'deaths':
                case 'teamscore':
                case 'cmdscore':
                case 'skillscore':
                case 'heals':
                case 'revives':
                case 'resupplies':
                case 'repairs':
                case 'captures':
                case 'captureassists':
                case 'neutralizes':
                case 'neutralizeassists':
                case 'defends':
                case 'driverspecials':
                case 'damageassists':
                case 'rounds':
                case 'teamdamage':
                case 'teamvehicledamage':
                case 'suicides':
                case 'killstreak':
                case 'bestscore':
                    $data[$key] = number_format((int)$value);
                    break;
                case 'wins':
                    $wins = (int)$value;
                    $data[$key] = number_format($wins);
                    break;
                case 'losses':
                    $losses = (int)$value;
                    $data[$key] = number_format($losses);
                    break;
                case 'score':
                    $score = (int)$value;
                    $data[$key] = number_format($score);
                    break;
                case 'permban':
                    $banned = ((int)$value == 1);
                    $data[$key] = $value;

                    if ($banned)
                    {
                        $data['statustext'] = 'Banned';
                        $data['badge'] = 'important';
                    }
                    else
                    {
                        $online = (int)$player['online'];
                        if ($online !== 0)
                        {
                            $data['statustext'] = 'Online';
                            $data['badge'] = 'success';
                        }
                        else
                        {
                            $lastSeen = (int)$player['lastonline'];
                            $aMonthAgo = time() - (86400 * 30);
                            if ($aMonthAgo > $lastSeen)
                            {
                                $data['statustext'] = 'Inactive';
                                $data['badge'] = 'inactive';
                            }
                            else
                            {
                                $data['statustext'] = 'Active';
                                $data['badge'] = 'info';
                            }
                        }
                    }
                    break;
                default:
                    $data[$key] = $value;
                    break;
            }
        }

        // Get player ratio
        $kills = (int)$player['kills'];
        $deaths = (int)$player['deaths'];
        $den = $this->getDenominator($kills, $deaths);
        $data['ratio'] = $den == 0 ? "0/0" : ($kills / $den) . '/' . ($deaths / $den);
        $data['ratio2'] = ($deaths > 0) ? number_format( $kills / $deaths, 2) : $kills . ".00";
        $data['ratioColor'] = ($data['ratio2'] > 0.99) ? "green" : "red";

        // Set W/L Ratio
        $den = $this->getDenominator($wins, $losses);
        $data['WLRatio'] = $den == 0 ? "0/0" : ($wins / $den) . '/' . ($losses / $den);
        $data['WLRatio2'] = ($losses > 0) ? number_format( $wins / $losses, 2) : $wins . ".00";
        $data['WLRatioColor'] = ($data['WLRatio2'] > 0.99) ? "green" : "red";

        // Set rank name
        $data['rankName'] = Battlefield2::GetRankName((int)$player['rank_id']);

        // Calculate SPM
        $data['spm'] = ($time > 0) ? number_format( $score / ($time / 60), 3 ) : 0;
        return $data;
    }

    /**
     * Appends a players Award data to a view
     *
     * @param int $id
     */
    public function attachAwardData($id, View $view): void
    {
        $medals = [];
        $badges = [];
        $ribbons = [];

        // Ensure pid is an int
        $id = (int)$id;

        // Grab all awards
        $awards = $this->pdo->query("SELECT * FROM award")->fetchAll();
        foreach ($awards as $award)
        {
            $aid = (int)$award['id'];
            $type = (int)$award['type'];

            $data = [
                'id' => $aid,
                'name' => $award['name'],
                'type' => $type,
                'level' => 0,
                'first' => "Never",
                'last' => "Never"
            ];

            switch ($type)
            {
                case 0:
                    $ribbons[$aid] = $data;
                    break;
                case 1:
                    $badges[$aid] = $data;
                    break;
                case 2:
                    $medals[$aid] = $data;
                    break;
            }
        }

        // Now fetch player awards
        $query = "SELECT * FROM player_awards_view AS v JOIN award AS a ON a.id = v.id WHERE pid=".$id;
        $awards = $this->pdo->query($query)->fetchAll();
        foreach ($awards as $award)
        {
            $id = (int)$award['id'];
            $type = (int)$award['type'];

            $data = [
                'level' => (int)$award['level'],
                'first' => date('F jS, Y g:i A T', (int)$award['first']),
                'last' => date('F jS, Y g:i A T', (int)$award['earned'])
            ];

            switch ($type)
            {
                case 0:
                    $ribbons[$id]['level'] = $data['level'];
                    $ribbons[$id]['first'] = $data['first'];
                    $ribbons[$id]['last'] = $data['last'];
                    break;
                case 1:
                    $badges[$id]['prefix'] = Battlefield2::GetBadgePrefix($data['level']);
                    $badges[$id]['level'] = $data['level'];
                    $badges[$id]['first'] = $data['first'];
                    $badges[$id]['last'] = $data['last'];
                    break;
                case 2:
                    $medals[$id]['level'] = $data['level'];
                    $medals[$id]['first'] = $data['first'];
                    $medals[$id]['last'] = $data['last'];
                    break;
            }
        }

        $view->set('medals', $medals);
        $view->set('badges', $badges);
        $view->set('ribbons', $ribbons);
    }

    /**
     * Appends a players unlocks data to a view
     *
     * @param int $id
     */
    public function attachUnlockData($id, View $view): void
    {
        // Init
        $viewData = [];

        // Get all current unlocks, and set the status to locked by default
        $unlockStatus = new Dictionary();
        $result = $this->pdo->query("SELECT u.`id` AS `id`, u.`desc` AS `desc`, k.name AS `kit` FROM `unlock` AS u JOIN kit AS k on u.kit_id = k.id ORDER BY u.`id` ASC");
        while ($row = $result->fetch())
        {
            $unlockStatus[$row['id']] = [false, $row['desc'], $row['kit'], 0];
        }

        // Get players current unlocks
        $query = "SELECT `unlock_id`, `timestamp` FROM `player_unlock` WHERE `player_id`={$id} ORDER BY `unlock_id` ASC";
        $result = $this->pdo->query($query);
        while ($row = $result->fetch())
        {
            // Dictionary array values are not referenced, so we must Get then Set
            $item = $unlockStatus[$row['unlock_id']];
            $item[0] = true;
            $item[3] = $row['timestamp'];

            // IMPORTANT! Dictionary values are not referenced, so we must Set the
            // full array value again after changing any values!
            $unlockStatus[$row['unlock_id']] = $item;
        }

        foreach ($unlockStatus as $uid => $unlock)
        {
            $data = [
                'id' => $uid,
                'level' => ($unlock[0] == true) ? "1" : "0",
                'name' => $unlock[1],
                'kit' => $unlock[2],
                'timestamp' => ($unlock[0] == true) ? date('F jS, Y g:i A T', (int)$unlock[3]) : "Never"
            ];

            $viewData[] = $data;
        }

        $view->set('unlocks', $viewData);
    }

    /**
     * Adds the favorite victim and opponent data to the current output
     *
     * @param int $id
     */
    public function attachTopVictimAndOpp($id, View $view): void
    {
        // Fetch Fav Victim
        $result = $this->pdo->query("SELECT victim, `count` FROM player_kill WHERE attacker={$id} ORDER BY `count` DESC LIMIT 1");
        if ($row = $result->fetch())
        {
            $victim = $row['victim'];
            $count = $row['count'];
            $result = $this->pdo->query("SELECT name, rank_id FROM player WHERE id={$victim}");
            if ($row = $result->fetch())
            {
                $data = [
                    'id' => $victim,
                    'name' => $row['name'],
                    'rank' => $row['rank_id'],
                    'count' => $count
                ];
                $view->set('favVictim', $data);
            }
        }
        else
        {
            $data = [
                'id' => $id . "/#",
                'name' => "N/A",
                'rank' => 0,
                'count' => 0
            ];
            $view->set('favVictim', $data);
        }

        // Fetch Fav Opponent
        $result = $this->pdo->query("SELECT attacker, `count` FROM player_kill WHERE victim={$id} ORDER BY `count` DESC LIMIT 1");
        if ($row = $result->fetch())
        {
            $attacker = $row['attacker'];
            $count = $row['count'];
            $result = $this->pdo->query("SELECT name, rank_id FROM player WHERE id={$attacker}");
            if ($row = $result->fetch())
            {
                $data = [
                    'id' => $attacker,
                    'name' => $row['name'],
                    'rank' => $row['rank_id'],
                    'count' => $count
                ];
                $view->set('worstOp', $data);
            }
        }
        else
        {
            $data = [
                'id' => $id . "/#",
                'name' => "N/A",
                'rank' => 0,
                'count' => 0
            ];
            $view->set('worstOp', $data);
        }
    }

    /**
     * Appends a players Map data to a view
     *
     * @param int $id
     */
    public function attachMapData($id, View $view): void
    {
        $return = [];
        $totals = [
            'wins' => 0,
            'losses' => 0,
            'time' => 0,
            'ratio' => 0.00,
            'best' => 0.00,
        ];
        $averages = [
            'wins' => 0,
            'losses' => 0,
            'time' => '00:00:00',
            'ratio' => 0.00,
            'best' => 0.00,
        ];

        // Ensure pid is an int
        $id = (int)$id;

        // Grab top 20 maps
        $result = $this->pdo->query("SELECT pm.*, m.displayname FROM player_map AS pm LEFT JOIN map AS m ON m.id = pm.map_id WHERE player_id={$id} ORDER BY pm.time DESC LIMIT 20");
        while ($map = $result->fetch())
        {
            // Format time
            $time = (int)$map['time'];
            $format = TimeHelper::SecondsToHms($time);

            // Get player ratio
            $wins = (int)$map['wins'];
            $losses = (int)$map['losses'];
            $ratio = ($losses == 0) ? 1.00 : round($wins / $losses, 2);

            // Add to return list
            $return[] = [
                'id' => $map['map_id'],
                'name' => $map['displayname'],
                'time' => $format,
                'wins' => $map['wins'],
                'losses' => $map['losses'],
                'ratio' => number_format($ratio, 2),
                'best' => $map['bestscore']
            ];

            // Add to totals
            $totals['wins'] += $wins;
            $totals['losses'] += $losses;
            $totals['best'] += $map['bestscore'];
            $totals['time'] += $time;
            $totals['ratio'] += $ratio;
        }

        $length = count($return);
        if ($length > 0)
        {
            $averages['wins'] = number_format(round($totals['wins'] / $length, 0));
            $averages['losses'] = number_format(round($totals['losses'] / $length, 0));
            $averages['time'] = TimeHelper::SecondsToHms(round($totals['time'] / $length, 0));
            $averages['ratio'] = number_format(round($totals['ratio'] / $length, 2), 2);
            $averages['best'] = number_format(round($totals['best'] / $length, 0));
        }

        $totals['time'] = TimeHelper::SecondsToHms($totals['time']);

        $view->set('mapData', $return);
        $view->set('mapTotals', $totals);
        $view->set('mapAverage', $averages);
    }

    /**
     * Appends a players top played servers to a view
     *
     * @param int $id
     */
    public function attachTopPlayedServers($id, View $view): void
    {
        // Grab top 20 servers
        $query = <<<SQL
SELECT s.id, s.name, COUNT(s.id) as `count` FROM player_round_history AS prh
  LEFT JOIN round AS r ON prh.round_id = r.id
  LEFT JOIN server AS s ON r.server_id = s.id
WHERE prh.player_id = $id
GROUP BY s.id
ORDER BY `count` DESC, r.time_end DESC
LIMIT 20
SQL;

        $return = [];
        $result = $this->pdo->query($query);
        while ($server = $result->fetch())
        {
            $server['count'] = number_format($server['count']);
            $return[] = $server;
        }

        $view->set('serverData', $return);
    }

    public function attachNextRanks($id, $count): void
    {
        $calc = new RankCalculator();
        $calc->getNextRanks($id, $count);

        
    }

    /**
     * Imports a series of bot players into the player table from a botNames.ai file.
     *
     * @param string $filePath The filepath to the botNames.ai file
     *
     * @return int The number of bot entries imported into the database
     *
     * @throws Exception
     */
    public function importBotsFromFile($filePath): int
    {
        // Open file
        $lines = File::ReadAllLines($filePath);

        // Prepare for adding bots
        $pattern = "/^aiSettings\.addBotName[\s\t]+(?<name>[". Player::NAME_REGEX ."]+)$/i";
        $bots = [];
        $imported = 0;

        // Parse file lines
        foreach ($lines as $line)
        {
            if (preg_match($pattern, $line, $match))
            {
                $bots[] = $match["name"];
            }
        }

        try
        {
            // wrap these inserts in a transaction, to speed things along.
            $this->pdo->beginTransaction();
            foreach ($bots as $bot)
            {
                try
                {
                    // Quote name
                    $name = $this->pdo->quote($bot);

                    // Check if name exists already
                    $exists = $this->pdo->query("SELECT id FROM player WHERE name={$name} LIMIT 1")->fetchColumn(0);
                    if ($exists === false)
                    {
                        $query = "INSERT INTO `player`(`name`, `country`, `email`, `password`, `rank_id`) VALUES ({$name}, 'US', 'bot@botNames.ai', '', 0)";
                        $this->pdo->exec($query);
                        $imported++;
                    }
                }
                catch (PDOException)
                {
                    // ignore
                }
            }

            // Submit changes
            $this->pdo->commit();

            // Return number of imported bots
            return $imported;
        }
        catch (Exception $e)
        {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Appends a players time until advancement data to the view
     *
     * @param int $playerId
     * @param int $score
     * @param int $timePlayed
     * @param int $lastonline
     * @param int $joined
     *
     * @throws Exception
     */
    public function attachTimeToAdvancement($playerId, $score, $timePlayed, $lastonline, $joined, View $view): void
    {
        // sanitize
        $playerId = (int)$playerId;

        // Grab player
        $calc = new RankCalculator();
        $result = $calc->getNextRanks($playerId, 22);
        $return = $result;

        // Calculate player score per minute
        $minutes = round($timePlayed / 60, 4);
        $spm = ($minutes == 0) ? 0 : round($score / $minutes, 4);

        foreach ($result as $key => $rank)
        {
            // Get Needed Points for this next rank
            $reqPoints = (int)$rank['points'];
            $needed = max(0, ($reqPoints - $score));

            // Get our percentage to this next rank based on needed points
            $percent = round(($score / $reqPoints) * 100, 0);
            if ($percent > 100) {
                $percent = 100;
            }

            // Get the time to completion, based on our score per minute
            $ttc = $needed / max($spm / 60, 0.1);

            // Get our days to completion time, based on our Join date, Last battle, and average Points per day
            $span = TimeSpan::FromSeconds($lastonline - $joined);
            $days = max($span->getWholeDays(), 1);
            $spd = round($score / $days, 0);

            // Set array variables
            $return[$key]['points'] = number_format($rank['points']);
            $return[$key]['points_needed'] = number_format($needed);
            $return[$key]['percent_complete'] = $percent;
            $return[$key]['time_complete'] = TimeHelper::SecondsToHms($ttc);
            $return[$key]['days_complete'] = ($spd == 0) ? "Never" : number_format(round($needed / $spd, 0));
        }

        $view->set('nextRanks', $return);
    }
}
