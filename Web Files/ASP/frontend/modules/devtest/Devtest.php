<?php
/**
 * BF2Statistics ASP Framework
 *
 * Author:       Steven Wilson
 * Copyright:    Copyright (c) 2006-2017, BF2statistics.com
 * License:      GNU GPL v3
 *
 */
use System\Controller;
use System\Keygen\Keygen;
use System\Database;
use System\Debug;
use System\BF2\RankCalculator;

/**
 * A Dev testing module
 */
class Devtest extends Controller
{
    /**
     * @var RankCalculator
     */
    protected $model;

    public function index(): void
    {
        $data = [
            'AuthID' => Keygen::numeric(6)->prefix('1')->generate(),
            'AuthToken' => Keygen::alphanum(16)->generate()
        ];
        echo '<pre>' . var_export($data, true) . '</pre>';
    }

    public function fixMapNames(): void
    {
        // Require a database connection
        $this->requireDatabase(true);

        // Fetch database connection
        $pdo = Database::GetConnection('stats');

        // Replace all underscores with a space
        $pdo->exec("UPDATE `map` SET `displayname` = REPLACE(`displayname`, '_', ' ')");

        // Now capitalize each letter of each word in the display name
        $select = $pdo->prepare("SELECT * FROM `map`");
        $select->setFetchMode(PDO::FETCH_ASSOC);
        $select->execute();

        // Use a prepared query
        $update = $pdo->prepare("UPDATE `map` SET `displayname`=:column WHERE `id`=:id");
        while ($data = $select->fetch())
        {
            $id = (int)$data['id'];
            $column = $data['displayname'];
            $column = ucwords(strtolower((string) $column)); // Capitalize each word

            $update->bindParam(':id', $id, PDO::PARAM_INT);
            $update->bindParam(':column', $column, PDO::PARAM_STR);
            $update->execute();
        }

        echo 'Completed... Redirecting';
        header( "refresh:3;url=/ASP/" );
    }

    public function ranks(): void
    {
        $this->model = new RankCalculator();
        Debug::Dump($this->model->getNextRanks(2900126, 22));
    }

    public function dropConstraint(): void
    {
        // Require a database connection
        $this->requireDatabase(true);

        // Fetch database connection
        $pdo = Database::GetConnection('stats');

        var_dump($pdo->exec("ALTER TABLE `server` DROP INDEX `ip-port-unq`;"));
    }

    public function createIndex(): void
    {
        // Require a database connection
        $this->requireDatabase(true);

        // Fetch database connection
        $pdo = Database::GetConnection('stats');

        var_dump($pdo->exec("CREATE INDEX `idx_round_processed` ON round(`map_id`, `server_id`, `time_end`, `time_start`)"));
    }

    public function phpInfo(): void
    {
        echo phpinfo();
    }

    /**
     *
     */
    public function php(): void
    {
        //$pdo = System\Database::GetConnection('stats');
        //$pdo->from('player_army')->select('army_id', 'player_id')->where('army_id')->between(1, 6);

        $indicesServer = [
            'PHP_SELF',
            'argv',
            'argc',
            'GATEWAY_INTERFACE',
            'SERVER_ADDR',
            'SERVER_NAME',
            'SERVER_SOFTWARE',
            'SERVER_PROTOCOL',
            'REQUEST_METHOD',
            'REQUEST_TIME',
            'REQUEST_TIME_FLOAT',
            'QUERY_STRING',
            'DOCUMENT_ROOT',
            'HTTP_ACCEPT',
            'HTTP_ACCEPT_CHARSET',
            'HTTP_ACCEPT_ENCODING',
            'HTTP_ACCEPT_LANGUAGE',
            'HTTP_CONNECTION',
            'HTTP_HOST',
            'HTTP_REFERER',
            'HTTP_USER_AGENT',
            'HTTPS',
            'REMOTE_ADDR',
            'REMOTE_HOST',
            'REMOTE_PORT',
            'REMOTE_USER',
            'REDIRECT_REMOTE_USER',
            'SCRIPT_FILENAME',
            'SERVER_ADMIN',
            'SERVER_PORT',
            'SERVER_SIGNATURE',
            'PATH_TRANSLATED',
            'SCRIPT_NAME',
            'REQUEST_URI',
            'PHP_AUTH_DIGEST',
            'PHP_AUTH_USER',
            'PHP_AUTH_PW',
            'AUTH_TYPE',
            'PATH_INFO',
            'ORIG_PATH_INFO'
        ];

        echo '<table cellpadding="10">' ;
        foreach ($indicesServer as $arg) {
            if (isset($_SERVER[$arg])) {
                echo '<tr><td>'.$arg.'</td><td>' . $_SERVER[$arg] . '</td></tr>' ;
            }
            else {
                echo '<tr><td>'.$arg.'</td><td>-</td></tr>' ;
            }
        }
        echo '</table>' ;

        //throw new Exception('test');
    }
}
