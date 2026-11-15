<?php
/**
 * BF2Statistics ASP Framework
 *
 * Author:       Steven Wilson
 * Copyright:    Copyright (c) 2006-2021, BF2statistics.com
 * License:      GNU GPL v3
 *
 */
use System\Database\MySqlConnectionStringBuilder;
use System\Response;
use System\Controller;
use System\AspResponse;
use System\Autoloader;
use System\Database;
use System\Config;
use System\ErrorHandler;
use System\IO\Path;
use System\LogWriter;
use System\Request;
use System\Security;
use System\View;

/**
 * Asp Class
 *
 * Responsible for loading the frontend
 */
class System
{
    /**
     * Indicates whether the Asp is running
     */
    private static bool $isRunning = false;

    /**
     * Initiates the ASP admin interface.
     */
    public static function Run(): void
    {
        // Only allow the system to run once
        if (self::$isRunning) {
            return;
        }

        // Register that we are running
        self::$isRunning = true;

        // Register AutoLoader
        Autoloader::Register();

        // Process request based on type
        if (isset($_GET['aspx'])) {
            self::HandleAspxRequest();
        } else {
            self::HandleAdminRequest();
        }
    }

    /**
     * Handles an .ASPX request
     */
    protected static function HandleAspxRequest()
    {
        // Disable Zlib Compression, and hide errors
        ini_set('zlib.output_compression', '0');
        ini_set("display_errors", "0");

        foreach (array_merge($_GET, $_POST) as $value) {
            if (!is_scalar($value)) {
                $response = new AspResponse();
                $response->responseError(true, 107);
                $response->writeHeaderLine('asof', 'err');
                $response->writeDataLine(time(), 'Invalid Syntax!');
                $response->send();
            }
        }

        // Check user-agent
        if (Config::Get('stats_strict_api') == 1 && trim((string) ($_SERVER['HTTP_USER_AGENT'] ?? '')) !== "GameSpyHTTP/1.0") {
            header('HTTP/1.1 403 Forbidden');
            echo "<h1>403 Forbidden</h1>";
            echo "You are not authorised to access this page.";
            die;
        }

        // Connect to the stats database
        $connection = null;
        try
        {
            // Create connection using the MySQL connection builder
            $builder = new MySqlConnectionStringBuilder();
            $builder->host = Config::Get('db_host');
            $builder->port = Config::Get('db_port');
            $builder->user = Config::Get('db_user');
            $builder->password = Config::Get('db_pass');
            $builder->database = Config::Get('db_name');
            $connection = Database::CreateConnection('stats', $builder);
        }
        catch (Exception $e)
        {
            goto DatabaseOffline;
        }

        // Sometimes an exception isn't thrown by PDO
        if (!($connection instanceof PDO))
        {
            DatabaseOffline:
            $Response = new AspResponse();
            $Response->responseError(true);
            $Response->writeHeaderLine("asof", "err");
            $Response->writeDataLine(time(), "Stats Database Offline");
            $Response->send();
        }

        // Determine requested file name, protecting against Local File Inclusion
        $file = Path::GetFilenameWithoutExtension(strtolower((string) $_GET['aspx']));
        $file = Path::Combine(ROOT, "aspx", $file . ".php");

        // Make sure file exists
        if (!file_exists($file))
        {
            header('HTTP/1.1 404 Not Found');
            echo "<h1>404 Not Found</h1>";
            echo "The page that you have requested could not be found.";
            die;
        }

        // Define content type as Text, and run the ASPX file
        header("Content-Type: text/plain; charset=utf-8");

        try
        {
            /** @noinspection PhpIncludeInspection */
            include $file;
        }
        catch (\Throwable $e)
        {
            // Create ASP log file instance
            try
            {
                new LogWriter(Path::Combine(SYSTEM_PATH, "logs", "asp_debug.log"), "Asp");
                System::LogException($e);
            }
            catch (Exception)
            {
                // ignore
            }

            $response = new AspResponse();
            $response->responseError(true, 500);
            $response->writeHeaderLine("asof", "err");
            $response->writeDataLine(time(), "Internal Server Error");
            $response->send();
        }
    }

    /**
     * Handles an admin request
     */
    protected static function HandleAdminRequest()
    {
        // First and Foremost, Set timezone
        date_default_timezone_set(Config::Get('admin_timezone'));

        // Then register error handler
        ErrorHandler::Register(true, true);

        $isLocalHost = Request::IsLocalHost();

        // Next, Lets make sure the IP can view the ASP
        if (!$isLocalHost && !Security::IsAuthorizedIp(Request::ClientIp())) {
            die('<span style="color: red; ">ERROR:</span> You are NOT Authorised to access this Page! (Ip: ' . Request::ClientIp() . ')');
        }

        // Always set a post and get actions
        if (!isset($_POST['action'])) {
            $_POST['action'] = '';
        }
        if (!isset($_GET['action'])) {
            $_GET['action'] = '';
        }

        // Validate unsafe HTTP methods, including login, before dispatching them.
        if (!in_array(Request::Method(), ['GET', 'HEAD'], true)
            && !Security::IsValidCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN'] ?? $_POST['csrf_token'] ?? null))
        {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid CSRF token. Refresh the page and try again.']);
            return;
        }

        // Check for login / logout requests
        if ($_POST['action'] == 'login' && isset($_POST['username']) && isset($_POST['password']))
        {
            Security::Login($_POST['username'], $_POST['password']);
        }
        elseif ($_POST['action'] == 'logout')
        {
            Security::Logout();
        }

        // Check and see if the user is logged in
        if (!Security::IsValidSession())
        {
            // Loopback connections can originate from reverse proxies. Always
            // require credentials, even when the peer passes the IP allowlist.
            if (Request::IsAjax())
            {
                $message = "Login session has expired! Please refresh the page and login again.";
                echo json_encode(['success' => false, 'message' => $message, 'error' => $message]);
            }
            else
            {
                $view = new View('login');
                $view->render(false);
            }
            return;
        }

        // Create the required log writer for the ASP
        try {
            // Create ASP log file instance
            $LogWriter = new LogWriter(Path::Combine(SYSTEM_PATH, "logs", "asp_debug.log"), "Asp");
        }
        catch (Exception $e) {
            // Use temporary file
            $LogWriter = new LogWriter('php://memory', "Asp");
        }

        // Connect to the stats database
        try
        {
            // Create connection using the MySQL connection builder
            $builder = new MySqlConnectionStringBuilder();
            $builder->host = Config::Get('db_host');
            $builder->port = Config::Get('db_port');
            $builder->user = Config::Get('db_user');
            $builder->password = Config::Get('db_pass');
            $builder->database = Config::Get('db_name');
            $builder->setAttribute(PDO::MYSQL_ATTR_LOCAL_INFILE, true);
            $DB = Database::CreateConnection('stats', $builder);

            // Fetch database version
            $stmt = $DB->query("SELECT `version` FROM `_version` ORDER BY `updateid` DESC LIMIT 1;");
            $result = $stmt->fetchColumn(0);

            define('DB_VERSION', ($result === false) ? '0.0.0' : $result);
        }
        catch (Exception $e)
        {
            define('DB_VERSION', '0.0.0');
            $LogWriter->logDebug("Database connection failed: " . $e->getMessage());
        }

        // Get our MVC route
        $uri = (isset($_GET['uri']) && !empty($_GET['uri'])) ? $_GET['uri'] : 'home';
        $uri = str_replace('/ASP/', '', $uri);
        $parts = explode('/', $uri);
        $length = count($parts);
        $GLOBALS['controller'] = $controller = ($length > 0) ? $parts[0] : 'home';
        $action = ($length > 1 && (isset($parts[1]) && ($parts[1] !== '' && $parts[1] !== '0'))) ? $parts[1] : 'index';
        $params = array_slice($parts, 2);

        // Process DB version messages
        if (DB_VERSION == '0.0.0') {
            View::ShowGlobalMessage('Unable to establish a database connection. If you need to setup the ASP, <a href="/ASP/install">Click Here to begin Installation</a>');
        } elseif (DB_VERSION != DB_EXPECTED_VERSION) {
            // Only allow the database, install, or config controllers at this point...
            if ($controller !== 'database' && $controller !== 'config' && $controller !== 'install')
            {
                Response::Redirect('/database/update');
                die;
            }
            View::ShowGlobalMessage('Database is outdated! Please upgrade your database to the current version to prevent errors from occurring.');
        }

        // Load task
        self::LoadModule($controller, $action, $params);
    }

    /**
     * Loads and runs a module with the specified action and parameters
     *
     * @param string $name The module and controller name
     * @param string $action The action, or method name to execute in the controller
     * @param array $params The parameters to be passed to the controller's action method.
     *
     * @throws ReflectionException
     */
    public static function LoadModule($name, $action, array $params): void
    {
        if (!preg_match('/^[a-z][a-z0-9]*$/iD', $name)
            || !preg_match('/^[a-z][a-z0-9]*$/iD', $action)
            || strtolower($name) === 'devtest') {
            http_response_code(404);
            return;
        }
        // Process the task by making sure the module exists
        $modNMame = strtolower($name);
        $className = ucfirst($name);

        // The way we built this path here naturally protects against Local File Inclusion
        $file = Path::Combine(ROOT, 'frontend', 'modules', $modNMame, $className . '.php');

        // Check if the controller exists already, if not, import it
        if (!class_exists($className, false))
        {
            // Build file path to the controller, check if it exists
            if (!file_exists($file))
            {
                // Show 404
                $view = new View('404');
                $view->set('message', "Module \"{$modNMame}\" Does not Exist!");
                $view->render();
                return;
            }

            // Load our controller file
            /** @noinspection PhpIncludeInspection */
            require $file;
        }

        // Load the controller reflection
        $rController = new ReflectionClass($className);

        // Make sure the controller is not abstract object
        if ($rController->isAbstract()) {
            die('Module controller "'. $className .'" is abstract, and cannot be called via url');
        }

        // Ensure this class can be accessed by URL
        if (!$rController->isSubclassOf(Controller::class)) {
            die('Module controller "'. $className .'" does not extend \\System\\Controller, therefor it cannot be called via url');
        }

        // Construct our controller
        $controller = new $className();

        // Check request method prefix'd action
        $m = strtolower( Request::Method() ) . ucfirst($action);
        if ($rController->hasMethod($m))
        {
            $action = $m;
        }
        elseif (!$rController->hasMethod($action))
        {
            $view = new View('404');
            $view->set('message', "Module \"{$className}\" does not contain the method \"{$action}\" or \"{$m}\"!");
            $view->render();
            return;
        }

        // Create a reflection of the controller method
        $method = new ReflectionMethod($controller, $action);

        // Direct URLs such as /players/postDelete must not bypass the HTTP
        // method check (and therefore the CSRF check above).
        if (str_starts_with(strtolower($action), 'post') && !Request::IsPost()) {
            http_response_code(405);
            header('Allow: POST');
            return;
        }

        // If the method is not public, then we don't allow URL access!
        if (!$method->isPublic()) {
            die("Method \"{$action}\" is not a public method, and cannot be called via URL.");
        }

        // Invoke the module controller and action
        $method->invokeArgs($controller, $params);
    }

    /**
     * Logs a detailed and recursive exception to the asp_debug.log file
     */
    public static function LogException(\Throwable $e): void
    {
        ErrorHandler::LogException($e);
    }
}
