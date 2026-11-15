<?php
/**
 * BF2Statistics ASP Framework
 *
 * Author:       Steven Wilson
 * Copyright:    Copyright (c) 2006-2021, BF2statistics.com
 * License:      GNU GPL v3
 *
 */

use System\Collections\Dictionary;
use System\Database;
use System\Database\SqlFileParser;
use System\Database\UpdateOrInsertQuery;
use System\IO\Directory;
use System\IO\File;
use System\IO\Path;
use System\Version;

/**
 * Database Model
 *
 * @package Models
 * @subpackage Database
 */
class DatabaseModel
{
    /**
     * @var array A list of tables we backup and restore from
     *
     * @remarks Order is important here (foreign keys)!
     */
    protected static $BackupTables = [
        'army', 'award', 'kit', 'rank', 'vehicle', 'weapon', 'unlock', 'unlock_requirement', 'map',
        'stats_provider', 'stats_provider_auth_ip', 'server', 'game_mod', 'game_mode', 'round',
        'failed_snapshot', 'player', 'player_army', 'player_award', 'player_weapon', 'player_kit',
        'player_kill', 'player_map', 'player_vehicle', 'player_unlock', 'player_army_history',
        'player_round_history', 'player_kill_history', 'player_kit_history', 'player_rank_history',
        'player_weapon_history', 'player_vehicle_history', 'battlespy_report', 'battlespy_message'
    ];

    /**
     * @var array A list of tables that we can clear.
     *
     * @remarks Reverse order is important here (foreign keys)!
     */
    protected static $ClearTables = [
        'battlespy_message', 'battlespy_report', 'failed_snapshot', 'player_army_history', 'player_round_history',
        'player_kill_history', 'player_kit_history', 'player_weapon_history', 'player_vehicle_history',
        'player_unlock', 'player_vehicle', 'player_weapon', 'player_rank_history', 'player_map',
        'player_kill', 'player_kit', 'player_award', 'player_army', 'risingstar', 'player', 'round', 'server', 'map'
    ];

    /**
     * @var int The maximum rows to pull per query (used for backups)
     */
    const MAX_PAGE_SIZE = 1000;

    /**
     * Fetches the table status of the specified tables
     *
     * @param string[] $tables An array of tables to fetch the status for
     */
    public function getTableStatus($tables): array
    {
        $pdo = Database::GetConnection('stats');
        $return = [];

        // Get table sizes
        $q = $pdo->query("SHOW TABLE STATUS");
        while ($row = $q->fetch())
        {
            // Skip tables we don't care about
            if (!in_array($row['Name'], $tables)) {
                continue;
            }

            // Get an accurate row count with InnoDB, since it returns an estimate in STATUS
            $rowCount = (strtolower((string) $row['Engine']) === 'innodb')
                ? (int)$pdo->query("SELECT COUNT(*) FROM ". $row['Name'])->fetchColumn(0)
                : $row['Rows'];

            // Determine size, and output data
            $size = $row["Data_length"] + $row["Index_length"];
            $return[] = [
                'name' => $row['Name'],
                'size' => $size,
                'filesize' => $this->toFilesize($size),
                'rows' => number_format($rowCount),
                'avg_row_filesize' => $this->toFilesize($row['Avg_row_length']),
                'avg_row_length' => $row['Avg_row_length'],
                'engine' => $row['Engine']
            ];
        }

        return $return;
    }

    /**
     * Creates a backup of all the stats related tables into csv files.
     *
     * @param string $path The full directory path where the backup files are to be stored
     */
    public function createStatsBackup(string $path): void
    {
        // Delete directory for sub path if it does exist already
        if (Directory::Exists($path)) {
            Directory::Delete($path, true);
        }

        // Create directory
        Directory::CreateDirectory($path, 0775);

        // Grab database
        $pdo = Database::GetConnection('stats');
        $pageSize = DatabaseModel::MAX_PAGE_SIZE;

        // Perform backups
        // Process each upgrade only if the version is newer
        foreach (self::$BackupTables as $table)
        {
            $backupFile = $path . DS . $table . ".csv";
            $file = File::OpenWrite($backupFile);

            // Check Table Exists
            $result = $pdo->query("SHOW TABLES LIKE '" . $table . "'");
            if (!empty($result->fetchAll()))
            {
                /** @noinspection SqlResolve */
                $count = (int)$pdo->query("SELECT COUNT(*) FROM `{$table}`")->fetchColumn(0);
                $i = 0;
                while ($count > 0)
                {
                    // Table Exists, lets back it up
                    /** @noinspection SqlResolve */
                    $query = "SELECT * FROM `{$table}` LIMIT {$pageSize} OFFSET $i;";
                    $result = $pdo->query($query);
                    while ($row = $result->fetch())
                    {
                        $file->writeCSVLine($row);
                    }

                    $i += $pageSize;
                    $count -= $pageSize;
                }
            }

            $file->close();
        }

        // Create manifest
        $json = json_encode(['dbver' => DB_VERSION, 'timestamp' => time()], JSON_PRETTY_PRINT);
        $file = File::OpenWrite($path . DS . "backup.json");
        $file->write($json);
        $file->close();
    }

    /**
     * Replaces the current stats data from a backup
     *
     * @param string $path The full directory path where the backup.json file is located
     *
     *
     * @throws Exception if there are any SQL errors when restoring the data
     */
    public function restoreStatsFromBackup(string $path): void
    {
        // Grab database
        $pdo = Database::GetConnection('stats');

        // Check for the manifest
        $file = File::OpenRead($path . DS . "backup.json");
        $data = json_decode($file->readToEnd(), true);

        // Check that the DB version matches the backup table version
        if (!isset($data['dbver']) || !Version::Equals($data['dbver'], DB_VERSION)) {
            throw new Exception('The backup database version does not match the table version!');
        }

        try
        {
            // Start transaction as we are moving A LOT of data around.
            $pdo->beginTransaction();

            // Store the backup tables list in new variable, for when we reverse it later.
            $tables = self::$BackupTables;

            // Clear old stuff
            foreach (array_reverse($tables) as $table)
            {
                /** @noinspection SqlResolve */
                $pdo->exec("DELETE FROM `{$table}`;");
            }

            // Reset auto increments
            $pdo->exec("ALTER TABLE `player` AUTO_INCREMENT = 2900000;");
            $pdo->exec("ALTER TABLE `server` AUTO_INCREMENT = 1;");
            $pdo->exec("ALTER TABLE `stats_provider` AUTO_INCREMENT = 1;");
            $pdo->exec("ALTER TABLE `round` AUTO_INCREMENT = 1;");
            $pdo->exec("ALTER TABLE `failed_snapshot` AUTO_INCREMENT = 1;");
            $pdo->exec("ALTER TABLE `risingstar` AUTO_INCREMENT = 1;");
            $pdo->exec("ALTER TABLE `battlespy_report` AUTO_INCREMENT = 1;");
            $pdo->exec("ALTER TABLE `battlespy_message` AUTO_INCREMENT = 1;");

            // Process each upgrade only if the version is newer
            foreach (self::$BackupTables as $table)
            {
                // Check Table Exists
                $result = $pdo->query("SHOW TABLES LIKE '" . $table . "'");
                if (!empty($result->fetchAll()))
                {
                    // Table Exists, lets back it up
                    $backupFile = $pdo->quote($path . DS . $table . ".csv");

                    // Try to execute
                    $query = "LOAD DATA LOCAL INFILE {$backupFile} INTO TABLE `{$table}` FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\n';";
                    $pdo->exec($query);
                }
            }

            // Commit changes
            $pdo->commit();
        }
        catch (Exception $e)
        {
            $pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Clears all *STATS* related data from the stats database
     *
     * @param bool $preserveAccounts if true, accounts in the players table will remain
     * @param bool $preserveProviders if true, all provider information will remain
     * @param bool $preserveServers if true, all server information will remain
     *
     * @throws Exception
     */
    public function clearStatsTables($preserveAccounts = false, $preserveProviders = false, $preserveServers = false): void
    {
        // Grab database
        $pdo = Database::GetConnection('stats');

        // Check sanity
        if ($preserveServers && !$preserveProviders) {
            throw new ArgumentException('Cannot delete stats_providers but not servers!');
        }

        try
        {
            // Start transaction as we are moving A LOT of data around.
            $pdo->beginTransaction();

            // Clear old stuff
            foreach (self::$ClearTables as $table)
            {
                if ($table == 'player' && $preserveAccounts) {
                    // Reset all player stats
                    $query = new UpdateOrInsertQuery($pdo, 'player');
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
                    $query->executeUpdate();
                } elseif ($table == 'stats_provider' && $preserveProviders) {
                    $query = new UpdateOrInsertQuery($pdo, 'stats_provider');
                    $query->set('lastupdate', '=', 0);
                    $query->executeUpdate();
                } elseif ($table == 'server' && $preserveServers) {
                    $query = new UpdateOrInsertQuery($pdo, 'server');
                    $query->set('lastupdate', '=', 0);
                    $query->executeUpdate();
                } else
                {
                    /** @noinspection SqlResolve */
                    $pdo->exec("DELETE FROM `{$table}`;");
                }
            }

            // Reset auto increments
            $pdo->exec("ALTER TABLE `round` AUTO_INCREMENT = 1;");
            $pdo->exec("ALTER TABLE `failed_snapshot` AUTO_INCREMENT = 1;");
            $pdo->exec("ALTER TABLE `risingstar` AUTO_INCREMENT = 1;");
            $pdo->exec("ALTER TABLE `battlespy_report` AUTO_INCREMENT = 1;");
            $pdo->exec("ALTER TABLE `battlespy_message` AUTO_INCREMENT = 1;");

            // Only reset auto increment on the player table if we deleted everything
            if (!$preserveAccounts)
            {
                $pdo->exec("ALTER TABLE `player` AUTO_INCREMENT = 2900000;");
            }

            if (!$preserveProviders)
            {
                $pdo->exec("ALTER TABLE `stats_provider` AUTO_INCREMENT = 1;");
            }

            if (!$preserveServers)
            {
                $pdo->exec("ALTER TABLE `server` AUTO_INCREMENT = 1;");
            }

            // Commit changes
            $pdo->commit();
        }
        catch (Exception $e)
        {
            $pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Migrates the current database version to the latest database schema
     *
     * @return bool true if the migration succeeded, false otherwise.
     *
     * @throws Exception if there is any errors moving the database schema up
     */
    public function migrateUp(): bool
    {
        $expected = Version::Parse(DB_EXPECTED_VERSION);
        $version = Version::Parse(DB_VERSION);

        // Do we really need to migrate?
        if ($version->compare($expected) >= 0) {
            return false;
        }

        // Grab database connection
        $pdo = Database::GetConnection('stats');
        $inTransaction = false;

        // Get our migrations list
        $migrations = include Path::Combine(SYSTEM_PATH, 'sql', 'migrations', 'migrations.php');
        $migrations = new Dictionary(false, $migrations);

        // Grab current database version
        $stmt = $pdo->query("SELECT `version` FROM `_version` ORDER BY `updateid` DESC LIMIT 1;");
        $result = $stmt->fetchColumn(0);
        if ($result === false) {
            return false;
        }

        try
        {
            // Apply all migrations until we are current
            do
            {
                // Get our next migration sql file
                $currentMigration = $migrations[$result];
                $nextVersion = $currentMigration['up'];

                // Upgrade until we hit the end
                if ($nextVersion == null) {
                    break;
                }

                // Create parser
                $file = Path::Combine(SYSTEM_PATH, 'sql', 'migrations', 'up', $nextVersion . '.sql');
                $parser = new SqlFileParser($file);
                $queries = $parser->getStatements();

                // Start transaction as we are moving A LOT of data around.
                $pdo->beginTransaction();
                $inTransaction = true;

                // Read file contents
                foreach ($queries as $query)
                {
                    $pdo->exec($query);
                }

                // Apply changes
                $pdo->commit();
                $inTransaction = false;

                // Grab new current version
                $stmt = $pdo->query("SELECT `version` FROM `_version` ORDER BY `updateid` DESC LIMIT 1;");
                $newResult = $stmt->fetchColumn(0);

                // Make sure we upgraded!
                if ($result == $newResult)
                {
                    throw new Exception("Version was not updated between {$result} to {$currentMigration['up_string']}!");
                }

                $result = $newResult;
            } while (true);
        }
        catch (Exception $e)
        {
            if ($inTransaction) {
                $pdo->rollBack();
            }

            throw $e;
        }

        return true;
    }

    /**
     * Converts a size in bytes to a compact human readable string
     *
     * @param int $bytes The size in bytes
     * @param int $decimals The number of decimal places
     */
    private function toFilesize($bytes, $decimals = 2): string
    {
        $size = ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $factor = (int)floor((strlen($bytes) - 1) / 3);

        return sprintf("%.{$decimals}f", $bytes / 1024 ** $factor) . ' ' . $size[$factor];
    }
}