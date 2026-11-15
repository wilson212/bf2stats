<?php
if (!getenv('BF2STATS_TEST_ASP')) {
    exit("Run tests/container-run.sh in the disposable test container.\n");
}
define('ROOT', getenv('BF2STATS_TEST_ASP'));
define('DS', DIRECTORY_SEPARATOR);
define('SYSTEM_PATH', ROOT . '/system');
define('BF2_ADMIN', true);
$_SERVER += ['HTTP_HOST' => 'localhost', 'PHP_SELF' => '/ASP/index.php',
    'REQUEST_METHOD' => 'GET', 'REMOTE_ADDR' => '203.0.113.1', 'SERVER_ADDR' => '127.0.0.1'];
require ROOT . '/system/framework/Autoloader.php';
\System\Autoloader::Register();

function connection(): \System\Database\DbConnection
{
    $builder = new \System\Database\MySqlConnectionStringBuilder();
    foreach (['host', 'port', 'user', 'password', 'database'] as $property) {
        $key = ['password' => 'pass', 'database' => 'name'][$property] ?? $property;
        $builder->$property = \System\Config::Get('db_' . $key);
    }
    return \System\Database::CreateConnection('stats', $builder);
}
