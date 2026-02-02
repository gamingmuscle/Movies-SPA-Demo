<?php
	class baseApiEndpoint
	{
		public $details=array();
		/*
			__call
			magic method
		*/
		public function __call($name,$arguments)
		{
			$this->sendOutput(405,'Method Not Allowed');
		}
		/*
			getUriSegments()
			
			return array
		*/
		protected function getUriSegments()
		{
			return explode('/',parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH));
		}
		/*
			getQueryStringParams()
			Get querystring params.
		 
			return array
		*/
		protected function getQueryStringParams()
		{
			if(!isset($_SERVER['QUERY_STRING']) || $_SERVER['QUERY_STRING'] == "")return [];
			parse_str($_SERVER['QUERY_STRING'], $query);
			return $query;
		}
		/*
			getRequestPayload()
			Gets the JSON request body and decodes it

			returns array || null
		*/
		protected function getRequestPayload()
		{
			return json_decode(file_get_contents("php://input"), true);
		}
		/*
			Send API output as a response pack object.
			Input:
				$code - int - http response code
				$desc - string - description of the response
				$data - mixed - response payload
				$httpHeaders
		*/
		protected function sendOutput($code,$desc,$data=null, $httpHeaders=array())
		{
			header_remove('Set-Cookie');
			header('Content-Type: application/json');
			if (is_array($httpHeaders) && count($httpHeaders)) 
			{
				foreach ($httpHeaders as $httpHeader) 
				{
					header($httpHeader);
				}
			}
			if($code != 200)http_response_code($code);									// Update response code if necessary
			$msg=new responsePackage($code,$desc);
			if(sizeof($this->details) > 0)
				$msg->addDetail($this->details);
			$msg->addPayload($data);
			echo $msg;
			exit;
		}
		/*
			validate($input, $rules)
			Validates input against a set of rules
			Input:
				$input - array - the request payload
				$rules - array - validation rules per field:
					[
						'field_name' => [
							'required' => bool,
							'type' => 'int'|'string'|'array'|'bool',
							'allowed' => [...] // allowed values
						]
					]

			returns true if valid || array of errors
		*/
		protected function validate($input, $rules)
		{
			$errors = [];
			foreach($rules as $field => $rule)
			{
				// Check required
				if(!empty($rule['required']) && !isset($input[$field]))
				{
					$errors[$field] = 'required';
					continue;
				}

				if(isset($input[$field]))
				{
					$value = $input[$field];

					// Check type
					if(isset($rule['type']))
					{
						$valid = match($rule['type']) {
							'int' => is_numeric($value),
							'string' => is_string($value),
							'array' => is_array($value),
							'bool' => is_bool($value),
							default => true
						};
						if(!$valid)
							$errors[$field] = 'invalid type';
					}

					// Check allowed values
					if(isset($rule['allowed']) && !in_array($value, $rule['allowed']))
						$errors[$field] = 'invalid value';
				}
			}
			return empty($errors) ? true : $errors;
		}
		/*
			getMethod()
			Gets the HTTP request method

			returns string
		*/
		protected function getMethod()
		{
			return $_SERVER["REQUEST_METHOD"];
		}
		public function handleRequest()
		{
			$method=$this->getMethod();
			
			$call=strtolower($method);
			$this->{$call}();
		}
	}
?>
