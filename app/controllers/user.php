<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {

	function __construct(){
		parent::__construct();
		
		$this->load->theme('demo');
		$this->load->library('session');
		
		
	}
	
	public function index()
	{
		if (session('user_id'))
		{
			
		}
		
		$this->load->view('home');
	}
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */