<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'movies_app');

function testDb()
{
	$dbName=DB_NAME;
	try
	{
		$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);
	}
	catch(Exception $ex)
	{
		return ['success'=>false,'msg'=>$ex->getMessage()];
	}
	if(!$conn) return ['success'=>false,'msg'=>'DB failed connection'];
	$queryStr="select SCHEMA_NAME from information_schema.schemata where SCHEMA_NAME =?";
	$stmt = $conn->prepare($queryStr);
	$stmt->bind_param("s",$dbName);
	$stmt->execute();
	$stmt->store_result();

	if($stmt->num_rows <= 0) return ['success'=>false, 'msg'=>'Schema not found'];
	return ['success'=>true,'msg'=>'Connected & Schema found.'];
}
?>
