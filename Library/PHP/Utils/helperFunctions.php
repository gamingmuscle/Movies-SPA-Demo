<?php
	namespace  Utils;

	function refValues(array &$arr) 
	{
		$refs = [];
		foreach ($arr as $key => &$value) 
		{
			$refs[$key] = &$value;
		}
		unset($value);
		//print_r($refs);
		return $refs;
	}
?>