<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * Every little thing that doesn't fit in any other helper
 */


/*
 * Checks if a link should have the class 'active'.
 * It does so by checking if the uri starts with the $segment_value
 * This is useful mostly in menus where we want to have the active item highlighted.
 * 
 * For example to check if the uri starts with 'arrangement' do a active_class('arrangement')
 * This will return 'active' if it's true  
 * 
 * @param		string		$segment_value		The start of the URL you want to check against
 * @param		bool		$return				If set, the function returns the class instead of echoing it
 * @returns		string							Echoes 'active' on success, returns false on failure
 */
function active_class($segment_value, $return = FALSE){
	$ci =& get_instance();

	if (strpos($ci->uri->uri_string(), $segment_value) === 0)
	{
		if($return)
		{
			return 'active';
		}
		echo 'active';
	}
	else {
		return false;
	}
}

/**
 * Simplifies the form validation
 */
function validate($rules = array())
{
	$ci =& get_instance();

	// If there are no rules set for the validation then just check
	// if there was a form submited or not
	if (count($rules) == 0){
		if (count($_POST) > 0){
			return TRUE;
		}
		else {
			return FALSE;
		}
	}

	$ci->load->library('form_validation');

	foreach ($rules as $rule){
		$ci->form_validation->set_rules($rule, $rule, 'required');
	}

	return $ci->form_validation->run();
}



/*
 * Returns the current datetime formated for mysql's datetime field
 */
function now_mysql(){
	return(date ("Y-m-d H:i:s"));
}





// Shorthands !

/*
 * Shorthand for $this->input->post()
 */
function post($item = false)
{
	$ci = & get_instance();
	return $ci->input->post($item);
}


/*
 * Shorthand for $this->session->userdata('something')
 */
function session($item = false)
{
	$ci =& get_instance();
	return $ci->session->userdata($item);
}