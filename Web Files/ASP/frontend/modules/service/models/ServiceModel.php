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
use System\Config;
use System\Collections\Dictionary;
use System\TimeHelper;
use System\TimeSpan;

/**
 * Service Model
 *
 * @package Models
 * @subpackage Service
 */
class ServiceModel
{
    /**
     * @var DbConnection The stats database connection
     */
    public $pdo;

    /**
     * @var int Time stamp of one week ago
     */
    private static int $OneWeekAgo = 0;

    /**
     * @var int Time stamp of two weeks ago
     */
    private static int $TwoWeekAgo = 0;

    /**
     * ServiceModel constructor.
     */
    public function __construct()
    {
        // Fetch database connection
        $this->pdo = Database::GetConnection('stats');

        // Set static vars
        if (self::$OneWeekAgo == 0)
        {
            self::$OneWeekAgo = time() - (86400 * 7);
            self::$TwoWeekAgo = time() - (86400 * 14);
        }
    }

    /**
     * Fetches the number of records in the rising star table
     */
    public function getRisingStarCount(): int
    {
        return (int)$this->pdo->query("SELECT COUNT(player_id) FROM risingstar")->fetchColumn(0);
    }

    /**
     * Fetches the number of Sergeant Majors that are eligible for promotion to SMOC
     */
    public function getNumOfEligibleSergeantMajors(): int
    {
        return (int)$this->pdo->query("SELECT COUNT(id) FROM player WHERE rank_id=10")->fetchColumn(0);
    }

    /**
     * Fetches the number of Lieutenant Generals that are eligible for promotion to General
     */
    public function getNumOfEligibleGenerals(): int
    {
        return (int)$this->pdo->query("SELECT COUNT(id) FROM player WHERE rank_id=20")->fetchColumn(0);
    }

    /**
     * Fetches the number of rounds played
     */
    public function getNumRoundsPlayed(): int
    {
        return (int)$this->pdo->query("SELECT COUNT(id) FROM `round`")->fetchColumn(0);
    }

    /**
     * Fetches an array of all players with the rank of Sergeant Major of the Corp
     *
     * @return array an array of all players with the rank of Sergeant Major of the Corp
     */
    public function getCurrentSmoc()
    {
        $query = <<<SQL
SELECT p.id, p.name, p.country, p.lastip, COUNT(h.player_id) AS games, p.lastonline, p.joined, p.permban, p.online
FROM player AS p 
  LEFT JOIN player_round_history AS h on p.id = h.player_id AND h.rank_id = 11
WHERE p.rank_id = 11
SQL;

        return $this->pdo->query($query)->fetchAll();
    }

    /**
     * Fetches an array of all players with the rank of 4-Star general
     *
     * @return array an array of all players with the rank of 4 star general
     */
    public function getCurrentGenerals()
    {
        $query = <<<SQL
SELECT p.id, p.name, p.country, p.lastip, COUNT(h.player_id) AS games, p.lastonline, p.joined, p.permban, p.online
FROM player AS p 
  LEFT JOIN player_round_history AS h on p.id = h.player_id AND h.rank_id = 21
WHERE p.rank_id = 21
SQL;

        return $this->pdo->query($query)->fetchAll();
    }

    /**
     * Clears out the current Rising Star table, and rebuilds it from scratch.
     *
     * Rising star is calculated by comparing a player's career score per minute,
     * and comparing that against all the score per minute the player has achieved
     * over the last 7 days.
     *
     * This process is performed for each player who has scored at least 10 points in
     * their career, have played for at least 2 weeks, and have played a game in the
     * last 7 days.
     */
    public function buildRisingStarTable(): void
    {
        // Create a list of players
        $players = new Dictionary();

        // Delete contents of the last rising star
        $this->pdo->exec("DELETE FROM `risingstar`");
        $this->pdo->exec("ALTER TABLE `risingstar` AUTO_INCREMENT = 1");

        // Prepare statement
        $one = self::$OneWeekAgo;
        $query = <<<SQL
SELECT SUM(prh.score) AS `score`, SUM(prh.time) AS `time`
FROM player_round_history AS prh 
  JOIN round AS r on prh.round_id = r.id 
WHERE player_id=:id AND r.time_imported > $one
SQL;
        $stmt = $this->pdo->prepare($query);

        // Fetch each player
        $query = "SELECT id, name, country, score, time FROM player WHERE score > 10 AND joined < ". self::$TwoWeekAgo;
        $reader = $this->pdo->query($query);

        // Get each player's last week SPM
        while ($player = $reader->fetch())
        {
            // Convert values
            $score = (int)$player['score'];
            $time = (int)$player['time'];
            $minutes = $time / 60;
            $playerId = (int)$player['id'];

            // Skip if no spm
            if ($score == 0 || $minutes == 0) {
                continue;
            }

            // Get total SPM
            $totalSpm = round($score / $minutes, 7);

            // Get last week's SPM
            $stmt->bindParam(':id', $playerId, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch();

            // Convert values
            $score = (int)$row['score'];
            $time = (int)$row['time'];
            $minutes = $time / 60;

            // Skip if no spm
            if ($score == 0 || $minutes == 0) {
                continue;
            }

            // Calculate weekly score per min
            $weekSpm = round($score / $minutes, 7);

            // Calculate the score
            $percent = min(999, round($weekSpm / $totalSpm, 7) * 100);
            $score = $percent * 10000;

            // Add to dictionary
            $players->add($playerId, $score);
        }

        // Sort the dictionary
        $players = $players->toArray();
        arsort($players);

        try
        {
            // Wrap in a transaction!
            $this->pdo->beginTransaction();

            // Create statement
            $query = "INSERT INTO risingstar(player_id, weeklyscore) VALUES (:id, :score)";
            $stmt = $this->pdo->prepare($query);

            // Insert values into the risingstar table
            foreach ($players as $id => $score)
            {
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->bindParam(':score', $score, PDO::PARAM_INT);
                $stmt->execute();
            }

            // commit
            $this->pdo->commit();
        }
        catch (Exception $e)
        {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Clears out the current SMOC Eligibility table, and rebuilds it from scratch.
     *
     * This process is performed for each player who is a Sergeant Major or current
     * SMOC, and have played a game in the last 7 days.
     */
    public function buildSmocEligibilityTable(): void
    {
        // Create a list of players
        $players = new Dictionary();

        // Delete contents of the last rising star
        $this->pdo->exec("DELETE FROM `eligible_smoc`");

        // Prepare statement
        $one = self::$OneWeekAgo;
        $query = <<<SQL
SELECT SUM(prh.score) AS `score`, SUM(prh.time) AS `time`, COUNT(r.id) AS `games`
FROM player_round_history AS prh 
  JOIN round AS r on prh.round_id = r.id 
WHERE player_id=:id AND r.time_imported > $one
SQL;
        $stmt = $this->pdo->prepare($query);

        // Fetch each player
        $query = "SELECT id, score FROM player WHERE (rank_id = 10 OR rank_id = 11) AND permban=0";
        $reader = $this->pdo->query($query);

        // Get each player's last week SPM
        while ($player = $reader->fetch())
        {
            // Convert values
            $gScore = (int)$player['score'];
            $playerId = (int)$player['id'];

            // Get last week's SPM
            $stmt->bindParam(':id', $playerId, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch();

            // Convert values
            $score = (int)$row['score'];
            $time = (int)$row['time'];
            $minutes = $time / 60;

            // Skip if no spm
            if ($score == 0 || $minutes == 0) {
                continue;
            }

            // Calculate weekly score per min
            $weekSpm = round($score / $minutes, 5) * 10000;
            $data = [
                'player_id' => $playerId,
                'global_score' => $gScore,
                'rank_score' => $score,
                'rank_time' => $time,
                'rank_games' => (int)$row['games'],
                'spm' => $weekSpm
            ];

            // Add to dictionary
            $players->add($playerId, $data);
        }

        try
        {
            // Wrap in a transaction!
            $this->pdo->beginTransaction();

            // Create statement
            $query = "INSERT INTO eligible_smoc VALUES (:id, :gs, :rs, :rt, :rg, :spm)";
            $stmt = $this->pdo->prepare($query);

            // Insert values into the eligible_smoc table
            foreach ($players as $id => $data)
            {
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->bindParam(':gs', $data['global_score'], PDO::PARAM_INT);
                $stmt->bindParam(':rs', $data['rank_score'], PDO::PARAM_INT);
                $stmt->bindParam(':rt', $data['rank_time'], PDO::PARAM_INT);
                $stmt->bindParam(':rg', $data['rank_games'], PDO::PARAM_INT);
                $stmt->bindParam(':spm', $data['spm'], PDO::PARAM_INT);
                $stmt->execute();
            }

            // commit
            $this->pdo->commit();
        }
        catch (Exception $e)
        {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Clears out the current General Eligibility table, and rebuilds it from scratch.
     *
     * This process is performed for each player who is a Lieutenant General or current
     * 4-Star, and have played a game in the last 7 days.
     */
    public function buildGeneralEligibilityTable(): void
    {
        // Create a list of players
        $players = new Dictionary();

        // Delete contents of the last rising star
        $this->pdo->exec("DELETE FROM `eligible_general`");

        // Prepare statement
        $one = self::$OneWeekAgo;
        $query = <<<SQL
SELECT SUM(prh.score) AS `score`, SUM(prh.time) AS `time`, COUNT(r.id) AS `games`
FROM player_round_history AS prh 
  JOIN round AS r on prh.round_id = r.id 
WHERE player_id=:id AND r.time_imported > $one
SQL;
        $stmt = $this->pdo->prepare($query);

        // Fetch each player
        $query = "SELECT id, score FROM player WHERE rank_id = 20 AND permban=0";
        $reader = $this->pdo->query($query);

        // Get each player's last week SPM
        while ($player = $reader->fetch())
        {
            // Convert values
            $gScore = (int)$player['score'];
            $playerId = (int)$player['id'];

            // Get last week's SPM
            $stmt->bindParam(':id', $playerId, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch();

            // Convert values
            $score = (int)$row['score'];
            $time = (int)$row['time'];
            $minutes = $time / 60;

            // Skip if no spm
            if ($score == 0 || $minutes == 0) {
                continue;
            }

            // Calculate weekly score per min
            $weekSpm = round($score / $minutes, 5) * 10000;
            $data = [
                'player_id' => $playerId,
                'global_score' => $gScore,
                'rank_score' => $score,
                'rank_time' => $time,
                'rank_games' => (int)$row['games'],
                'spm' => $weekSpm
            ];

            // Add to dictionary
            $players->add($playerId, $data);
        }

        try
        {
            // Wrap in a transaction!
            $this->pdo->beginTransaction();

            // Create statement
            $query = "INSERT INTO eligible_general VALUES (:id, :gs, :rs, :rt, :rg, :spm)";
            $stmt = $this->pdo->prepare($query);

            // Insert values into the eligible_smoc table
            foreach ($players as $id => $data)
            {
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->bindParam(':gs', $data['global_score'], PDO::PARAM_INT);
                $stmt->bindParam(':rs', $data['rank_score'], PDO::PARAM_INT);
                $stmt->bindParam(':rt', $data['rank_time'], PDO::PARAM_INT);
                $stmt->bindParam(':rg', $data['rank_games'], PDO::PARAM_INT);
                $stmt->bindParam(':spm', $data['spm'], PDO::PARAM_INT);
                $stmt->execute();
            }

            // commit
            $this->pdo->commit();
        }
        catch (Exception $e)
        {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Demotes all players who have the rank of SMOC, and promotes the selected player
     *
     * @param int $playerId The player ID to select as SMOC
     */
    public function selectSMOC($playerId): void
    {
        // Reset any existing players
        $this->pdo->exec("UPDATE player SET rank_id = 10, chng = 0, decr = 1 WHERE rank_id = 11");

        // Set the new player
        $this->pdo->exec(sprintf("UPDATE player SET rank_id = 11, chng = 1, decr = 0 WHERE id = %d", $playerId));
    }

    /**
     * Promotes the specified player to the rank of 4-star general
     *
     * @param int $playerId The player ID to select as SMOC
     * @param bool $unique If true, then all other 4-star generals will be demoted
     */
    public function selectGeneral($playerId, $unique = true): void
    {
        // Reset any existing players
        if ($unique) {
            $this->pdo->exec("UPDATE player SET rank_id = 20, chng = 0, decr = 1 WHERE rank_id = 21");
        }

        // Set the new player
        $this->pdo->exec(sprintf("UPDATE player SET rank_id = 21, chng = 1, decr = 0 WHERE id = %d", $playerId));
    }

    /**
     * Formats player scores and times
     *
     *
     */
    public function formatPlayerData(array $player): array
    {
        $data = [];

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
        return $data;
    }
}
