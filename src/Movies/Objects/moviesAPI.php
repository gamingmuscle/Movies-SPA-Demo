<?php
	require_once PROJECT_ROOT."Library/PHP/baseApiEndpoint.php";

	/*
		moviesApi
		API endpoint handler for movie operations extending baseApiEndpoint
	*/
	class moviesApi extends baseApiEndpoint
	{
		private $movie;

		/*
			__construct($dbh)
			Initializes the API with a database handler and creates Movie object
			Input:
				$dbh - dbHandler - database connection handler
		*/
		function __construct($dbh)
		{
			$this->movie = new Movie($dbh);
		}

		/*
			post()
			Handles POST requests to register a vote
			Expects JSON body with movie_id and vote_type ('up' or 'down')
		*/
		protected function post()
		{
			$input = $this->getRequestPayload();
			
			// Validate inputs
			if($input == null)
				$this->sendOutput(400, 'Invalid Request JSON');

			$rules = [
				'movie_id'  => ['required' => true, 'type' => 'int'],
				'vote_type' => ['required' => true, 'allowed' => ['up', 'down']]
			];
			$validation = $this->validate($input, $rules);
			if($validation !== true)
			{
				$this->details = $validation;
				$this->sendOutput(422, 'Input validation failed');
			}

			try
			{
				$this->movie->vote(intval($input['movie_id']), $input['vote_type'],$_SERVER['REMOTE_ADDR']);
				$movies = $this->movie->list();
				$this->sendOutput(200, 'Ok', $movies);
			}
			catch(Exception $e)
			{
				$this->sendOutput($e->getCode(), $e->getMessage());
			}
		}

		/*
			get()
			Handles GET requests to fetch the list of movies ordered by net votes
		*/
		protected function get()
		{
			try
			{
				$movies = $this->movie->list();
				$this->sendOutput(200, 'Ok', $movies);
			}
			catch(Exception $e)
			{
				$this->sendOutput($e->getCode(), $e->getMessage());
			}
		}
	}
?>