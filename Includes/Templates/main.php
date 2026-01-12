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
        <table id="moviesTable">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Movie Title</th>
					<th>Released</th>
                    <th class="votes">Votes</th>
					<th class="votes">% Positive</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="moviesBody">
			<?php for ($i = 1; $i <= 1; $i++): /*Generate empty rows*/?>
				<tr data-movie-id="">
					<td class="rank">#<?= $i ?></td>
					<td class="movie-title"></td>
					<td class="movie-release"></td>
					<td class="vote-net">0</td>
					<td class="vote-percent">0%</td>
					<td class="actions">
						<button class="vote-btn up" onclick="" title="Upvote"></button>
						<button class="vote-btn down" onclick="" title="Downvote"></button>
					</td>
				</tr>
			<?php endfor; ?>

            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="Includes/JS/script.js"></script>
</body>
</html>
