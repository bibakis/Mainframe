<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Router extends CI_Router {
	private $plugin = ''; 
	
	function __construct(){
		parent::__construct();
		
	}
	
	function fetch_plugin()
	{
		return $this->plugin;
	}
	
	function set_plugin($val)
	{
		$this->plugin = $val;
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
		$segments = $this->uri->segment_array();
		
		//It is possible for the $this->uri->segment_array() array not to be reindexed when 
		//this function is called, so this check is necessary
		if(array_key_exists(0, $segments))
		{
			$this->uri->_reindex_segments();
		}
		
		$segment = $this->uri->segment(1);
		
		if(is_dir(APPPATH.'plugins/'.$segment) && $segment)
		{
			
			$result =  APPPATH.'plugins/'.$segment.'/';			
			
		}
		else 
		{
			$result = APPPATH;
		}
				
		return $result;
		
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
		if(is_dir(APPPATH.'plugins/'.$segments[0]))
		{
			$this->set_plugin($segments[0]);
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
		$a = $segments[0];
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
						show_404($this->fetch_plugin().$this->fetch_directory().$segments[0]);
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
					//If no, there is nothing to show but a 404
					show_404();
					return array();
				}
	
			}
				
			return $segments;
		}
		
		// If we've gotten this far it means that the URI does not correlate to a valid
		// controller class.  We will now see if there is an override
		if ( ! empty($this->routes['404_override']))
		{
			$x = explode('/', $this->routes['404_override']);
		
			$this->set_class($x[0]);
			$this->set_method(isset($x[1]) ? $x[1] : 'index');
		
			return $x;
		}
		
		
		// Nothing else to do at this point but show a 404
		show_404($segments[0]);
	}
}