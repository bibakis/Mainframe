<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class MY_URI extends CI_URI {
	
	function hsegment()
	{
	
	}
	
	
	function anchor()
	{
		$ci = &get_instance();
		return $ci->input->cookie('anchor');
	}
	
	
	function huri_string()
	{
		$ci = &get_instance();
		return $this->uri_string().'#'.$ci->input->cookie('anchor');
	}
	
	
}