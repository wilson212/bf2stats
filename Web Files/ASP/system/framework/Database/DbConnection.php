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
 * Class DbConnection, PDO extension driver
 *
 * @author      Steven Wilson
 * @package     System
 * @subpackage  Database
 */
class DbConnection extends PDO
{
    /**
     * @var string
     */
    public $lastQuery;

    protected DbConnectionStringBuilder $builder;

    /**
     * Constructor
     */
    public function __construct(DbConnectionStringBuilder $builder)
    {
        // Connect using the PDO Constructor
        parent::__construct(
            $builder->getConnectionString(),
            $builder->user,
            $builder->password,
            $builder->getConnectAttributes()
        );

        // Store the connection string builder
        $this->builder = $builder;
    }

    public function commit(): bool
    {
        try
        {
            return $res = parent::commit();
        }
        catch (\PDOException $e)
        {
            if ($e->getMessage() == "There is no active transaction") {
                return true;
            }

            throw $e;
        }
    }

    public function rollBack(): bool
    {
        try
        {
            return $res = parent::rollBack();
        }
        catch (\PDOException $e)
        {
            if ($e->getMessage() == "There is no active transaction") {
                return true;
            }

            throw $e;
        }
    }

    /**
     * Quotes an SQL identifier using the current driver's quoting strategy
     *
     * @param string $identifier The identifier name
     *
     * @return string The identifier wrapped in driver specific quotes
     */
    public function quoteIdentifier($identifier): string
    {
        // Ignore star identifiers and identifiers with a dot
        if ($identifier == '*' || strpos($identifier, '.')) {
            return $identifier;
        }

        // Perform escape
        $char = $this->builder->identifierEscapeChar;
        $identifier = str_replace($char, "{$char}{$char}", $identifier);
        return "{$char}{$identifier}{$char}";
    }

    /**
     * An easy method that will delete data from a table
     *
     * @param string $table The table name we are updating
     * @param string|string[] $where The where statement Ex: "id = 5"
     *   Also accepts an array of $column => $value
     *
     * @return bool Returns TRUE on success of FALSE on error
     */
    public function delete($table, $where): bool
    {
        // Parse where clause
        if (is_array($where))
        {
            $sql = null;
            foreach ($where as $col => $value)
            {
                $val = (is_int($value)) ? $value : $this->quote($value);
                $sql .= "{$this->quoteIdentifier($col)}={$val} AND ";
            }

            $where = substr((string) $sql, 0, -5);
        }

        // Return TRUE or FALSE
        $table = $this->quoteIdentifier($table);
        $this->lastQuery = 'DELETE FROM ' . $table . ($where != '' ? ' WHERE ' . $where : '');
        return $this->exec($this->lastQuery) > 0;
    }

    /**
     * An easy method that will insert data into a table
     *
     * @param string $table The table name we are inserting into
     * @param mixed[] $data An array of "column => value"'s
     *
     * @return bool Returns TRUE on success of FALSE on error
     */
    public function insert($table, $data): int|false
    {
        $pairs = [];

        // Escape values for the query
        foreach ($data as $key => $value)
        {
            $col = $this->quoteIdentifier($key);
            $pairs[$col] = (is_int($value)) ? $value : $this->quote($value);
        }

        // enclose the column names in grave accents
        $columns = implode(', ', array_keys($pairs));
        $values = implode(', ', array_values($pairs));

        // Run the query
        $table = $this->quoteIdentifier($table);
        $this->lastQuery = "INSERT INTO {$table}({$columns}) VALUES ({$values})";

        return $this->exec($this->lastQuery);
    }

    /**
     * An easy method that will update an existing row in a table
     *
     * @param string $table The table name we are updating
     * @param mixed[] $data An array of "column => value"'s
     * @param string|string[] $where The where statement Ex: "id = 5"
     *   Also accepts an array of $column => $value
     *
     * @return bool Returns TRUE on success of FALSE on error
     */
    public function update($table, $data, $where): int|false
    {
        // Our string of columns
        $table = $this->quoteIdentifier($table);
        $query = "UPDATE {$table} SET ";

        // start creating the SQL string and enclose field names in `
        $first = true;
        foreach ($data as $key => $value)
        {
            if (!$first)
            {
                $query .= ', ';
            }

            $first = false;
            $col = $this->quoteIdentifier($key);
            $val = (is_int($value)) ? $value : $this->quote($value);
            $query .= "{$col}={$val}";
        }

        // Parse where clause
        if (!empty($where))
        {
            if (is_array($where))
            {
                $sql = ' WHERE ';
                foreach ($where as $col => $value)
                {
                    $col = $this->quoteIdentifier($col);
                    $val = (is_int($value)) ? $value : $this->quote($value);
                    $sql .= "{$col}={$val} AND ";
                }

                $query .= substr($sql, 0, -5);
            }
            else {
                $query .= $where;
            }
        }

        // Build our query
        $this->lastQuery = $query;

        return $this->exec($this->lastQuery);
    }
}
