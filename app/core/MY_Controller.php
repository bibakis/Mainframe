<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class MY_Controller extends CI_Controller{
	var $modular = false;
	
	function __construct()
	{
		parent::__construct();
		$segments = $this->uri->segment_array();
		
		
		//If a module path with the name of the 1st uri segment exist, it means that the 
		//controller being loaded is a modular one
		
		if(count($segments)>0)
		{
			if(is_dir(APPPATH.'/modules/'.$segments[1]))
			{
				$this->load->add_package_path(APPPATH.'modules/'.$segments[1], TRUE);
				die(self);
			}
		}
	}
	
	
}