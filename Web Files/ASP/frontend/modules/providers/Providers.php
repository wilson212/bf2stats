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

class Providers extends Controller
{
    /**
     * @var ProviderModel
     */
    protected $providerModel;

    /** @var ProviderAjaxModel */
    protected $ajaxModel;

    /**
     * @protocol    ANY
     * @request     /ASP/providers
     * @output      html
     */
    public function index(): void
    {
        // Require database connection
        $this->requireDatabase();

        // Attach Model
        $this->loadModel('ProviderModel', 'providers');

        // Fetch server list!
        $providers = $this->providerModel->fetchProviders();
        // Attach tags
        $counter = count($providers);

        // Attach tags
        for ($i = 0; $i < $counter; $i++)
        {
            $providers[$i]['auth_badge'] = ($providers[$i]['authorized']) ? 'success' : 'important';
            $providers[$i]['auth_text'] = ($providers[$i]['authorized']) ? 'Authorized' : 'Not Authorized';
            $providers[$i]['plasma_badge'] = ($providers[$i]['plasma']) ? 'success' : 'inactive';
            $providers[$i]['plasma_text'] = ($providers[$i]['plasma']) ? 'Yes' : 'No';
        }

        // Load view
        $view = new View('index', 'providers');
        $view->set('providers', $providers);

        // Attach needed scripts for the form
        $view->attachScript("/ASP/frontend/js/jquery.form.js");
        $view->attachScript("/ASP/frontend/js/validate/jquery.validate-min.js");
        $view->attachScript("/ASP/frontend/js/datatables/jquery.dataTables.js");
        $view->attachScript("/ASP/frontend/modules/providers/js/index.js");

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
    public function view($id): void
    {
        // Require database connection
        $this->requireDatabase();

        // Attach Model
        $this->loadModel('ProviderModel', 'providers');

        // Ensure correct format for ID
        $id = (int)$id;
        if ($id == 0)
        {
            Response::Redirect('providers');
            die;
        }

        // Fetch provider list!
        $provider = $this->providerModel->fetchProviderById($id);
        if (empty($provider))
        {
            Response::Redirect('providers');
            die;
        }

        // Set last seen
        $provider['last_update'] = TimeHelper::FormatDifference((int)$provider['lastupdate'], time());
        $provider['auth_badge'] = ($provider['authorized']) ? 'success' : 'important';
        $provider['auth_text'] = ($provider['authorized']) ? 'Authorized' : 'Not Authorized';
        $provider['plasma_badge'] = ($provider['plasma']) ? 'success' : 'inactive';
        $provider['plasma_text'] = ($provider['plasma']) ? 'Yes' : 'No';

        // Create view
        $view = new View('view', 'providers');
        $view->set('provider', $provider);

        // Fetch Auth Token authorized addresses
        $addresses = $this->providerModel->fetchAuthorizedProviderIpsById($id);
        $view->set('addresses', $addresses);

        // Fetch servers
        $servers = $this->providerModel->getServersByProviderId($id);
        // Attach tags
        $counter = count($servers);

        // Attach tags
        for ($i = 0; $i < $counter; $i++)
        {
            $servers[$i]['auth_badge'] = ($servers[$i]['authorized']) ? 'success' : 'important';
            $servers[$i]['auth_text'] = ($servers[$i]['authorized']) ? 'Authorized' : 'Not Authorized';
        }
        $view->set('servers', $servers);

        // Attach needed scripts for the form
        $view->attachScript("/ASP/frontend/js/jquery.form.js");
        $view->attachScript("/ASP/frontend/js/validate/jquery.validate.js");
        $view->attachScript("/ASP/frontend/js/flot/jquery.flot.min.js");
        $view->attachScript("/ASP/frontend/js/flot/plugins/jquery.flot.tooltip.js");
        $view->attachScript("/ASP/frontend/js/datatables/jquery.dataTables.js");
        $view->attachScript("/ASP/frontend/js/bootstrap/bootstrap-tagsinput.js");
        $view->attachScript("/ASP/frontend/modules/providers/js/view.js");

        // Attach Stylesheets
        $view->attachStylesheet("/ASP/frontend/css/icons/icol16.css");
        $view->attachStylesheet("/ASP/frontend/js/bootstrap/bootstrap-tagsinput.css");

        // Send output
        $view->render();
    }

    /**
     * @protocol    GET
     * @request     /ASP/providers/history/$id
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
            Response::Redirect('providers');
            die;
        }

        // Require database
        $this->requireDatabase();

        // Load view
        $view = new View('history', 'providers');
        $view->set('providerId', $id);

        // Attach needed scripts for the form
        $view->attachScript("/ASP/frontend/js/datatables/jquery.dataTables.js");
        $view->attachScript("/ASP/frontend/modules/providers/js/history.js");

        // Attach needed stylesheets
        $view->attachStylesheet("/ASP/frontend/modules/players/css/links.css");

        // Send output
        $view->render();
    }

    /**
     * @protocol    POST
     * @request     /ASP/providers/history
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
        $this->loadModel('ProviderAjaxModel', 'providers', 'ajaxModel');

        // Get provider id
        $providerId = (int)$_POST['providerId'];

        try
        {
            $data = $this->ajaxModel->getRoundList($providerId, $_POST);
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
     * @request     /ASP/providers/add
     * @output      json
     */
    public function postAdd(): void
    {
        // Require database connection
        $this->requireDatabase(true);

        // Attach Model
        $this->loadModel('ProviderModel', 'providers');

        try
        {
            // Use a dictionary here to gain an exception on missing array item
            $items = new Dictionary(false, $_POST);

            // Switch on our action base
            switch ($_POST['action'])
            {
                case 'add':
                    // Add the server via the Model
                    $id = $this->providerModel->addProvider(
                        $items['providerName'],
                        true,
                        $authID,
                        $authToken
                    );

                    // Add additional information for the output
                    $items->add('mode', 'add');
                    $items->add('providerId', $id);
                    $items->add('authId', $authID);
                    $items->add('authToken', $authToken);

                    // Send client response
                    $this->sendJsonResponse(true, '', $items->toArray());
                    break;
                case 'edit':
                    // Update the server via the Model
                    $this->providerModel->updateProviderById(
                        (int)$items['providerId'],
                        $items['providerName']
                    );

                    // Add additional information for the output
                    $items->add('mode', 'update');
                    $items->add('providerId', (int)$items['providerId']);

                    $this->sendJsonResponse(true, '', $items->toArray());
                    break;
                default:
                    $this->sendJsonResponse(false, 'Invalid Action');
                    return;
            }
        }
        catch (Exception $e)
        {
            $this->sendJsonResponse(false, 'Query Failed! '. $e->getMessage(), [
                'lastQuery' => $this->providerModel->pdo->lastQuery
            ]);
        }
    }

    /**
     * @protocol    POST
     * @request     /ASP/providers/token
     * @output      json
     */
    public function postToken(): void
    {
        // Ensure correct action
        $this->requireAction('address', 'newId', 'newToken');

        // Require the database
        $this->requireDatabase(true);

        // Attach Model
        $this->loadModel('ProviderModel', 'providers');

        // Sanitize id
        $id = (int)$_POST['providerId'];

        try
        {
            // Process based on action
            switch ($_POST['action'])
            {
                case 'address':
                    // Sync list with database
                    $this->providerModel->syncAuthorizedProviderIpsById($id, $_POST['addresses']);

                    // Send success
                    $this->sendJsonResponse(true, 'Successfully sync\'d list');
                    break;
                case 'newId':
                    $newId = $this->providerModel->generateNewAuthIdForProvider($id);
                    if ($newId === false) {
                        $this->sendJsonResponse(false, 'Unable to generate new AuthId for provider');
                    } else {
                        $this->sendJsonResponse(true, $newId);
                    }

                    break;
                case 'newToken':
                    $newId = $this->providerModel->generateNewAuthTokenForProvider($id);
                    if (empty($newId)) {
                        $this->sendJsonResponse(false, 'Unable to generate new AuthToken for provider');
                    } else {
                        $this->sendJsonResponse(true, $newId);
                    }
                    break;
            }
        }
        catch (Exception $e)
        {
            System::LogException($e);

            // Send success
            $this->sendJsonResponse(false, $e->getMessage());
        }
    }

    /**
     * @protocol    POST
     * @request     /ASP/providers/delete
     * @output      json
     */
    public function postDelete(): void
    {
        // Require specific actions
        $this->requireAction('delete');

        // Require database connection
        $this->requireDatabase(true);

        // Attach Model
        $this->loadModel('ProviderModel', 'providers');

        try
        {
            // Delete servers
            $this->providerModel->deleteProviders($_POST['providers']);
            $this->sendJsonResponse(true, $_POST['providers']);
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
     * @request     /ASP/providers/authorize
     * @output      json
     */
    public function postAuthorize(): void
    {
        // Require specific actions
        $this->requireAction('auth', 'unauth');

        // Require database connection
        $this->requireDatabase(true);

        // Attach Model
        $this->loadModel('ProviderModel', 'providers');

        try
        {
            // Authorize servers
            $mode = ($_POST['action'] == 'auth');
            $this->providerModel->authorizeProviders($mode, $_POST['providers']);
            $this->sendJsonResponse(true, $_POST['providers']);
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
     * @request     /ASP/providers/plasma
     * @output      json
     */
    public function postPlasma(): void
    {
        // Require specific actions
        $this->requireAction('plasma', 'unplasma');

        // Require database connection
        $this->requireDatabase(true);

        // Attach Model
        $this->loadModel('ProviderModel', 'providers');

        try
        {
            // Set Plasma mode on servers
            $mode = ($_POST['action'] == 'plasma');
            $this->providerModel->plasmaProviders($mode, $_POST['providers']);
            $this->sendJsonResponse(true, $_POST['providers']);
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
     * @protocol    GET
     * @request     /ASP/providers/chartData/{id}
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
        $this->loadModel('ProviderModel', 'providers');

        // Use our model to do all the hard work
        $data = $this->providerModel->getServerChartData($id);
        echo json_encode($data, JSON_PRETTY_PRINT, 10);
    }
}