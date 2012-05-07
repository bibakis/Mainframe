<?php
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

 