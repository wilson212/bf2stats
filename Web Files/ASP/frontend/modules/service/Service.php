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
use System\Controller;
use System\TimeHelper;
use System\TimeSpan;
use System\View;

/**
 * Service Module Controller
 *
 * @package Modules
 */
class Service extends Controller
{
    /**
     * @var ServiceModel
     */
    protected $serviceModel;

    /**
     * @var PlayerModel
     */
    protected $playerModel;

    /**
     * @var ServiceAjaxModel
     */
    protected $serviceAjaxModel;

    /**
     * @protocol    ANY
     * @request     /ASP/service/risingstar
     * @output      html
     */
    public function risingstar(): void
    {
        // Grab database connection
        $this->requireDatabase();

        // Load model
        $this->loadModel('ServiceModel', 'service');

        // Load view
        $view = new View('risingstar', 'service');

        // Attach needed scripts for the form
        $view->attachScript("/ASP/frontend/js/jquery.form.js");
        $view->attachScript("/ASP/frontend/js/datatables/jquery.dataTables.js");
        $view->attachScript("/ASP/frontend/modules/service/js/risingstar.js");

        // Attach needed stylesheets
        $view->attachStylesheet("/ASP/frontend/modules/service/css/general.css");

        // Get last refresh and next refresh times
        $interval = Config::GetOrDefault('stats_risingstar_interval', 7);
        $last = Config::Get('stats_risingstar_refresh');
        $next = $last + (86400 * $interval);
        $isDue = ($next < time());

        // Create time spans
        $spanNext = TimeSpan::FromSeconds($next - time())->toString('%e days, %y hours');
        if ($isDue)
        {
            $spanNext .= " ago";
            $view->set('next_color', 'red');
        }
        else
        {
            $view->set('next_color', 'black');
        }

        // Set view variables
        $view->set('last', TimeHelper::FormatDifference($last, time()));
        $view->set('next', $spanNext);
        $view->set('records', $this->serviceModel->getRisingStarCount());

        // Send output
        $view->render();
    }

    /**
     * @protocol    ANY
     * @request     /ASP/service/smoc
     * @output      html
     */
    public function smoc(): void
    {
        // Grab database connection
        $this->requireDatabase();

        // Load model
        $this->loadModel('ServiceModel', 'service');

        // Load view
        $view = new View('smoc', 'service');

        // Attach needed scripts for the form
        $view->attachScript("/ASP/frontend/js/jquery.form.js");
        $view->attachScript("/ASP/frontend/js/datatables/jquery.dataTables.js");
        $view->attachScript("/ASP/frontend/modules/service/js/smoc.js");


        // Attach needed stylesheets
        $view->attachStylesheet("/ASP/frontend/css/icons/icol16.css");
        $view->attachStylesheet("/ASP/frontend/modules/service/css/general.css");

        // Set data
        $numEligible = $this->serviceModel->getNumOfEligibleSergeantMajors();
        $view->set('records', $numEligible);

        // Get last refresh and next refresh times
        $interval = Config::GetOrDefault('stats_smoc_interval', 7);
        $last = Config::GetOrDefault('stats_smoc_last_select', 0);
        $next = $last + (86400 * $interval);
        $isDue = ($next < time());

        // Create time spans
        $spanNext = TimeSpan::FromSeconds($next - time())->toString('%e days, %y hours');
        if ($last == 0)
        {
            if ($numEligible == 0)
            {
                $spanNext = "No eligible candidates";
                $view->set('next_color', 'black');
            }
            else
            {
                $spanNext = "Now";
                $view->set('next_color', 'red');
            }
        }
        elseif ($isDue)
        {
            $spanNext .= " ago";
            $view->set('next_color', 'red');
        }
        else
        {
            $view->set('next_color', 'black');
        }

        // Set view variables
        $view->set('last_selection', TimeHelper::FormatDifference($last, time()));
        $view->set('next_due', $spanNext);

        $last = Config::Get('stats_smoc_refresh');
        $next = $last + (86400 * $interval);
        $isDue = ($next < time());

        // Create time spans
        $spanNext = TimeHelper::FormatDifference($last, time());
        $view->set('last_color', ($isDue) ? 'red' : 'black');

        // Set view variables
        $view->set('last_rebuild', $spanNext);

        // Default data
        $player = [
            'id' => 0,
            'name' => "",
            'rank' => '',
            'country' => '',
            'lastip' => '0.0.0.0',
            'badge' => '',
            'statustext' => 'None',
            'joined' => '',
            'lastonline' => '',
            'timeplayed' => 'None',
            'games' => 0
        ];

        $players = $this->serviceModel->getCurrentSmoc();
        if (count($players) > 1) {
            // We have a problem...
        } elseif (!empty($players)) {
            // Just one
            $smoc = $players[0];
            // Load players model
            $player = $this->serviceModel->formatPlayerData($smoc);
        }

        // Set view variables
        $view->set('player', $player);

        // Send output
        $view->render();
    }

    /**
     * @protocol    ANY
     * @request     /ASP/service/general
     * @output      html
     */
    public function general(): void
    {
        $option = (int)Config::GetOrDefault('stat_general_mode', 0);
        if ($option == 0)
        {
            $this->clanGeneral();
        }
    }

    /**
     * @protocol    GET
     * @request     protected
     * @output      html
     */
    protected function clanGeneral()
    {
        // Grab database connection
        $this->requireDatabase();

        // Load model
        $this->loadModel('ServiceModel', 'service');

        // Load view
        $view = new View('general_clan', 'service');

        // Attach needed scripts for the form
        $view->attachScript("/ASP/frontend/js/jquery.form.js");
        $view->attachScript("/ASP/frontend/js/datatables/jquery.dataTables.js");
        $view->attachScript("/ASP/frontend/modules/service/js/general_clan.js");


        // Attach needed stylesheets
        $view->attachStylesheet("/ASP/frontend/css/icons/icol16.css");
        $view->attachStylesheet("/ASP/frontend/modules/service/css/general.css");

        // Set data
        $numEligible = $this->serviceModel->getNumOfEligibleGenerals();
        $view->set('records', $numEligible);

        // Get last refresh and next refresh times
        $interval = Config::GetOrDefault('stats_general_interval', 7);
        $last = Config::GetOrDefault('stats_general_last_select', 0);
        $next = $last + (86400 * $interval);
        $isDue = ($next < time());

        // Create time spans
        $spanNext = TimeSpan::FromSeconds($next - time())->toString('%e days, %y hours');
        if ($last == 0)
        {
            if ($numEligible == 0)
            {
                $spanNext = "No eligible candidates";
                $view->set('next_color', 'black');
            }
            else
            {
                $spanNext = "Now";
                $view->set('next_color', 'red');
            }
        }
        elseif ($isDue)
        {
            $spanNext .= " ago";
            $view->set('next_color', 'red');
        }
        else
        {
            $view->set('next_color', 'black');
        }

        // Set view variables
        $view->set('last_selection', TimeHelper::FormatDifference($last, time()));
        $view->set('next_due', $spanNext);

        $last = Config::Get('stats_general_refresh');
        $next = $last + (86400 * $interval);
        $isDue = ($next < time());

        // Create time spans
        $spanNext = TimeHelper::FormatDifference($last, time());
        $view->set('last_color', ($isDue) ? 'red' : 'black');

        // Set view variables
        $view->set('last_rebuild', $spanNext);

        // Default data
        $player = [
            'id' => 0,
            'name' => "",
            'rank' => '',
            'country' => '',
            'lastip' => '0.0.0.0',
            'badge' => '',
            'statustext' => 'None',
            'joined' => '',
            'lastonline' => '',
            'timeplayed' => 'None',
            'games' => 0
        ];

        $players = $this->serviceModel->getCurrentGenerals();
        if (count($players) > 1) {
            // We have a problem...
        } elseif (!empty($players)) {
            // Just one
            $smoc = $players[0];
            // Load players model
            $player = $this->serviceModel->formatPlayerData($smoc);
        }

        // Set view variables
        $view->set('player', $player);

        // Send output
        $view->render();
    }

    /**
     * @protocol    POST
     * @request     /ASP/service/list
     * @output      json
     */
    public function postList(): void
    {
        // Grab database connection
        $this->requireDatabase(true);

        // We only accept these POST actions
        $this->requireAction("risingstar", "smoc", "general");

        // Load model
        $this->loadModel('ServiceAjaxModel', 'service');

        // Process action
        try
        {
            switch ($_POST['action'])
            {
                case "risingstar":
                    $data = $this->serviceAjaxModel->getRisingStarList($_POST);
                    echo json_encode($data);
                    break;
                case "smoc":
                    $data = $this->serviceAjaxModel->getSmocList($_POST);
                    echo json_encode($data);
                    break;
                case "general":
                    $data = $this->serviceAjaxModel->getGeneralList($_POST);
                    echo json_encode($data);
                    break;
                default:
                    $this->sendJsonResponse(false, "Invalid Action");
                    break;
            }
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
     * @request     /ASP/service/select
     * @output      json
     */
    public function postSelect(): void
    {
        // Grab database connection
        $this->requireDatabase(true);

        // We only accept these POST actions
        $this->requireAction("smoc", "general");

        // Load model
        $this->loadModel('ServiceModel', 'service');

        // Extract player ID
        $playerId = (int)$_POST['playerId'];

        // Process action
        try
        {
            switch ($_POST['action'])
            {
                case "smoc":
                    // Make Sure Script doesn't timeout even if the user disconnects!
                    ignore_user_abort(true);

                    // Save config
                    Config::Set('stats_smoc_last_select', time());
                    Config::Save();

                    // Build table
                    $this->serviceModel->selectSMOC($playerId);

                    // Send response
                    $this->sendJsonResponse(true, "Action Completed");
                    break;
                case "general":
                    // Make Sure Script doesn't timeout even if the user disconnects!
                    ignore_user_abort(true);

                    // Save config
                    Config::Set('stats_general_last_select', time());
                    Config::Save();

                    // Build table
                    $this->serviceModel->selectGeneral($playerId);

                    // Send response
                    $this->sendJsonResponse(true, "Action Completed");
                    break;
                default:
                    $this->sendJsonResponse(false, "Invalid Action");
                    break;
            }
        }
        catch (Exception $e)
        {
            System::LogException($e);
            $this->sendJsonResponse(false, $e->getMessage());
        }
    }

    /**
     * @protocol    POST
     * @request     /ASP/service/cron
     * @output      json
     */
    public function postCron(): void
    {
        // Grab database connection
        $this->requireDatabase(true);

        // We only accept these POST actions
        $this->requireAction("risingstar", "smoc", "general");

        // Load model
        $this->loadModel('ServiceModel', 'service');

        // Process action
        try
        {
            switch ($_POST['action'])
            {
                case "risingstar":
                    // Make Sure Script doesn't timeout even if the user disconnects!
                    set_time_limit(180);
                    ignore_user_abort(true);

                    // Save config
                    Config::Set('stats_risingstar_refresh', time());
                    Config::Save();

                    // Build table
                    $this->serviceModel->buildRisingStarTable();

                    // Send response
                    $this->sendJsonResponse(true, "Action Completed");
                    break;
                case "smoc":
                    // Make Sure Script doesn't timeout even if the user disconnects!
                    set_time_limit(180);
                    ignore_user_abort(true);

                    // Save config
                    Config::Set('stats_smoc_refresh', time());
                    Config::Save();

                    // Build table
                    $this->serviceModel->buildSmocEligibilityTable();

                    // Send response
                    $this->sendJsonResponse(true, "Action Completed");
                    break;
                case "general":
                    // Make Sure Script doesn't timeout even if the user disconnects!
                    set_time_limit(180);
                    ignore_user_abort(true);

                    // Save config
                    Config::Set('stats_general_refresh', time());
                    Config::Save();

                    // Build table
                    $this->serviceModel->buildGeneralEligibilityTable();

                    // Send response
                    $this->sendJsonResponse(true, "Action Completed");
                    break;
                default:
                    $this->sendJsonResponse(false, "Invalid Action");
                    break;
            }
        }
        catch (Exception $e)
        {
            System::LogException($e);
            $this->sendJsonResponse(false, $e->getMessage());
        }
    }

    /**
     * @protocol    POST
     * @request     /ASP/service/alerts
     * @output      json
     */
    public function postAlerts(): void
    {
        // We only accept these POST actions
        $this->requireAction("retrieve", "clear");

        // Load model
        $this->loadModel('ServiceModel', 'service');

        // Our list of alerts
        $messages = [];

        // Check for rising star leaderboard refresh
        if ($this->serviceModel->getNumRoundsPlayed() > 0)
        {
            $interval = Config::GetOrDefault('stats_risingstar_interval', 7);
            $last = Config::Get('stats_risingstar_refresh');
            $next = $last + (86400 * $interval);
            if ($next < time())
            {
                $spanNext = TimeSpan::FromSeconds($next - time());
                $message = 'The Rising Star Leaderboard update is past due';
                $message .= ($last > 0 && $spanNext->getWholeHours() > 1)
                    ? ' by ' . $spanNext->toString('%e days and %y hours')
                    : '!';
                $messages[] = ['Attention Required!', $message, '/ASP/service/risingstar'];
            }
        }

        // Check for new Sergeant Major of the Corps!
        if ($this->serviceModel->getNumOfEligibleSergeantMajors() > 0)
        {
            $interval = Config::GetOrDefault('stats_smoc_interval', 7);
            $last = Config::GetOrDefault('stats_smoc_refresh', 0);
            $next = $last + (86400 * $interval);
            if ($next < time())
            {
                $spanNext = TimeSpan::FromSeconds($next - time());
                $message = 'Selection of the next Sergeant Major of the Corps is past due';
                $message .= ($last > 0 && $spanNext->getWholeHours() > 1)
                    ? ' by ' . $spanNext->toString('%e days and %y hours')
                    : '!';
                $messages[] = ['Attention Required!', $message, '/ASP/service/smoc'];
            }
        }

        // Check for General Promotions!
        if ($this->serviceModel->getNumOfEligibleGenerals() > 0)
        {
            $interval = Config::GetOrDefault('stats_general_interval', 30);
            $last = Config::GetOrDefault('stats_general_refresh', 0);
            $next = $last + (86400 * $interval);
            if ($next < time())
            {
                $spanNext = TimeSpan::FromSeconds($next - time());
                $message = 'Selection of the next 4-Star General is past due';
                $message .= ($last > 0 && $spanNext->getWholeHours() > 1)
                    ? ' by ' . $spanNext->toString('%e days and %y hours')
                    : '!';
                $messages[] = ['Attention Required!', $message, '/ASP/service/general'];
            }
        }

        $this->sendJsonResponse($messages !== [], $messages);
    }
}