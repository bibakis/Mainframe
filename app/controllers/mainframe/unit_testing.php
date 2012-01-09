<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * This class performs automated tests for all parts of your Mainframe application
 */
class Unit_testing extends CI_Controller {

	private $controllers = array();

	
	function __construct()
	{
		parent::__construct();
		$this->load->library('unit_test');
	}
	
	
	function index()
	{
		$this->load->theme('mainframe');
		$this->load->view('unit_testing/home');
	}

	
	function _init()
	{
		$this->controllers = array(
			'unit_testing' => array($this->index() => 'is_string'),
		);
	}
	
	
	
	
	function controllers()
	{
		$this->_init();

		$this->unit->run($this->index(), 'is_string');
		echo $this->unit->report();
		
	}
	
	

}