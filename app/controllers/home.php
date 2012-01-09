<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends MY_Controller {

	function index()
	{
		//echo ($this->uri->anchor());

		try {
			$test = 1/0;
		}
		catch (Exception $e){
			echo 'Caught exception: ',  $e->getMessage(), "\n";
		}
		
		$this->load->theme('demo');
		$this->load->view('home');
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */