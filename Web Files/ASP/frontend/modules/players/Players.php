<?php
/**
 * BF2Statistics ASP Framework
 *
 * Author:       Steven Wilson
 * Copyright:    Copyright (c) 2006-2021, BF2statistics.com
 * License:      GNU GPL v3
 *
 */
use System\StatsData;
use System\Battlefield2;
use System\BF2\Player;
use System\Collections\Dictionary;
use System\Controller;
use System\IO\Directory;
use System\IO\Path;
use System\Response;
use System\View;

/**
 * Players Module Controller
 *
 * @package Modules
 */
class Players extends Controller
{
    /**
     * @var PlayerModel
     */
    protected $playerModel;

    /**
     * @var PlayerHistoryModel
     */
    protected $playerHistoryModel;

    /**
     * @var PlayerAjaxModel
     */
    protected $ajaxModel;

    /**
     * @protocol    ANY
     * @request     /ASP/players
     * @output      html
     */
    public function index(): void
    {
        // Require database connection
        $this->requireDatabase();

        // Load view
        $view = new View('index', 'players');

        // Attach needed scripts for the form
        $view->attachScript("/ASP/frontend/js/jquery.form.js");
        $view->attachScript("/ASP/frontend/js/validate/jquery.validate-min.js");
        $view->attachScript("/ASP/frontend/js/datatables/jquery.dataTables.js");
        $view->attachScript("/ASP/frontend/js/select2/select2.min.js");
        $view->attachScript("/ASP/frontend/js/fileinput/fileinput.js");
        $view->attachScript("/ASP/frontend/modules/players/js/index.js");

        // Attach needed stylesheets
        $view->attachStylesheet("/ASP/frontend/modules/players/css/links.css");
        $view->attachStylesheet("/ASP/frontend/css/icons/icol16.css");
        $view->attachStylesheet("/ASP/frontend/js/select2/select2.css");

        // Load ranks
        StatsData::Load();
        $view->set('ranks', StatsData::$RankNames);

        // Send output
        $view->render();
    }

    /**
     * @protocol    ANY
     * @request     /ASP/players/view/$id
     * @output      html
     *
     * @param int $id The player ID
     */
    public function view($id): void
    {
        // Ensure correct format for ID
        $id = (int)$id;
        if ($id == 0)
        {
            Response::Redirect('players');
            return;
        }

        // Attach Model
        $this->loadModel("PlayerModel", 'players');

        // Require database connection
        $this->requireDatabase();

        // Fetch player
        $player = $this->playerModel->fetchPlayer($id);
        if (empty($player))
        {
            // Load view
            $view = new View('404', 'players');
            $view->set('id', $id);
            $view->render();
            return;
        }

        // Store values for later, before we format them
        $score = (int)$player['score'];
        $joined = (int)$player['joined'];
        $lastonline = (int)$player['lastonline'];
        $timePlayed = (int)$player['time'];

        // Load view
        $view = new View('view',  'players');
        $view->set('id', $id);
        $view->set('player', $this->playerModel->formatPlayerData($player));

        // Attach player object stats
        $this->playerModel->attachArmyData($id, $view);
        $this->playerModel->attachKitData($id, $view);
        $this->playerModel->attachVehicleData($id, $view);
        $this->playerModel->attachWeaponData($id, $view);
        $this->playerModel->attachAwardData($id, $view);
        $this->playerModel->attachUnlockData($id, $view);
        $this->playerModel->attachMapData($id, $view);
        $this->playerModel->attachTopVictimAndOpp($id, $view);
        $this->playerModel->attachTopPlayedServers($id, $view);
        $this->playerModel->attachTimeToAdvancement($id, $score, $timePlayed, $lastonline, $joined, $view);

        // Attach needed scripts for the form
        $view->attachScript("/ASP/frontend/js/jquery.form.js");
        $view->attachScript("/ASP/frontend/js/validate/jquery.validate-min.js");
        $view->attachScript("/ASP/frontend/js/select2/select2.min.js");
        $view->attachScript("/ASP/frontend/js/datatables/jquery.dataTables.js");

        // Attach chart plotting scripts
        $view->attachScript("/ASP/frontend/js/flot/jquery.flot.min.js");
        $view->attachScript("/ASP/frontend/js/flot/plugins/jquery.flot.tooltip.js");
        $view->attachScript("/ASP/frontend/modules/players/js/view.js");

        // Attach needed stylesheets
        $view->attachStylesheet("/ASP/frontend/js/select2/select2.css");
        $view->attachStylesheet("/ASP/frontend/css/icons/icol16.css");
        $view->attachStylesheet("/ASP/frontend/css/icons/icol32.css");
        $view->attachStylesheet("/ASP/frontend/modules/players/css/view.css");

        // Send output
        $view->render();
    }

    /**
     * @protocol    ANY
     * @request     /ASP/players/history/$id/$subid
     * @output      html
     *
     * @param int $id The player ID
     * @param int $subid The round id, if any
     */
    public function history($id, $subid = 0): void
    {
        // Ensure correct format for ID
        $id = (int)$id;
        if ($id == 0)
        {
            Response::Redirect('players');
            return;
        }

        // Showing history list or specific round?
        $rid = (int)$subid;
        if ($rid == 0)
        {
            // Load view
            $view = new View('history', 'players');
            $view->set('id', $id);

            // Attach needed scripts for the form
            $view->attachScript("/ASP/frontend/js/datatables/jquery.dataTables.js");
            $view->attachScript("/ASP/frontend/modules/players/js/history.js");
            $view->attachStylesheet("/ASP/frontend/modules/players/css/links.css");

            // Send output
            $view->render();
        }
        else
        {
            // Grab database connection
            $this->requireDatabase();

            // Attach Model
            $this->loadModel("PlayerHistoryModel", 'players');

            // Fetch round
            $round = $this->playerHistoryModel->fetchPlayerRound($id, $rid);
            if (empty($round))
            {
                Response::Redirect('players/history/'. $id);
                die;
            }

            // Create view
            $view = new View('history_detail', 'players');

            // Assign custom round values and attach to view
            $view->set('round', $this->playerHistoryModel->formatRoundInfo($round));

            // Add advanced info if we can
            $advanced = $this->playerHistoryModel->addAdvancedRoundInfo($id, $round, $view);
            $view->set('advanced', $advanced);

            // Get next round ID
            $n = $this->playerHistoryModel->getPlayerNextRoundId($id, $rid);
            $view->set('nextRoundId', $n);
            $view->set('nBtnStyle', ($n == 0) ? ' disabled="disabled"' : '');

            // Get previous round ID
            $n = $this->playerHistoryModel->getPlayerPreviousRoundId($id, $rid);
            $view->set('prevRoundId', $n);
            $view->set('pBtnStyle', ($n == 0) ? ' disabled="disabled"' : '');

            // Attach scripts
            $view->attachScript("/ASP/frontend/js/flot/jquery.flot.min.js");
            $view->attachScript("/ASP/frontend/js/flot/plugins/jquery.flot.pie.min.js");
            $view->attachScript("/ASP/frontend/js/flot/plugins/jquery.flot.tooltip.js");
            $view->attachScript("/ASP/frontend/js/datatables/jquery.dataTables.js");
            $view->attachScript("/ASP/frontend/modules/players/js/history_detail.js");

            // Attach stylesheets
            $view->attachStylesheet("/ASP/frontend/modules/players/css/history_detail.css");
            $view->attachStylesheet("/ASP/frontend/modules/players/css/view.css");

            if ($advanced)
            {
                // Set kill/death Ratio Chart Data
                $data = [
                    ['label' => "Kills", 'data' => $round['kills'], 'color' => "#00479f"],
                    ['label' => "Deaths", 'data' => $round['deaths'], 'color' => "#c75d7b"]
                ];
                $view->setJavascriptVar('killData', $data);

                // Set Time Played As chart data
                $vars = $view->getVars();
                $data = [
                    ['label' => "Weapons", 'data' => $vars['weaponTotals']['time'], 'color' => "#00479f"],
                    ['label' => "Vehicles", 'data' => $vars['vehicleTotals']['time'], 'color' => "#c75d7b"]
                ];
                $view->setJavascriptVar('timePlayedData', $data);
            }

            // Send output
            $view->render();
        }
    }

    /**
     * @protocol    POST
     * @request     /ASP/players/reset
     * @output      json
     */
    public function postReset(): void
    {
        // We only accept these POST actions
        $this->requireAction("stats", "awards", "unlocks");

        // Grab database connection
        $this->requireDatabase(true);

        // Attach Model
        $this->loadModel('PlayerModel', 'players');

        // Extract player ID
        $playerId = (int)$_POST['playerId'];
        try
        {
            switch ($_POST['action'])
            {
                case "stats":
                    $this->playerModel->resetPlayerStats($playerId);
                    break;
                case "awards":
                    $this->playerModel->resetPlayerAwards($playerId);
                    break;
                case "unlocks":
                    $this->playerModel->resetPlayerUnlocks($playerId);
                    break;
            }

            // Echo success
            $this->sendJsonResponse(true, $_POST['playerId']);
        }
        catch (Exception $e)
        {
            // Log exception
            System::LogException($e);

            // Tell the client that we have failed
            $this->sendJsonResponse(false, 'Query Failed! '. $e->getMessage());
        }
    }

    /**
     * @protocol    POST
     * @request     /ASP/players/authorize
     * @output      json
     */
    public function postAuthorize(): void
    {
        // We only accept these POST actions
        $this->requireAction("ban", "unban");

        // Grab database connection
        $this->requireDatabase(true);

        // Attach Model
        $this->loadModel('PlayerModel', 'players');

        try
        {
            // Ensure pid exists
            if (!isset($_POST['playerId'])) {
                throw new Exception('No Player ID Specified!');
            }

            // Extract player ID
            $playerId = (int)$_POST['playerId'];
            $mode = ($_POST['action'] == 'ban');

            // Set status
            $this->playerModel->setPlayerBanned($playerId, $mode);

            // Echo success
            $this->sendJsonResponse(true, $_POST['playerId']);
        }
        catch (Exception $e)
        {
            // Log exception
            System::LogException($e);

            // Tell the client that we have failed
            $this->sendJsonResponse(false, 'Query Failed! '. $e->getMessage());
        }
    }

    /**
     * @protocol    POST
     * @request     /ASP/players/delete
     * @output      json
     */
    public function postDelete(): void
    {
        // We only accept these POST actions
        $this->requireAction("deleteBots");

        // Grab database connection
        $this->requireDatabase(true);

        // Attach Model
        $this->loadModel('PlayerModel', 'players');

        try
        {
            // Delete bots via the model
            $result = $this->playerModel->deleteBotPlayers();
            $this->sendJsonResponse(true, $result);
        }
        catch (Exception $e)
        {
            // Log exception
            System::LogException($e);

            // Tell the client that we have failed
            $this->sendJsonResponse(false, 'Query Failed! '. $e->getMessage());
        }
    }

    /**
     * @protocol    POST
     * @request     /ASP/players/add
     * @output      json
     */
    public function postAdd(): void
    {
        // Grab database connection
        $this->requireDatabase(true);

        // Attach Model
        $this->loadModel('PlayerModel', 'players');

        try
        {
            // Use a dictionary here to gain an exception on missing array item
            $items = new Dictionary(false, $_POST);

            // Define server id
            $id = (isset($_POST['playerId'])) ? (int)$items['playerId'] : 0;
            $name = preg_replace("/[^". Player::NAME_REGEX ."]/", '', (string) $items['playerName']);

            // Switch on our action base
            switch ($_POST['action'])
            {
                case 'add':
                    // Create player record
                    $this->playerModel->createPlayer(
                        $items['playerName'],
                        $items['playerPassword'],
                        $items['playerEmail'],
                        $items['playerCountry'],
                        $items['playerRank']
                    );
                    $this->sendJsonResponse(true, 'Player Created', ['mode' => 'add']);
                    break;
                case 'edit':
                    $cols = [
                        'name' => trim($name),
                        'country' => $items['playerCountry'],
                        'rank_id' => $items['playerRank']
                    ];

                    // Add password if it is not empty
                    $pass = trim((string) $items['playerPassword']);
                    if ($pass !== '' && $pass !== '0') {
                        $cols['password'] = md5($pass);
                    }

                    // Add email if it is not empty
                    $email = trim((string) $items['playerEmail']);
                    if ($email !== '' && $email !== '0') {
                        $cols['email'] = $email;
                    }

                    // Do update
                    $this->playerModel->updatePlayer($id, $cols);
                    $this->sendJsonResponse(true, 'Player Updated', [
                        'mode' => 'update',
                        'name' => $name,
                        'rank_id' => $items['playerRank'],
                        'email' => $items['playerEmail'],
                        'iso' => $items['playerCountry'],
                        'rankName' => Battlefield2::GetRankName((int)$items['playerRank'])
                    ]);
                    break;
                default:
                    $this->sendJsonResponse(false, "Invalid Action.");
                    die;
            }
        }
        catch (Exception $e)
        {
            // Log exception
            System::LogException($e);

            // Tell the client that we have failed
            $this->sendJsonResponse(false, 'Query Failed! '. $e->getMessage());
        }
    }

    /**
     * @protocol    POST
     * @request     /ASP/players/list
     * @output      json
     */
    public function postList(): void
    {
        // Require a database connection
        $this->requireDatabase(true);

        // Attach Model
        $this->loadModel('PlayerAjaxModel', 'players', 'ajaxModel');

        try
        {
            // Fetch player list
            $data = $this->ajaxModel->getPlayerList($_POST);
            echo json_encode($data);
        }
        catch (Exception $e)
        {
            // Log Exception
            System::LogException($e);

            // Send error message to client
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    /**
     * @protocol    POST
     * @request     /ASP/players/history
     * @output      json
     *
     * @param int $id
     * @param int $subid
     *
     * @throws Exception
     */
    public function postHistory($id = 0, $subid = 0): void
    {
        // Make sure we aren't logging into this page
        if (!isset($_POST['playerId']))
        {
            $this->history($id, $subid);
            return;
        }

        // Require a database connection
        $this->requireDatabase(true);

        // Attach Model
        $this->loadModel('PlayerAjaxModel', 'players', 'ajaxModel');
        $id = (int)$_POST['playerId'];

        try
        {
            // Fetch player round history list
            $data = $this->ajaxModel->fetchPlayerRoundHistory($id, $_POST);
            echo json_encode($data);
        }
        catch (Exception $e)
        {
            // Log Exception
            System::LogException($e);

            // Send error message to client
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    /**
     * @protocol    POST
     * @request     /ASP/players/import
     * @output      json
     */
    public function postImport(): void
    {
        $output = Path::Combine(SYSTEM_PATH, "config", "botNames.ai");

        if (isset($_FILES["botNamesFile"]))
        {
            $file = $_FILES["botNamesFile"];

            //Filter the file types , if you want.
            if ($file["error"] > 0 && $file['error'] != UPLOAD_ERR_OK) {
                match ($file['error']) {
                    UPLOAD_ERR_NO_FILE => $this->sendJsonResponse(false, "No file received."),
                    UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => $this->sendJsonResponse(false, "Exceeded file size limit."),
                    default => $this->sendJsonResponse(false, "Unknown Error."),
                };
            } else
            {
                // Ensure the config directory if writable!
                $path = Path::Combine(SYSTEM_PATH, "config");
                if (!Directory::IsWritable($path))
                {
                    $this->sendJsonResponse(false, "System config directory is not writable!");
                    return;
                }

                // Attach Model
                $this->loadModel('PlayerModel', 'players');

                try
                {
                    // Move the uploaded file to the config folder
                    @move_uploaded_file($file["tmp_name"], $output);

                    // Import bots
                    $count = $this->playerModel->importBotsFromFile($output);

                    // Output success message
                    $this->sendJsonResponse(true, "File Received OK. Added ". $count . " Bots");
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
        else
        {
            $this->sendJsonResponse(false, "No file received.");
        }
    }

    /**
     * @protocol    GET
     * @request     /ASP/players/timePlayed/{pid}
     * @output      json
     *
     * @param int $id
     */
    public function getTimePlayed($id = 0): void
    {
        // Hide errors. Specifically, Daylight Savings errors
        ini_set("display_errors", "0");

        // Require database connection
        $this->requireDatabase(true);

        // Load model
        $this->loadModel('PlayerAjaxModel', 'players', 'ajaxModel');

        // Use our model to do all the hard work
        $data = $this->ajaxModel->getTimePlayedChartData($id);
        echo json_encode($data);
    }
}
