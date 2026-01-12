<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movie Voting App - Setup Required</title>
	<link type="text/css" rel="stylesheet" href="Includes/CSS/movie.css"/>
	<link type="text/css" rel="stylesheet" href="Includes/CSS/setup.css"/>
</head>
<body>
    <div class="container">
		<h1>Movie Voting App - Setup</h1>

		<!-- Status Indicator -->
		<div class="setup-status <?= $test['success'] ? 'status-success' : 'status-error' ?>">
			<h2><?= $test['success'] ? 'Connection Successful' : 'Database Connection Failed' ?></h2>
			<p><strong><?= htmlspecialchars($test['msg']) ?></strong></p>
		</div>

		<!-- Database Connection Details -->
		<div class="setup-section">
			<h2>Database Connection Details</h2>
			<p>The application is attempting to connect with these credentials:</p>
			<table id="configTable" class="setup-table">
				<thead>
					<tr>
						<th>Setting</th>
						<th>Value</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>DB_HOST</td>
						<td><code><?= htmlspecialchars(DB_HOST) ?></code></td>
					</tr>
					<tr>
						<td>DB_USER</td>
						<td><code><?= htmlspecialchars(DB_USER) ?></code></td>
					</tr>
					<tr>
						<td>DB_PASS</td>
						<td><code><?= str_repeat('•', strlen(DB_PASS)) ?></code> <small>(hidden for security)</small></td>
					</tr>
					<tr>
						<td>DB_NAME</td>
						<td><code><?= htmlspecialchars(DB_NAME) ?></code></td>
					</tr>
				</tbody>
			</table>
		</div>

		<!-- Setup Instructions -->
		<div class="setup-section">
			<h2>Setup Instructions</h2>
			<p>Follow these steps to get your Movie Voting App running:</p>
			<ol class="setup-steps">
				<li><strong>Ensure MySQL Server is running</strong>
				</li>
				<li><strong>Create the Database</strong>
					<ul>
						<li>Option A: <code>mysql -u root -p &lt; schema.sql</code></li>
						<li>Option B: Import <code>schema.sql</code> via phpMyAdmin</li>
					</ul>
				</li>
				<li><strong>Update Database Credentials</strong>
					<ul>
						<li>Edit <code>Includes/config.php</code></li>
						<li>Update DB_HOST, DB_USER, DB_PASS, and DB_NAME if needed</li>
					</ul>
				</li>
				<li><strong>Refresh This Page</strong>
				</li>
			</ol>
		</div>

		<!-- Common Issues -->
		<div class="setup-section">
			<h2>Common Issues & Solutions</h2>
			<ul class="troubleshooting-list">
				<li>
					<strong>Error: Access denied for user</strong>
					<p>Check that DB_USER and DB_PASS are correct in <code>Includes/config.php</code></p>
					<p>Ensure that DB_USER has atleast SELECT & UPDATE permissions on TABLE: movies</p>
				</li>
				<li>
					<strong>Error: Unknown database</strong>
					<p>The database hasn't been created. Run <code>schema.sql</code> to create the <code>movies_app</code> database</p>
				</li>
				<li>
					<strong>Error: Can't connect to MySQL server</strong>
					<p>Check that DB_HOST is correct</p>
					<p>Esnure MySQL service is running.</p>
				</li>
				<li>
					<strong>Error: Connection refused</strong>
					<p>Check that DB_HOST is correct.</p>
				</li>
			</ul>
		</div>

		<!-- File Location -->
		<div class="setup-section">
			<h2>Important Files</h2>
			<ul>
				<li><strong>Documentation:</strong> <a href="README.md">README.md</a></li>
				<li><strong>Database Schema:</strong> <a href="schema.sql">schema.sql</a></li>
				<li><strong>Configuration:</strong> <code>Includes/config.php</code></li>				
			</ul>
		</div>
    </div>
</body>
</html>
