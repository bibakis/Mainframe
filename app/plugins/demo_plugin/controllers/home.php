<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller
{
	
	/*
	 * This is just here so you can easily see and test the plugin functionality.
	 * Should be accessible through http://domain.tld/demo_plugin/
	 */
	function index()
	{
		$this->load->theme('demo');
		
		$this->load->helper('demo');
		$this->load->language('demo','english');
		$this->load->library('demo_lib');
		$this->load->model('demo_model');
		
		$data = array(
			'helper'	=> test_helper_function(),
			'language'	=> $this->lang->line('demo_message'),
			'library'	=> $this->demo_lib->test_library(),
			'model'		=> $this->demo_model->model_test(),
		);
		
		$this->load->view('plugin_home',$data);
	}
	
}