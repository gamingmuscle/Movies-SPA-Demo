<?php
/*
	Movie
	Business logic class for movie operations including voting and listing
*/
class movie
{
	private $conn;																		//DB connection object
	/*
		default constructor, initializes connection to database
	*/
	function __construct($dbCon)
	{
		$this->conn=$dbCon;
		$this->conn->open();
	}
	
	/*
		vote($id, $type)
		Processes a vote for a movie
		Input:
			$id - int - id of the movie
			$type - string - type of vote being cast, 'up' || 'down'
			$clientIP - string - ip of requestor

		throws Exception on failure
	*/
	public function vote($id, $type, $clientIP)
	{
		if(!$this->conn->prepare("INSERT into vote_transactions(movies_id,vote_type,ip_address) values (?,?,INET6_ATON(?))"))
			throw new Exception('Failed to update vote', 500);

		$types = 'iss';
		if(!$this->conn->bindParams($types, [$id, $type, $clientIP]))
			throw new Exception('Failed to update vote', 500);

		if(!$this->conn->execute())
			throw new Exception('Failed to update vote', 500);
	}
	/*
		list()
		Fetches an ordered list of movies from the database. List is ordered by most net votes (up - down)

		returns array of movies:
			{
				id: int,
				title: str,
				upvotes: int,
				downvotes: int,
				netvotes: int
			}

		throws Exception on failure
	*/
	public function list()
	{
		// Fetch updated movie list ordered by net votes (upvotes - downvotes)
		$this->conn->query("SELECT id, title, date_format(release_date,'%b %D, %Y') as release_date, upvotes, downvotes, (upvotes - downvotes) as netvotes FROM movies ORDER BY netvotes DESC, downvotes ASC");
		$results = $this->conn->fetchAll(true);
		if($results === false)
			throw new Exception('Failed to fetch movies', 500);

		return $results;
	}
}
?>
