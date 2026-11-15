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
use System\View;

class Stats extends Controller
{
    /** @var StatsModel */
    protected $model;

    /** @var StatsAjaxModel */
    protected $ajaxModel;

    /**
     * @protocol    ANY
     * @request     /ASP/stats
     * @output      html
     */
    public function index(): void
    {
        // Require database connection
        $this->requireDatabase();

        // Load view
        $view = new View('index', 'stats');

        // Send output
        $view->render();
    }

    /**
     * @protocol    ANY
     * @request     /ASP/stats/armies
     * @output      html
     */
    public function armies(): void
    {
        // Require database connection
        $this->requireDatabase();

        // Load model
        $this->loadModel('StatsModel', 'stats', 'model');

        // Load view
        $view = new View('armies', 'stats');

        // Set items
        $view->set('items', $this->model->getArmies());

        // Attach stylesheets
        $view->attachStylesheet("/ASP/frontend/modules/stats/css/table.css");
        $view->attachStylesheet("/ASP/frontend/js/select2/select2.css");

        // Attach scripts
        $view->attachScript("/ASP/frontend/js/datatables/jquery.dataTables.js");
        $view->attachScript("/ASP/frontend/js/select2/select2.min.js");
        $view->attachScript("/ASP/frontend/modules/stats/js/armies.js");

        // Send output
        $view->render();
    }

    /**
     * @protocol    POST
     * @request     /ASP/stats/topArmyPlayers
     * @output      json
     */
    public function postTopArmyPlayers(): void
    {
        // Require a database connection
        $this->requireDatabase(true);

        // Attach Model
        $this->loadModel('StatsAjaxModel', 'stats', 'ajaxModel');

        try
        {
            // Grab mapId
            $postData = new Dictionary(false, $_POST);
            $armyId = (int)$postData['filterItem'];

            // Fetch top players list
            $data = $this->ajaxModel->getTopArmyPlayersById($armyId, $_POST);
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
     * @protocol    ANY
     * @request     /ASP/stats/kits
     * @output      html
     */
    public function kits(): void
    {
        // Require database connection
        $this->requireDatabase();

        // Load model
        $this->loadModel('StatsModel', 'stats', 'model');

        // Load view
        $view = new View('table', 'stats');
        $view->set('icon', 'accessibility-2');
        $view->set('title', 'Top Kit Players');
        $view->set('ajax', '/ASP/stats/topKitPlayers');

        // Set items
        $view->set('items', $this->model->getKits());

        // Attach stylesheets
        $view->attachStylesheet("/ASP/frontend/modules/stats/css/table.css");
        $view->attachStylesheet("/ASP/frontend/js/select2/select2.css");

        // Attach scripts
        $view->attachScript("/ASP/frontend/js/datatables/jquery.dataTables.js");
        $view->attachScript("/ASP/frontend/js/select2/select2.min.js");
        $view->attachScript("/ASP/frontend/modules/stats/js/table.js");

        // Send output
        $view->render();
    }

    /**
     * @protocol    POST
     * @request     /ASP/stats/topKitPlayers
     * @output      json
     */
    public function postTopKitPlayers(): void
    {
        // Require a database connection
        $this->requireDatabase(true);

        // Attach Model
        $this->loadModel('StatsAjaxModel', 'stats', 'ajaxModel');

        try
        {
            // Grab mapId
            $postData = new Dictionary(false, $_POST);
            $kitId = (int)$postData['filterItem'];

            // Fetch top players list
            $data = $this->ajaxModel->getTopKitPlayersById($kitId, $_POST);
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
     * @protocol    ANY
     * @request     /ASP/stats/vehicles
     * @output      html
     */
    public function vehicles(): void
    {
        // Require database connection
        $this->requireDatabase();

        // Load model
        $this->loadModel('StatsModel', 'stats', 'model');

        // Load view
        $view = new View('table', 'stats');
        $view->set('icon', 'chopper');
        $view->set('title', 'Top Vehicle Players');
        $view->set('ajax', '/ASP/stats/topVehiclePlayers');

        // Set items
        $view->set('items', $this->model->getVehicles());

        // Attach stylesheets
        $view->attachStylesheet("/ASP/frontend/modules/stats/css/table.css");
        $view->attachStylesheet("/ASP/frontend/js/select2/select2.css");

        // Attach scripts
        $view->attachScript("/ASP/frontend/js/datatables/jquery.dataTables.js");
        $view->attachScript("/ASP/frontend/js/select2/select2.min.js");
        $view->attachScript("/ASP/frontend/modules/stats/js/table.js");

        // Send output
        $view->render();
    }

    /**
     * @protocol    POST
     * @request     /ASP/stats/topVehiclePlayers
     * @output      json
     */
    public function postTopVehiclePlayers(): void
    {
        // Require a database connection
        $this->requireDatabase(true);

        // Attach Model
        $this->loadModel('StatsAjaxModel', 'stats', 'ajaxModel');

        try
        {
            // Grab mapId
            $postData = new Dictionary(false, $_POST);
            $itemId = (int)$postData['filterItem'];

            // Fetch top players list
            $data = $this->ajaxModel->getTopVehiclePlayersById($itemId, $_POST);
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
     * @protocol    ANY
     * @request     /ASP/stats/weapons
     * @output      html
     */
    public function weapons(): void
    {
        // Require database connection
        $this->requireDatabase();

        // Load model
        $this->loadModel('StatsModel', 'stats', 'model');

        // Load view
        $view = new View('weapons', 'stats');

        // Set items
        $view->set('items', $this->model->getWeapons());

        // Attach stylesheets
        $view->attachStylesheet("/ASP/frontend/modules/stats/css/table.css");
        $view->attachStylesheet("/ASP/frontend/js/select2/select2.css");

        // Attach scripts
        $view->attachScript("/ASP/frontend/js/datatables/jquery.dataTables.js");
        $view->attachScript("/ASP/frontend/js/select2/select2.min.js");
        $view->attachScript("/ASP/frontend/modules/stats/js/weapons.js");

        // Send output
        $view->render();
    }

    /**
     * @protocol    POST
     * @request     /ASP/stats/topWeaponPlayers
     * @output      json
     */
    public function postTopWeaponPlayers(): void
    {
        // Require a database connection
        $this->requireDatabase(true);

        // Attach Model
        $this->loadModel('StatsAjaxModel', 'stats', 'ajaxModel');

        try
        {
            // Grab mapId
            $postData = new Dictionary(false, $_POST);
            $itemId = (int)$postData['filterItem'];

            // Fetch top players list
            $data = $this->ajaxModel->getTopWeaponPlayersById($itemId, $_POST);
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
}