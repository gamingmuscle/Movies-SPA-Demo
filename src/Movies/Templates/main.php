<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movie Voting App</title>
	<link type="text/css" rel="stylesheet" href="Includes/CSS/movie.css"/>
</head>
<body>
    <div class="container">
		<div id="errorMessage" class="error hidden">
			Something went wrong. Please try again.
		</div>

        <h1>Movie Voting App</h1>
		<p class="ranking-info">
			Movies are ranked first by <strong>NET Votes</strong> (upvotes minus downvotes) then by <strong>% Positive</strong> when NET votes are tied.
		</p>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="API/JS/?file=ajaxHandler.js"></script>
	<script src="API/JS/?file=htmlTable.js"></script>
    <script src="Includes/JS/script.js"></script>
</body>
</html>
