<?php
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
 * Returns a string that is properly escaped so it won't break your javascript
 * 
 * This helper function properly escapes the string and makes sure the js statement unescapes it
 * on page render so your onclick handlers won't break
 * 
 * @param unknown_type $string
 */
function js_escape($string)
{
	return 'unescape(\''.addslashes($string).'\')';
}

function body_style()
{
	if(config('text_direction')=='ltr')
	{
		return 'text-align:left !important;';
	}
	return 'text-align:right !important;';
}

/**
 * Shows the provided time according to the application setting
 * @param int $time
 */
function date_show($time = FALSE)
{	
	
	if(!trim($time) OR !strtotime($time))
	{
		return '';
	}
	$segments = explode(' ',$time);
	
	if(!isset($segments[0]) OR ! $segments[0])
	{
		$segments[0] = date('Y-m-d', 0);;
	}
	
	if( ! isset($segments[1]) OR ! $segments[1])
	{
		$segments[1] = date('H:i:s.u',0);;
	}
	
	$time = $segments[0].' '.$segments[1];

	$time = substr($time, 0, 22);
	$time = DateTime::createFromFormat('Y-m-d H:i:s.u', $time);
	
	return $time->format(config('date_format'));
}

/**
 * Shows the provided date according to the application setting
 * @param int $time
 */
function time_show($time = FALSE)
{	
	if(!trim($time) OR !strtotime($time))
	{
		return '';
	}
	$segments = explode(' ',$time);
	
	if(!isset($segments[0]) OR ! $segments[0])
	{
		$segments[0] = date('Y-m-d', 0);;
	}
	
	if( ! isset($segments[1]) OR ! $segments[1])
	{
		$segments[1] = date('H:i:s.u',0);;
	}
	
	$time = $segments[0].' '.$segments[1];

	$time = substr($time, 0, 22);
	$time = DateTime::createFromFormat('Y-m-d H:i:s.u', $time);
	
	return $time->format(config('time_format'));
}

function timestamp_show($timestamp = FALSE)
{

	if(!trim($timestamp) OR !strtotime($timestamp))
	{
		return '';
	}
	$segments = explode(' ',$timestamp);
	
	if(!isset($segments[0]) OR ! $segments[0])
	{
		$segments[0] = date('Y-m-d',0);
	}
	
	if( ! isset($segments[1]) OR ! $segments[1])
	{
		$segments[1] = date('H:i:s.u',0);
	}
	
	$timestamp = $segments[0].' '.$segments[1];

	$timestamp = substr($timestamp, 0, 22);
	$timestamp = DateTime::createFromFormat('Y-m-d H:i:s.u', $timestamp);
	return $timestamp->format(config('date_format').' '.config('time_format'));
}

/**
 * Receives a string formatted in the application default format and 
 * converts it to to DB-friendly format
 * 
 *  @param $timestamp a timestamp string formatted according to the applications settings
 */
function db_date_parse($timestamp)
{
	if(! trim($timestamp))
	{
		return '';
	}
	
	$segments = explode(' ', $timestamp);
	if(!isset($segments[0]) || !trim($segments[0]))
	{
		$segments[0] = date(config('date_format'), 0);
	}
	
	if(!isset($segments[1]) || !trim($segments[1]))
	{
		$segments[1] = date(config('time_format'), 0);
	}
	
	
	
	$timestamp = implode(' ', $segments);
	$datetime_obj = DateTime::createFromFormat(config('date_format').' '.config('time_format'), $timestamp);
	if( ! $datetime_obj)
	{
		return '';
	}
	return $datetime_obj->format('Y-m-d');
}

/**
 * Converts the supplied time string from the application default format
 * to DB-friendly format
 * 
 *  @param $timestamp a timestamp string formatted according to the applications settings
 */
function db_time_parse($timestamp)
{
	if(! trim($timestamp))
	{
		return '';
	}
	
	$segments = explode(' ', $timestamp);
	if(!isset($segments[0]) || !trim($segments[0]))
	{
		$segments[0] = date(config('date_format'), 0);
	}
	
	if(!isset($segments[1]) || !trim($segments[1]))
	{
		$segments[1] = date(config('time_format'), 0);
	}
	
	
	
	$timestamp = implode(' ', $segments);
	$datetime_obj = DateTime::createFromFormat(config('date_format').' '.config('time_format'), $timestamp);
	if( ! $datetime_obj)
	{
		return '';
	}
	
	return $datetime_obj->format('H:i:s');
}

/**
 * Converts the supplied datetime string from the application default format
 * to DB-friendly format
 * 
 * @param $timestamp a timestamp string formatted according to the applications settings
 */
function db_datetime_parse($timestamp)
{
	
	if(! trim($timestamp))
	{
		return '';
	}
	
	$segments = explode(' ', $timestamp);
	if(!isset($segments[0]) || !trim($segments[0]))
	{
		$segments[0] = date(config('date_format'), 0);
	}
	
	if(!isset($segments[1]) || !trim($segments[1]))
	{
		$segments[1] = date(config('time_format'), 0);
	}
	
	
	
	$timestamp = implode(' ', $segments);
	$datetime_obj = DateTime::createFromFormat(config('date_format').' '.config('time_format'), $timestamp);
	if( ! $datetime_obj)
	{
		return '';
	}

	return $datetime_obj->format('Y-m-d H:i:s');
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

/*
 * Returns an array with breadcrumb links
 */
function breadcrumb()
{
	$ci =& get_instance();

	$current_path = $ci->uri->uri_string();
	
	// Break down the current path
	$parts = explode('/',$current_path);
	
	// Breadcrumb items
	$items = array();
	
	$sections = $ci->db->get('sections');
	
	$i = 0;
	for ($i = count($parts); $i > 0; $i--)
	{
		$path = implode('/', $parts);
		foreach ($sections->result() as $section)
		{
			if (($section->path == $path) OR ($section->path == ($path.'/index')))
			{
				$items[$i] = array(
					'title'		=> $section->title,
					'path'		=> $section->path
				);
			}
		}
		$last = array_pop($parts);
		if ($last == 'index')
		{
			array_pop($parts);
			$i--;
		}
	} 
	$items = array_reverse($items);
	
	return $items;
	
}



