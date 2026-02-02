<?php
/*
	Movies API Endpoint
	Handles GET requests to fetch movie list and POST requests to register votes

	GET  - returns JSON array of all movies ordered by net votes
	POST - registers a vote, expects JSON body with:
		movie_id - int - the movie to vote on
		vote_type - string - 'up' or 'down'
*/
require_once __DIR__.'/../../../../src/Movies/config.php';
require_once PROJECT_ROOT.'src/Movies/Objects/Movie.php';
require_once PROJECT_ROOT.'src/Movies/Objects/moviesAPI.php';

$dbh = new dbHandler($dbAuths['movies_app']);
$api = new moviesApi($dbh);
$api->handleRequest();
?>
