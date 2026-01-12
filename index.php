<?php
	include_once "Includes/config.php";
	if(!($test=testDB())['success'] || isset($_GET['setup']))						// Test for error or URL param to see setup
		include_once "Includes/Templates/setup.php";
	else												// Display main app
		include_once "Includes/Templates/main.php";
?>

