<?php
/**
 * BF2Statistics ASP Framework
 *
 * Author:       Steven Wilson
 * Copyright:    Copyright (c) 2006-2021, BF2statistics.com
 * License:      GNU GPL v3
 *
 */
namespace System\Database;
use PDO;

/**
 * Provides a base class for strongly typed connection string builders.
 *
 * @package System\Database
 */
abstract class DbConnectionStringBuilder
{
    /**
     * @var string Gets or sets the server IP address used to connect with.
     */
    public $host = '127.0.0.1';

    /**
     * @var int Gets or sets the port number that is used when the socket protocol is being used.
     */
    public $port = 3306;

    /**
     * @var string Gets or sets the user id that should be used to connect with.
     */
    public $user = '';

    /**
     * @var string Gets or sets the password that should be used to connect with.
     */
    public $password = '';

    /**
     * @var string Gets or sets the name of the database the connection should initially connect to.
     */
    public $database = '';

    /**
     * @var string Gets or sets the identifier escape character
     */
    public $identifierEscapeChar = '"';

    /**
     * @var array Gets or sets A key => value array of driver-specific connection options.
     */
    protected $pdoOptions = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ];

    /**
     * Gets the connection string associated with this DbConnectionStringBuilder.
     *
     * @return string
     */
    public abstract function getConnectionString();

    /**
     * Gets an array of PDO connect attributes set by this DbConnectionStringBuilder.
     *
     * @return array
     */
    public function getConnectAttributes()
    {
        return $this->pdoOptions;
    }

    /**
     * @param $pdoOption
     * @param $value
     */
    public function setAttribute($pdoOption, $value): void
    {
        $this->pdoOptions[$pdoOption] = $value;
    }

    /**
     * @param $pdoOption
     *
     * @return mixed|null
     */
    public function getAttribute($pdoOption)
    {
        return $this->pdoOptions[$pdoOption] ?? null;
    }
}