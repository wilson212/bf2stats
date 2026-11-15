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
use System\Controller;
use System\Collections\Dictionary;
use System\Response;
use System\View;

/**
 * Mapinfo Module Controller
 *
 * @package Mapinfo
 */
class Mapinfo extends Controller
{
    /** @var MapinfoModel */
    protected $model;

    /** @var MapinfoAjaxModel */
    protected $ajaxModel;

    /**
     * @protocol    ANY
     * @request     /ASP/mapinfo
     * @output      html
     */
    public function index(): void
    {
        // Require database connection
        $this->requireDatabase();

        // Fetch server list!
        $this->loadModel('MapinfoModel', 'mapinfo', 'model');
        $maps = $this->model->getMapStatistics();

        // Load view
        $view = new View('index', 'mapinfo');
        $view->set('maps', $maps);

        // Attach needed scripts for the form
        $view->attachScript("/ASP/frontend/js/datatables/jquery.dataTables.js");
        $view->attachScript("/ASP/frontend/js/jquery.form.js");
        $view->attachScript("/ASP/frontend/js/validate/jquery.validate-min.js");
        $view->attachScript("/ASP/frontend/modules/mapinfo/js/index.js");

        // Send output
        $view->render();
    }

    /**
     * @protocol    ANY
     * @request     /ASP/mapinfo/view/:id
     * @output      html
     */
    public function view($id): void
    {
        // Ensure correct format for ID
        $id = (int)$id;
        if ($id < 0)
        {
            Response::Redirect('mapinfo');
            return;
        }

        // Require database connection
        $this->requireDatabase();

        // Load model!
        $this->loadModel('MapinfoModel', 'mapinfo', 'model');

        // Fetch map by ID
        $map = [];
        $result = $this->model->getMapStatisticsById($id, $map, true);
        if (!$result)
        {
            Response::Redirect('mapinfo');
            return;
        }

        // Lowercase name for map image
        $map['lcname'] = strtolower((string) $map['name']);

        // Load view
        $view = new View('map_details', 'mapinfo');
        $view->set('map', $map);

        // Attach needed stylesheets
        $view->attachStylesheet("/ASP/frontend/modules/mapinfo/css/view.css");
        $view->attachStylesheet("/ASP/frontend/css/icons/icol16.css");

        // Attach needed scripts for the form
        $view->attachScript("/ASP/frontend/js/datatables/jquery.dataTables.js");
        $view->attachScript("/ASP/frontend/js/jquery.form.js");
        $view->attachScript("/ASP/frontend/js/validate/jquery.validate-min.js");
        $view->attachScript("/ASP/frontend/js/flot/jquery.flot.min.js");
        $view->attachScript("/ASP/frontend/js/flot/plugins/jquery.flot.pie.min.js");
        $view->attachScript("/ASP/frontend/js/flot/plugins/jquery.flot.tooltip.js");
        $view->attachScript("/ASP/frontend/modules/mapinfo/js/view.js");

        $team1_names = implode('<br />', $map['team1_armies']);
        $team2_names = implode('<br />', $map['team2_armies']);

        // Set win/loss/tie Ratio Chart Data
        $data = [
            ['label' => $team1_names, 'data' => $map['team1_wins'], 'color' => "#418CF0"],
            ['label' => $team2_names, 'data' => $map['team2_wins'], 'color' => "#FCB441"],
            ['label' => "Tie Games", 'data' => $map['ties'], 'color' => "#E0400A"]
        ];
        $view->setJavascriptVar('armyData', $data);

        // Send output
        $view->render();
    }

    /**
     * @protocol    POST
     * @request     /ASP/mapinfo/edit
     * @output      json
     */
    public function postEdit(): void
    {
        // Require action
        $this->requireAction('edit');

        // Require database connection
        $this->requireDatabase(true);

        // Load model
        $this->loadModel('MapinfoModel', 'Mapinfo', 'model');

        try
        {
            // Use a dictionary here to gain an exception on missing array item
            $items = new Dictionary(false, $_POST);

            // Define server id
            $this->model->setMapDisplayNameById($items['mapId'], $items['mapName']);

            // Send response
            $this->sendJsonResponse(true, 'Success', ['mapId' => $items['mapId'], 'displayName' => $items['mapName']]);
        }
        catch (Exception $e)
        {
            $pdo = Database::GetConnection('stats');
            $this->sendJsonResponse(false, 'Query Failed! '. $e->getMessage(), ['lastQuery' => $pdo->lastQuery]);
            die;
        }
    }

    /**
     * @protocol    POST
     * @request     /ASP/mapinfo/topPlayerList
     * @output      json
     */
    public function postTopPlayerList(): void
    {
        // Require a database connection
        $this->requireDatabase(true);

        // Attach Model
        $this->loadModel('MapinfoAjaxModel', 'mapinfo', 'ajaxModel');

        try
        {
            // Grab mapId
            $postData = new Dictionary(false, $_POST);
            $mapId = (int)$postData['mapId'];

            // Fetch top players list
            $data = $this->ajaxModel->getTopMapPlayersById($mapId, $_POST);
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
