<?php
require_once "dbAuth.php";
require_once "responsePackage.php";
use function Utils\refValues;

/*
	dbHandler
	Database abstraction layer providing prepared statement support, connection management,
	and query execution for mysqli databases.
*/
class dbHandler
{
	private $dbHost="";
	private $dbUser="";
	private $dbPass="";
	private $dbName=null;
	private $last=null;
	private $dbConnection;
	private $dbStatement;
	private $result;
	public $error=null;

	/*
		__construct($dbAuth)
		Initializes database handler with credentials from a dbAuth object
		Input:
			$dbAuth - dbAuth - object containing database connection credentials
	*/
	function __construct($dbAuth)
	{
		foreach( ['dbHost','dbUser','dbPass','dbName'] as $property)  // instead of manual assigning can iterate through properties 
			$this->$property = $dbAuth->$property;
	}
	/*
		__destruct()
		Cleans up database resources by closing open connections and statements
	*/
	function __destruct()
	{
		if($this->dbConnection != null && $this->dbConnection->ping())$this->dbConnection->close();
		if($this->dbStatement instanceof mysqli_stmt)$this->dbStatement->close();
	}
	/*
		open()
		Opens a connection to the database using stored credentials

		returns true on success || false on failure (sets $error property)
	*/
	public function open()
	{
		$this->dbConnection = new mysqli($this->dbHost, $this->dbUser,$this->dbPass, $this->dbName);

		if ($this->dbConnection->connect_error) 
		{
			$this->error=$this->dbConnection->connect_error;
			return false;
		}

		$this->dbConnection->set_charset('utf8mb4');
		return true;
	}
	/*
		query($qStr)
		Runs the given query string against the database and returns a mysqli_result object on success or false on 
		failure
		Input:
			$qStr - string - query string to be run

		returns mysqli_result || false
	*/
	public function query($qStr)
	{
		$this->last="query";
		if($this->dbConnection->connect_error)
		{
			$this->error = $this->dbConnection->connect_error;
			return false;
		}
		$this->result = $this->dbConnection->query($qStr);
		
		return $this->result;
	}
	/*
		prepare($qStr,[$pTypes,$pVals])
		Creates prepares the give query string creating a database statement object.  If bind param values are given it 
		will call bindParams.  Returns true on a successful prepare otherwise false and sets the error property to the 
		error
		Input: 
			$qStr - string - query to be prepared
			$pTypes - string (optional) - list of value types when binding params
			$pVals - array (optional) - array of values to bind

		returns true || false
	*/
	public function prepare($qStr,$pTypes=null,$pVals=array())
	{
		if(!($this->dbStatement = $this->dbConnection->prepare($qStr)))
		{
			$this->error=$this->dbConnection->error;
			return false;
		}
		if($pTypes != null && sizeof($pVals) > 0)
			return $this->bindParams($pTypes,$pVals);
		return true;
	}
	/*
		bindParams(pTypes,pValues)
		Binds given values to a prepared database statement
		Input:
			pTypes - string - a string of characters disgnating the type of the values
			pValues - array - an array of values to bind to the statement
		returns true || false
	*/
	public function bindParams($pTypes,$pValues)
	{
		/* Sanity Checks */
		if(!$this->dbStatement)
		{
			$this->error = "bindParams: no prepared statement";
			return false;
		}
		// Test type string
		if(empty($pTypes) || !is_string($pTypes) || !preg_match('/^[idsb]+$/', $pTypes))
		{
			$this->error="bindParams: Invalid types, must be a non empty string with containing only i d s b characters";
			return false;
		}
		// Make sure type count matches value count
		if( strlen($pTypes) !== count($pValues) )
		{
			$this->error = "bindParams: count of Types does not match value count";
			return false;
		}
		
		try																				// Bind params
		{
			$params=array_merge([$pTypes],$pValues);
			$refs=refValues($params);
			if(!call_user_func_array([$this->dbStatement,'bind_param'],$refs))
			{
				$this->error = "bindParams: bind_param failed - " . $this->dbStatement->error;
				return false;
			}
			return true;
		}
		catch(Exception $ex)																// Binding failed
		{
			$this->error = "bindParams: ".$ex->getMessage();
			return false;
		}
	}
	/*
		execute()
		Executes a prepared statement

		returns true on success || false on failure (sets $error property)
	*/
	public function execute()
	{
		$this->last="statement";
		if($this->dbStatement->execute())
		{
			return true;
		}
		$this->error=$this->dbConnection->error;
		
		return false;
	}
	/*
		fetch()
		Fetches the next row from a prepared statement result

		returns array row || false
	*/
	public function fetch()
	{
		return $this->dbStatement->fetch();
	}
	/*
		fetchAll($assoc)
		Fetches all database query/statement results and returns as either an associative or regular array
		Input:
			$assoc - boolean - when true will return results array of associate of arrays or just an array of arrays.

		returns [[...,...],...] ||[[field1=>...,field2=>...],...] depending of $assoc value
	*/
	public function fetchAll($assoc=false)
	{
		if($this->last == null) return false;  											// only null if nothing has been run
		else if( $this->last == "statement")$this->result=$this->dbStatement->get_result();
		$result=[];

		$method = $assoc ? 'fetch_assoc' : 'fetch';										// determine fetch method
		if($this->result)
		{
			while($row=$this->result->$method())
				$result[]=$row;
			return $result;																// return results
		}
		$this->error="fetchAll: result was false/null";
		return false;
	}
	/*
		testDb()
		A helper function that checks if the app can connect to database and if the schema exists.
		
		returns null on success and an object contain if the test was unsuccessful and a human readable message 
		{
			success: boolean,
			msg: string
		}
	*/
	public static function testDb($dbHost,$dbUser,$dbPass,$dbName)
	{
		
		try
		{
			$conn = new mysqli($dbHost, $dbUser, $dbPass);
		}
		catch(Exception $ex)																							// Catch connection exception
		{
			return ['success'=>false,'msg'=>$ex->getMessage()];
		}
		if(!$conn) return ['success'=>false,'msg'=>'DB failed connection'];  											// Connection failed
		$queryStr="select SCHEMA_NAME from information_schema.schemata where SCHEMA_NAME =?";
		$stmt = $conn->prepare($queryStr);
		$stmt->bind_param("s",$dbName);
		$stmt->execute();
		$stmt->store_result();
		
		if($stmt->num_rows <= 0) $reseult = ['success'=>false, 'msg'=>'Schema not found']; 									// Schema not found
		else $result = ['success'=>true,'msg'=>'Database connection successful!'];												// Indicates success

		return $result;
	}

}

?>