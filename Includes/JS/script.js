let isLoading = false;		// used to prevent user from voting again while processing a vote.

// Initialize on document ready
$(document).ready(function() {
    getMovies();
});
/*
	getMovies()
	makes a get request to movies api and if successful will update the the Movies table
*/
function getMovies()
{
    request('GET', null)
        .then(data => updateMoviesTable(data.movies));
}
/*
	vote(movieId, voteType)
	makes a post request to movie api to register either an up/down vote
		movieId - int represents the primary key id of the movie
		voteType - text - up||down designating the type of vote
*/
function vote(movieId, voteType)
{
    // Prevent multiple simultaneous votes
    if (isLoading) return;

    request('POST', {movie_id: movieId, vote_type: voteType})
        .then(data => updateMoviesTable(data.movies));
}
/*
	request(method, body)
	main ajax request function, takes in method and payload and makes the call to the API
		method - string - HTTP method
		body - object - HTTP request payload
*/
function request(method, body)
{
    isLoading = true;
    $('#moviesBody').addClass('loading');
	//setup ajax config
    let ajaxConfig = {
        url: 'API/Movie/index.php',
        type: method,
        dataType: 'json'
    };

    if (body !== null) {						//if body is set, stringify as json and att to ajaxConfig
        ajaxConfig.contentType = 'application/json';
        ajaxConfig.data = JSON.stringify(body);
    }
	// make call
    return $.ajax(ajaxConfig)
        .fail(function(xhr, status, error) {	//handle error
            handleError(error);
        })
        .always(function() {					//reset isLoading
            isLoading = false;
            $('#moviesBody').removeClass('loading');
        });
}

/*
	updateMoviesTable(movies)
	Updates existing table rows based on the movies array. If more movies exist than rows, additional rows are appended. This reduces unwanted flickering when redning the table.
		movies - array - an array of objects with structure:
			{
				id: int,
				title: str,
				upvotes: int,
				downvotes: int
			}
*/
function updateMoviesTable(movies)
{
    let $tbody = $('#moviesBody');
    let $existingRows = $tbody.find('tr');

    $.each(movies, function(index, movie)
	{
        let $row = $existingRows.eq(index);

        // If row exists at this index, update it
        if ($row.length)
		{
            $row.attr('data-movie-id', movie.id);
            $row.find('.rank').text('#' + (index + 1));
            $row.find('.movie-title').text(movie.title);
			$row.find('.movie-release').text(movie.release_date);
			let vTot =$row.find('.vote-net');
			let up=parseInt(movie.upvotes);
			let down=parseInt(movie.downvotes);
			if(up>down)		// Display as Green
				vTot.removeClass('net-negative').addClass('net-positive');
			else 			// Display as Red
				vTot.removeClass('net-postive').addClass('net-negative');
			vTot.text(movie.netvotes);
            $row.find('.vote-percent').text((100*up/(up+down)).toFixed(1)+'%');
            $row.find('.vote-btn.up').attr('onclick', `vote(${movie.id}, 'up')`);
            $row.find('.vote-btn.down').attr('onclick', `vote(${movie.id}, 'down')`);
        }
		else	// Create new row if it doesn't exist
		{
			let up=parseInt(movie.upvotes);
			let down=parseInt(movie.downvotes);
			let voteClass= up > down ? 'net-positive' : 'net-negative';
            let newRow = `
                <tr data-movie-id="${movie.id}">
                    <td class="rank">#${index + 1}</td>
                    <td class="movie-title">${escapeHtml(movie.title)}</td>
					<td class="movie-release">${escapeHtml(movie.release_date)}</td>
                    <td class="vote-net  ${voteClass}">${up-down}</td>a
                    <td class="vote-percent">${(100*up/(up+down)).toFixed(1)}%</td>
                    <td class="actions">
                        <button class="vote-btn up" onclick="vote(${movie.id}, 'up')" title="Upvote"></button>
                        <button class="vote-btn down" onclick="vote(${movie.id}, 'down')" title="Downvote"></button>
                    </td>
                </tr>
            `;
            $tbody.append(newRow);
        }
    });

    $('#moviesTable').addClass('loaded');
}

/*
	escapeHtml(text)
	escapes any special characters in input string and returns html
		text - string - any text string
*/
function escapeHtml(text)
{
    return $('<div>').text(text).html();
}
/*
	handleError(error)
	simple error handler writs the error to console and display a simple error message to the user
		error - string - error message
*/
function handleError(error)
{
    console.error('Error:', error);

    let $errorMsg = $('#errorMessage');
    $errorMsg.text('An error occurred. Please try again.');
    $errorMsg.removeClass('hidden');

    // Auto-hide after 10 seconds
    setTimeout(function() {
        $errorMsg.addClass('hidden');
    }, 10000);

    // Click to dismiss
    $errorMsg.off('click').on('click', function() {
        $(this).addClass('hidden');
    });
}
