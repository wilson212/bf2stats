<?php
/****************************************************************************/
/*  				< Database Class For BF2statistics >					*/
/*							Written By Wilson212							*/
/****************************************************************************/

class Database
{

	// Queries statistics.
    var $_statistics = array(
        'time'  => 0,
        'count' => 0,
    );
    private $mysql;

/************************************************************
* Creates the connection to the mysql database, selects the posted DB
* Returns 0 if unable to connect to the database
* Returns 2 if the Database does not exist
* Returns TRUE on success
*/

    public function __construct($db_host, $db_port, $db_user, $db_pass, $db_name)
    {
        $this->mysql = @mysql_connect($db_host.":".$db_port, $db_user, $db_pass, true);
        $this->selected_database = @mysql_select_db($db_name, $this->mysql);
		return TRUE;
    }

//	************************************************************
// Closes the mysql DB connection

    public function __destruct()
    {
        @mysql_close($this->mysql) or die(mysql_error());
    }
	
/************************************************************
* Checks the connection to the mysql database, selects the posted DB
* Returns 0 if unable to connect to the database
* Returns 2 if the Database does not exist
* Returns TRUE on success
*/	
	public function status()
	{
		if(!$this->mysql)
		{
			return 0;
		}
		if(!$this->selected_database)
		{
			return 2;
		}
		return 1;
	}

//	************************************************************
// Query function is best used for INSERT and UPDATE functions

    public function query($query)
    {
        $sql = mysql_query($query,$this->mysql) or die("Couldnt Run Query: ".$query."<br />Error: ".mysql_error($this->mysql)."");
		$this->_statistics['count']++;
		return TRUE;
    }

//	************************************************************
// Select function is great for getting huge arrays of multiple rows and tables

    public function select($query)
    {
        $sql = mysql_query($query,$this->mysql) or die("Couldnt Run Query: ".$query."<br />Error: ".mysql_error($this->mysql)."");
		$this->_statistics['count']++;
		$i = 1;
		if(mysql_num_rows($sql) == 0)
		{
			$result = FALSE;
		}
		else
		{
			while($row = mysql_fetch_assoc($sql))
			{
				foreach($row as $colname => $value)
				{
					$result[$i][$colname] = $value;
				}
				$i++;
			}
		}
		return $result;
    }

//	************************************************************	
// selectRow is perfect for getting 1 row of data. Technically can be used for multiple rows,
// though select function is better for more then 1 row

	public function selectRow($query)
    {
        $sql = mysql_query($query,$this->mysql) or die("Couldnt Run Query: ".$query."<br />Error: ".mysql_error($this->mysql)."");
		$this->_statistics['count']++;
		if(mysql_num_rows($sql) == 0)
		{
			return FALSE;
		}
		else
		{
			$row = mysql_fetch_array($sql);
			return $row;
		}
    }
	
//	************************************************************
// selectCell returns 1 cell of data, Not recomended unless you want data from a specific cell in a table

	public function selectCell($query)
    {
        $sql = mysql_query($query,$this->mysql) or die("Couldnt Run Query: ".$query."<br />Error: ".mysql_error($this->mysql)."");
		$this->_statistics['count']++;
		if(mysql_num_rows($sql) == 0)
		{
			return FALSE;
		}
		else
		{
			$row = mysql_fetch_array($sql);
			return $row['0'];
		}
    }

//	************************************************************	
// count is a perfect function for counting the num of rows, or results in a table
// returns the direct count, for ex: 5

	public function count($query)
    {
        $sql = mysql_query($query, $this->mysql) or die("Couldnt Run Query: ".$query."<br />Error: ".mysql_error($this->mysql)."");
		$this->_statistics['count']++;
		return mysql_result($sql, 0);
    }

//	************************************************************	
// Run a sql file function. Not written by me.
// $file is the path location to the sql file

	function runSQL($file)
	{
		$handle = @fopen($file, "r");
		if ($handle) 
		{
			while(!feof($handle)) 
			{
				$sql_line[] = fgets($handle);
			}
			fclose($handle);
		}
		else 
		{
			return FALSE;
		}
		foreach ($sql_line as $key => $query) 
		{
			if (trim($query) == "" || strpos ($query, "--") === 0 || strpos ($query, "#") === 0) 
			{
				unset($sql_line[$key]);
			}
		}
		unset($key, $query);

		foreach ($sql_line as $key => $query) 
		{
			$query = rtrim($query);
			$compare = rtrim($query, ";");
			if ($compare != $query) 
			{
				$sql_line[$key] = $compare . "|br3ak|";
			}
		}
		unset($key, $query);

		$sql_lines = implode($sql_line);
		$sql_line = explode("|br3ak|", $sql_lines);
		
		foreach($sql_line as $query)
		{
			if($query)
			{
				mysql_query($query, $this->mysql) or die("Couldnt Run Query: ".$query."<br />Error: ".mysql_error($this->mysql)."");
			}
		}
		return TRUE;
	}
}
?>