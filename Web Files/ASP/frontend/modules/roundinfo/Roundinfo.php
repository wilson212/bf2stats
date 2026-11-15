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
use System\Controller;
use System\Response;
use System\View;

/**
 * Roundinfo Module Controller
 *
 * @package Modules
 */
class Roundinfo extends Controller
{
    /**
     * @var RoundInfoAjaxModel
     */
    protected $ajaxModel;

    /**
     * @var RoundInfoModel
     */
    protected $roundInfoModel;

    /**
     * @protocol    ANY
     * @request     /ASP/roundinfo
     * @output      html
     */
    public function index(): void
    {
        // Require database connection
        $this->requireDatabase();

        // Load view
        $view = new View('index', 'roundinfo');

        // Attach needed scripts for the form
        $view->attachScript("/ASP/frontend/js/datatables/jquery.dataTables.js");
        $view->attachScript("/ASP/frontend/modules/roundinfo/js/index.js");

        // Attach needed stylesheets
        $view->attachStylesheet("/ASP/frontend/modules/players/css/links.css");

        // Send output
        $view->render();
    }

    /**
     * @protocol    ANY
     * @request     /ASP/roundinfo/view/$id
     * @output      html
     *
     * @param int $id The round history ID
     */
    public function view($id = 0): void
    {
        // Require a database connection
        $this->requireDatabase();

        // Ensure correct format for ID
        $id = (int)$id;
        if ($id == 0)
        {
            Response::Redirect('roundinfo');
            die;
        }

        // Attach model
        $this->loadModel('RoundInfoModel', 'roundinfo');

        // Fetch round
        $round = $this->roundInfoModel->fetchRoundInfoById($id);
        if (empty($round))
        {
            Response::Redirect('roundinfo');
            die;
        }

        // Attach round information to view
        $view = new View('view', 'roundinfo');
        $view->set('round', $round['round']);
        $view->set('players', $round['players']);
        $view->set('players1', $round['players1']);
        $view->set('players2', $round['players2']);

        // Add advanced information, and awards
        StatsData::Load();
        $this->roundInfoModel->addAdvancedRoundInfo($round['players'], $id, $view);
        $this->roundInfoModel->attachAwards($id, $view);

        // Check for a battlespy report
        $reportId =  $this->roundInfoModel->getBattleSpyReportId($id);
        if ($reportId > 0)
        {
            $link = "<a href=\"/ASP/battlespy/report/{$reportId}\" target=\"_blank\">Click Here</a>";
            $view->displayMessage('warning', "This round has a BattleSpy report attached to it. {$link} to view the report.");
        }

        // Attach needed scripts for the form
        $view->attachScript("/ASP/frontend/js/datatables/jquery.dataTables.js");
        $view->attachScript("/ASP/frontend/modules/roundinfo/js/view.js");

        // Attach stylesheets
        $view->attachStylesheet("/ASP/frontend/css/icons/icol32.css");
        $view->attachStylesheet("/ASP/frontend/modules/roundinfo/css/view.css");

        // Send output
        $view->render();
    }

    /**
     * @protocol    POST
     * @request     /ASP/roundinfo/list
     * @output      json
     */
    public function postList(): void
    {
        // Require a database connection
        $this->requireDatabase(true);

        // Attach Model
        $this->loadModel('RoundInfoAjaxModel', 'roundinfo', 'ajaxModel');

        try
        {
            $data = $this->ajaxModel->getRoundList($_POST);
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
