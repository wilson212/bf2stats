<?php
/**
 * BF2Statistics ASP Framework
 *
 * Author:       Steven Wilson
 * Copyright:    Copyright (c) 2006-2021, BF2statistics.com
 * License:      GNU GPL v3
 *
 */
namespace System;
use Exception;
use System\Database\DbConnection;
use System\Database\DbConnectionStringBuilder;

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
     * @var DbConnection[]
     */
    protected static $connections = [];

    /**
     * Initiates a new database connection.
     *
     * @param string $name Name or ID of the connection
     * @param bool $new If connection already exists, setting to true
     *    will overwrite the old connection ID with the new connection
     *
     * @throws Exception
     * @return DbConnection Returns a Database Driver Object
     */
    public static function CreateConnection($name, DbConnectionStringBuilder $builder, $new = false)
    {
        // If the connection already exists, and $new is false, return existing
        if (isset(self::$connections[$name]) && !$new) {
            return self::$connections[$name];
        }

        // Connect using the PDO Constructor
        self::$connections[$name] = new DbConnection($builder);
        return self::$connections[$name];
    }

    /**
     * Returns the connection object for the given Name or ID
     *
     * @param string $name Name or ID of the connection
     *
     * @return bool|DbConnection Returns a Database Driver Object,
     *    or false of the connection $name does'nt exist
     */
    public static function GetConnection($name = 'stats')
    {
        return self::$connections[$name] ?? false;
    }
}