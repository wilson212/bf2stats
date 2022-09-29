<?php
/**
 * Database Factory Class
 *
 * @author      Steven Wilson 
 * @package     Database
 */
class Database
{
    /**
     * An array of all stored connections
     * @var PDO
     */
    protected static $connections = array();
    
    /**
     * Initiates a new database connection.
     *
     * @param string $name Name or ID of the connection
     * @param array $i The database connection information
     *     array(
     *       'driver'
     *       'host'
     *       'port'
     *       'database'
     *       'username'
     *       'password')
     * @param bool $new If connection already exists, setting to true
     *    will overwrite the old connection ID with the new connection
     * @return \Database\Driver Returns a Database Driver Object
     * @throws DatabaseConnectError if there is a database connection error
     */
    public static function Connect($name, $i, $new = false)
    {
        // If the connection already exists, and $new is false, return existing
        if(isset(self::$connections[$name]) && !$new)
            return self::$connections[$name];
        
        // Create our DSN string
        $dsn = $i['driver'] .':host='.$i['host'] .';port='.$i['port'] .';dbname='.$i['database'];
        
        // Connect using the PDO Constructor
        try {
            self::$connections[$name] = new PDODriver($dsn, $i['username'], $i['password'], array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
        }
        catch(Exception $e) {
            ErrorLog('Database Connect Error: '. $e->getMessage(), 1);
            throw $e;
        }
        
        return self::$connections[$name];
    }
    
    /**
     * Returns the connection object for the given Name or ID
     *
     * @param string $name Name or ID of the connection
     * @return bool|\Database\Driver Returns a Database Driver Object,
     *    or false of the connection $name doesnt exist
     */
    public static function GetConnection($name = 'bf2stats')
    {
        if(isset(self::$connections[$name]))
            return self::$connections[$name];
        return false;
    }
}

require SYSTEM_PATH . DS . 'core' . DS . 'PDODriver.php';