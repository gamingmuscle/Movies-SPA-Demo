let app;
// Initialize when window ready
window.onload = function () {
	// Ensure htmlTable && ajaxHandler both loaded properly
    if(!window.htmlTable || !window.ajaxHandler)										// Hard failure...redirect to setup page
	{
		console.log("Failed loading required javascript files");
		let error="";
		if(!window.htmlTable)error="Script error: htmlTable.js";
		if(!window.ajaxHandler)error += (error != "" ?" and ajaxHandler.js did not load":"Script error: ajaxHandler.js is not loaded" );
		else error+=" is not loaded";
		window.location.href = "index.php?setup&jserror="+encodeURIComponent(error);
	}
	else
	{
		app = new movieApp('API/Movie/')
		app.initialize();
	}
}

/*
	movieApp
	Main appication, initialize display table, fetches start data and binds voting event handlers to the table
*/
function movieApp (apiPath)
{
	let isLoading = false;		// used to prevent user from voting again while processing a vote.
	const tableContainer ='.container';
	const tableConfig={
		table:{
			id:'moviesTable',
		},
		body:{
			id:'moviesBody',
			rows:{
				"data-movie-id":(index,data) => data.id
			}
		},
		columns:[
			{label:'Rank',data:{class:'rank',value:(index)=>{return index+1;}}},
			{label:'Movie Title',data:{class:'movie-title',value:'title'}},
			{label:'Released',data:{value:'release_date',class:'movie-release'}},
			{label:'NET Votes',header:{class:'votes'},data:{value:'netvotes',class:(index,data)=>{return 'vote-net'+(parseInt(data.upvotes)>=parseInt(data.downvotes)? ' net-positive' :' net-negative');}}},
			{label:'% Positive',data:{class:'vote-percent',value: (index,data)=>{ let up=parseInt(data.upvotes);let down=parseInt(data.downvotes);return (up+down > 0 ?(100*up/(up+down)).toFixed(1):"0.0")+"%"}},header:{class:'votes'}},
			{label:'Actions',data:{class: 'actions',value: ()=>{
				let up=$('<button>')
					.attr('title','Upvote')
					.addClass('vote-btn')
					.addClass('up');
				let down =$('<button>')
					.attr('title','Downvote')
					.addClass('vote-btn')
					.addClass('down');
				return [up,down];
			}}}
		],
		pagination:												// Not needed but I would like it
		{
			chunks: 50,
		}
	}
	const table = new htmlTable(tableConfig);					// Table Obj
	const ajax = new AjaxHandler(apiPath);						// Ajax handler Obj
	
	
	/*
		Private Functions
	*/
	
	/*
		updateMoviesTable(movies)
		Updates existing table rows based on the movies array. If more movies exist than rows, additional rows are appended. This reduces unwanted flickering when redning the table.
			movies - array - an array of objects with structure:
				{
					id: int,
					title: str,
					releasedate: date:watch
					upvotes: int,
					downvotes: int,
					netvotes: int
				}
	*/
	function updateMoviesTable(movies)
	{
		table.setData(movies);									// update table data
		table.render(tableContainer);							// re-render table
		$('#moviesTable').addClass('loaded');
		isLoading=false;
	}
	/*
		handleError(error)
		simple error handler writs the error to console and display a simple error message to the user
			error - string - error message
	*/
	function handleError(error)
	{
		console.log('movieApp Error:', error);
		isLoading=false;
		$('#moviesTable').addClass('loaded');
		let $errorMsg = $('#errorMessage');
		$errorMsg.text('An error occurred. Please try again.');
		$errorMsg.removeClass('hidden');

		// Auto-hide after 10 seconds
		setTimeout(function() 
		{
			$errorMsg.addClass('hidden');
		}, 10000);

		// Click to dismiss
		$errorMsg.off('click').on('click', function() 
		{
			$(this).addClass('hidden');
		});
	}
	/*
		Public functions
	*/
	let myApp= {
		/*
		*/
		initialize ()
		{
			ajax.setErrorCB((xhr,status,err) => {return handleError(err);})
			/* Build Table */
			table.render(tableContainer);
			/* Vote Event binding*/
			$('#moviesTable').on('click', '.vote-btn.up', function () {
				const id = $(this).closest('tr').attr('data-movie-id');
				app.vote(id, 'up');
			});
			$('#moviesTable').on('click', '.vote-btn.down', function () {
				const id = $(this).closest('tr').attr('data-movie-id');
				app.vote(id, 'down');
			});
			
			this.getList();
		},
		/*
			vote(movieId, voteType)
			makes a post request to movie api to register either an up/down vote
				movieId - int represents the primary key id of the movie
				voteType - text - up||down designating the type of vote
		*/
		vote(movieId, voteType)
		{
			// Prevent multiple simultaneous votes
			if (isLoading) return;

			isLoading=true;
			$('#moviesTable').removeClass('loaded');
			ajax.POST('index.php', {movie_id: movieId, vote_type: voteType})
				.then(data => {
					updateMoviesTable(data.payload);
					// Pulse the vote count on the voted row
					let $voteCell = $(`tr[data-movie-id="${movieId}"]`).find('.vote-net');
					$voteCell.addClass('updated');
					$voteCell.one('animationend', function() {
						$(this).removeClass('updated');
					});
				});
		},
		/*
			getList()
			Makes a get request to the Movies API to get the list of movies then updates the DOM
		*/
		getList()
		{
			ajax.GET('index.php').then(data => updateMoviesTable(data.payload));
		}
	}
	
	return myApp;
}


