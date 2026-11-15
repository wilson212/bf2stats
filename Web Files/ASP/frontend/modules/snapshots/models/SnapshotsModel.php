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
use System\BattleSpy;
use System\Text\StringBuilder;
use System\Database\DbConnection;
use System\Collections\Dictionary;
use System\IO\Directory;
use System\IO\File;
use System\IO\Path;
use System\Snapshot;
use System\Text\StringHelper;
use System\View;

/**
 * Snapshots Model
 *
 * @package Models
 * @subpackage Snashots
 */
class SnapshotsModel
{
    /**
     * @var DbConnection The stats database connection
     */
    public $pdo;

    /**
     * SnapshotsModel constructor.
     */
    public function __construct()
    {
        // Fetch database connection
        $this->pdo = Database::GetConnection('stats');
    }

    /**
     * Fetches an array of all un-authorized snapshots, and returns a data array
     * of information about the snapshot.
     *
     * @param string $folder The snapshot folder name
     *
     *
     * @throws DirectoryNotFoundException thrown if the snapshot folder does not exist.
     * @throws IOException thrown if there is an error opening a snapshot file.
     */
    public function getSnapshots($folder): array
    {
        // Get snapshots
        $path = Path::Combine(SYSTEM_PATH, "snapshots", $folder);
        $files = Directory::GetFiles($path, '.*\.json');

        // Create objects
        $snapshots = [];
        foreach ($files as $file)
        {
            // Open snapshot file, and grab its JSON
            $stream = File::OpenRead($file);
            $json = json_decode($stream->readToEnd(), true);
            $stream->close();

            // Ensure the JSON is valid
            if ($json != null)
            {
                $snapshot = new Dictionary(true, $json);
                $snapshots[] = [
                    'name' => Path::GetFilenameWithoutExtension($file),
                    'authid' => $snapshot['authId'],
                    'server' => $snapshot['serverName'],
                    'port' => $snapshot['gamePort'],
                    'ipaddress' => $snapshot['serverIp'],
                    'map' => $snapshot['mapName'],
                    'players' => count($snapshot['players']),
                    'date' => date('M j, Y G:i T', (int)$snapshot['mapEnd'])
                ];
            }
        }

        return $snapshots;
    }

    /**
     * Fetches an array of all failed snapshots from the database, and returns a data array
     * of information about the snapshot.
     *
     * @return array
     */
    public function getFailedSnapshots()
    {
        $query = <<<SQL
SELECT s.*, s2.id AS `server_id`, s2.name AS `server_name` 
FROM `failed_snapshot` AS `s`
  JOIN `server` AS `s2` on s.server_id = s2.id
ORDER BY s.id ASC
SQL;

        $snapshots = $this->pdo->query($query)->fetchAll();
        if (empty($snapshots)) {
            return [];
        }

        return $snapshots;
    }

    /**
     * Deletes a failed snapshot record from the database, as well as the
     * snapshot file
     *
     * @param int $id
     *
     * @return bool
     */
    public function deleteFailedSnapshotById($id)
    {
        // Sanitize
        $id = (int)$id;

        // Grab failed snapshot first
        $query = "SELECT `filename` FROM `failed_snapshot` WHERE id=". $id;
        $snapshot = $this->pdo->query($query)->fetch();
        if (empty($snapshot)) {
            return false;
        }

        // Delete file
        File::Delete(Path::Combine(SYSTEM_PATH, 'snapshots', 'failed', $snapshot['filename'] . '.json'));

        // Delete record
        return $this->pdo->delete('failed_snapshot', ['id' => $id]);
    }

    /**
     * Imports a snapshot, adding the server to the server table if it does not exist,
     * otherwise authorizing the server to post snapshots.
     *
     * After the snapshot is parsed, it will be moved to the 'processed' snapshot directory.
     *
     * @param string $file The full file path to the snapshot json file
     * @param bool $ignoreAuthorization if true, all security and authorization protocols will be skipped.
     *      Should always remain false unless an administrator OK's the snapshot for manual processing.
     * @param string $message [Reference Variable] Gets the result message
     *
     *
     * @throws Exception thrown if there is an error parsing the JSON content, or if
     *  the snapshot data is incomplete.
     * @throws IOException thrown if there is a problem moving the snapshot file to the
     *  processed folder.
     */
    public function importSnapshot($file, $ignoreAuthorization, &$message): void
    {
        // Load and parse snapshot
        $data = $this->openAndParseSnapshot($file);

        // Create snapshot
        $data = new Dictionary(false, $data);
        $snapshot = new Snapshot($data);

        // Ensure snapshot is not already processed from before!
        if ($snapshot->isProcessed())
        {
            $message = "Snapshot was already processed.";
        }
        else
        {
            try
            {
                // Process data
                $snapshot->processData($ignoreAuthorization);
                $message = "Snapshot was processed successfully.";
            }
            catch (Exception $e)
            {
                // Log into the database
                if ($snapshot->serverId > 0)
                {
                    $this->pdo->insert('failed_snapshot', [
                        'server_id' => $snapshot->serverId,
                        'timestamp' => time(),
                        'filename' => Path::GetFilenameWithoutExtension($snapshot->getFilename()),
                        'reason' => StringHelper::SubStrWords($e->getMessage(), 128)
                    ]);
                }

                throw $e;
            }
        }

        /**
         * Move file. Use snapshot's getFilename() in case this import was planted by an admin,
         * which was created locally on the bf2 servers snapshot path. Having the correct filename
         * is important for the /roundinfo/view/ ASP page.
         */
        $newPath = Path::Combine(SYSTEM_PATH, "snapshots", "processed", $snapshot->getFilename());
        File::Move($file, $newPath);
    }

    /**
     * Loads the data from a snapshot, into a view file
     *
     * @param string $file The full file path to the snapshot
     *
     * @throws FileNotFoundException
     * @throws IOException
     * @throws ObjectDisposedException
     */
    public function loadSnapshotIntoView($file, View $view): void
    {
        // Load and parse snapshot
        $data = $this->openAndParseSnapshot($file);

        // Lets load our data into a snapshot object
        $data = new Dictionary(false, $data);
        $snapshot = new Snapshot($data);

        // Attach players
        $players = $snapshot->getPlayersByArmy();
        $view->set('players1', $players[$snapshot->team1ArmyId]);
        $view->set('players2', $players[$snapshot->team2ArmyId]);
        $view->set('players', $snapshot->players);

        // Skill Players
        $players = [];
        foreach ($snapshot->getTopSkillPlayers() as $key => $data)
        {
            // Split on capital character so we can insert a space
            $catName = preg_split('/(?=[A-Z])/', ucfirst($key));
            $data['category'] = implode(' ', $catName);
            $players[] = $data;
        }
        $view->set('topSkillPlayers', $players);

        // Add data
        $view->set('topKitPlayers', $snapshot->getTopKitPlayers());
        $view->set('topVehiclePlayers', $snapshot->getTopVehiclePlayers());
        $view->set('commanders', $snapshot->getCommanders());

        // Default placement data (Incase bot stats are ignored)
        $data = ['id' => 0, 'name' => 'N/A', 'rank' => 0, 'team' => -1];
        $view->set('first_place', $data);
        $view->set('second_place', $data);
        $view->set('third_place', $data);
        $awards = $snapshot->getEarnedAwards();

        // Assign Player positions
        $i = 0;
        foreach ($awards as $award)
        {
            $id = (int)$award['award_id'];
            if ($id == 2051907) {
                $awards[$i]['award_type'] = 3;
                // for medal image
                $data = ['id' => $award['player_id'], 'name' => $award['player_name'], 'rank' => $award['player_rank'], 'team' => $award['player_team']];
                $view->set('first_place', $data);
            } elseif ($id == 2051919) {
                $awards[$i]['award_type'] = 4;
                // for medal image
                $data = ['id' => $award['player_id'], 'name' => $award['player_name'], 'rank' => $award['player_rank'], 'team' => $award['player_team']];
                $view->set('second_place', $data);
            } elseif ($id == 2051902) {
                $awards[$i]['award_type'] = 5;
                // for medal image
                $data = ['id' => $award['player_id'], 'name' => $award['player_name'], 'rank' => $award['player_rank'], 'team' => $award['player_team']];
                $view->set('third_place', $data);
            }
            $i++;
        }
        $view->set('awards', $awards);

        // Assign custom round values and attach to view
        $round = [
            'map_id' => $snapshot->mapId,
            'server_id' => $snapshot->serverId,
            'mapname' => strtolower($snapshot->mapName),
            'round_start_date' => date('F jS, Y g:i A T', (int)$snapshot->roundStartTime),
            'round_end_date' => date('F jS, Y g:i A T', (int)$snapshot->roundEndTime),
            'team1Tickets' => $snapshot->team1Tickets,
            'team2Tickets' => $snapshot->team2Tickets,
            'team1ArmyId' => $snapshot->team1ArmyId,
            'team2ArmyId' => $snapshot->team2ArmyId,
            'team1name' => $this->fetchColumnOrDefault('name', 'army', 'id', $snapshot->team1ArmyId, PDO::PARAM_INT),
            'team2name' => $this->fetchColumnOrDefault('name', 'army', 'id', $snapshot->team2ArmyId, PDO::PARAM_INT),
            'map_display_name' => $this->fetchColumnOrDefault('displayname', 'map', 'id', $snapshot->mapId, PDO::PARAM_INT),
            'server' => $this->fetchColumnOrDefault('name', 'server', 'id', $snapshot->serverId, PDO::PARAM_INT),
            'modname' => $this->fetchColumnOrDefault('longname', 'game_mod', 'name', $snapshot->mod),
            'gamemode' => $this->fetchColumnOrDefault('name', 'game_mode', 'id', $snapshot->gameMode)
        ];

        // Set winning team name
        $round['winningTeamName'] = match ((int)$snapshot->winningTeam) {
            1 => $round['team1name'],
            2 => $round['team2name'],
            default => "None",
        };

        // Add more information used in the view
        $view->set('round', $round);

        // Load battlespy
        $messages = [];
        $battleSpy = new BattleSpy($this->pdo, 0, 0);
        foreach ($snapshot->players as $player)
        {
            $battleSpy->analyze($player);
        }

        // Add formatting
        foreach ($battleSpy->getMessages() as $row)
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

        $view->set('battlespy_messages', $messages);
    }

    /**
     * Fetches a value from the database, or a string saying "Unknown"
     *
     * @param string $column
     * @param string $table
     * @param string $key
     * @param mixed $value
     * @param int $type
     *
     * @return mixed|string
     */
    public function fetchColumnOrDefault($column, $table, $key, $value, $type = PDO::PARAM_STR)
    {
        $value = $this->pdo->quote($value, $type);

        /** @noinspection SqlResolve */
        $val = $this->pdo->query("SELECT `{$column}` FROM `{$table}` WHERE `{$key}`={$value}")->fetchColumn(0);
        return ($val === false) ? "Unknown" : $val;
    }

    /**
     * Loads the snapshot data from a snapshot file, and returns the data array
     *
     * @param string $file the full file path the snapshot
     *
     * @return array
     *
     * @throws FileNotFoundException
     * @throws IOException
     * @throws ObjectDisposedException
     */
    public function openAndParseSnapshot(string $file)
    {
        // Parse snapshot data
        $stream = File::OpenRead($file);
        $json = $stream->readToEnd();
        $data = json_decode($json, true);
        $stream->close();

        // Ensure we can parse json
        if ($data == null)
        {
            $code = json_last_error();
            $message = match ($code) {
                JSON_ERROR_NONE => 'No errors',
                JSON_ERROR_DEPTH => 'Maximum stack depth exceeded',
                JSON_ERROR_STATE_MISMATCH => 'Underflow or the modes mismatch',
                JSON_ERROR_CTRL_CHAR => 'Unexpected control character found',
                JSON_ERROR_SYNTAX => (str_contains($data, '\mapname\\'))
                    ? 'Detected old SNAPSHOT format'
                    : 'Syntax error, malformed JSON',
                JSON_ERROR_UTF8 => 'Malformed UTF-8 characters, possibly incorrectly encoded',
                default => 'Unknown error',
            };

            // Create Message
            $string = new StringBuilder();
            $string->appendLine("Unable to decode json from snapshot!");
            $string->appendLine();
            $string->appendLine("Error Message: " . $message);
            $string->appendLine("Error Code: " . $code);
            $string->appendLine("Snapshot: " . $file);
            throw new Exception($string->toString());
        }

        return $data;
    }
}
