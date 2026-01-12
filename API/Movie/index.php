<?php
	header('Content-Type: application/json');
	require_once __DIR__.'/../../Includes/config.php';
	Include_once __DIR__.'/../../Includes/Objects/Movie.php';

	// Handle POST & GET requests only, else error 
	if ($_SERVER['REQUEST_METHOD'] == 'POST') 
	{
		// Get JSON input
		$input = json_decode(file_get_contents('php://input'), true);

		//Validate inputs	
		if (!isset($input['movie_id']) || !isset($input['vote_type']) || !in_array($input['vote_type'], ['up', 'down'])) 
		{
			http_response_code(400);
			echo json_encode(['success' => false, 'error' => 'Missing required parameters']);
			exit;
		}


		$movie=new Movie();
		$movie->vote(intval($input['movie_id']),$input['vote_type']);		//register the vote
	}
	else if ($_SERVER['REQUEST_METHOD'] == 'GET')   // GET - return list of movies
	{
		$movie=new Movie();
		$movie->list();
	}
	else     // Unsupported REQUEST
	{
		http_response_code(405);
		echo json_encode(['success' => false, 'error' => 'Method not allowed']);
		exit;
	}
?>
