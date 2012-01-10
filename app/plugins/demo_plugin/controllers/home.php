<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller
{
	function index()
	{
		$this->load->theme('demo');
		$this->load->view('plugin_home');
	}
	
}