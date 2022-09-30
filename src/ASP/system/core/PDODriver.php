<?php
/**
 *	The PDODriver object is an extension of PHP's PDO
 *	 Database Layer.
 */
class PDODriver extends PDO
{
	// Counter: Total number of queries
    public $queryCount = 0;
	
	// Counter: Total queries execution time
    public $queryTime = 0;
	
	// The last ran query string
	protected $queryString = null;
    
	/**
	 * Constructor
	 * @throws PDOException if there is a connection error.
	 */
    public function __construct($dsn, $username = null, $password = null)
    {
        parent::__construct($dsn, $username, $password);
        $this->setAttribute(PDO::ATTR_STATEMENT_CLASS, array('PDOStatementExtended', array($this)));
    }
    
	/**
	 * Executes a traditional SELECT query
	 * @return PDOStatement | false.
	 */
    public function query($statement, $fetchType = PDO::FETCH_ASSOC, $c = null, $ctorargs = array())
    {
		// Increment Query Counter
        ++$this->queryCount;
		$this->queryString = $statement;
        $start = microtime(1);
		
		// Do the query
        if(!empty($c))
			$return = (!empty($ctorargs)) ? parent::query($statement, $fetchType, $c, $ctorargs) : parent::query($statement, $fetchType, $c);
		else
			$return = parent::query($statement, $fetchType);
		
		// Log query time
        $this->queryTime += (microtime(1) - $start);
        return $return;
    }
    
	/**
	 * Executes a DELETE or UPDATE query
	 * @return int | false Number of rows affected by the last statement
	 */
    public function exec($statement)
    {
		// Increment Query Counter
        ++$this->queryCount;
        $start = microtime(1);
		
		// Execute the query
        $return = parent::exec($statement);
		
		// Log Query time
        $this->queryTime += (microtime(1) - $start);
        return $return;
    }
	
	/**
	 * Since PDO is unreliable when fetching the number of rows
	 * in our query result, we will manually fetch it.
	 */
	public function numRows() 
	{
        $regex = '/^SELECT\s+(?:ALL\s+|DISTINCT\s+)?(?:.*?)\s+FROM\s+(.*)$/i';
        if (preg_match($regex, $this->queryString, $output) > 0) 
		{
            $stmt = parent::query("SELECT COUNT(*) FROM {$output[1]}", PDO::FETCH_NUM);
            return $stmt->fetchColumn();
        }

        return false;
    }
    
	/**
	 *	Returns the number of queries ran
	 */
    public function numQueries()
    {
        return $this->queryCount;
    }
    
	/**
	 *	Returns the total execution time for all ran queries
	 */
    public function queryExecutionTime()
    {
        return $this->queryTime;
    }
}

/**
 * The PDOStatementExtended object is an extension of PHP's 
 * PDOStatement Object, which is returned by the PDO::prepare
 * and PDO::query methods.
 */
class PDOStatementExtended extends PDOStatement
{
	// The PDODriver object
    protected $parent;
    
    private function __construct($parent)
    {
        $this->parent = $parent;
    }
    
	/**
	 *	Extend the execute method, to increment the query count and execution time.
	 */
    public function execute($input_parameters = array())
    {
		// Increment Query Counter
        ++$this->parent->queryCount;
        $start = microtime(1);
		
		// Execute the statement
        $return = (empty($input_parameters)) ? parent::execute() : parent::execute($input_parameters);
		
		// Log Query time
        $this->parent->queryTime += (microtime(1) - $start);
        return $return;
    }
}