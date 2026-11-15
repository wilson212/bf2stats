<?php
/**
 * BF2Statistics ASP Framework
 *
 * Author:       Steven Wilson
 * Copyright:    Copyright (c) 2006-2021, BF2statistics.com
 * License:      GNU GPL v3
 *
 */
use System\Config;
use System\Collections\Dictionary;
use System\Controller;
use System\IO\Directory;
use System\IO\File;
use System\IO\Path;
use System\Version;
use System\View;

/**
 * Database Module Controller
 *
 * @package Modules
 */
class Database extends Controller
{
    /**
     * @var DatabaseModel
     */
    protected $databaseModel;

    /**
     * @protocol    ANY
     * @request     /ASP/database
     * @output      html
     */
    public function index(): void
    {
        // Require database connection
        $this->requireDatabase();

        // Load Model
        $this->loadModel('DatabaseModel', 'database');

        // A list of tables we care about
        $tables = [
            'map', 'server', 'round', 'player', 'player_army', 'player_army_history', 'player_award',
            'player_weapon', 'player_weapon_history', 'player_kit', 'player_kit_history', 'player_kill',
            'player_kill_history', 'player_map', 'player_round_history', 'player_rank_history',
            'player_vehicle', 'player_vehicle_history', 'player_unlock', 'battlespy_report', 'battlespy_message',
            'stats_provider', 'stats_provider_auth_ip'
        ];

        // Create view
        $view = new View('index', 'database');
        $view->attachScript("/ASP/frontend/js/datatables/jquery.dataTables.js");
        $view->attachScript("/ASP/frontend/modules/database/js/index.js");
        $view->set('tables', $this->databaseModel->getTableStatus($tables));
        $view->render();
    }

    /**
     * @protocol    GET
     * @request     /ASP/database/backup
     * @output      html
     */
    public function getBackup(): void
    {
        $serverList = ['localhost', '127.0.0.1', '::1'];
        if (in_array(Config::Get('db_host'), $serverList))
        {
            // Create view
            $view = new View('backup', 'database');
            $view->attachScript("/ASP/frontend/modules/database/js/backup.js");
            $view->render();
        }
        else
        {
            // Create view
            $view = new View('cannot_backup', 'database');
            $view->attachScript("/ASP/frontend/modules/database/cannot_backup.js");
            $view->render();
        }
    }

    /**
     * @protocol    POST
     * @request     /ASP/database/backup
     * @output      html
     */
    public function postBackup(): void
    {
        // Require proper action or redirect
        if ($_POST['action'] != 'backup')
        {
            if (isset($_POST['ajax'])) {
                $this->sendJsonResponse(false, 'Invalid Action!');
            } else {
                $this->getBackup();
            }

            return;
        }

        // We require a database!
        $this->requireDatabase(true);

        // Check that the backup directory is writable
        $path = Path::Combine(SYSTEM_PATH, 'backups');
        if (!Directory::IsWritable($path))
        {
            $this->sendJsonResponse(false, 'Database backup path (' . $path . ') is not writable!');
            die;
        }

        try
        {
            // Define backup folder path
            $path = Path::Combine(SYSTEM_PATH, 'backups', date('Y-m-d_Hi'));

            // Load model, and call method
            $this->loadModel('DatabaseModel', 'database');
            $this->databaseModel->createStatsBackup($path);

            // Tell the client that we were successful
            $this->sendJsonResponse(true, 'System Data Backup Successful!');
        }
        catch (Exception $e)
        {
            // Log exception
            System::LogException($e);

            // Tell the client that we have failed
            $this->sendJsonResponse(false, $e->getMessage());
        }
    }

    /**
     * @protocol    GET
     * @request     /ASP/database/restore
     * @output      html
     */
    public function getRestore(): void
    {
        // Create view
        $view = new View('restore', 'database');
        $view->attachScript("/ASP/frontend/js/jquery.form.js");
        $view->attachScript("/ASP/frontend/modules/database/js/restore.js");

        // Grab our backups
        $results = [];
        $dirs = Directory::GetDirectories(Path::Combine(SYSTEM_PATH, 'backups'));
        $length = count($dirs) - 1;

        // Add each backup to the list if it is valid.
        for ($i = $length; $i >= 0; $i--)
        {
            // Declare full path to the meta file
            $fullPath = Path::Combine($dirs[$i], 'backup.json');

            // Ensure that the meta file exists
            if (!File::Exists($fullPath)) {
                continue;
            }

            // Open and decode meta file
            $contents = File::ReadAllText($fullPath);
            $meta = json_decode($contents, true);

            // Ensure database version matches!
            if ($meta['dbver'] != DB_VERSION) {
                continue;
            }

            // Add backup to the list of backups we can restore from
            $results[] = [
                'id' => Path::GetFileName($dirs[$i]),
                'date' => date("F jS, Y @ g:i A T", $meta['timestamp'])
            ];
        }

        // Send the output
        $view->set('backups', $results);
        $view->render();
    }

    /**
     * @protocol    POST
     * @request     /ASP/database/restore
     * @output      html
     */
    public function postRestore(): void
    {
        // Require proper action or redirect
        if ($_POST['action'] != 'restore')
        {
            if (isset($_POST['ajax'])) {
                $this->sendJsonResponse(false, 'Invalid Action!');
            } else {
                $this->getRestore();
            }

            return;
        }

        // We require a database!
        $this->requireDatabase(true);

        // Ensure we have a backup selected
        if (!isset($_POST['backup']))
        {
            $this->sendJsonResponse(false, 'No backup specified!');
            return;
        }

        // Define backup folder path
        $path = Path::Combine(SYSTEM_PATH, 'backups', $_POST['backup']);

        // Ensure the backup directory is real!
        if (!Directory::Exists($path))
        {
            $this->sendJsonResponse(false, 'Invalid Backup: Does not exist!');
            return;
        }

        try
        {
            // Load model, and call method
            $this->loadModel('DatabaseModel', 'database');
            $this->databaseModel->restoreStatsFromBackup($path);

            // Tell the client that we were successful
            $this->sendJsonResponse(true, 'System Data Backup Successful!');
        }
        catch (Exception $e)
        {
            // Log exception
            System::LogException($e);

            // Tell the client that we have failed
            $this->sendJsonResponse(false, $e->getMessage());
        }
    }

    /**
     * @protocol    GET
     * @request     /ASP/database/clear
     * @output      html
     */
    public function getClear(): void
    {
        // Create view
        $view = new View('clear', 'database');

        // Attach needed scripts for the form
        $view->attachScript("/ASP/frontend/js/jquery.form.js");
        $view->attachScript("/ASP/frontend/modules/database/js/clear.js");

        // Draw
        $view->render();
    }

    /**
     * @protocol    POST
     * @request     /ASP/database/clear
     * @output      json
     */
    public function postClear(): void
    {
        // Require proper action or redirect
        if ($_POST['action'] != 'clear')
        {
            if (isset($_POST['ajax'])) {
                $this->sendJsonResponse(false, 'Invalid Action!');
            } else {
                $this->getClear();
            }

            return;
        }

        // We require a database!
        $this->requireDatabase(true);

        try
        {
            // Load model, and call method
            $this->loadModel('DatabaseModel', 'database');

            // Convert to dictionary
            $data = new Dictionary(true, $_POST);
            $acnts = $data->getValueOrDefault('accounts', 'off');
            $provs = $data->getValueOrDefault('providers', 'off');
            $servs = $data->getValueOrDefault('servers', 'off');

            // Process
            $this->databaseModel->clearStatsTables($acnts == 'on', $provs == 'on', $servs == 'on');

            // Tell the client that we were successful
            $this->sendJsonResponse(true, 'Stats Data Cleared Successfully!');
        }
        catch (Exception $e)
        {
            // Log exception
            System::LogException($e);

            // Tell the client that we have failed
            $this->sendJsonResponse(false, $e->getMessage());
        }
    }

    /**
     * @protocol    GET
     * @request     /ASP/database/update
     * @output      html
     */
    public function getUpdate(): void
    {
        $expected = Version::Parse(DB_EXPECTED_VERSION);
        $version = Version::Parse(DB_VERSION);
        $comparison = $version->compare($expected);

        // Do we really need to migrate?
        if ($comparison == 0) {
            // Create view
            $view = new View('uptodate', 'database');
            $view->attachScript("/ASP/frontend/modules/database/js/update.js");
            $view->render();
        } elseif ($comparison == -1) {
            // Create view
            $view = new View('update', 'database');
            $view->attachScript("/ASP/frontend/modules/database/js/update.js");
            $view->render();
        } else // Larger than expected
        {
            // Create view
            $view = new View('update_error', 'database');
            $view->attachScript("/ASP/frontend/modules/database/js/update_error.js");
            $view->render();
        }
    }

    /**
     * @protocol    POST
     * @request     /ASP/database/update
     * @output      json
     */
    public function postUpdate(): void
    {
        // Require proper action or redirect
        if ($_POST['action'] != 'update')
        {
            if (isset($_POST['ajax'])) {
                $this->sendJsonResponse(false, 'Invalid Action!');
            } else {
                $this->getUpdate();
            }

            return;
        }

        // We require a database!
        $this->requireDatabase(true);

        // Dont let this time out!
        ignore_user_abort(true);
        set_time_limit(180);

        // Wrap in a try catch
        try
        {
            // Load model, and call method
            $this->loadModel('DatabaseModel', 'database');
            $this->databaseModel->migrateUp();

            // Tell the client that we were successful
            $this->sendJsonResponse(true, 'Database Upgraded to version: ');
        }
        catch (Exception $e)
        {
            // Log exception
            System::LogException($e);

            // Tell the client that we have failed
            $this->sendJsonResponse(false, $e->getMessage());
        }
    }
}
