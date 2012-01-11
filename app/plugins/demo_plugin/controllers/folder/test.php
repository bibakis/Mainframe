<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test extends CI_Controller
{
	function index()
	{	
		$this->load->view('subfolder_view');
	}
	
}