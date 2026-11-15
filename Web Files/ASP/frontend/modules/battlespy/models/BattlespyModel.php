<?php
/**
 * BF2Statistics ASP Framework
 *
 * Author:       Steven Wilson
 * Copyright:    Copyright (c) 2006-2021, BF2statistics.com
 * License:      GNU GPL v3
 *
 */
use System\Controller;
use System\Database;
use System\Config;
use System\Database\DbConnection;
use System\Response;
use System\TimeSpan;

/**
 * Battlespy Model
 *
 * @package Models
 * @subpackage Battlepspy
 */
class BattlespyModel extends Controller
{
    /**
     * @var DbConnection The stats database connection
     */
    protected $pdo;

    /**
     * @var RoundInfoModel
     */
    protected $roundInfoModel;

    /**
     * BattlespyModel constructor.
     */
    public function __construct()
    {
        // Fetch database connection
        $this->pdo = Database::GetConnection('stats');
    }

    /**
     * This method retrieves the battlespy report list
     */
    public function getReportList(): array
    {
        // Fetch reports
        $query = <<<SQL
SELECT r.*, rh.map_id, rh.time_end AS `timestamp`, s.name AS `server`, mi.displayname AS `mapname`,
  (SELECT COUNT(id) FROM battlespy_message WHERE `report_id` = r.id) AS `count`
FROM battlespy_report AS r
  LEFT JOIN round AS rh ON r.round_id = rh.id
  LEFT JOIN server AS s ON r.server_id = s.id
  LEFT JOIN map AS mi ON rh.map_id = mi.id
SQL;
        $result = $this->pdo->query($query);
        $reports = [];

        // Add date format
        while ($report = $result->fetch())
        {
            $i = (int)$report['timestamp'];
            $report['date'] = date('F jS, Y g:i A T', $i);
            $reports[] = $report;
        }

        return $reports;
    }

    /**
     * Fetches a battlespy report, and it's messages
     *
     * @param int $id The report id
     *
     * @return array|bool false if the report does not exist, otherwise the
     *  report information in a two-dimensional array.
     */
    public function getReportById($id): false|array
    {
        // Fetch report
        $query = <<<SQL
SELECT r.*, rh.map_id, rh.time_end AS `timestamp`, s.name AS `server_name`, mi.name AS `mapname`, 
  rh.time_end - rh.time_start AS `time`
FROM battlespy_report AS r
  LEFT JOIN round AS rh ON r.round_id = rh.id
  LEFT JOIN server AS s ON r.server_id = s.id
  LEFT JOIN map AS mi ON rh.map_id = mi.id
WHERE r.id = {$id} LIMIT 1
SQL;

        // Execute query and ensure report exists
        $report = $this->pdo->query($query)->fetch();
        if (empty($report)) {
            return false;
        }

        // Format round length
        $time = (int)$report['time'];
        $span = TimeSpan::FromSeconds($time);
        $report['roundTime'] = $span->format("%y Hours, %j Mins, %w Seconds");

        // Fetch report messages
        $messages = [];
        $query = 'SELECT m.*, p.name, p.rank_id FROM battlespy_message AS m JOIN player AS p ON m.player_id = p.id WHERE report_id='. $id .' ORDER BY `severity` DESC';
        $results = $this->pdo->query($query);

        // Add css badge text
        while ($row = $results->fetch())
        {
            $message = $row;
            $severity = (int)$row['severity'];

            switch ($severity)
            {
                case 3:
                    $message['badge'] = 'important';
                    $message['severity_name'] = 'Major';
                    break;
                case 2:
                    $message['badge'] = 'warning';
                    $message['severity_name'] = 'Moderate';
                    break;
                default:
                    $message['badge'] = 'info';
                    $message['severity_name'] = 'Minor';
                    break;
            }

            // Add message
            $messages[] = $message;
        }

        // Attach model
        $this->loadModel('RoundInfoModel', 'roundinfo');

        // Fetch round
        $round = $this->roundInfoModel->fetchRoundInfoById($report['round_id']);
        if (empty($round))
        {
            Response::Redirect('battlespy');
            die;
        }

        return [
            'report' => $report,
            'round' => $round['round'],
            'messages' => $messages
        ];
    }

    /**
     * Deletes a list of BattleSpy reports by id
     *
     * @param int[] $ids A list of report ids to perform the action on
     *
     * @throws Exception thrown if there is an error in the SQL statement
     */
    public function deleteReports($ids): void
    {
        $count = count($ids);

        // Prepared statement!
        try
        {
            // Transaction if more than 2 servers
            if ($count > 2) {
                $this->pdo->beginTransaction();
            }

            // Prepare statement
            $stmt = $this->pdo->prepare("DELETE FROM battlespy_report WHERE id=:id");
            foreach ($ids as $id)
            {
                // Ignore the all!
                if ($id == 'all') {
                    continue;
                }

                // Bind value and run query
                $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
                $stmt->execute();
            }

            // Commit?
            if ($count > 2) {
                $this->pdo->commit();
            }
        }
        catch (Exception $e)
        {
            // Rollback?
            if ($count > 2) {
                $this->pdo->rollBack();
            }

            throw $e;
        }
    }

    /**
     * Deletes a list of BattleSpy report messages by id
     *
     * @param int[] $ids A list of message ids to perform the action on
     *
     * @throws Exception thrown if there is an error in the SQL statement
     */
    public function deleteMessages($ids): void
    {
        $count = count($ids);

        // Prepared statement!
        try
        {
            // Transaction if more than 2 servers
            if ($count > 2) {
                $this->pdo->beginTransaction();
            }

            // Prepare statement
            $stmt = $this->pdo->prepare("DELETE FROM battlespy_message WHERE id=:id");
            foreach ($ids as $id)
            {
                // Ignore the all!
                if ($id == 'all') {
                    continue;
                }

                // Bind value and run query
                $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
                $stmt->execute();
            }

            // Commit?
            if ($count > 2) {
                $this->pdo->commit();
            }
        }
        catch (Exception $e)
        {
            // Rollback?
            if ($count > 2) {
                $this->pdo->rollBack();
            }

            throw $e;
        }
    }

    /**
     * Removes the specified BattleSpy report from the database
     *
     * @param int $id The report id
     */
    public function deleteReportById($id): bool
    {
        // Prepare statement
        $stmt = $this->pdo->prepare("DELETE FROM battlespy_message WHERE id=:id");

        // Bind value and run query
        $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
        return $stmt->execute();

    }

    /**
     * Removes the specified BattleSpy report message from the database
     *
     * @param int $id The report message id
     */
    public function deleteMessageById($id): bool
    {
        // Prepare statement
        $stmt = $this->pdo->prepare("DELETE FROM battlespy_report WHERE id=:id");

        // Bind value and run query
        $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Fetches an array of weapons, and their config selection for flagging weapon accuracy
     */
    public function getWeaponsConfig(): array
    {
        $return = [];
        $query = "SELECT * FROM weapon WHERE is_equipment = 0 AND is_explosive = 0";
        $result = $this->pdo->query($query);

        // Load weapons config
        $weapons = array_map('intval', Config::GetOrDefault('battlespy_weapons', []));

        // build our return array
        while ($row = $result->fetch())
        {
            $id = (int)$row['id'];
            $return[] = [
                'id' => $id,
                'name' => $row['name'],
                'selected' => (in_array($id, $weapons)) ? 'selected="selected"' : ''
            ];
        }

        return $return;
    }
}
