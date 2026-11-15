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
use System\Database\MySqlConnectionStringBuilder;
use System\Database\DbConnection;
use System\Config;
use System\Database;
use System\Database\SqlFileParser;
use System\LogWriter;
use System\View;

/**
 * Install Module Controller
 *
 * @package Modules
 */
class Install extends Controller
{
    /**
     * @protocol    GET
     * @request     /ASP/install
     * @output      html
     */
    public function getIndex(): void
    {
        // Convert admin hosts array to a string
        $items = implode("\n", Config::Get('admin_hosts'));

        // Create view
        $view = new View('index', 'install');
        $view->set('admin_user', Config::Get('admin_user'));
        $view->set('admin_pass', Config::Get('admin_pass'));
        $view->set('ip_list', trim(htmlentities($items, ENT_HTML5, "UTF-8")));

        $view->set('db_host', Config::Get('db_host'));
        $view->set('db_port', Config::Get('db_port'));
        $view->set('db_user', Config::Get('db_user'));
        $view->set('db_pass', Config::Get('db_pass'));
        $view->set('db_name', Config::Get('db_name'));

        // Attach stylesheets for the wizard form
        $view->attachStylesheet("/ASP/frontend/js/wizard/wizard.css");
        $view->attachStylesheet("/ASP/frontend/css/icons/icol16.css");
        $view->attachStylesheet("/ASP/frontend/css/icons/icol32.css");

        // Attach scripts for the wizard form
        $view->attachScript("/ASP/frontend/js/wizard/wizard.js");
        $view->attachScript("/ASP/frontend/js/jquery.form.js");
        $view->attachScript("/ASP/frontend/js/validate/jquery.validate-min.js");
        $view->attachScript("/ASP/frontend/js/autosize/jquery.autosize-min.js");
        $view->attachScript("/ASP/frontend/modules/install/js/wizard.js");

        // Render view
        $view->render();
    }

    /**
     * @protocol    POST
     * @request     /ASP/install
     * @output      json
     */
    public function postIndex(): void
    {
        // Form post?
        if (!isset($_POST['process']) || $_POST['process'] != 'config')
        {
            $this->getIndex();
            die;
        }

        foreach ($_POST as $item => $val)
        {
            $key = explode('__', $item);
            if (count($key) > 1 && $key[0] === 'cfg')
            {
                // Fix array
                if ($key[1] === 'admin_hosts') {
                    $val = array_map('trim', explode("\n", trim((string) $val)));
                }

                Config::Set($key[1], $val);
            }
        }

        // Save changes
        Config::Save();
        $connection = null;

        // Try to connect to the database with new settings
        try
        {
            // Create connection using the MySQL connection builder
            $builder = new MySqlConnectionStringBuilder();
            $builder->host = Config::Get('db_host');
            $builder->port = Config::Get('db_port');
            $builder->user = Config::Get('db_user');
            $builder->password = Config::Get('db_pass');
            $builder->database = Config::Get('db_name');
            $connection = new DbConnection($builder);
        }
        catch (Exception $e)
        {
            $message = 'Failed to establish connection to (' . Config::Get('db_host') . '): ' . $e->getMessage();
            $this->sendJsonResponse(false, $message, ['tablesExist' => false]);
            die;
        }

        // Fetch tables version
        try
        {
            $col = $connection->quoteIdentifier('version');
            $stmt = $connection->query("SELECT {$col} FROM _version;");
            $versions = $stmt->fetchAll();
            if (!empty($versions))
            {
                $this->sendJsonResponse(true, '', ['tablesExist' => true]);
                die;
            }
        }
        catch (Exception)
        {
            //$this->sendJsonResponse(true, '', ['tablesExist' => false]);
            //die;
        }

        // Successful connection
        $this->sendJsonResponse(true, '', ['tablesExist' => false]);
        die;
    }

    /**
     * @protocol    POST
     * @request     /ASP/install/tables
     * @output      json
     */
    public function postTables(): void
    {
        // Form post?
        if (!isset($_POST['process']) || $_POST['process'] != 'installdb')
        {
            $this->getIndex();
            die;
        }

        // Try to connect to the database with new settings
        try
        {
            // Create connection using the MySQL connection builder
            $builder = new MySqlConnectionStringBuilder();
            $builder->host = Config::Get('db_host');
            $builder->port = Config::Get('db_port');
            $builder->user = Config::Get('db_user');
            $builder->password = Config::Get('db_pass');
            $builder->database = Config::Get('db_name');
            $pdo = new DbConnection($builder);
        }
        catch (Exception $e)
        {
            $message = 'Failed to establish connection to (' . Config::Get('db_host') . '): ' . $e->getMessage();
            $this->sendJsonResponse(false, $message);
            die;
        }

        // Fetch tables version
        try
        {
            // MySQL does not allow transactions on DDL statements
            $pdo->beginTransaction();

            // Create parser
            $parser = new SqlFileParser(SYSTEM_PATH . DS . 'sql' . DS . 'schema.sql');
            $queries = $parser->getStatements();
            $current = '';

            try
            {
                // Read file contents
                foreach ($queries as $query)
                {
                    $current = $query;
                    $pdo->exec($query);
                }
            }
            catch (Exception $e)
            {
                $logWriter = new LogWriter(SYSTEM_PATH . DS . 'logs' . DS . 'php_errors.log');
                $logWriter->logDebug('Query Failed: ' . $current);

                // Send Error Results
                $this->sendJsonResponse(false, 'Failed to install database tables! ' . $e->getMessage());
                die;
            }

            // Commit changes
            $pdo->commit();

            // --------------------------------------------------
            // Insert Default Data
            // --------------------------------------------------
            $pdo->beginTransaction();

            // Create parser
            $parser = new SqlFileParser(SYSTEM_PATH . DS . 'sql' . DS . 'data.sql');
            $queries = $parser->getStatements();

            try
            {
                // Read file contents
                foreach ($queries as $query)
                {
                    $current = $query;
                    $pdo->exec($query);
                }
            }
            catch (Exception $e)
            {
                $logWriter = new LogWriter(SYSTEM_PATH . DS . 'logs' . DS . 'php_errors.log');
                $logWriter->logDebug('Query Failed: ' . $current);

                // Send Error Results
                $this->sendJsonResponse(false, 'Failed to install database default data! ' . $e->getMessage());
                die;
            }

            // Commit changes
            $pdo->commit();

            // Success
            $this->sendJsonResponse(true, 'Tables Created Successfully');
            die;
        }
        catch (Exception $e)
        {
            // Undo changes
            $pdo->rollBack();

            $logWriter = new LogWriter(SYSTEM_PATH . DS . 'logs' . DS . 'php_errors.log');
            $logWriter->logDebug('Failed to create database tables: ' . $e);

            // Send Error Results
            $this->sendJsonResponse(false, 'Failed to install database tables! ' . $e->getMessage());
            die;
        }
    }
}
