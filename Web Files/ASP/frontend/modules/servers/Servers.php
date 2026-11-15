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
use System\Controller;
use System\Response;
use System\TimeHelper;
use System\View;

/**
 * Servers Module Controller
 *
 * @package Modules
 */
class Servers extends Controller
{
    /**
     * @var ServerModel
     */
    protected $serverModel;

    /**
     * @var ServerAjaxModel
     */
    protected $ajaxModel;

    /**
     * @protocol    ANY
     * @request     /ASP/servers
     * @output      html
     */
    public function index(): void
    {
        // Require database connection
        $this->requireDatabase();

        // Attach Model
        $this->loadModel('ServerModel', 'servers');

        // Fetch server list!
        $servers = $this->serverModel->fetchServers();
        // Attach tags
        $counter = count($servers);

        // Attach tags
        for ($i = 0; $i < $counter; $i++)
        {
            $servers[$i]['auth_badge'] = ($servers[$i]['authorized']) ? 'success' : 'important';
            $servers[$i]['auth_text'] = ($servers[$i]['authorized']) ? 'Authorized' : 'Not Authorized';
            $servers[$i]['plasma_badge'] = ($servers[$i]['plasma']) ? 'success' : 'inactive';
            $servers[$i]['plasma_text'] = ($servers[$i]['plasma']) ? 'Yes' : 'No';
        }

        // Load view
        $view = new View('index', 'servers');
        $view->set('servers', $servers);

        // Attach needed scripts for the form
        $view->attachScript("/ASP/frontend/js/jquery.form.js");
        $view->attachScript("/ASP/frontend/js/validate/jquery.validate-min.js");
        $view->attachScript("/ASP/frontend/js/datatables/jquery.dataTables.js");
        $view->attachScript("/ASP/frontend/modules/servers/js/serverinfo.js");

        // Attach needed stylesheets
        $view->attachStylesheet("/ASP/frontend/css/icons/icol16.css");

        // Send output
        $view->render();
    }

    /**
     * @protocol    ANY
     * @request     /ASP/servers/view/$id
     * @output      html
     *
     * @param int $id The Server ID
     */
    public function view($id = 0): void
    {
        // Require database connection
        $this->requireDatabase();

        // Attach Model
        $this->loadModel('ServerModel', 'servers');

        // Ensure correct format for ID
        $id = (int)$id;
        if ($id == 0)
        {
            Response::Redirect('servers');
            die;
        }

        // Fetch server list!
        $server = $this->serverModel->fetchServerById($id);
        if (empty($server))
        {
            Response::Redirect('servers');
            die;
        }

        // Additional information to display
        $server['last_seen'] = TimeHelper::FormatDifference((int)$server['lastseen'], time());
        $server['last_update'] = TimeHelper::FormatDifference((int)$server['lastupdate'], time());
        $server['server_auth_badge'] = ($server['server_authorized']) ? 'success' : 'important';
        $server['server_auth_text'] = ($server['server_authorized']) ? 'Authorized' : 'Not Authorized';
        $server['plasma_badge'] = ($server['plasma']) ? 'success' : 'inactive';
        $server['plasma_text'] = ($server['plasma']) ? 'Yes' : 'No';

        // Create view
        $view = new View('view', 'servers');
        $view->set('server', $server);

        // Attach needed scripts for the form
        $view->attachScript("/ASP/frontend/js/jquery.form.js");
        $view->attachScript("/ASP/frontend/js/validate/jquery.validate.js");
        $view->attachScript("/ASP/frontend/js/flot/jquery.flot.min.js");
        $view->attachScript("/ASP/frontend/js/flot/plugins/jquery.flot.tooltip.js");
        $view->attachScript("/ASP/frontend/js/datatables/jquery.dataTables.js");
        $view->attachScript("/ASP/frontend/js/bootstrap/bootstrap-tagsinput.js");
        $view->attachScript("/ASP/frontend/modules/servers/js/view.js");

        // Attach Stylesheets
        $view->attachStylesheet("/ASP/frontend/css/icons/icol16.css");
        $view->attachStylesheet("/ASP/frontend/js/bootstrap/bootstrap-tagsinput.css");

        // Send output
        $view->render();
    }

    /**
     * @protocol    GET
     * @request     /ASP/servers/history/$id
     * @output      HTML
     *
     * @param int $id The server ID
     */
    public function getHistory($id = 0): void
    {
        // Ensure correct format for ID
        $id = (int)$id;
        if ($id == 0)
        {
            Response::Redirect('servers');
            die;
        }

        // Require database
        $this->requireDatabase();

        // Load view
        $view = new View('history', 'servers');
        $view->set('id', $id);

        // Attach needed scripts for the form
        $view->attachScript("/ASP/frontend/js/datatables/jquery.dataTables.js");
        $view->attachScript("/ASP/frontend/modules/servers/js/history.js");

        // Attach needed stylesheets
        $view->attachStylesheet("/ASP/frontend/modules/players/css/links.css");

        // Send output
        $view->render();
    }

    /**
     * @protocol    POST
     * @request     /ASP/servers/history
     * @output      json
     */
    public function postHistory($id = 0): void
    {
        // Make sure we aren't logging into this page
        if ($_POST['action'] !== 'list')
        {
            $this->getHistory($id);
            return;
        }

        // Require a database connection
        $this->requireDatabase(true);

        // Attach Model
        $this->loadModel('ServerAjaxModel', 'servers', 'ajaxModel');

        // Get server id
        $serverId = (int)$_POST['serverId'];

        try
        {
            $data = $this->ajaxModel->getRoundList($serverId, $_POST);
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
     * @request     /ASP/servers/edit
     * @output      json
     */
    public function postEdit(): void
    {
        // Require specific actions
        $this->requireAction('edit');

        // Require database connection
        $this->requireDatabase(true);

        // Attach Model
        $this->loadModel('ServerModel', 'servers');

        // Use a dictionary here to gain an exception on missing array item
        $items = new Dictionary(false, $_POST);

        try
        {
            // Edit server
            // Update the server via the Model
            $this->serverModel->updateServerById(
                (int)$items['serverId'],
                $items['serverName'],
                $items['serverIp'],
                (int)$items['serverPort'],
                (int)$items['serverQueryPort']
            );

            // Add additional information for the output
            $items->add('mode', 'update');
            $items->add('serverId', (int)$items['serverId']);

            // Send success
            $this->sendJsonResponse(true, '', $items->toArray());
        }
        catch (Exception $e)
        {
            // Log exception
            System::LogException($e);

            // Tell the client that we have failed
            $this->sendJsonResponse(false, 'Query Failed! '. $e->getMessage(), [
                'lastQuery' => $this->serverModel->pdo->lastQuery
            ]);
        }
    }

    /**
     * @protocol    POST
     * @request     /ASP/servers/delete
     * @output      json
     */
    public function postDelete(): void
    {
        // Require specific actions
        $this->requireAction('delete');

        // Require database connection
        $this->requireDatabase(true);

        // Attach Model
        $this->loadModel('ServerModel', 'servers');

        try
        {
            // Delete servers
            $this->serverModel->deleteServers($_POST['servers']);
            $this->sendJsonResponse(true, $_POST['servers']);
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
     * @request     /ASP/servers/status
     * @output      json
     */
    public function postStatus(): void
    {
        // Form post?
        if ($_POST['action'] != 'status' || !isset($_POST['serverId']))
        {
            $this->sendJsonResponse(false, 'Invalid Action');
            return;
        }

        // Ensure correct format for ID
        $id = (int)$_POST['serverId'];
        if ($id == 0)
        {
            $this->sendJsonResponse(false, 'Invalid Server Id');
            return;
        }

        // Require database connection
        $this->requireDatabase(true);

        // Attach Model
        $this->loadModel('ServerModel', 'servers');

        // Fetch server
        $server = $this->serverModel->fetchServerById($id);
        if (empty($server))
        {
            $this->sendJsonResponse(false, 'Server Not Found!');
            return;
        }

        // Query Server, if we get a false response than the server is offline
        $result = $this->serverModel->queryServer($server['ip'], $server['queryport']);
        if (empty($result))
        {
            $this->sendJsonResponse(true, '', ['online' => false]);
            return;
        }

        // Load view and start settings variables
        $view = new View('details', 'servers');
        $view->set('server', $this->serverModel->formatRules($result['server']));
        $view->set('players1', $result['team1']);
        $view->set('players2', $result['team2']);

        // Try and get team 1's real name and flag
        $name = $result['server']['bf2_team1'];
        if ($this->serverModel->getArmyFromAbbreviation($name, $flag))
        {
            $view->set('team1name', $name);
            $view->set('team1flag', $flag);
        }
        else
        {
            $view->set('team1name', $result['server']['bf2_team1']);
            $view->set('team1flag', -1);
        }

        // Try and get team 2's real name and flag
        $name = $result['server']['bf2_team2'];
        if ($this->serverModel->getArmyFromAbbreviation($name, $flag))
        {
            $view->set('team2name', $name);
            $view->set('team2flag', $flag);
        }
        else
        {
            $view->set('team2name', $result['server']['bf2_team2']);
            $view->set('team2flag', -1);
        }

        // Send output
        $this->sendJsonResponse(true, $view->render(false, true), [
            'online' => true,
            'image' => $result['server']['bf2_sponsorlogo_url']
        ]);
    }

    /**
     * @protocol    GET
     * @request     /ASP/servers/chartData/{id}
     * @output      json
     *
     * @param int $id The server ID to fetch chart data for
     *
     * @throws Exception
     */
    public function getChartData($id = 0): void
    {
        // Hide errors. Specifically, Daylight Savings errors
        ini_set("display_errors", "0");

        // Require database connection
        $this->requireDatabase(true);

        // Load model
        $this->loadModel('ServerModel', 'servers');

        // Use our model to do all the hard work
        $data = $this->serverModel->getServerChartData($id);
        echo json_encode($data, JSON_PRETTY_PRINT, 10);
    }
}