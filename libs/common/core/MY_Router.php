<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Router extends CI_Router {
	
	var $module;		//This variable contains the name of the module the request should be routed to. If empty, load the controller as usual
	
	function __construct(){
		parent::__construct();
	}
	
	/**
	 * 
	 * Fetches the appropriate base path of the controller being currently handled.
	 * Actually returns the APPPATH if the controller doesn't belong to a module,
	 * APPPATH + module path if it does
	 * 
	 * @return string
	 */
	function _get_base_path()
	{
		if($this->module)
		{
			return APPPATH.'modules/'.$this->module.'/';
		}
		else
		{
			return APPPATH;
		}
	}
	
   /**
	* Validates the supplied segments.  Attempts to determine the path to
	* the controller.
	* 
	* Edited to allow modular development
	*
	* @access	private
	* @param	array
	* @return	array
	*/
	function _validate_request($segments)
	{	
			
		//Does a module with the name of $segments[0] exist?
		if(is_dir(APPPATH.'modules/'.$segments[0]))
		{
			$this->module = $segments[0];
			$segments = array_slice($segments,1);
		}
		
		
		
		if (count($segments) == 0)
		{
			return $segments;
		}
		
		// Does the requested controller exist in the root folder?
		if (file_exists($this->_get_base_path().'controllers/'.$segments[0].'.php'))
		{
			return $segments;
		}
		
		
	
		// Is the controller in a sub-folder?
		if (is_dir($this->_get_base_path().'controllers/'.$segments[0]))
		{
			// Set the directory and remove it from the segment array
			$this->set_directory($segments[0]);
			$segments = array_slice($segments, 1);
	
			if (count($segments) > 0)
			{
				// Does the requested controller exist in the sub-folder?
				if ( ! file_exists($this->_get_base_path().'controllers/'.$this->fetch_directory().$segments[0].'.php'))
				{
					if ( ! empty($this->routes['404_override']))
					{
						$x = explode('/', $this->routes['404_override']);
	
						$this->set_directory('');
						$this->set_class($x[0]);
						$this->set_method(isset($x[1]) ? $x[1] : 'index');
	
						return $x;
					}
					else
					{
						show_404($this->fetch_directory().$segments[0]);
					}
				}
			}
			else
			{
				// Is the method being specified in the route?
				if (strpos($this->default_controller, '/') !== FALSE)
				{
					$x = explode('/', $this->default_controller);
	
					$this->set_class($x[0]);
					$this->set_method($x[1]);
				}
				else
				{
					$this->set_class($this->default_controller);
					$this->set_method('index');
				}
	
				// Does the default controller exist in the sub-folder?
				if ( ! file_exists($this->_get_base_path().'controllers/'.$this->fetch_directory().$this->default_controller.'.php'))
				{
					$this->directory = '';
					return array();
				}
	
			}
				
			return $segments;
		}
	}
}