<?php
/**
 * BF2Statistics ASP Framework
 *
 * Author:       Steven Wilson
 * Copyright:    Copyright (c) 2006-2021, BF2statistics.com
 * License:      GNU GPL v3
 *
 */
use System\TimeSpan;
use System\Database;
use System\TimeHelper;

class MapinfoModel
{
    /**
     * Sets the display name of a map by id
     *
     * Sanitization does occur in this method
     *
     * @param int $id
     * @param string $name
     *
     * @return bool
     */
    public function setMapDisplayNameById($id, $name)
    {
        $pdo = Database::GetConnection('stats');
        $id = (int)$id;
        $name = preg_replace('/[^A-Za-z0-9_\-\s\t\/\.]/', '', trim($name));
        return $pdo->update('map', ['displayname' => $name], ['id' => $id]);
    }

    /**
     * @param bool $format Use number_format
     */
    public function getMapStatistics($format = true): array
    {
        // Fetch map list
        $pdo = Database::GetConnection('stats');
        $query = <<<SQL
SELECT 
	m.id AS id, 
	m.name,
    m.displayname, 
    COALESCE(SUM(rh.kills), 0) AS kills, 
    COALESCE(SUM(rh.deaths), 0) AS deaths, 
    COALESCE(SUM(rh.score), 0) AS score, 
    COUNT(DISTINCT r.id) as `count`
FROM map AS m
    LEFT JOIN round AS r ON r.map_id = m.id
    LEFT JOIN player_round_history AS rh ON rh.round_id = r.id
GROUP BY m.id ORDER BY m.id
SQL;

        $maps = [];
        $rows = $pdo->query($query);
        while ($row = $rows->fetch())
        {
            if ($format)
            {
                $row['score'] = number_format($row['score']);
                $row['kills'] = number_format($row['kills']);
                $row['deaths'] = number_format($row['deaths']);
            }
            $maps[] = $row;
        }
        // Append time statistics
        $counter = count($maps);

        // Append time statistics
        for ($i = 0; $i < $counter; $i++)
        {
            $id = (int)$maps[$i]['id'];
            $query = "SELECT COALESCE(SUM(time_end - time_start), 0) AS `time` FROM `round` WHERE map_id={$id}";
            $time = (int)$pdo->query($query)->fetchColumn(0);

            if ($format) {
                $maps[$i]['time_display'] = $this->secondsToTime($time);
            }

            $maps[$i]['time'] = $time;
        }

        return $maps;
    }

    /**
     * @param $id
     * @param bool $format Use number_format
     *
     * @param array $data [Reference Variable] Data filled if the map has been played with map info
     *
     * @return bool true if map data is found in the database, false otherwise
     */
    public function getMapStatisticsById($id, array &$data, $format = true): bool
    {
        // Initialize array
        $data = [];

        // Fetch server list!
        $pdo = Database::GetConnection('stats');
        $query = <<<SQL
SELECT 
	m.id AS id, 
	m.name,
    m.displayname, 
    COALESCE(SUM(rh.kills), 0) AS kills, 
    COALESCE(SUM(rh.deaths), 0) AS deaths, 
    COALESCE(SUM(rh.score), 0) AS score, 
    COUNT(DISTINCT r.id) as `count`
FROM map AS m
    LEFT JOIN round AS r ON r.map_id = m.id
    LEFT JOIN player_round_history AS rh ON rh.round_id = r.id
WHERE m.id = $id
GROUP BY m.id
SQL;

        $row = $pdo->query($query)->fetch();
        if (empty($row))
        {
            // No map data
            return false;
        }
        else
        {
            // Fetch time played
            $query = "SELECT COALESCE(SUM(time_end - time_start), 0) AS `time` FROM `round` WHERE map_id={$id}";
            $time = (int)$pdo->query($query)->fetchColumn(0);
            $row['time_display'] = $this->secondsToTime($time);

            // Fetch winning ratio
            $query = <<<SQL
SELECT 
  (SELECT COUNT(id) FROM round WHERE map_id = $id AND winner = 0) AS `ties`,
  (SELECT COUNT(id) FROM round WHERE map_id = $id AND winner = 1) AS `team1_wins`,
  (SELECT COUNT(id) FROM round WHERE map_id = $id AND winner = 2) AS `team2_wins`
FROM map AS m
WHERE m.id = $id
GROUP BY m.id
SQL;
            $row += $pdo->query($query)->fetch();
            $row['team1_armies'] = [];
            $row['team2_armies'] = [];

            // Get army id's
            $query = <<<SQL
SELECT DISTINCT r.team1_army_id, r.team2_army_id, a1.name AS `team1_army`, a2.name AS `team2_army`
FROM round AS r
  JOIN army AS a1 on r.team1_army_id = a1.id
  JOIN army AS a2 on r.team2_army_id = a2.id
WHERE r.map_id = $id
SQL;
            $row2 = $pdo->query($query)->fetchAll();
            foreach ($row2 as $r)
            {
                $id = (int)$r['team1_army_id'];
                $row['team1_armies'][$id] = $r['team1_army'];

                $id = (int)$r['team2_army_id'];
                $row['team2_armies'][$id] = $r['team2_army'];
            }

            // Set data and return
            $data = $row;

            // Add formatting
            if ($format)
            {
                $data['count'] = number_format($data['count']);
                $data['score'] = number_format($data['score']);
                $data['kills'] = number_format($data['kills']);
                $data['deaths'] = number_format($data['deaths']);
            }

            return true;
        }
    }

    /**
     * Gets the top X players for a provided map ID
     *
     * @param int $mapId
     * @param int $count
     * @param bool $format
     */
    public function getTopMapPlayersById($mapId, $count, $format): array
    {
        $pdo = Database::GetConnection('stats');
        $query = <<<SQL
SELECT 
  m.player_id AS `id`,
  p.name,
  p.country,
  p.rank_id,
  m.time,  
  SUM(h.score) AS `score`,
  SUM(h.kills) AS `kills`,
  SUM(h.deaths) AS `deaths`,
  COUNT(h.player_id) AS `games`
FROM player_map AS m
  JOIN player AS p on m.player_id = p.id
  JOIN round AS r ON r.map_id = m.map_id
  JOIN player_round_history AS h on r.id = h.round_id AND h.player_id = m.player_id
WHERE m.map_id = $mapId
GROUP BY h.player_id
ORDER BY `score` DESC
LIMIT $count
SQL;

        $players = [];
        $rows = $pdo->query($query);
        while ($row = $rows->fetch())
        {
            if ($format)
            {
                $row['score_string'] = number_format($row['score']);
                $row['kills_string'] = number_format($row['kills']);
                $row['deaths_string'] = number_format($row['deaths']);
                $row['games_string'] = number_format($row['games']);
                $row['time_string'] = TimeHelper::SecondsToHms($row['time']);
            }
            $players[] = $row;
        }
        return $players;
    }

    private function secondsToTime(int $seconds): string
    {
        $span = TimeSpan::FromSeconds($seconds);
        $days = $span->getWholeDays();
        $obj = '';

        if ($days > 0)
        {
            $days = number_format($days);
            $obj = $days . " Days, ";
        }

        return $obj . $span->format('%y Hours, %j Minutes');
    }
}
