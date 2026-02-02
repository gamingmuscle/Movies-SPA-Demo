<?php
/*
	dbAuth
	Container class for database connection credentials with password masking support
*/
	class dbAuth
	{
		public $dbHost="";
		public $dbUser="";
		public $dbPass="";
		public $dbName="";
		private $showPass=false;

		/*
			__construct($dbHost, $dbUser, $dbPass, $dbName)
			Creates a new database auth object with the given credentials
			Input:
				$dbHost - string - database server hostname
				$dbUser - string - database username
				$dbPass - string - database password
				$dbName - string (optional) - database name
		*/
		function __construct($dbHost,$dbUser,$dbPass,$dbName=null)
		{
			$this->dbHost = $dbHost;
			$this->dbUser = $dbUser;
			$this->dbPass = $dbPass;
			$this->dbName = $dbName;
		}
		/*
			showPass($bool)
			Toggles whether password is shown or masked in __toString output
			Input:
				$bool - boolean - true to show password, false to mask
		*/
		public function showPass($bool=true)
		{
			$this->showPass=$bool;
		}
		/*
			__toString()
			Returns JSON representation of credentials with password masked unless showPass(true) was called

			returns string - JSON encoded credentials
		*/
		public function __toString() : string
		{
			$tmp=[];
			$tmp['dbHost']=$this->dbHost;
			$tmp['dbUser']=$this->dbUser;
			$tmp['dbPass']=$this->showPass ? $this->dbPass : 'xxxxxx';
			$tmp['dbName']=$this->dbName;
			return json_encode($tmp);
		}
	}
?>