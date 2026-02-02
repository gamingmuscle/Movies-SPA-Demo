# Movie Voting App

A PHP application that allows users to vote on movies with real-time updates using AJAX. Features a clean separation of concerns with business logic outside the web root for security.

## Features

- Display movies in a ranked table by Net votes
- Thumbs up/down voting system with custom button graphics
- Shows net votes (upvotes - downvotes) and percentage positive
- Real-time vote count updates without page refresh
- Automatic reordering by net votes after each vote
- Vote transaction tracking with IP address logging
- Database trigger automatically maintains aggregate vote counts
- Smooth animations and modern UI
- Setup helper page for troubleshooting database connections

## Setup Instructions

### 1. Database Setup

Import the database schema:

```bash
mysql -u root -p < schema.sql
```

Or manually run the SQL commands in `schema.sql` through phpMyAdmin or MySQL Workbench.
It is recommended to remove `schema.sql` from Movies once setup is complete.

### 2. Configure Database Connection

Edit `src/Movies/config.php` and update project root global and the database credentials if needed:
```php
	define('PROJECT_ROOT', 'c:/test/');		// Parent of Webserver public directory,  File paths depend on this

$dbAuths['movies_app']=new dbAuth(
	'localhost',							// Database Host
	'root',									// Database User
	'',										// Database Pass
	'movies_app'							// Database Name - DO NOT Change unless you also change schema.sql. schema.sql uses this name.
);
```

### 3. Install the PHP Code

- Place the Movies\ directory into your webserver public directory (IE: `C:\prod\www\Movies`)
- Place Library\ and src\ one level above your webserver public directory (IE: `C:\prod\Library` and `C:\prod\src`)

### 4. Run the Application
- Open your browser and navigate to `http://localhost/Movies/`
--If steps 1 & 2 completed correctly, main app display will be rendered
--If app detects a setup error, a setup helper page will be rendered

## File Structure

```
{Webserver Public dir}/Movies/
	index.php                    	# Main page with movie table
	schema.sql                  	# Database schema and sample data	
	API/
		Movie/
			index.php            	# API endpoint for handling votes
		JS/
			index.php				# API javascript proxy endpoint
	Includes/
		Assets/						# Art assets
			thumbsup.png
			thumbsdown.png
		CSS/
			movie.css            	# Styling for the application
			setup.css            	# Styling for setup page
		JS/
			script.js            	# main app javascript file
{Parent of Webserver Public dir}/src/
	Movies/
		config.php					# Database configuration & Db test helper function
		Objects/
			Movie.php				# Object with functions to fetch from/update Database
			moviesAPI.php			# Movies API handler
		Templates/
			main.php				# Main application HTML
			setup.php				# Setup helper page
{Parent of Webserver Public dir}/Library/
	PHP/
		Utils/
			helperFunctions.php		# Helper functions that don't fit within an existing class
		dbAuth.php					# Database Authentication object
		dbHandler.php				# Database Handler, provides common interface for interacting with database
		FileProxy.php				# File proxy class, provides an interface for serving up files that are outside of the public directory
		responsePackage.php			# A common structure for returning data from an API
		baseApiEndpoint.php			# Base class for REST API endpoints
	JS/
		ajaxHandler.js				# Closure providing a common reusable ajax handler
		htmlTable.js				# Closure for dynamically building/updating an html table
```

## How It Works

1. **Frontend**: `index.php` displays the movie table structure with voting buttons
2. **Initial Load**: 
		a.  Get Request is made to `API/JS/index.php` for ajaxHandler.js and one for htmlTable.js 
		b.  `script.js` 
			1.  Builds the initial empty movies table 
			2.  makes a GET request to `API/Movie/index.php` to fetch all movies and updates the table with the result set
3. **AJAX Request**: When a user clicks a vote button, the ajaxHandler object makes a POST request to `API/Movie/index.php`
4. **Backend**: The `moviesAPI` handler validates input, instantiates the business logic `Movie.php` and returns a standardized JSON response
5. **Update UI**: The table is automatically updated and reordered without refreshing the page.  Rows are added/trimmed as needed.

## Technologies Used

- PHP 7.4+
- MySQL
- jQuery 3.7.1
- JavaScript
- HTML5/CSS3
