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

use PDO;
use PDOException;

class DataTables
{
    /**
     * @var PDO
     */
    protected static $connection;

    /**
     * Perform the SQL queries needed for an server-side processing requested,
     * utilising the helper functions of this class, limit(), order() and
     * filter() among others. The returned array is ready to be encoded as JSON
     * in response to an SSP request, or can be modified if needed before
     * sending back to the client.
     *
     * @param  array $request Data sent to server by DataTables
     * @param  PDO $conn PDO connection resource or connection parameters array
     * @param  string $table SQL table to query
     * @param  string $primaryKey Primary key of the table
     * @param  array $columns Column information array
     *
     * @param string $customFilter
     *
     * @return array Server-side processing response array
     */
    public static function FetchData($request, $conn, $table, $primaryKey, $columns, ?string $customFilter = ''): array
    {
        $bindings = [];
        self::$connection = $conn;

        // Build the SQL query string from the request
        $limit = self::Limit($request);
        $order = self::Order($request, $columns);
        $where = self::Filter($request, $columns, $bindings, $customFilter);

        // Main query to actually get the data
        $data = self::ExecuteSql($bindings,
            "SELECT `" . implode("`, `", self::Pluck($columns, 'db')) . "`
			 FROM `$table`
			 $where
			 $order
			 $limit"
        );

        // Data set length after filtering
        /** @noinspection SqlResolve */
        $resFilterLength = self::ExecuteSql($bindings, "SELECT COUNT(`{$primaryKey}`) FROM `{$table}` {$where}");
        $recordsFiltered = $resFilterLength[0][0];

        // Total data set length
        if ($customFilter === null || $customFilter === '' || $customFilter === '0')
        {
            /** @noinspection SqlResolve */
            $resTotalLength = self::ExecuteSql($bindings, "SELECT COUNT(`{$primaryKey}`) FROM `{$table}`");
        }
        else
        {
            /** @noinspection SqlResolve */
            $resTotalLength = self::ExecuteSql($bindings, "SELECT COUNT(`{$primaryKey}`) FROM `{$table}` WHERE " . $customFilter);
        }
        $recordsTotal = $resTotalLength[0][0];

        // Output
        return [
            "draw" => isset($request['draw']) ? intval($request['draw']) : 0,
            "recordsTotal" => intval($recordsTotal),
            "recordsFiltered" => intval($recordsFiltered),
            "data" => self::Format($columns, $data)
        ];
    }

    /**
     * Execute an SQL query on the database
     *
     * @param  array $bindings Array of PDO binding values from bind() to be
     *   used for safely escaping strings. Note that this can be given as the
     *   SQL query string if no bindings are required.
     * @param  string $sql SQL query to execute.
     *
     * @return array         Result from the query (all rows)
     */
    protected static function ExecuteSql($bindings, $sql = null): array
    {
        // Argument shifting
        if ($sql === null)
        {
            $sql = $bindings;
        }

        // Prepare statement
        $stmt = self::$connection->prepare($sql);

        // Bind parameters
        if (is_array($bindings))
        {
            for ($i = 0, $ien = count($bindings); $i < $ien; $i++)
            {
                $binding = $bindings[$i];
                $stmt->bindValue($binding['key'], $binding['val'], $binding['type']);
            }
        }

        // Execute
        try
        {
            $stmt->execute();
        }
        catch (PDOException $e)
        {
            self::Fetal("An SQL error occurred: " . $e->getMessage());
        }

        // Return all
        return $stmt->fetchAll(PDO::FETCH_BOTH);
    }

    /**
     * Paging
     *
     * Construct the LIMIT clause for server-side processing SQL query
     *
     * @param  array $request Data sent to server by DataTables
     *
     * @return string SQL limit clause
     */
    private static function Limit($request): string
    {
        $limit = '';

        if (isset($request['start']) && $request['length'] != -1)
        {
            $limit = "LIMIT " . intval($request['start']) . ", " . intval($request['length']);
        }

        return $limit;
    }


    /**
     * Ordering
     *
     * Construct the ORDER BY clause for server-side processing SQL query
     *
     * @param  array $request Data sent to server by DataTables
     * @param  array $columns Column information array
     *
     * @return string SQL order by clause
     */
    private static function Order($request, array $columns): string
    {
        $order = '';

        if (isset($request['order']) && count($request['order']))
        {
            $orderBy = [];
            $dtColumns = self::Pluck($columns, 'dt');

            for ($i = 0, $ien = count($request['order']); $i < $ien; $i++)
            {
                // Convert the column index into the column data property
                $columnIdx = intval($request['order'][$i]['column']);
                $requestColumn = $request['columns'][$columnIdx];

                $columnIdx = array_search($requestColumn['data'], $dtColumns);
                $column = $columns[$columnIdx];

                if ($requestColumn['orderable'] == 'true')
                {
                    $dir = $request['order'][$i]['dir'] === 'asc' ?
                        'ASC' :
                        'DESC';

                    $orderBy[] = '`' . $column['db'] . '` ' . $dir;
                }
            }

            $order = 'ORDER BY ' . implode(', ', $orderBy);
        }

        return $order;
    }


    /**
     * Searching / Filtering
     *
     * Construct the WHERE clause for server-side processing SQL query.
     *
     * NOTE this does not match the built-in DataTables filtering which does it
     * word by word on any field. It's possible to do here performance on large
     * databases would be very poor
     *
     * @param  array $request Data sent to server by DataTables
     * @param  array $columns Column information array
     * @param  array $bindings Array of values for PDO bindings, used in the
     *    sql_exec() function
     *
     * @param $customFilter
     *
     * @return string SQL where clause
     */
    private static function Filter($request, array $columns, array &$bindings, ?string $customFilter): string
    {
        $globalSearch = [];
        $columnSearch = [];
        $dtColumns = self::Pluck($columns, 'dt');

        if (isset($request['search']) && $request['search']['value'] != '')
        {
            $str = $request['search']['value'];

            for ($i = 0, $ien = count($request['columns']); $i < $ien; $i++)
            {
                $requestColumn = $request['columns'][$i];
                $columnIdx = array_search($requestColumn['data'], $dtColumns);
                $column = $columns[$columnIdx];

                if ($requestColumn['searchable'] == 'true')
                {
                    $binding = self::Bind($bindings, '%' . $str . '%', PDO::PARAM_STR);
                    $globalSearch[] = "`" . $column['db'] . "` LIKE " . $binding;
                }
            }
        }

        // Individual column filtering
        if (isset($request['columns']))
        {
            for ($i = 0, $ien = count($request['columns']); $i < $ien; $i++)
            {
                $requestColumn = $request['columns'][$i];
                $columnIdx = array_search($requestColumn['data'], $dtColumns);
                $column = $columns[$columnIdx];

                $str = $requestColumn['search']['value'];

                if ($requestColumn['searchable'] == 'true' &&
                    $str != ''
                )
                {
                    $binding = self::Bind($bindings, '%' . $str . '%', PDO::PARAM_STR);
                    $columnSearch[] = "`" . $column['db'] . "` LIKE " . $binding;
                }
            }
        }

        // Combine the filters into a single string
        $where = '';

        if (count($globalSearch))
        {
            $where = '(' . implode(' OR ', $globalSearch) . ')';
        }

        if (count($columnSearch))
        {
            $where = $where === '' ?
                implode(' AND ', $columnSearch) :
                $where . ' AND ' . implode(' AND ', $columnSearch);
        }

        if ($where !== '') {
            $where = 'WHERE ' . $where;
            if ($customFilter !== null && $customFilter !== '' && $customFilter !== '0') {
                $where .= ' AND ' . $customFilter;
            }
        } elseif ($customFilter !== null && $customFilter !== '' && $customFilter !== '0') {
            $where = 'WHERE ' . $customFilter;
        }

        return $where;
    }

    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * Internal methods
	 */

    /**
     * Create the data output array for the DataTables rows
     *
     * @param array $columns Column information array
     * @param array $data Data from the SQL get
     *
     * @return array Formatted data in a row based format
     */
    private static function Format(array $columns, array $data): array
    {
        $out = [];

        for ($i = 0, $ien = count($data); $i < $ien; $i++)
        {
            $row = [];

            for ($j = 0, $jen = count($columns); $j < $jen; $j++)
            {
                $column = $columns[$j];

                // Is there a formatter?
                if (isset($column['formatter']))
                {
                    $row[$column['dt']] = $column['formatter']($data[$i][$column['db']], $data[$i]);
                }
                else
                {
                    $row[$column['dt']] = $data[$i][$columns[$j]['db']];
                }
            }

            $out[] = $row;
        }

        return $out;
    }

    /**
     * Throw a fatal error.
     *
     * This writes out an error message in a JSON string which DataTables will
     * see and show to the user in the browser.
     *
     * @param  string $msg Message to send to the client
     */
    private static function Fetal(string $msg): never
    {
        echo json_encode(["error" => $msg]);
        exit(0);
    }

    /**
     * Create a PDO binding key which can be used for escaping variables safely
     * when executing a query with sql_exec()
     *
     * @param  array &$a Array of bindings
     * @param  *      $val  Value to bind
     * @param  int $type PDO field type
     *
     * @return string       Bound key to be used in the SQL where this parameter
     *   would be used.
     */
    static function Bind(&$a, $val, $type): string
    {
        $key = ':binding_' . count($a);
        $a[] = [
            'key' => $key,
            'val' => $val,
            'type' => $type
        ];

        return $key;
    }

    /**
     * Pull a particular property from each assoc. array in a numeric array,
     * returning and array of the property values from each item.
     *
     * @param  array $a Array to get data from
     * @param  string $prop Property to read
     *
     * @return array        Array of property values
     */
    static function Pluck(array $a, $prop): array
    {
        $out = [];

        for ($i = 0, $len = count($a); $i < $len; $i++)
        {
            $out[] = $a[$i][$prop];
        }

        return $out;
    }
}