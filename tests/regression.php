<?php
require __DIR__ . '/bootstrap.php';

use System\AspResponse;
use System\Config;
use System\Request;
use System\Security;
use System\Unlocks;

// Buffer test output so session cookie and regeneration tests run before headers.
ob_start();
$checks = 0;
function check($condition, string $message): void
{
    global $checks;
    if (!$condition) {
        throw new RuntimeException($message);
    }
    $checks++;
}
function rejects(callable $action, string $message): void
{
    try {
        $action();
    } catch (DomainException $e) {
        check($e->getMessage() === $message, 'Unexpected rejection: ' . $e->getMessage());
        return;
    }
    throw new RuntimeException('Expected rejection: ' . $message);
}

$_SERVER['HTTP_CLIENT_IP'] = '127.0.0.1';
$_SERVER['HTTP_X_FORWARDED_FOR'] = '127.0.0.1';
check(Request::ClientIp() === '203.0.113.1', 'Spoofed client IP header accepted');
check(!Request::IsLocalHost(), 'Remote peer treated as local');
$ipCache = new ReflectionProperty(Request::class, 'clientIp');
foreach (['garbage', "127.0.0.1' OR 1=1", ['127.0.0.1']] as $address) {
    $ipCache->setValue(null, null);
    $_SERVER['REMOTE_ADDR'] = $address;
    check(Request::ClientIp() === '0.0.0.0', 'Invalid peer IP accepted');
}
$ipCache->setValue(null, null);
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
check(Request::IsLocalHost(), 'Loopback not recognized');

check(!Security::Login([], []), 'Array credentials accepted');
$token = Security::CsrfToken();
check(strlen($token) === 64 && Security::IsValidCsrfToken($token), 'CSRF token rejected');
check(!Security::IsValidCsrfToken(null) && !Security::IsValidCsrfToken([])
    && !Security::IsValidCsrfToken(str_repeat('0', 64)), 'Invalid CSRF token accepted');
$oldId = session_id();
check(Security::Login('admin', 'test-admin-password'), 'Valid login failed');
check(session_id() !== $oldId, 'Login did not rotate session ID');
check(!Security::IsValidCsrfToken($token), 'Login did not rotate CSRF token');
check(Security::IsValidSession(), 'Authenticated session invalid');
Security::Logout();
check(!Security::IsValidSession() && session_status() === PHP_SESSION_ACTIVE, 'Logout did not clear auth');
check(Security::IsValidCsrfToken(Security::CsrfToken()), 'Anonymous session cannot log in again');
$configFile = SYSTEM_PATH . '/config/config.php';
$saved = file_get_contents($configFile);
try {
    Config::Set('admin_pass', '000123');
    Config::Set('admin_hosts', ["x'); throw new Exception('injected'); //", "a\\b"]);
    Config::Save();
    $loaded = (static function ($path) { include $path; return get_defined_vars(); })($configFile);
    check($loaded['admin_pass'] === '000123', 'Numeric password was changed');
    check($loaded['admin_hosts'] === Config::Get('admin_hosts'), 'Config array did not round trip');
} finally {
    file_put_contents($configFile, $saved);
    Config::Set('admin_pass', 'test-admin-password');
    Config::Set('admin_hosts', []);
}
foreach (['admin', ''] as $password) {
    Config::Set('admin_pass', $password);
    check(!Security::Login('admin', $password), 'Default/empty password accepted');
}
Config::Set('admin_pass', 'test-admin-password');

$response = new AspResponse();
$response->writeLine('OK');
check($response->getString() === "O\nOK\n$\t3\t$", 'GameSpy OK framing changed');
$response = new AspResponse();
$response->writeHeaderLine('id', 'state');
$response->writeDataLine(11, 's');
check($response->getString() === "O\nH\tid\tstate\nD\t11\ts\n$\t13\t$", 'GameSpy unlock framing changed');
foreach ([0 => 0, 1 => 0, 2 => 1, 3 => 2, 6 => 5, 7 => 6, 8 => 6, 9 => 7, 21 => 7] as $rank => $count) {
    check(Unlocks::countByRank($rank) === $count, 'Incorrect rank unlock budget');
}

$pdo = connection();
$pdo->exec("INSERT INTO player (id,name,password,rank_id,score) VALUES
    (1001,'test-player','unused',2,100), (1002,'other-player','unused',3,50),
    (1003,'race-player','unused',2,0)");
Config::Set('game_unlocks_bonus', 0);
rejects(fn() => Unlocks::select(9999, 11, $pdo), 'Player Not Found!');
rejects(fn() => Unlocks::select(1001, 65535, $pdo), 'Invalid Unlock!');
$parent = $pdo->query('SELECT parent_id, child_id FROM unlock_requirement LIMIT 1')->fetch();
rejects(fn() => Unlocks::select(1001, (int)$parent['child_id'], $pdo), 'Required Unlock Not Selected!');
Unlocks::select(1001, 11, $pdo);
Unlocks::select(1001, 11, $pdo);
check((int)$pdo->query('SELECT COUNT(*) FROM player_unlock WHERE player_id=1001')->fetchColumn() === 1,
    'Repeated selection spent another point');
rejects(fn() => Unlocks::select(1001, 22, $pdo), 'No Unlocks Available!');
check(!$pdo->inTransaction(), 'Failed selection left an open transaction');

// Multiple children can share a parent; multiple parents can be required.
$pdo->exec('INSERT INTO unlock_requirement (parent_id, child_id) VALUES (11,22), (33,22)');
Unlocks::select(1002, 11, $pdo);
rejects(fn() => Unlocks::select(1002, 22, $pdo), 'Required Unlock Not Selected!');
Unlocks::select(1002, 33, $pdo);
$pdo->exec('UPDATE player SET rank_id=4 WHERE id=1002');
Unlocks::select(1002, 22, $pdo);
$pdo->exec('DELETE FROM unlock_requirement WHERE child_id=22');

// Create relational fixtures for repeated levels of a kit badge.
$pdo->exec("INSERT INTO stats_provider (id,auth_id,auth_token,authorized) VALUES (60000,123,'test-provider',1)");
$pdo->exec("INSERT INTO stats_provider_auth_ip VALUES (60000,'127.0.0.1')");
$pdo->exec("INSERT INTO server (id,provider_id,ip,queryport) VALUES (60000,60000,'127.0.0.1',29901)");
$pdo->exec('INSERT INTO `round` (id,map_id,server_id,mod_id,gamemode_id,team1_army_id,team2_army_id,
    time_start,time_end,time_imported,winner,tickets1,tickets2)
    SELECT 1,(SELECT MIN(id) FROM map),60000,(SELECT MIN(id) FROM game_mod),(SELECT MIN(id) FROM game_mode),0,1,
    100,200,200,0,0,0');
$pdo->exec('INSERT INTO player_award (player_id,award_id,level,round_id) VALUES (1001,1031119,2,1),(1001,1031119,3,1)');
Config::Set('game_unlocks_bonus', 2);
check(Unlocks::countByBadges(1001, 2, $pdo) === 1, 'Badge levels counted twice');
Config::Set('game_unlocks', 1);
Unlocks::select(1001, 22, $pdo);
check((int)$pdo->query('SELECT COUNT(*) FROM player_unlock WHERE player_id=1001')->fetchColumn() === 1,
    'All-unlocks mode spent points');
Config::Set('game_unlocks', 2);
rejects(fn() => Unlocks::select(1001, 22, $pdo), 'Unlock Selection Disabled!');
Config::Set('game_unlocks', 0);

// Parsing valid-looking, unauthenticated data must not create a new map.
$snapshot = ['serverIp' => '127.0.0.1', 'version' => '3.1', 'authId' => 123,
    'authToken' => 'invalid', 'serverName' => 'test', 'gamePort' => 16567,
    'queryPort' => 29901, 'mapName' => 'unauthorized_map', 'mapId' => 65534,
    'mapStart' => 100, 'mapEnd' => 200, 'gameMode' => 0, 'mod' => 'bf2',
    'pc' => 0, 'winner' => 0, 'ra1' => 0, 'rs1' => 0, 'ra2' => 1, 'rs2' => 0, 'players' => []];
$parsed = new \System\Snapshot(new \System\Collections\Dictionary(false, $snapshot));
try {
    $parsed->checkAuthorization();
    throw new RuntimeException('Invalid snapshot token accepted');
} catch (SecurityException $e) {
    check($e->getCode() === 1, 'Wrong snapshot rejection');
}
check((int)$pdo->query('SELECT COUNT(*) FROM map WHERE id=65534')->fetchColumn() === 0,
    'Unauthenticated snapshot created a map');

Config::Set('stats_players_min', 0);
$snapshot['authToken'] = 'test-provider';
$snapshot['mapEnd'] = -1;
$parsed = new \System\Snapshot(new \System\Collections\Dictionary(false, $snapshot));
try {
    $parsed->processData();
    throw new RuntimeException('Invalid round data was committed');
} catch (PDOException $e) {
    check(!$pdo->inTransaction(), 'Invalid snapshot left an open transaction');
}
check((int)$pdo->query('SELECT COUNT(*) FROM map WHERE id=65534')->fetchColumn() === 0,
    'Failed snapshot left a partially created map');
$snapshot['mapEnd'] = 200;
$parsed = new \System\Snapshot(new \System\Collections\Dictionary(false, $snapshot));
$parsed->processData();
check($parsed->isProcessed(), 'Authorized snapshot was not processed');
check((int)$pdo->query('SELECT COUNT(*) FROM map WHERE id=65534')->fetchColumn() === 1,
    'Authorized snapshot did not create its map');

// A deferred foreign key constraint makes SQLite commit throw: ensure the
// original PDOException survives DbConnection::commit(), not "Undefined constant e".
if (in_array('sqlite', PDO::getAvailableDrivers(), true)) {
    $builder = new class extends \System\Database\DbConnectionStringBuilder {
        public function getConnectionString() { return 'sqlite::memory:'; }
    };
    $sqlite = new \System\Database\DbConnection($builder);
    $sqlite->exec('PRAGMA foreign_keys=ON; CREATE TABLE parent(id INTEGER PRIMARY KEY);
        CREATE TABLE child(id INTEGER REFERENCES parent(id) DEFERRABLE INITIALLY DEFERRED);');
    $sqlite->beginTransaction();
    $sqlite->exec('INSERT INTO child VALUES (1)');
    try {
        $sqlite->commit();
        throw new RuntimeException('Invalid commit succeeded');
    } catch (PDOException $e) {
        check(str_contains($e->getMessage(), 'FOREIGN KEY'), 'Original transaction error lost');
    }
    $sqlite->rollBack();
}

echo "Backend regression checks passed: {$checks}\n";
ob_end_flush();
