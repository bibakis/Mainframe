<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * Functions related to user interface
 * These functions intentionaly echo their output most of the times
 * instead of returning it. This is because they are aimed exclusively 
 * at the user interface and should not be used to format text or presentation
 * of other parts of the application (for example email reports).
 */

/*
 * Checks if a link should have the class 'active'.
 * It does so by checking if the uri starts with the $segment_value
 * This is useful mostly in menus where we want to have the active item highlighted.
 * 
 * If an empty string is provided as a $segment_value the function returns 'active' if
 * the browser is currently in the index function of the default controller
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
	
	//Special case: No segment is provided, or the provided segment is the name of the default helper
	//and we are in the application root, so the class should be active
	if(  (! $segment_value || ($ci->router->fetch_class() === $segment_value)) &&
		 ($ci->router->fetch_class() === $ci->router->routes['default_controller']) && 
		 ($ci->router->fetch_method() === 'index'))
	{
		if($return)
		{
			return 'active';
		}
		else
		{
			echo'active';
			return TRUE;
		}
		
	}	
	
	//If the provided segment matches the current url, return active
	//All the other cases are being handled here
	if (strpos($ci->uri->uri_string(), $segment_value) === 0)
	{
		if($return)
		{
			return 'active';
		}
		else
		{
			echo 'active';
			return TRUE;
		}
	}
	else 
	{
		return FALSE;
	}
}


/**
 * Returns a string that is properly escaped so it won't break your javascript
 * 
 * This helper function properly escapes the string and makes sure the js statement unescapes it
 * on page render so your onclick handlers won't break
 * 
 * @param string $string
 */
function js_escape($string)
{
	return 'unescape(\''.addslashes($string).'\')';
}


/**
 * Echoes 'disabled="disabled"' id the expression provided evaluates to 'true',
 * returns FALSE if it doesn't
 * 
 * Useful for populating forms with many fields that need to be checked if they are active
 * 
 * @param expression $expression
 */
function ui_disabled($expression)
{
	$e = $expression;
	if(is_callable($expression))
	{
		$e = $expression();
	}
	
	if($e)
	{
		echo 'disabled="disabled"';
		return TRUE;
	}
	
	return FALSE;
	
}


/**
 * Echoes 'checked="checked"' id the expression provided evaluates to 'true',
 * returns FALSE if it doesn't
 * 
 * Useful for populating forms with many checkbox fields that need to be checked
 * 
 * @param expression $expression
 */
function ui_checked($expression)
{
	$e = $expression;
	if(is_callable($expression))
	{
		$e = $expression();
	}
	
	if($e)
	{
		echo 'checked="checked"';
		return TRUE;
	}
	return FALSE;
}



