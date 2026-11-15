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
use System\Database\DbConnection;
use System\BF2\Player;
use System\Keygen\Keygen;
use System\Net\IPAddress;
use System\TimeHelper;

/**
 * Server Model
 *
 * @package Models
 * @subpackage Servers
 */
class ProviderModel
{
    /**
     * @var DbConnection The stats database connection
     */
    public $pdo;

    /**
     * @var int Time stamp of one week ago
     */
    private static int $OneWeekAgo = 0;

    /**
     * @var int Time stamp of two weeks ago
     */
    private static int $TwoWeekAgo = 0;

    /**
     * ServerModel constructor.
     */
    public function __construct()
    {
        // Fetch database connection
        $this->pdo = Database::GetConnection('stats');

        // Set static vars
        if (self::$OneWeekAgo == 0)
        {
            self::$OneWeekAgo = time() - (86400 * 7);
            self::$TwoWeekAgo = time() - (86400 * 14);
        }
    }

    /**
     * Fetches a list of all the servers from the server table, including the
     * number of snapshots they have submitted.
     *
     * @return array
     */
    public function fetchProviders()
    {
        $providers = $this->pdo->query("SELECT * FROM stats_provider ORDER BY id ASC")->fetchAll();
        if (empty($providers)) {
            return [];
        }
        // Select counts of snapshots received by each provider
        $counter = count($providers);

        // Select counts of snapshots received by each provider
        for ($i = 0; $i < $counter; $i++)
        {
            $key = (int)$providers[$i]['id'];
            $providers[$i]['snapshots'] = $this->getNumGamesByProviderId($key);
        }

        return $providers;
    }

    /**
     * Fetches a provider record from the database by id
     *
     * @param int $id The provider id
     *
     * @return bool|array false if the provider id does not exist, otherwise
     *  the server row is returned
     */
    public function fetchProviderById($id)
    {
        $id = (int)$id;
        return $this->pdo->query("SELECT * FROM stats_provider WHERE id={$id}")->fetch();
    }

    /**
     * Adds a new stats provider record in the server table
     *
     * @param string $name The provider name, no longer than 100 characters.
     * @param bool $authorized true if the server is authorized to post snapshots,
     *  false otherwise
     *
     * @param string $authID [Reference Variable]
     * @param string $authToken [Reference Variable]
     *
     * @return int the provider id, or 0 on error
     *
     * @throws ArgumentException
     */
    public function addProvider($name, $authorized, &$authID, &$authToken): int
    {
        // Check length of server name
        $name = preg_replace("/[^". Player::NAME_REGEX ."]/", '', trim($name));
        if (empty($name)) {
            throw new ArgumentException('Empty or illegal provider name passed', 'name');
        }

        // Name Length check
        if (strlen($name) > 100) {
            throw new ArgumentException('Provider name cannot be longer than 100 characters!', 'name');
        }

        // Generate a AuthID and AuthToken
        $authID = $this->generateUniqueAuthId();
        $authToken = $this->generateAuthToken();

        // Prepare statement
        $this->pdo->insert('stats_provider', [
            'auth_id' => $authID,
            'auth_token' => $authToken,
            'name' => $name,
            'authorized' => $authorized ? 1 : 0
        ]);

        // Authorize the input address!
        //$this->pdo->insert('server_auth_ip', ['id' => $providerId, 'address' => $address->toString()]);

        // Return server ID
        return (int)$this->pdo->lastInsertId('id');
    }

    /**
     * Updates a stats provider record in the server table
     *
     * @param int $id The server id
     * @param string $name The provider name, no longer than 100 characters.
     *  false otherwise
     *
     * @return bool true on success, false otherwise
     *
     * @throws ArgumentException
     */
    public function updateProviderById($id, $name)
    {

        // Check length of server name
        $name = preg_replace("/[^". Player::NAME_REGEX ."]/", '', trim($name));
        if (empty($name)) {
            throw new ArgumentException('Empty or illegal provider name passed', 'name');
        }

        // Name Length check
        if (strlen($name) > 100) {
            throw new ArgumentException('Provider name cannot be longer than 100 characters!', 'name');
        }

        // Prepare statement
        return $this->pdo->update('stats_provider', ['name' => $name,], ['id' => (int)$id]);
    }

    /**
     * Gets a list of authorized server IP's by server ID
     *
     * @param int $id The server ID
     */
    public function fetchAuthorizedProviderIpsById($id): array
    {
        $list = [];
        $stmt = $this->pdo->query("SELECT address FROM stats_provider_auth_ip WHERE provider_id={$id}");
        while ($row = $stmt->fetch())
        {
            $list[] = $row['address'];
        }
        return $list;
    }

    /**
     * Syncs a list of authorized IP Addresses for a stats provider in the database.
     *
     * @param int $id The stats provider id
     * @param array $addresses The full list of authorized addresses
     *
     * @throws Exception if an supplied address is not a valid IP address
     */
    public function syncAuthorizedProviderIpsById($id, array $addresses): void
    {
        // Sanitize id
        $id = (int)$id;

        // Begin transaction
        $this->pdo->beginTransaction();

        // Delete all addresses
        $this->pdo->delete('stats_provider_auth_ip', ['provider_id' => $id]);

        // Add new addresses
        foreach ($addresses as $item)
        {
            // Parse IP here. If invalid, an exception will be thrown
            if (!IPAddress::TryParse($item, $addy))
            {
                $this->pdo->rollBack();
                throw new Exception(sprintf('Invalid IP Address passed (%s)', $item));
            }

            // Add item
            $this->pdo->insert('stats_provider_auth_ip', ['provider_id' => $id, 'address' => $item]);
        }

        // Commit transaction
        $this->pdo->commit();
    }

    /**
     * Generates a complete, unique AuthID
     */
    public function generateUniqueAuthId(): int
    {
        // Keep generating keys until we have a unique one
        do
        {
            $authID = (int)Keygen::numeric(5)->prefix('1')->generate(true);
            $query = "SELECT COUNT(id) FROM `stats_provider` WHERE auth_id=" . $authID;
            $result = (int)$this->pdo->query($query)->fetchColumn(0);
        }
        while ($result > 0);

        return $authID;
    }

    /**
     * Generate an auth token
     *
     * @return string
     */
    public function generateAuthToken()
    {
        return Keygen::alphanum(16)->generate();
    }

    /**
     * Generates a new AuthID for the specified stats provider ID
     *
     * @param int $id the provider id
     *
     * @return bool|int the new Auth ID on success, false otherwise
     */
    public function generateNewAuthIdForProvider($id)
    {
        $id = (int)$id;
        $newId = $this->generateUniqueAuthId();

        $result = $this->pdo->update('stats_provider', ['auth_id' => $newId], ['id' => $id]);
        return ($result) ? $newId : false;
    }

    /**
     * Generates a new AuthToken for the specified stats provider ID
     *
     * @param int $id the provider id
     *
     * @return bool|string the new Auth Token on success, false otherwise
     */
    public function generateNewAuthTokenForProvider($id)
    {
        $id = (int)$id;
        $newId = $this->generateAuthToken();

        $result = $this->pdo->update('stats_provider', ['auth_token' => $newId], ['id' => $id]);
        return ($result) ? $newId : false;
    }

    /**
     * Returns the number of games this provider has submitted
     *
     * @param int $id The stats provider ID
     */
    public function getNumGamesByProviderId($id): int
    {
        // Sanitize
        $id = (int)$id;

        // First, grab server ID's for this provider!
        $list = [];
        $query = "SELECT id FROM server WHERE provider_id=". $id;
        $stmt = $this->pdo->query($query);
        while ($row = $stmt->fetch())
        {
            $list[] = (int)$row['id'];
        }

        // If no servers, then no games right?
        if ($list === []) {
            return 0;
        }

        // Fetch round counts
        $strList = implode(',', $list);
        $query = <<<SQL
SELECT COUNT(round.id) FROM round
  LEFT JOIN `server` ON `server`.id = round.server_id
WHERE `server`.`id` IN ({$strList})
SQL;

        return (int)$this->pdo->query($query)->fetchColumn(0);
    }

    /**
     * Deletes a list of providers by id
     *
     * @param int[] $ids A list of provider ids to perform the action on
     *
     * @throws Exception thrown if a provider has a game processed,
     *          or there is an error in the SQL statement
     */
    public function deleteProviders($ids): void
    {
        $count = count($ids);

        // Prepared statement!
        try
        {
            // Transaction if more than 2 servers
            if ($count > 2) {
                $this->pdo->beginTransaction();
            }

            // Prepare statement
            $stmt = $this->pdo->prepare("DELETE FROM server WHERE provider_id=:id");
            $stmt2 = $this->pdo->prepare("DELETE FROM stats_provider_auth_ip WHERE provider_id=:id");
            $stmt3 = $this->pdo->prepare("DELETE FROM stats_provider WHERE id=:id");
            foreach ($ids as $providerId)
            {
                // Ignore the all!
                if ($providerId == 'all') {
                    continue;
                }

                // Ensure no games have been played!
                $games = $this->getNumGamesByProviderId($providerId);
                if ($games > 0) {
                    throw new Exception("Cannot delete Provider (ID: {$providerId}) because it has saved stats in the database");
                }

                // Bind value and run query
                $stmt2->bindValue(':id', (int)$providerId, PDO::PARAM_INT);
                $stmt2->execute();

                // Bind value and run query
                $stmt->bindValue(':id', (int)$providerId, PDO::PARAM_INT);
                $stmt->execute();

                // Bind value and run query
                $stmt3->bindValue(':id', (int)$providerId, PDO::PARAM_INT);
                $stmt3->execute();
            }

            // Commit?
            if ($count > 2) {
                $this->pdo->commit();
            }
        }
        catch (Exception $e)
        {
            // Rollback?
            if ($count > 2) {
                $this->pdo->rollBack();
            }

            throw $e;
        }
    }

    /**
     * Sets the authorization level on a list of providers by id
     *
     * @param bool $mode true to authorize the specified stats provider, otherwise false
     * @param int[] $ids A list of provider ids to perform the action on
     *
     * @throws Exception thrown if there is an error in the SQL statement
     */
    public function authorizeProviders($mode, $ids): void
    {
        $count = count($ids);
        $mode = ($mode) ? 1 : 0;

        // Prepared statement!
        try
        {
            // Transaction if more than 2 servers
            if ($count > 2) {
                $this->pdo->beginTransaction();
            }

            // Prepare statement
            $stmt = $this->pdo->prepare("UPDATE stats_provider SET authorized=$mode WHERE id=:id");
            foreach ($ids as $providerId)
            {
                // Ignore the all!
                if ($providerId == 'all') {
                    continue;
                }

                // Bind value and run query
                $stmt->bindValue(':id', (int)$providerId, PDO::PARAM_INT);
                $stmt->execute();
            }

            // Commit?
            if ($count > 2) {
                $this->pdo->commit();
            }
        }
        catch (Exception $e)
        {
            // Rollback?
            if ($count > 2) {
                $this->pdo->rollBack();
            }

            throw $e;
        }
    }

    /**
     * Sets the plasma indicator on a list of providers by id
     *
     * @param bool $mode true to plasma the specified stats provider, otherwise false
     * @param int[] $ids A list of stat provider ids to perform the action on
     *
     * @throws Exception thrown if there is an error in the SQL statement
     */
    public function plasmaProviders($mode, $ids): void
    {
        $count = count($ids);
        $mode = ($mode) ? 1 : 0;

        // Prepared statement!
        try
        {
            // Transaction if more than 2 servers
            if ($count > 2) {
                $this->pdo->beginTransaction();
            }

            // Prepare statement
            $stmt = $this->pdo->prepare("UPDATE stats_provider SET plasma=$mode WHERE id=:id");
            foreach ($ids as $providerId)
            {
                // Ignore the all!
                if ($providerId == 'all') {
                    continue;
                }

                // Bind value and run query
                $stmt->bindValue(':id', (int)$providerId, PDO::PARAM_INT);
                $stmt->execute();
            }

            // Commit?
            if ($count > 2) {
                $this->pdo->commit();
            }
        }
        catch (Exception $e)
        {
            // Rollback?
            if ($count > 2) {
                $this->pdo->rollBack();
            }

            throw $e;
        }
    }

    /**
     * Fetches the chart data for the Home page
     *
     * @param int $providerId
     */
    public function getServerChartData($providerId): array
    {
        // sanitize
        $providerId = (int)$providerId;

        // prepare output
        $output = [
            'week' => ['y' => ['server' => [], 'total' => []], 'x' => ['server' => [], 'total' => []]],
            'month' => ['y' => ['server' => [], 'total' => []], 'x' => ['server' => [], 'total' => []]],
            'year' => ['y' => ['server' => [], 'total' => []], 'x' => ['server' => [], 'total' => []]]
        ];

        // Get server id's for this provider
        $serverIds = $this->getServerIdsByProviderId($providerId);
        $idString = implode(',', $serverIds);
        if (empty($serverIds)) {
            $idString = '0';
        }

        /* -------------------------------------------------------
         * WEEK
         * -------------------------------------------------------
         */
        $todayStart = new DateTime('6 days ago midnight');
        $timestamp = $todayStart->getTimestamp();

        // Build array
        $serverCounts = [];
        $totalCounts = [];
        for ($iDay = 6; $iDay >= 0; $iDay--)
        {
            $key = date('l (m/d)', time() - ($iDay * 86400));
            $serverCounts[$key] = 0;
            $totalCounts[$key] = 0;
        }

        $query = "SELECT `time_imported`, `server_id` FROM round WHERE `time_imported` > $timestamp";
        $result = $this->pdo->query($query);
        while ($row = $result->fetch())
        {
            $key = date("l (m/d)", (int)$row['time_imported']);
            $totalCounts[$key] += 1;

            if (in_array($row['server_id'], $serverIds)) {
                $serverCounts[$key] += 1;
            }
        }

        $i = 0;
        foreach ($serverCounts as $key => $value)
        {
            $output['week']['y']['server'][] = [$i, $value];
            $output['week']['x']['server'][] = [$i++, $key];
        }

        $i = 0;
        foreach ($totalCounts as $key => $value)
        {
            $output['week']['y']['total'][] = [$i, $value];
            $output['week']['x']['total'][] = [$i++, $key];
        }

        /* -------------------------------------------------------
         * MONTH
         * -------------------------------------------------------
         */

        $serverCounts = [];

        $start = new DateTime('6 weeks ago');
        $end = new DateTime('now');
        $interval = DateInterval::createFromDateString('1 week');

        $period = new DatePeriod($start, $interval, $end);
        $timeArrays = [];

        foreach ($period as $p)
        {
            // Start
            /* @var $p DateTime */
            $p->modify('+1 minute');
            $key1 = $p->format('M d');
            $timestamp = $p->getTimestamp();

            // End
            $p->modify('+7 days');
            $key2 = $p->format('M d');

            // Append
            $timeArrays[$timestamp] = $p->getTimestamp();
            $serverCounts[] = $key1 . ' - ' . $key2;
        }

        $i = 0;
        foreach ($timeArrays as $start => $finish)
        {
            // Server
            $query = "SELECT COUNT(`time_imported`) FROM round WHERE server_id IN ($idString) AND `time_imported` BETWEEN $start AND $finish";
            $result = (int)$this->pdo->query($query)->fetchColumn(0);

            $output['month']['y']['server'][] = [$i, $result];
            $output['month']['x']['server'][] = [$i, $serverCounts[$i]];

            // Total
            $query = "SELECT COUNT(`time_imported`) FROM round WHERE `time_imported` BETWEEN $start AND $finish";
            $result = (int)$this->pdo->query($query)->fetchColumn(0);

            $output['month']['y']['total'][] = [$i, $result];
            $output['month']['x']['total'][] = [$i, $serverCounts[$i]];
            $i++;
        }

        /* -------------------------------------------------------
         * YEAR
         * -------------------------------------------------------
         */

        $serverCounts = [];

        // Yep, php DateTime using strings is BadAss!!
        $start = new DateTime('first day of 11 months ago');
        $end = new DateTime('last day of this month');
        $interval = DateInterval::createFromDateString('1 month');

        $period = new DatePeriod($start, $interval, $end);
        $timeArrays = [];

        foreach ($period as $p)
        {
            // Start
            $serverCounts[] = $p->format('M Y');
            $timestamp = $p->getTimestamp();

            // End
            $p->modify('+1 month');

            // Append
            $timeArrays[$timestamp] = $p->getTimestamp();
        }

        $i = 0;
        foreach ($timeArrays as $start => $finish)
        {
            // Server
            $query = "SELECT COUNT(`time_imported`) FROM round WHERE server_id IN ($idString) AND `time_imported` BETWEEN $start AND $finish";
            $result = (int)$this->pdo->query($query)->fetchColumn(0);

            $output['year']['y']['server'][] = [$i, $result];
            $output['year']['x']['server'][] = [$i, $serverCounts[$i]];

            // Total
            $query = "SELECT COUNT(`time_imported`) FROM round WHERE `time_imported` BETWEEN $start AND $finish";
            $result = (int)$this->pdo->query($query)->fetchColumn(0);

            $output['year']['y']['total'][] = [$i, $result];
            $output['year']['x']['total'][] = [$i, $serverCounts[$i]];
            $i++;
        }

        // return chart data
        return $output;
    }

    /**
     * Returns a list of server ID's that use the provided provider ID
     *
     * @param int $providerId The provider id
     *
     * @return int[] an array of server id's that fall under the indicated provider
     */
    public function getServerIdsByProviderId($providerId): array
    {
        // Sanitize
        $id = (int)$providerId;

        // First, grab server ID's for this provider!
        $list = [];
        $query = "SELECT id FROM server WHERE provider_id=". $id;
        $stmt = $this->pdo->query($query);
        while ($row = $stmt->fetch())
        {
            $list[] = (int)$row['id'];
        }

        return $list;
    }

    /**
     * Returns a list of servers that use the provided provider ID
     *
     * @param int $providerId The provider id
     *
     * @return array an array of servers that fall under the indicated provider
     */
    public function getServersByProviderId($providerId)
    {
        // Sanitize
        $id = (int)$providerId;

        $query = <<<SQL
SELECT s.*, p.authorized, p.plasma, COUNT(r2.id) AS `snapshots`
FROM `server` AS s
  LEFT JOIN stats_provider AS p on s.provider_id = p.id
  LEFT JOIN round AS r2 on s.id = r2.server_id
WHERE s.provider_id = $id
GROUP BY s.id
SQL;

        // Get real authorization
        $addresses = [];
        $servers = $this->pdo->query($query)->fetchAll();
        $query = "SELECT `address` FROM `stats_provider_auth_ip` WHERE `provider_id`={$id}";
        $rows = $this->pdo->query($query)->fetchAll();

        foreach ($rows as $row)
        {
            $addresses[] = $row['address'];
        }
        // Add additional tags per server
        $counter = count($servers);

        // Add additional tags per server
        for ($i = 0; $i < $counter; $i++)
        {
            $serverIp = IPAddress::Parse($servers[$i]['ip']);
            $auth = false;

            foreach ($addresses as $address)
            {
                if ($serverIp->isInCidr($address))
                {
                    $auth = true;
                    break;
                }
            }

            $servers[$i]['authorized'] = ($auth) ? 1 : 0;
            $servers[$i]['last_seen'] = TimeHelper::FormatDifference((int)$servers[$i]['lastseen'], time());
            $servers[$i]['last_update'] = TimeHelper::FormatDifference((int)$servers[$i]['lastupdate'], time());
        }

        return $servers;
    }
}
