<?PHP
/*
	responsePackage
	Standardized JSON response structure for API endpoints
	Properties:
		code - int - HTTP status code
		description - string - human readable status message
		details - array - additional context or debug info
		payload - mixed - the response data
*/
	class responsePackage
	{
		public $code;
		public $description="";
		public array $details=[];
		public $payload=null;

		/*
			__construct($code, $description)
			Creates a new response package with the given status
			Input:
				$code - int - HTTP status code (default 200)
				$description - string - status message (default 'Ok')
		*/
		function __construct($code=200,$description='Ok')
		{
			$this->code = $code;
			$this->description = $description;
		}
		/*
			addDetail($details)
			Adds additional context to the response, merges arrays or appends values
			Input:
				$details - mixed - array to merge or value to append

			returns $this for method chaining
		*/
		public function addDetail($details)
		{
			if(is_array($details))// TEST if ARRAY THEN MERGE
				$this->details=array_merge($this->details,$details);
			else
				$this->details[]=$details;
			
			return $this;
		}
		/*
			addPayload($pl)
			Sets the response data payload
			Input:
				$pl - mixed - the data to return in the response
		*/
		public function addPayload($pl)
		{
			$this->payload = $pl;
		}
		/*
			__toString()
			Converts response package to JSON string for output

			returns string - JSON encoded response
		*/
		public function __toString(): string
		{
			return json_encode($this);
		}
	}
?>