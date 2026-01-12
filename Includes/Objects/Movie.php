<?php
include_once __DIR__ ."/../config.php";

class movie
{
	private $conn;
	/*
		default constructor, initializes connection to database
	*/
	function __construct()
	{
		$this->conn=$this->getDBConnection();
	}
	// Create and returns a database connection
	function getDBConnection() {
		$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

		if ($conn->connect_error) {
			die(json_encode(['success' => false, 'error' => 'Database connection failed: ' . $conn->connect_error]));
		}

		$conn->set_charset('utf8mb4');
		return $conn;
	}
	
	/*
		processes a vote
		$id - int - id of the movie
		$type - string - type of vote being cast, 'up' || 'down'
		if succesful returns the updated list
	*/
	public function vote($id,$type)
	{
		$clientIP=$_SERVER['REMOTE_ADDR'];
		$stmt = $this->conn->prepare("INSERT into vote_transactions(movies_id,vote_type,ip_address) values (?,?,INET6_ATON(?))");
		$stmt->bind_param('iss', $id, $type, $clientIP);
		if ($stmt->execute()) 
		{
			$stmt->close();
			$this->list();
			return;
		}
		else 
		{
			http_response_code(500);
			echo json_encode(['success' => false, 'error' => 'Failed to update vote']);
		}
	}
	/*
		list()
		Fetches an ordered list of movies from the database.  List is order by most net votes (up - down)
		returns an array of movies
			{
				id: int,
				title: str,
				upvotes: int,
				downvotes: int,
				netvotes: int
			}
	*/
	public function list()
	{
		
		// Fetch updated movie list ordered by net votes (upvotes - downvotes)
		$result = $this->conn->query("SELECT id, title, release_date, upvotes, downvotes, (upvotes - downvotes) as netvotes FROM movies ORDER BY netvotes DESC, upvotes DESC");

		$movies = [];
		while ($row = $result->fetch_assoc()) 
		{
			$movies[] = $row;
		}

		echo json_encode(['success' => true, 'movies' => $movies]);
	
	}
	/*
		default destructor, closes db connection.
	*/
	function __destruct()
	{
		$this->conn->close();
	}
}
?>
