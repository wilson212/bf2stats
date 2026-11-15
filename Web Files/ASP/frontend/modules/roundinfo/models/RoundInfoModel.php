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
use System\StatsData;
use System\TimeHelper;
use System\View;

/**
 * Round Info Model
 *
 * @package Models
 * @subpackage Roundinfo
 */
class RoundInfoModel
{
    /**
     * @var DbConnection The stats database connection
     */
    protected $pdo;

    /**
     * RoundInfoModel constructor.
     */
    public function __construct()
    {
        // Fetch database connection
        $this->pdo = Database::GetConnection('stats');
    }

    /**
     * Fetches information about a round in a 2 dimensional array.
     *
     * @param int $id The round id
     *
     * @return array|bool the round information on success, false otherwise
     */
    public function fetchRoundInfoById($id): false|array
    {
        // Fetch round
        $query = <<<SQL
SELECT h.*, 
  mi.name AS `name`, 
  mi.displayname AS `map_display_name`, 
  s.name AS `server`, 
  s.ip AS `ip`, 
  s.gameport AS `port`,
  g.longname AS `modname`,
  gm.name AS `gamemode`
FROM round AS h 
  LEFT JOIN map AS mi ON h.map_id = mi.id 
  LEFT JOIN server AS s ON h.server_id = s.id
  LEFT JOIN game_mod AS g on h.mod_id = g.id
  LEFT JOIN game_mode AS gm on h.gamemode_id = gm.id
WHERE h.id={$id}
SQL;
        $round = $this->pdo->query($query)->fetch();
        if (empty($round)) {
            return false;
        }

        // Assign custom round values and attach to view
        $round['name'] = strtolower((string) $round['name']);
        $round['round_start_date'] = date('F jS, Y g:i A T', (int)$round['time_start']);
        $round['round_end_date'] = date('F jS, Y g:i A T', (int)$round['time_end']);
        $round['team1name'] = $this->pdo->query("SELECT `name` FROM army WHERE id=". $round['team1_army_id'])->fetchColumn(0);
        $round['team2name'] = $this->pdo->query("SELECT `name` FROM army WHERE id=". $round['team2_army_id'])->fetchColumn(0);

        // Set winning team name
        $round['winningTeamName'] = match ((int)$round['winner']) {
            1 => $round['team1name'],
            2 => $round['team2name'],
            default => "None",
        };

        // Load players
        $players = [];
        $players1 = [];
        $players2 = [];
        $query = "SELECT h.*, p.id, p.name FROM player_round_history AS h LEFT JOIN player AS p ON h.player_id = p.id WHERE round_id={$id}";

        $result = $this->pdo->query($query);
        while ($row = $result->fetch())
        {
            $armyId = (int)$row['army_id'];

            // calculate spm and format time
            $time = (int)$row['time'];
            $score = (int)$row['score'];
            $row['spm'] = ($time > 0 && $score > 0) ? round($score / ($time / 60), 2) : "0.00";
            $row['time_formatted'] = TimeHelper::SecondsToHms($time);

            // Assign player to the proper team
            if ($armyId == $round['team1_army_id'])
            {
                $players1[] = $row;
            }
            else
            {
                $players2[] = $row;
            }

            $players[] = $row;
        }

        return ['round' => $round, 'players1' => $players1, 'players2' => $players2, 'players' => $players];
    }

    /**
     * Returns the BattleSpy report ID for the indicated round id
     *
     * @param $id
     *
     * @return int the battlespy report id, or zero if there is none.
     */
    public function getBattleSpyReportId($id): int
    {
        $query = "SELECT id FROM battlespy_report WHERE round_id=". (int)$id;
        return (int)$this->pdo->query($query)->fetchColumn(0);
    }

    /**
     * Attempts to attach advanced round info from the snapshot into the view
     *
     * @param int $roundId
     * @param View $view The view to attach advanced info into
     *
     * @return bool true if the snapshot was loaded, otherwise false
     *
     */
    public function addAdvancedRoundInfo(array $players, $roundId, View $view): bool
    {
        // Wrap in a try-catch. We should not have any issues since this snapshot
        // has been loaded before, but you never know...
        try
        {
            $roundId = (int)$roundId;

            // Skill Players
            $view->set('topSkillPlayers', $this->getTopSkillPlayers($players));
            $view->set('topKitPlayers', $this->getTopKitPlayers($roundId));
            $view->set('topVehiclePlayers', $this->getTopVehiclePlayers($roundId));
            $view->set('commanders', $this->getCommanders($players));
            return true;
        }
        catch (Exception)
        {
            return false;
        }
    }

    /**
     * Fetches a list of specific score categories, and their respective top player.
     *
     *
     * @return array [ categoryName => [ 'id', 'name', 'rank', 'team', 'value' ] ]
     */
    protected function getTopSkillPlayers(array $players): array
    {
        $categories = [
            'score' => ['category' => 'Round Score', 'id' => 0, 'name' => 'N/A', 'rank' => 0, 'team' => -1, 'value' => 0],
            'skillscore' => ['category' => 'Skill Score', 'id' => 0, 'name' => 'N/A', 'rank' => 0, 'team' => -1,'value' => 0],
            'teamscore' => ['category' => 'Team Score', 'id' => 0, 'name' => 'N/A', 'rank' => 0, 'team' => -1,'value' => 0],
            'cmdscore' => ['category' => 'Command Score', 'id' => 0, 'name' => 'N/A', 'rank' => 0, 'team' => -1,'value' => 0],
            'heals' => ['category' => 'Heals', 'id' => 0, 'name' => 'N/A', 'rank' => 0, 'team' => -1,'value' => 0],
            'revives' => ['category' => 'Revives', 'id' => 0, 'name' => 'N/A', 'rank' => 0, 'team' => -1,'value' => 0],
            'resupplies' => ['category' => 'Resupplies', 'id' => 0, 'name' => 'N/A', 'rank' => 0, 'team' => -1,'value' => 0],
            'repairs' => ['category' => 'Repairs', 'id' => 0, 'name' => 'N/A', 'rank' => 0, 'team' => -1,'value' => 0],
            'captures' => ['category' => 'Flag Captures', 'id' => 0, 'name' => 'N/A', 'rank' => 0, 'team' => -1,'value' => 0],
            'defends' => ['category' => 'Flag Defends', 'id' => 0, 'name' => 'N/A', 'rank' => 0, 'team' => -1,'value' => 0],
            'killstreak' => ['category' => 'Kill Streak', 'id' => 0, 'name' => 'N/A', 'rank' => 0, 'team' => -1,'value' => 0],
            'deathstreak' => ['category' => 'Death Streak', 'id' => 0, 'name' => 'N/A', 'rank' => 0, 'team' => -1,'value' => 0],
            'damageassists' => ['category' => 'Damage Assists', 'id' => 0, 'name' => 'N/A', 'rank' => 0, 'team' => -1,'value' => 0],
            'driverspecials' => ['category' => 'Driver Specials', 'id' => 0, 'name' => 'N/A', 'rank' => 0, 'team' => -1,'value' => 0],
            'teamkills' => ['category' => 'Team Kills', 'id' => 0, 'name' => 'N/A', 'rank' => 0, 'team' => -1,'value' => 0],
            'teamdamage' => ['category' => 'Team Damage', 'id' => 0, 'name' => 'N/A', 'rank' => 0, 'team' => -1,'value' => 0]
        ];

        foreach ($players as $player)
        {
            foreach ($categories as $key => $values)
            {
                $value = $player[$key];
                if ($value > $values['value'])
                {
                    $categories[$key]['id'] = $player['id'];
                    $categories[$key]['name'] = $player['name'];
                    $categories[$key]['rank'] = $player['rank_id'];
                    $categories[$key]['team'] = $player['army_id'];
                    $categories[$key]['value'] = $value;
                }
            }
        }

        return $categories;
    }

    /**
     * Fetches a list of kits that were played this round, and their respective top player.
     *
     * @param int $roundId
     *
     * @return array [ kitName => [ 'id', 'pid', 'name', 'rank', 'team', 'kills', 'deaths', 'score', 'time' ] ]
     */
    protected function getTopKitPlayers($roundId): array
    {
        $return = [];

        $query = <<<SQL
SELECT h.score, h.kills, h.deaths, h.time, h.kit_id, h.player_id, p.name, r.rank_id, r.army_id
FROM player_kit_history AS h 
  LEFT JOIN player AS p ON p.id = h.player_id
  LEFT JOIN player_round_history AS r ON (r.round_id = h.round_id AND h.player_id = r.player_id)
WHERE h.round_id=$roundId
SQL;

        $rows = $this->pdo->query($query)->fetchAll();
        foreach ($rows as $data)
        {
            $id = (int)$data['kit_id'];
            $name = StatsData::$KitNames[$id];
            if (!isset($return[$name]) || $this->isPlayerBetter($data, $return[$name]))
            {
                $return[$name] = [
                    'id' => $id,
                    'pid' => $data['player_id'],
                    'name' => $data['name'],
                    'rank' => $data['rank_id'],
                    'team' => $data['army_id'],
                    'kills' => $data['kills'],
                    'deaths' => $data['deaths'],
                    'score' => $data['score'],
                    'time' => $data['time'],
                    'time_string' => TimeHelper::SecondsToHms($data['time'])
                ];
            }
        }

        return $return;
    }

    /**
     * Fetches a list of vehicles that were used this round, and their respective top player.
     *
     * @param $roundId
     *
     * @return array [ vehicleName => [ 'id', 'pid', 'name', 'rank', 'team', 'kills', 'deaths', 'time', 'score', 'roadKills' ] ]
     */
    public function getTopVehiclePlayers($roundId): array
    {
        $return = [];

        $query = <<<SQL
SELECT h.score, h.kills, h.deaths, h.time, h.vehicle_id, h.player_id, h.roadkills, p.name, r.rank_id, r.army_id
FROM player_vehicle_history AS h 
  LEFT JOIN player AS p ON h.player_id = p.id 
  LEFT JOIN player_round_history AS r ON (r.round_id = h.round_id AND h.player_id = r.player_id)
WHERE h.round_id=$roundId
SQL;

        $rows = $this->pdo->query($query)->fetchAll();
        foreach ($rows as $data)
        {
            $id = (int)$data['vehicle_id'];
            $name = StatsData::$VehicleNames[$id];
            if (!isset($return[$name]) || $this->isPlayerBetter($data, $return[$name]))
            {
                $return[$name] = [
                    'id' => $id,
                    'pid' => $data['player_id'],
                    'name' => $data['name'],
                    'rank' => $data['rank_id'],
                    'team' => $data['army_id'],
                    'kills' => $data['kills'],
                    'deaths' => $data['deaths'],
                    'score' => $data['score'],
                    'time' => $data['time'],
                    'time_string' => TimeHelper::SecondsToHms($data['time']),
                    'roadkills' => $data['roadkills']
                ];
            }
        }

        return $return;
    }

    /**
     * Returns a list of commanders for this game, sorted by command score
     *
     *
     * @return array [ index => [ 'id', 'name', 'rank', 'time', 'score', 'team' ] ]
     */
    protected function getCommanders(array $players): array
    {
        $commanders = [];
        foreach ($players as $player)
        {
            if ($player['cmdtime'] > 0)
            {
                $commanders[] = [
                    'id' => $player['id'],
                    'name' => $player['name'],
                    'rank' => $player['rank_id'],
                    'time' => $player['cmdtime'],
                    'time_string' => TimeHelper::SecondsToHms($player['cmdtime']),
                    'score' => $player['cmdscore'],
                    'team' => $player['army_id']
                ];
            }
        }

        usort($commanders, fn($a, $b): int|float => $b['score'] - $a['score']);
        return $commanders;
    }

    /**
     * Determines if a player ObjectStat is greater than the second
     * set of object data by comparing the kills, deaths and time played in
     * the object.
     *
     *
     */
    private function isPlayerBetter(array $data, array $best): bool
    {
        if ($data['score'] > $best['score']) {
            return true;
        } elseif ($data['score'] < $best['score']) {
            return false;
        }

        /** Kills Match, try deaths */

        if ($data['deaths'] < $best['deaths']) {
            return true;
        } elseif ($data['deaths'] > $best['deaths']) {
            return false;
        }

        /** Deaths and Score Match, try time played */

        if ($data['time'] > $best['time']) {
            return true;
        } elseif ($data['time'] < $best['time']) {
            return false;
        }

        /** It's a draw. Just say no */
        return false;
    }

    /**
     * Attaches all the earned awards from a round to a View
     *
     * @param int $id The round ID to fetch awards from
     */
    public function attachAwards($id, View $view): void
    {
        // Load awards
        $query = <<<SQL
SELECT pa.award_id AS `id`, pa.level AS `level`, a.type AS `type`, p.name AS `player_name`, p.id AS `player_id`, a.name AS `name`, 
  h.army_id AS `team`, pa.round_id AS `rid`, h.rank_id AS `rank`
FROM player_award AS pa 
  LEFT JOIN player AS p ON pa.player_id = p.id
  LEFT JOIN award AS a ON pa.award_id = a.id
  LEFT JOIN player_round_history AS h ON pa.player_id = h.player_id AND pa.round_id = h.round_id
WHERE pa.round_id = $id ORDER BY pa.award_id
SQL;

        // Fetch the round awards
        $awards = $this->pdo->query($query)->fetchAll();

        // Default placement data (Incase bot stats are ignored)
        $data = ['id' => 0, 'name' => 'N/A', 'rank' => 0, 'team' => -1];
        $view->set('first_place', $data);
        $view->set('second_place', $data);
        $view->set('third_place', $data);

        // Assign Player positions
        $i = 0;
        foreach ($awards as $award)
        {
            $id = (int)$award['id'];
            if ($id == 2051907) {
                //$awards[$i]['type'] = 3; // for medal image
                $data = ['id' => $award['player_id'], 'name' => $award['player_name'], 'rank' => $award['rank'], 'team' => $award['team']];
                $view->set('first_place', $data);
            } elseif ($id == 2051919) {
                //$awards[$i]['type'] = 4; // for medal image
                $data = ['id' => $award['player_id'], 'name' => $award['player_name'], 'rank' => $award['rank'], 'team' => $award['team']];
                $view->set('second_place', $data);
            } elseif ($id == 2051902) {
                //$awards[$i]['type'] = 5; // for medal image
                $data = ['id' => $award['player_id'], 'name' => $award['player_name'], 'rank' => $award['rank'], 'team' => $award['team']];
                $view->set('third_place', $data);
            }
            $i++;
        }

        // Attach awards
        $view->set('awards', $awards);
    }
}
