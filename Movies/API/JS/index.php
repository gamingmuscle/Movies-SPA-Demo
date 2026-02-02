<?php
/**
 * File proxy for serving Javascript files found in the Library
 * Serves JavaScript files a directory outside the web root
 * Usage: <script src="API/JS/?file=ajaxHandler.js"></script>
 */

require_once __DIR__.'/../../../../src/Movies/config.php';
require_once PROJECT_ROOT.'Library/PHP/FileProxy.php';

// Whitelist of allowed library files for security
$allowedFiles = [
    ['path'=> PROJECT_ROOT.'Library/JS/', 'file'=>'ajaxHandler.js'],
    ['path'=> PROJECT_ROOT.'Library/JS/', 'file'=>'htmlTable.js']
    // Add more library files here as needed
];

$fileProxy = new FileProxy($allowedFiles);
$requestedFile = $_GET['file'] ?? '';

if($fileProxy->checkFile($requestedFile))											// Validate requested file
{
	$lastModified = $fileProxy->getLastModified();
	header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $lastModified) . ' GMT');
	if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= $lastModified)
	{
		http_response_code(304); // Not Modified
        exit;
	}
	else
	{
		$file = $fileProxy->getFile();
		header('Content-Type: application/javascript; charset=utf-8');
		header('Cache-Control: public, max-age=86400'); // Cache for 24 hours
		header("Content-Length: " . $fileProxy->getSize());
		echo $file;
	}
}
else																					// Not found or not allowed
{
	http_response_code(404);
	exit('// Error: File not allowed or not found');
}
    
?>
