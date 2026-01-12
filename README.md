# Movie Voting App

A simple PHP application that allows users to vote on movies with real-time updates using AJAX.

## Features

- Display movies in a ranked table
- Thumbs up/down voting system
- Real-time vote count updates without page refresh
- Automatic reordering by net votes (upvotes - downvotes)
- Smooth animations and modern UI

## Setup Instructions

### 1. Database Setup

Import the database schema:

```bash
mysql -u root -p < schema.sql
```

Or manually run the SQL commands in `schema.sql` through phpMyAdmin or MySQL Workbench.

### 2. Configure Database Connection

Edit `config.php` and update the database credentials if needed:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'movies_app');	# If changing the name of the db schema, the sql in schema.sql will need to be updated to reflect this.
```

### 3. Install the PHP Code

- Place all files in your web server directory (IE: `C:\prod\www\Movies`)

### 4. Run the Application
- Open your browser and navigate to `http://localhost/Movies/`
--If steps 1 & 2 completed correctly, main app display will be rendered
--If app detects a setup error, a setup helper page will be rendered

## File Structure

```
Movies/
	index.php                    	# Main page with movie table
	schema.sql                   	# Database schema and sample data
	API/
		Movie/
			index.php            	# API endpoint for handling votes
	Includes/
		config.php					# Database configuration & Db test helper function
		Assets/						# Art assets
			thumbsup.png
			thumbsdown.png
		Objects/
			Movie.php				# Object with functions to fetch from/update Database
		CSS/
			movie.css            	# Styling for the application
		JS/
			script.js            	# jQuery-based AJAX voting functionality
		Templates/
			main.php				# Main application HTML
			setup.php				# Setup helper page
```

## How It Works

1. **Frontend**: `index.php` displays the movie table structure with voting buttons
2. **Initial Load**: On page load, `script.js` makes a GET request to `API/Movie/index.php` to fetch all movies
3. **AJAX Request**: When a user clicks a vote button, jQuery sends a POST request to `API/Movie/index.php`
4. **Backend**: The API inserts a vote transaction and update the vote count in the database and returns the updated movie list
5. **Update UI**: The table is automatically updated and reordered without refreshing the page

## Technologies Used

- PHP 7.4+
- MySQL
- jQuery 3.7.1
- JavaScript
- HTML5/CSS3
