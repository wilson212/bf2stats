<?php
/**
 * BF2Statistics ASP Framework
 *
 * Author:       Steven Wilson
 * Copyright:    Copyright (c) 2006-2021, BF2statistics.com
 * License:      GNU GPL v3
 *
 */
use System\Config AS Cfg;
use System\Controller;
use System\Database;
use System\IO\Directory;
use System\IO\File;
use System\IO\Path;
use System\Response;
use System\View;

/**
 * Config Module Controller
 *
 * @package Modules
 */
class Config extends Controller
{
    /**
     * @protocol    ANY
     * @request     /ASP/config/[?:index]
     * @output      html
     */
    public function index(): void
    {
        // Fetch all vars from the system config file
        $items = Cfg::FetchAll();

        // Set view variables
        $view = new View('index', 'config');
        $view->set('config', $items);

        $regions = [
            'Africa' => DateTimeZone::AFRICA,
            'America' => DateTimeZone::AMERICA,
            'Antarctica' => DateTimeZone::ANTARCTICA,
            'Asia' => DateTimeZone::ASIA,
            'Atlantic' => DateTimeZone::ATLANTIC,
            'Australia' => DateTimeZone::AUSTRALIA,
            'Europe' => DateTimeZone::EUROPE,
            'Indian' => DateTimeZone::INDIAN,
            'Pacific' => DateTimeZone::PACIFIC
        ];

        $timezones = [];
        foreach ($regions as $name => $mask)
        {
            $zones = DateTimeZone::listIdentifiers($mask);
            foreach($zones as $timezone)
            {
                // Lets sample the time there right now
                $time = new DateTime('now', new DateTimeZone($timezone));

                // Us dumb Americans can't handle military time
                $ampm = $time->format('H') > 12 ? ' ('. $time->format('g:i a'). ')' : '';

                // Remove region name and add a sample time
                $timezones[$name][$timezone] = substr($timezone, strlen($name) + 1) . ' - ' . $time->format('H:i') . $ampm;
            }
        }

        $view->set('timezones', $timezones);

        // Attach needed scripts for the form
        $view->attachScript("/ASP/frontend/js/jquery.form.js");
        $view->attachScript("/ASP/frontend/js/validate/jquery.validate-min.js");
        $view->attachScript("/ASP/frontend/js/select2/select2.min.js");
        $view->attachScript("/ASP/frontend/js/bootstrap/bootstrap-tagsinput.js");
        $view->attachScript("/ASP/frontend/modules/config/js/editconfig.js");

        // Attach needed stylesheets
        $view->attachStylesheet("/ASP/frontend/js/select2/select2.css");
        $view->attachStylesheet("/ASP/frontend/js/bootstrap/bootstrap-tagsinput.css");

        // Send output
        $view->render();
    }

    /**
     * @protocol    ANY
     * @request     /ASP/config/test
     * @output      html
     */
    public function test(): void
    {
        // Require database connection
        if (DB_VERSION == '0.0.0')
        {
            Response::Redirect('install');
            die;
        }

        // Check for POST data
        if ($_POST['action'] == 'runtests')
        {
            $this->runTests();
            return;
        }

        // Set view variables
        $view = new View('testconfig', 'config');

        // Attach needed scripts for the form
        $view->attachScript("/ASP/frontend/modules/config/js/testconfig.js");

        // Send output
        $view->render();
    }

    /**
     * @protocol    POST
     * @request     /ASP/config/save
     * @output      json
     */
    public function postSave(): void
    {
        // Require action
        $this->requireAction('save_config');

        try
        {
            foreach ($_POST as $item => $val)
            {
                $key = explode('__', $item);
                if ($key[0] === 'cfg')
                {
                    if ($key[1] === 'admin_hosts') {
                        Cfg::Set($key[1], explode(',', (string) $val));
                        continue;
                    } elseif (is_array($val)) {
                        Cfg::Set($key[1], array_values($val));
                        continue;
                    }

                    Cfg::Set($key[1], $val);
                }
            }

            // Determine if our save is a success
            $result = Cfg::Save();
            $this->sendJsonResponse($result, '');
        }
        catch (Exception $e)
        {
            // Output exception message
            $this->sendJsonResponse(false, $e->getMessage());
        }
    }

    protected function runTests()
    {
        // Remove our time limit!
        ini_set('max_execution_time', 30);

        // Define our pass/fail messages for less typing
        define('__PASS', '<b><span style="color: green; ">Pass</span></b><br />');
        define('__WARN', '<b><span style="color: orange; ">Warn</span></b><br />');
        define('__FAIL', '<b><span style="color: red; ">Fail</span></b><br />');

        // Vars
        $errors = false;
        $warns = false;

        // Check Cache Folder Write Access
        $out = " > Checking ASP URI...<br />";
        $out .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- ASP folder is properly stored in www root. (www/ASP/): ";
        if (isset($_SERVER['REQUEST_URI']) && str_starts_with((string) $_SERVER['REQUEST_URI'], "/ASP/"))
        {

            $out .= __PASS;
        }
        else
        {
            $errors = true;
            $out .= __FAIL;
        }

        // Check Database Access
        $out .= " > Checking Database...<br />";
        $DB = Database::GetConnection('stats');
        if ($DB instanceof PDO)
        {
            $out .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Database Connection (".Cfg::Get('db_host')."): ".__PASS;

            // Check Database Version
            if (DB_VERSION != DB_EXPECTED_VERSION) {
                $out .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Database version (". DB_EXPECTED_VERSION ."): ".__FAIL;
            } else {
                $out .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Database version (". DB_EXPECTED_VERSION ."): ".__PASS;
            }
        }
        else
        {
            $out .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Database Connection (".Cfg::Get('db_host')."): ".__FAIL;
            $errors = true;
        }

        // Check Cache Folder Write Access
        $out .= " > Checking System Cache...<br />";
        $out .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- System Cache Path Writable (system/cache): ";
        $path = SYSTEM_PATH . DS .'cache'. DS;
        if (!Directory::IsWritable( $path ))
        {
            $out .= __FAIL;
            $errors = true;
        }
        else
        {
            $out .= __PASS;
        }

        // Check Config File Write Access
        $out .= " > Checking System Config...<br />";
        $out .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- System Config Path Writable (system/config): ";
        $path = SYSTEM_PATH . DS .'config'. DS;
        if (!Directory::IsWritable( $path ))
        {
            $out .= __FAIL;
            $errors = true;
        }
        else
        {
            $out .= __PASS;
        }

        $out .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Config File Writable (system/config/config.php): ";
        if (!File::IsWritable( SYSTEM_PATH . DS .'config'. DS .'config.php' ))
        {
            $out .= __FAIL;
            $errors = true;
        }
        else
        {
            $out .= __PASS;
        }

        // Check Log File Write Access
        $out .= " > Checking System Log Files...<br />";
        $out .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Stats Debug Log File Writable (system/logs/stats_debug.log): ";
        $log = SYSTEM_PATH . DS . 'logs' . DS . 'stats_debug.log';
        if (!File::IsWritable( $log ))
        {
            $out .= __WARN;
            $warns = true;
        }
        else
        {
            $out .= __PASS;
        }

        $out .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- ASP Debug Log File Writable (system/logs/asp_debug.log): ";
        $log = SYSTEM_PATH . DS . 'logs' . DS . 'asp_debug.log';
        if (!File::IsWritable( $log ))
        {
            $out .= __WARN;
            $warns = true;
        }
        else
        {
            $out .= __PASS;
        }

        $out .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- PHP Error Log File Writable (system/logs/php_errors.log): ";
        $log = SYSTEM_PATH . DS . 'logs' . DS . 'php_errors.log';
        if (!File::IsWritable( $log ))
        {
            $out .= __WARN;
            $warns = true;
        }
        else
        {
            $out .= __PASS;
        }

        // SNAPSHOTS
        $out .= " > Checking SNAPSHOT Storage Paths...<br />";
        $out .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- SNAPSHOT Temporary Path Writable (system/snapshots/unprocessed): ";
        $path = SYSTEM_PATH . DS .'snapshots'. DS .'unprocessed'. DS;
        if (!Directory::IsWritable( $path ))
        {
            $out .= __FAIL;
            $errors = true;
        }
        else
        {
            $out .= __PASS;
        }

        // Snapshot Archive Path
        $out .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- SNAPSHOT Processed Path Writable (system/snapshots/processed): ";
        $path = SYSTEM_PATH . DS .'snapshots'. DS .'processed'. DS;
        if (!Directory::IsWritable( $path ))
        {
            $out .= __FAIL;
            $errors = true;
        }
        else
        {
            $out .= __PASS;
        }

        // Snapshot Fail Path
        $out .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- SNAPSHOT Fail Path Writable (system/snapshots/failed): ";
        $path = SYSTEM_PATH . DS .'snapshots'. DS .'failed'. DS;
        if (!Directory::IsWritable( $path ))
        {
            $out .= __FAIL;
            $errors = true;
        }
        else
        {
            $out .= __PASS;
        }

        // Snapshot Un-Authorized store Path
        $out .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- SNAPSHOT Un-Authorized Path Writable (system/snapshots/unauthorized): ";
        $path = SYSTEM_PATH . DS .'snapshots'. DS .'unauthorized'. DS;
        if (!Directory::IsWritable( $path ))
        {
            $out .= __FAIL;
            $errors = true;
        }
        else
        {
            $out .= __PASS;
        }

        // Check Admin Backup Write Access
        $out .= " > Checking Database Backup Storage Path...<br />";
        $path = Path::Combine(SYSTEM_PATH, 'backups');
        $out .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Backup Path Writable ({$path}): ";
        if (!Directory::IsWritable( $path ))
        {
            $out .= __FAIL;
            $errors = true;
        }
        else
        {
            $out .= __PASS;
        }

        // Finish :)
        $out .= '</p>';

        // Determine if our save is a success
        echo json_encode(
            [
                'success' => ($errors == false),
                'warnings' => $warns,
                'html' => $out
            ]
        );
    }
}
