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
use System\Config;
use System\Response;
use System\View;

/**
 * Battlespy Module Controller
 *
 * @package Modules
 */
class Battlespy extends Controller
{
    /**
     * @var BattlespyModel
     */
    protected $model;

    /**
     * @protocol    ANY
     * @request     /ASP/battlespy
     * @output      html
     */
    public function index(): void
    {
        // Require database connection
        $this->requireDatabase();

        // Attach Model
        $this->loadModel('BattlespyModel', 'battlespy', 'model');

        // Fetch report list!
        $reports = $this->model->getReportList();

        // Load view
        $view = new View('index', 'battlespy');
        $view->set('reports', $reports);

        // Attach needed scripts for the form
        $view->attachScript("/ASP/frontend/js/datatables/jquery.dataTables.js");
        $view->attachScript("/ASP/frontend/modules/battlespy/js/index.js");

        // Attach needed stylesheets
        $view->attachStylesheet("/ASP/frontend/css/icons/icol16.css");

        // Send output
        $view->render();
    }

    /**
     * @protocol    ANY
     * @request     /ASP/battlespy/config
     * @output      html
     */
    public function config(): void
    {
        // Load view
        $view = new View('config', 'battlespy');
        $view->set('battlespy_enable', Config::Get('battlespy_enable'));
        $view->set('battlespy_rank_check', Config::GetOrDefault('battlespy_rank_check', 1));
        $view->set('battlespy_max_spm', Config::Get('battlespy_max_spm'));
        $view->set('battlespy_max_kpm', Config::Get('battlespy_max_kpm'));
        $view->set('battlespy_max_target_kills', Config::Get('battlespy_max_target_kills'));
        $view->set('battlespy_max_team_kills', Config::Get('battlespy_max_team_kills'));
        $view->set('battlespy_max_awards', Config::Get('battlespy_max_awards'));
        $view->set('battlespy_max_accuracy', Config::GetOrDefault('battlespy_max_accuracy', 50));

        // Attach Model
        $this->loadModel('BattlespyModel', 'battlespy', 'model');

        // Attach weapons
        $weapons = $this->model->getWeaponsConfig();
        $view->set('weaponsCount', count($weapons));
        $view->set('weapons', $weapons);


        // Attach needed scripts for the form
        $view->attachScript("/ASP/frontend/js/jquery.form.js");
        $view->attachScript("/ASP/frontend/js/validate/jquery.validate-min.js");
        $view->attachScript("/ASP/frontend/modules/battlespy/js/config.js");

        // Send output
        $view->render();
    }

    /**
     * @protocol    ANY
     * @request     /ASP/battlespy/report/$id
     * @output      html
     *
     * @param int $id The report id
     */
    public function report($id = 0): void
    {
        // Require database connection
        $this->requireDatabase();

        // Attach Model
        $this->loadModel('BattlespyModel', 'battlespy', 'model');

        // Fetch report
        $report = $this->model->getReportById((int)$id);
        if (empty($report))
        {
            Response::Redirect('battlespy');
            die;
        }

        // Load view
        $view = new View('report', 'battlespy');
        $view->set('report', $report['report']);
        $view->set('round', $report['round']);
        $view->set('messages', $report['messages']);

        // Attach needed scripts for the form
        $view->attachScript("/ASP/frontend/js/datatables/jquery.dataTables.js");
        $view->attachScript("/ASP/frontend/modules/battlespy/js/report.js");

        // Attach needed stylesheets
        $view->attachStylesheet("/ASP/frontend/css/icons/icol16.css");
        $view->attachStylesheet("/ASP/frontend/modules/roundinfo/css/view.css");

        // Send output
        $view->render();
    }

    /**
     * @protocol    POST
     * @request     /ASP/battlespy/deleteReports
     * @output      json
     */
    public function postDeleteReports(): void
    {
        // Require specific actions
        $this->requireAction('delete');

        // Require database connection
        $this->requireDatabase(true);

        // Attach Model
        $this->loadModel('BattlespyModel', 'battlespy', 'model');

        try
        {
            // Delete servers
            $this->model->deleteReports($_POST['reports']);
            $this->sendJsonResponse(true, $_POST['reports']);
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
     * @request     /ASP/battlespy/deleteMessages
     * @output      json
     */
    public function postDeleteMessages(): void
    {
        // Require specific actions
        $this->requireAction('delete');

        // Require database connection
        $this->requireDatabase(true);

        // Attach Model
        $this->loadModel('BattlespyModel', 'battlespy', 'model');

        try
        {
            // Delete servers
            $this->model->deleteMessages($_POST['messages']);
            $this->sendJsonResponse(true, $_POST['messages']);
        }
        catch (Exception $e)
        {
            // Log exception
            System::LogException($e);

            // Tell the client that we have failed
            $this->sendJsonResponse(false, 'Query Failed! '. $e->getMessage());
        }
    }
}
