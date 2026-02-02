<?php
// Project root constant
define('PROJECT_ROOT','c:/prod/');	// Update this to parent of the bebserver public directory 

require_once PROJECT_ROOT.'Library/PHP/dbHandler.php';
require_once PROJECT_ROOT.'Library/PHP/Utils/helperFunctions.php';

// Database configuration
$dbAuths['movies_app']=new dbAuth(
	'localhost',					// Database Host
	'root',							// Database User
	'',								// Database Pass
	'movies_app'					// Database Name - DO NOT Change unless you also change schema.sql. schema.sql uses this name.
);


?>
