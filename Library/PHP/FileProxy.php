<?php
/*
	class FileProxy

	Provides mechanisms and sanity checks for serving up files that are outside the webservers public root

	Basic Useage
	$fp = new FileProxy([[file=>...,path=>...],...]);

	if($fp->checkFile(file))	// This must be done before getFile, or else it will only return false and populate fp.error
	{
		$contents=$fp->getFile();

		if($contents) echo $contents;
		else	// file read failed
		{
			// handle error
		}
	}
	else	//File invalide/not allowed
	{
		//handle error
	}
*/
	class FileProxy
	{
		private $allowedFiles = [];														// [{path: string,file:string},...]
		private $currentFile;															// file set to be read
		private $fileSize;																// Size of file to be read
		private $lastModified;															// last modified timestamp of file
		public $error = "";																// Holds the last error
		

		/*
			default constructor
			Sets allowed files and ensures all private variables are properly initialized via the reset function
		*/
		function __construct($files)
		{
			$this->allowedFiles=$files;
			$this->reset();
		}
		/*
			getSize()
			returns the size of the file that was most recently read
		*/
		public function getSize()
		{
			return $this->fileSize;
		}
		/*
			getLastModified
			returns the last modified timestamp of the file that was most recently read
		*/
		public function getLastModified()
		{
			return $this->lastModified;
		}
		/*
			checkFile(file)
			Performs validation and sanity chesks on the file.  
			Ensures
				filename is not empty
				filename does not have embedded directory nagivation
				file is white listed
				file exists
			if file fails validation it will set the public property error with an error message and returns false
		
			returns true/false
		*/
		public function checkFile($file)			
		{
			$this->reset();																// Resets key varaibles
			$fullPath="";
			if(empty($file)) 															// Validate file
			{
				return $this->error('Error: File name cannot be empty');
			}
			else 
			{
				if(basename($file) !== $file)
					return $this->error("Error: Invalid filename");
				$match = array_filter($this->allowedFiles, function ($row) use ($file) {
					return $row['file'] === $file;
				});
				
				if(empty($match))														// Validate access to file is allowed
				{
					return $this->error('Error: File not allowed or not found');
				}
				$match=reset($match);
				$fullPath = rtrim($match['path'], '/') . '/' . $match['file'];
				
				if( !file_exists($fullPath) )											// Validate file exist
				{
					return $this->error('Error: File not allowed or not found');
				}
			}

			$this->currentFile = $fullPath;
			$this->fileSize = filesize($this->currentFile);
			
			$this->lastModified = filemtime($this->currentFile); 
			return true;
		}
		/*
			getFile()
			reads and returns the file contents as a string.  If there is an error reading the file it will return false and set the error property with the error message
			
			returns string || false
		*/
		public function getFile()
		{
			if($this->currentFile === null)
				return $this->error('Error: no valid file provided');
			
			if( ($contents = file_get_contents($this->currentFile)) === false ) 
				return $this->error("Error: unable to read file");
			
			return $contents;
		}
		/*
			error($desc)
			private helper function for handling errors.  Sets the property error with the give description and returns false;
			Input:
				$descr - string - error message
		*/
		private function error($desc)
		{
			$this->error=$desc;
			return false;
		}
		/*
			reset()
			Resets properties related to the file that is read to their initial state
		*/
		private function reset()
		{
			$this->fileSize=-1;
			$this->currentFile=null;
			$this->lastModified=null;
		}
	}
?>