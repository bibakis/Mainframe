<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dummy extends MY_Controller
{
	function index()
	{		
		$this->load->view('dummy');
	}
	
}