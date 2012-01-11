<?php

class Demo_Lib {
	
	var $demo;
		
	function __construct(){
		$this->demo = 'The plugin library has been succesfully loaded.';
	}
	
	function test_library()
	{
		return $this->demo;
	}
}