<?php
	require_once __DIR__."/../../src/Movies/config.php";

	$test=dbHandler::testDB($dbAuths['movies_app']->dbHost, $dbAuths['movies_app']->dbUser, $dbAuths['movies_app']->dbPass,$dbAuths['movies_app']->dbName);

	if(!$test['success'] || isset($_GET['setup']))						// Test for error or URL param to see setup
		$file = PROJECT_ROOT."src/Movies/Templates/setup.php";
	else																			// Display main app
		$file = PROJECT_ROOT."src/Movies/Templates/main.php";
		
	if(!file_exists($file))																			// Ensure that template can be found
	{																								// Ut Oh!
		echo 
		'<div>
			Ut oh!
		</div>';
	}
	else
		require_once $file
?>
