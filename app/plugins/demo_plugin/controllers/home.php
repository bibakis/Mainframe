<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MyIndex extends MY_Controller
{
	function index()
	{
		$this->load->view('mod_view');
	}
	
}