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
use System\Battlefield2;
use System\StatsData;
use System\TimeHelper;
use System\TimeSpan;
use System\View;

/**
 * Player History Model
 *
 * @package Models
 * @subpackage Players
 */
class PlayerHistoryModel
{
    /**
     * @var PDO The stats database connection
     */
    protected $pdo;

    /**
     * PlayerHistoryModel constructor.
     */
    public function __construct()
    {
        // Fetch database connection
        $this->pdo = Database::GetConnection('stats');
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
     * Fetches player round history data for the Players controller
     *
     * @param int $pid The player id
     * @param int $roundid The round id
     *
     * @return bool|array
     */
    public function fetchPlayerRound($pid, $roundid)
    {
        // Cast to integers
        $pid = (int)$pid;
        $rid = (int)$roundid;

        // Fetch round
        $query = <<<SQL
SELECT ph.*, h.*, p.name, mi.name AS `mapname`, mi.displayname AS map_display_name, s.name AS `server`, 
  s.ip AS `ip`, s.gameport AS `port`, s.id AS `server_id`, g.longname AS `modname`, gm.name AS `gamemode`, 
  r2.name AS `rankName`, (SELECT COUNT(player_id) FROM player_round_history AS prh WHERE prh.round_id = {$rid}) AS `playerCount`
FROM player_round_history AS ph 
  LEFT JOIN player AS p ON ph.player_id = p.id
  LEFT JOIN round AS h ON ph.round_id = h.id
  LEFT JOIN map AS mi ON h.map_id = mi.id 
  LEFT JOIN server AS s ON h.server_id = s.id
  LEFT JOIN game_mod AS g on h.mod_id = g.id
  LEFT JOIN game_mode AS gm on h.gamemode_id = gm.id
  LEFT JOIN `rank` AS r2 on ph.rank_id = r2.id
WHERE player_id={$pid} AND round_id={$rid}
SQL;
        return $this->pdo->query($query)->fetch();
    }

    /**
     * Fetches the next round id that the specified player played in,
     * after the specified round id
     *
     * @param int $pid The player id
     * @param int $roundid The current round id
     *
     * @return int the next round id, or zero if the player hasn't played since the
     *  specified round id.
     */
    public function getPlayerNextRoundId($pid, $roundid): int
    {
        // Cast to integers
        $pid = (int)$pid;
        $rid = (int)$roundid;

        // Get next round ID
        $query = "SELECT MIN(round_id) FROM player_round_history WHERE player_id=$pid AND round_id > ". $rid;
        return (int)$this->pdo->query($query)->fetchColumn(0);
    }

    /**
     * Fetches the previous round id that the specified player played in,
     * before the specified round id
     *
     * @param int $pid The player id
     * @param int $roundid The current round id
     *
     * @return int the previous round id, or zero if the player hasn't played before the
     *  specified round id.
     */
    public function getPlayerPreviousRoundId($pid, $roundid): int
    {
        // Cast to integers
        $pid = (int)$pid;
        $rid = (int)$roundid;

        // Get next round ID
        $query = "SELECT MAX(round_id) FROM player_round_history WHERE player_id=$pid AND round_id < ". $rid;
        return (int)$this->pdo->query($query)->fetchColumn(0);
    }

    /**
     * Formats the values coming from the database to display properly in the view
     *
     *
     */
    public function formatRoundInfo(array $round): array
    {
        $data = [];
        $time = 0;
        $score = 0;

        foreach ($round as $key => $value)
        {
            switch ($key)
            {
                case 'mapname':
                    $data[$key] = $value;
                    $data['lc_mapname'] = strtolower((string) $value);
                    break;
                case 'time':
                    $time = (int)$value;
                    $span = TimeSpan::FromSeconds($time);
                    $data['time'] = $time;
                    $data['timePlayed'] = $span->format("%j minutes, %w seconds");
                    break;
                case 'kills':
                case 'deaths':
                case 'teamscore':
                case 'cmdscore':
                case 'skillscore':
                    $data[$key] = number_format((int)$value);
                    break;
                case 'score':
                    $score = (int)$value;
                    $data[$key] = number_format($score);
                    break;
                case 'army_id':
                    $val = (int)$value;
                    $data[$key] = $val;
                    $data['teamName'] = $this->pdo->query("SELECT `name` FROM army WHERE id=". $val)->fetchColumn(0);
                    break;
                default:
                    $data[$key] = $value;
                    break;
            }
        }

        // Get player ratio
        $kills = (int)$round['kills'];
        $deaths = (int)$round['deaths'];
        $den = $this->getDenominator($kills, $deaths);
        $data['ratio'] = $den == 0 ? "0/0" : ($kills / $den) . '/' . ($deaths / $den);
        $data['ratio2'] = ($deaths > 0) ? number_format( $kills / $deaths, 2) : $kills . ".00";
        $data['ratioColor'] = ($data['ratio2'] > 0.99) ? "green" : "red";

        // Set date formats
        $data['round_start_date'] = date('F jS, Y g:i A T', (int)$round['time_start']);
        $data['round_end_date'] = date('F jS, Y g:i A T', (int)$round['time_end']);

        // Set round time
        $span = TimeSpan::FromSeconds((int)$round['time_end'] - (int)$round['time_start']);
        $data['roundTime'] = $span->format("%j minutes, %w seconds");

        // Calculate SPM
        $data['spm'] = ($time > 0) ? number_format($score / ($time / 60), 3 ) : 0;
        return $data;
    }

    /**
     * Attempts to attach advanced round info from the snapshot into the view
     *
     * @param int $pid The player ID
     * @param array $round The round info array from the round_history table
     * @param View $view The view to attach advanced info into
     *
     * @return bool true if the snapshot was loaded, otherwise false
     *
     */
    public function addAdvancedRoundInfo($pid, array $round, View $view): bool
    {
        // Wrap in a try-catch. We should not have any issues since this snapshot
        // has been loaded before, but you never know...
        try
        {
            $roundId = (int)$round['id'];
            $query = "SELECT * FROM player_round_history WHERE player_id=$pid AND round_id=$roundId";
            $player = $this->pdo->query($query)->fetch();

            // If the round isnt found, then WTF
            if (empty($player)) {
                return false;
            }

            // Load stats data
            StatsData::Load();

            // Add player team stats
            $completed = (int)$player['completed'];
            $view->set('completed',  ($completed !== 0) ? "Yes" : "No");
            $view->set('kicked', $player['kicked']);
            $view->set('banned', $player['banned']);
            $view->set('heals', $player['heals']);
            $view->set('revives', $player['revives']);
            $view->set('resupplies', $player['resupplies']);
            $view->set('repairs', $player['repairs']);
            $view->set('captures', $player['captures']);
            $view->set('captureassists', $player['captureassists']);
            $view->set('neutralizes', $player['neutralizes']);
            $view->set('neutralizeassists', $player['neutralizeassists']);
            $view->set('defends', $player['defends']);
            $view->set('driverspecials', $player['driverspecials']);
            $view->set('damageassists', $player['damageassists']);
            $view->set('targetassists', $player['targetassists']);

            // Add player times
            $view->set('cmdtime', TimeHelper::SecondsToHms($player['cmdtime']));
            $view->set('sqltime', TimeHelper::SecondsToHms($player['sqltime']));
            $view->set('sqmtime', TimeHelper::SecondsToHms($player['sqmtime']));
            $view->set('lwtime', TimeHelper::SecondsToHms($player['lwtime']));

            // Negative Stats
            $view->set('teamkills', $player['teamkills']);
            $view->set('teamdamage', $player['teamdamage']);
            $view->set('teamvehicledamage', $player['teamvehicledamage']);
            $view->set('suicides', $player['suicides']);

            // Misc stats
            $view->set('killstreak', $player['killstreak']);
            $view->set('deathstreak', $player['deathstreak']);
            $this->attachTopVictimAndOpp($pid, $roundId, $view);

            // Attach players round stats
            $this->attachKitData($pid, $roundId, $view);
            $this->attachVehicleData($pid, $roundId, $view);
            $this->attachWeaponData($pid, $roundId, $view);
            $this->attachAwardData($pid, $roundId, $view);

            // Return
            return true;
        }
        catch (Exception $e)
        {
            System::LogException($e);
            return false;
        }
    }

    /**
     * Appends Kit data to a view
     *
     * @param int $playerId
     * @param int $roundId
     */
    public function attachKitData($playerId, $roundId, View $view): void
    {
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

        // Sanitize
        $playerId = (int)$playerId;
        $roundId = (int)$roundId;

        // Query
        $query = "SELECT * FROM player_kit_history WHERE player_id=$playerId AND round_id=$roundId";
        $kitData = $this->pdo->query($query)->fetchAll();

        // iterate through the results
        foreach ($kitData as $row)
        {
            $id = (int)$row['kit_id'];

            // Skip unknown objects
            if ($id >= StatsData::$NumKits) {
                continue;
            }

            // Get K/D ratio
            $ratio = ($row['deaths'] > 0) ? round($row['kills'] / $row['deaths'], 3) : (float)$row['kills'];

            // Add kit data
            $data[] = [
                'name' => StatsData::GetKitNameById($id),
                'kills' => $row['kills'],
                'deaths' => $row['deaths'],
                'time' => TimeHelper::SecondsToHms($row['time']),
                'ratio' => number_format($ratio, 3)
            ];

            // Totals
            $totals['kills'] += $row['kills'];
            $totals['deaths'] += $row['deaths'];
            $totals['time'] += $row['time'];
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
     * Appends Vehicle data to a view
     *
     * @param int $playerId
     * @param int $roundId
     */
    public function attachVehicleData($playerId, $roundId, View $view): void
    {
        // Prepare return data
        $data = [];
        $totals = [
            'kills' => 0,
            'deaths' => 0,
            'time' => 0,
            'timePlayed' => "00:00:00",
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

        // Sanitize
        $playerId = (int)$playerId;
        $roundId = (int)$roundId;

        // Query
        $query = "SELECT * FROM player_vehicle_history WHERE player_id=$playerId AND round_id=$roundId";
        $rowData = $this->pdo->query($query)->fetchAll();

        // iterate through the results
        foreach ($rowData as $row)
        {
            $id = (int)$row['vehicle_id'];

            // Skip unknown objects
            if ($id >= StatsData::$NumVehicles) {
                continue;
            }

            // Get K/D ratio
            $ratio = ($row['deaths'] > 0) ? round($row['kills'] / $row['deaths'], 3) : (float)$row['kills'];

            // Add vehicle data
            $data[] = [
                'name' => StatsData::GetVehicleNameById($id),
                'kills' => $row['kills'],
                'deaths' => $row['deaths'],
                'roadKills' => $row['roadkills'],
                'time' => TimeHelper::SecondsToHms($row['time']),
                'ratio' => number_format($ratio, 3)
            ];

            // Totals
            $totals['kills'] += $row['kills'];
            $totals['deaths'] += $row['deaths'];
            $totals['time'] += $row['time'];
            $totals['roadKills'] += $row['roadkills'];
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
        $totals['timePlayed'] = TimeHelper::SecondsToHms($totals['time']);
        $averages['time'] = TimeHelper::SecondsToHms($averages['time']);

        // attach data
        $view->set("vehicleData", $data);
        $view->set("vehicleTotals", $totals);
        $view->set("vehicleAverage", $averages);
    }

    /**
     * Appends Kit data to a view
     *
     * @param int $playerId
     * @param int $roundId
     */
    public function attachWeaponData($playerId, $roundId, View $view): void
    {
        // Prepare return data
        $data = [];
        $totals = [
            'kills' => 0,
            'deaths' => 0,
            'time' => 0,
            'timePlayed' => "00:00:00",
            'ratio' => 0.00,
            'accuracy' => 0.00,
            'fired' => 0,
            'hits' => 0
        ];
        $averages = [
            'kills' => 0,
            'deaths' => 0,
            'time' => 0,
            'timePlayed' => "00:00:00",
            'ratio' => 0.00,
            'accuracy' => 0.00,
            'fired' => 0,
            'hits' => 0
        ];

        // Sanitize
        $playerId = (int)$playerId;
        $roundId = (int)$roundId;

        // Query
        $query = "SELECT * FROM player_weapon_history WHERE player_id=$playerId AND round_id=$roundId";
        $rowData = $this->pdo->query($query)->fetchAll();

        // iterate through the results
        foreach ($rowData as $row)
        {
            $id = (int)$row['weapon_id'];

            // Skip unknown objects
            if ($id >= StatsData::$NumWeapons) {
                continue;
            }

            // Get K/D ratio
            $acc = ($row['fired'] > 0) ? (($row['hits'] / $row['fired']) * 100) : 0;
            $ratio = ($row['deaths'] > 0) ? round($row['kills'] / $row['deaths'], 3) : (float)$row['kills'];

            // Add vehicle data
            $data[] = [
                'name' => StatsData::GetWeaponNameById($id),
                'kills' => $row['kills'],
                'deaths' => $row['deaths'],
                'hits' => $row['hits'],
                'fired' => $row['fired'],
                'time' => TimeHelper::SecondsToHms($row['time']),
                'ratio' => number_format($ratio, 3),
                'accuracy' => round($acc, 2)
            ];

            // Totals
            $totals['kills'] += $row['kills'];
            $totals['deaths'] += $row['deaths'];
            $totals['time'] += $row['time'];
            $totals['fired'] += $row['fired'];
            $totals['hits'] += $row['hits'];
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
            $averages['fired'] = ($totals['fired'] > 0)
                ? number_format($totals['fired'] / $length, 0)
                : 0;
            $averages['hits'] = ($totals['hits'] > 0)
                ? number_format($totals['hits'] / $length, 0)
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
        $totals['timePlayed'] = TimeHelper::SecondsToHms($totals['time']);
        $averages['time'] = TimeHelper::SecondsToHms($averages['time']);

        // attach data
        $view->set("weaponData", $data);
        $view->set("weaponTotals", $totals);
        $view->set("weaponAverage", $averages);
    }

    /**
     * Adds the favorite victim and opponent data to the current output
     *
     * @param int $playerId
     * @param int $roundId
     */
    public function attachTopVictimAndOpp($playerId, $roundId, View $view): void
    {
        $victims = [];
        $enemies = [];
        $faVictim = ['id' => 0, 'name' => "N/A", 'rank' => 0, 'count' => 0];
        $woEnemy = ['id' => 0, 'name' => "N/A", 'rank' => 0, 'count' => 0];

        // Sanitize
        $playerId = (int)$playerId;
        $roundId = (int)$roundId;

        // Query to fetch victims
        $query = <<<SQL
SELECT p.id, pkh.count, p.name, rh.rank_id 
FROM player_kill_history AS pkh 
  LEFT JOIN player_round_history AS rh ON (pkh.victim = rh.player_id AND pkh.round_id = rh.round_id)
  LEFT JOIN player AS p ON pkh.victim = p.id
WHERE pkh.round_id=$roundId AND pkh.attacker=$playerId
SQL;
        $rowData = $this->pdo->query($query)->fetchAll();

        // Update Victims
        foreach ($rowData as $row)
        {
            $pid = (int)$row['id'];
            $count = (int)$row['count'];

            // Favorite victim?
            if ($count > $faVictim['count'])
            {
                $faVictim['id'] = $pid;
                $faVictim['name'] = $row['name'];
                $faVictim['rank'] = $row['rank_id'];
                $faVictim['count'] = $count;
            }

            $victims[] = [
                'id' => $pid,
                'name' => $row['name'],
                'rank' => $row['rank_id'],
                'count' => $count
            ];
        }

        // Query to fetch enemies
        $query = <<<SQL
SELECT p.id, pkh.count, p.name, rh.rank_id
FROM player_kill_history AS pkh 
  LEFT JOIN player_round_history AS rh ON (pkh.attacker = rh.player_id AND pkh.round_id = rh.round_id)
  LEFT JOIN player AS p ON pkh.attacker = p.id
WHERE pkh.round_id=$roundId AND pkh.victim=$playerId
SQL;
        $rowData = $this->pdo->query($query)->fetchAll();

        // Update Victims
        foreach ($rowData as $row)
        {
            $pid = (int)$row['id'];
            $count = (int)$row['count'];

            // Favorite victim?
            if ($count > $woEnemy['count'])
            {
                $woEnemy['id'] = $pid;
                $woEnemy['name'] = $row['name'];
                $woEnemy['rank'] = $row['rank_id'];
                $woEnemy['count'] = $count;
            }

            $enemies[] = [
                'id' => $pid,
                'name' => $row['name'],
                'rank' => $row['rank_id'],
                'count' => $count
            ];
        }

        // Update view
        $view->set('favVictim', $faVictim);
        $view->set('victims', $victims);
        $view->set('worstOp', $woEnemy);
        $view->set('enemies', $enemies);
    }

    /**
     * Appends Award data to a view
     *
     * @param int $playerId
     * @param int $roundId
     */
    public function attachAwardData($playerId, $roundId, View $view): void
    {
        $badges = [];
        $medals = [];
        $ribbons = [];

        $query = <<<SQL
SELECT award_id, a.type, level 
FROM player_award 
  JOIN award AS a ON player_award.award_id = a.id 
WHERE player_id=$playerId AND round_id=$roundId
SQL;
        $rowData = $this->pdo->query($query)->fetchAll();

        foreach ($rowData as $award)
        {
            $id = (int)$award['award_id'];
            $type = (int)$award['type'];
            $level = (int)$award['level'];

            switch ($type)
            {
                case 1:
                    $badges[] = ['id' => $id, 'prefix' => Battlefield2::GetBadgePrefix($level), 'level' => $level];
                    break;
                case 2:
                    $medals[] = ['id' => $id, 'level' => $level];
                    break;
                case 3:
                    $ribbons[$id] = ['id' => $id, 'level' => $level];
                    break;
            }
        }

        $view->set('medals', $medals);
        $view->set('badges', $badges);
        $view->set('ribbons', $ribbons);
    }
}
