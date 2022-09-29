<?php
/* 
| --------------------------------------------------------------
| BF2 Statistics Admin Util
| --------------------------------------------------------------
| Author:       Steven Wilson 
| Copyright:    Copyright (c) 2012
| License:      GNU GPL v3
| ---------------------------------------------------------------
| Class: Database()
| ---------------------------------------------------------------
|
*/

class Database
{

    // Queries statistics.
    protected $statistics = array(
        'total_time'  => 0,
        'total_queries' => 0,
    );
    private $mysql, $sql;

/*
| ---------------------------------------------------------------
| Constructer
| ---------------------------------------------------------------
*/
    public function __construct()
    {
        $Config = load_class('Config');
        $this->mysql = @mysql_connect(
            $Config->get('db_host').":".$Config->get('db_port'), 
            $Config->get('db_user'), 
            $Config->get('db_pass'), 
            true
        );
        $this->selected_database = @mysql_select_db($Config->get('db_name'), $this->mysql);
    }

/*
| ---------------------------------------------------------------
| Function: status()
| ---------------------------------------------------------------
|
| Checks the connection to the mysql database
| Returns 0 if unable to connect to the database
| Returns -1 if the Database does not exist
| Returns 1 on success
|
*/	
    public function status()
    {
        if(!$this->mysql) return 0;
        if(!$this->selected_database) return -1;
        return 1;
    }

/*
| ---------------------------------------------------------------
| Function: query()
| ---------------------------------------------------------------
|
| The Main Query Function
|
*/

    public function query($query, $supress = false)
    {
        // Add query to the last query and benchmark
        // $bench['query'] = $query;

        // Time, and process our query
        $start = microtime(true);
        
        // Supress errors if requested
        if( $supress == false )
        {
            $this->sql = mysql_query($query, $this->mysql) or die("Couldnt Run Query: ". $query ."<br />Error: ". mysql_error($this->mysql));
        }
        else
        {
            $this->sql = @mysql_query($query, $this->mysql);
        }
        
        // Get our benchmark time
        $bench = round(microtime(true) - $start, 5);

        // Add the query to the list of queries
        //$this->queries[] = $bench;

        // Up our statistic count
        ++$this->statistics['total_queries'];
        $this->statistics['total_time'] = ($this->statistics['total_time'] + $bench);
        return $this;
    }
    
/*
| ---------------------------------------------------------------
| Function: result()
| ---------------------------------------------------------------
|
| Returns the direct mysql_query result
|
*/
    public function result()
    {
        return $this->sql;
    }

/*
| ---------------------------------------------------------------
| Function: fetch_array()
| ---------------------------------------------------------------
|
| Returns an assoc array in a deep array format like so:
|	row 1: [0] => array(
|				'colname1' => 'value'
|				'colname2' => 'value'
|			)
|	row 2: [1] => array(
|				'colname1' => 'value'
|				'colname2' => 'value'
|			)
|
*/

    public function fetch_array($result = null)
    {
        // Check for a given result
        if($result == null) $result = $this->sql;

        // Make sure we have someting to return
        if($result == false || mysql_num_rows($result) == 0)
        {
            return FALSE;
        }
        else
        {
            $i = 0;
            $return = array();
            while($row = mysql_fetch_array($result))
            {
                foreach($row as $colname => $value)
                {
                    $return[$i][$colname] = $value;
                }
                ++$i;
            }
            
            return $return;
        }
    }

/*
| ---------------------------------------------------------------
| Function: fetch_row()
| ---------------------------------------------------------------
|
| Returns a single row of data from the database
|
*/
    public function fetch_row($result = null)
    {
        // Check for a given result
        if($result == null) $result = $this->sql;
        
        // Make sure we have someting to return
        if($result == false || mysql_num_rows($result) == 0)
        {
            return FALSE;
        }
        else
        {
            return mysql_fetch_array($result);
        }
    }
    
/*
| ---------------------------------------------------------------
| Function: fetch_column()
| ---------------------------------------------------------------
|
| Returns a single column's value as a string / Int
|
*/
    public function fetch_column($result = null)
    {
        // Check for a given result
        if($result == null) $result = $this->sql;
        
        // Make sure we have someting to return
        if($result == false || mysql_num_rows($result) == 0)
        {
            return FALSE;
        }
        else
        {
            return mysql_result($result, 0);
        }
    }
    
/*
| ---------------------------------------------------------------
| Function: num_rows()
| ---------------------------------------------------------------
|
| Returns the number of affected rows from the last query
| @Return: (int)
|
*/ 
    public function num_rows($result = null)
    {
        // Check for a given result
        if($result == null) $result = $this->sql;
        
        if($result == false) return 0;
        return mysql_num_rows($result);
    }
    
/*
| ---------------------------------------------------------------
| Function: insert_id()
| ---------------------------------------------------------------
|
| Returns the last insert ID
| @Return: (int)
|
*/
    public function insert_id($result = null)
    {
        // Check for a given result
        if($result == null) $result = $this->sql;
        return mysql_insert_id($result);
    }

/*
| ---------------------------------------------------------------
| Function: run_sql_file()
| ---------------------------------------------------------------
|
| Runs a sql file on the database
|
*/
    public function run_sql_file($file)
    {
        // Open the sql file, and add each line to an array
        $handle = @fopen($file, "r");
        if($handle) 
        {
            while(!feof($handle)) 
            {
                $queries[] = fgets($handle);
            }
            fclose($handle);
        }
        else 
        {
            return FALSE;
        }
        
        // loop through each line and process it
        foreach ($queries as $key => $aquery) 
        {
            // If the line is empty or a comment, unset it
            if (trim($aquery) == "" || strpos ($aquery, "--") === 0 || strpos ($aquery, "#") === 0) 
            {
                unset($queries[$key]);
                continue;
            }
            
            // Check to see if the query is more then 1 line
            $aquery = rtrim($aquery);
            $compare = rtrim($aquery, ";");
            if($compare != $aquery) 
            {
                $queries[$key] = $compare . "|br3ak|";
            }
        }

        // Combine the query's array into a string, 
        // and explode it back into an array seperating each query
        $queries = implode($queries);
        $queries = explode("|br3ak|", $queries);

        // Process each query
        foreach ($queries as $query) 
        {
            // Dont query if the query is empty
            if(empty($query)) continue;
            $this->query($query);
        }
        return TRUE;
    }
    
/*
| ---------------------------------------------------------------
| Function: statistics()
| ---------------------------------------------------------------
|
| Returns the statistic information of this connection
| @Return: (Array)
|
*/ 
    public function statistics()
    {
        return $this->statistics;
    }
    

/*
| ---------------------------------------------------------------
| Function: reset()
| ---------------------------------------------------------------
|
| Clears out and resets the query statistics
|
| @Return: (None)
|
*/
    public function reset()
    {
        $this->queries = array();
        $this->statistics = array(
            'total_time'  => 0,
            'total_queries' => 0
        );
    }
}
?>