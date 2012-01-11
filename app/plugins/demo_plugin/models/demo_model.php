<?php
class Demo_Model extends CI_Model{
	function __construct()
	{
		parent::__construct();
	}
	
	function model_test()
	{
		return 'Plugin model is loaded and working';
	}
}