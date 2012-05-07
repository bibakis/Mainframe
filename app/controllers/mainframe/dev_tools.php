<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dev_Tools extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		
		//Uncomment this line once you have a proper unit_testing config file
		//$this->load->config('unit_testing');
		$this->load->theme('mainframe');
		$this->load->helper('inflector');
	}


	function index(){
		$data = array();
		$this->load->view('dev_tools/main', $data);
	}
	
	/**
	 * Generates a unit testing report for the provided CI_Model file
	 * The methods that are to be tested are declared in the config/unit_testing.php file
	 * 
	 * The config item format is $config['models'][$filename][$method_name]
	 * 
	 * @param string $filename
	 */
	function models_report($filename, $plugin = ''){
		//If a valid plugin name is supplied, make sure to load anything from it's directory
		if($plugin && is_dir(APPPATH.'plugins/'.$plugin))
		{
			$this->load->add_package_path(APPPATH.'plugins/'.$plugin, TRUE);
			$models = $this->config->item($plugin);
			$models = @$models['models'];
		}
		else{
			$models = $this->config->item('models');
		}
		
		$config = $models[$filename];				
		$this->load->library('unit_test');		
		$this->load->model(reset(explode('.',$filename)), 'model');
		
		foreach($config as $function=>$data)
		{
			$this->unit->run(call_user_func_array(array($this->model, $function), $data['in']), $data['out'], $function.'("'.print_r($data['in'], TRUE).'")');
		}
		
		$data = array('reports'=>$this->unit->result());
		$this->load->view('dev_tools/unit_testing/home', $data);
		
	}
	
	
	/**
	 * Generates a unit testing report for the provided library file
	 * The methods that are to be tested are declared in the config/unit_testing.php file
	 * 
	 * The config item format is $config['libraries'][$filename][$method_name]
	 *  
	 * @param string $filename
	 */
	function libraries_report($filename, $plugin = ''){
		//If a valid plugin name is supplied, make sure to load anything from it's directory
		if($plugin && is_dir(APPPATH.'plugins/'.$plugin))
		{
			$this->load->add_package_path(APPPATH.'plugins/'.$plugin, TRUE);
			$libraries = $this->config->item($plugin);
			$libraries = @$libraries['libraries'];
		}else{
			$libraries = $this->config->item('libraries');
		}		
		
		$config = $libraries[$filename];
				
		$this->load->library('unit_test');
		
		$this->load->library(reset(explode('.',$filename)),FALSE ,'lib');
		
		foreach($config as $function=>$data)
		{
			$this->unit->run(call_user_func_array(array($this->lib, $function), $data['in']), $data['out'], $function.'("'.print_r($data['in'], TRUE).'")');
		}
		$data = array('reports'=>$this->unit->result());
		$this->load->view('dev_tools/unit_testing/home', $data);
	}
	
	
	/**
	 * Generates a unit testing report for the provided helper file
	 * The functions that are to be tested are declared in the config/unit_testing.php file
	 * 
	 * The config item format is $config['helpers'][$filename][$function_name]
	 *  
	 * @param string $filename
	 */	
	function helpers_report($filename, $plugin = ''){
		//If a valid plugin name is supplied, make sure to load anything from it's directory
		if($plugin && is_dir(APPPATH.'plugins/'.$plugin))
		{
			$this->load->add_package_path(APPPATH.'plugins/'.$plugin);
			$helpers = $this->config->item($plugin);
			$helpers = @$helpers['helpers'];
		}else{
			$helpers = $this->config->item('helpers');
		}		
		
		$config = $helpers[$filename];
				
		$this->load->helper(str_replace('_helper', '', reset(explode('.',$filename))));

		$this->load->library('unit_test');
		
		foreach($config as $function=>$data)
		{
			$this->unit->run(call_user_func_array($function, $data['in']), $data['out'], $function.'("'.print_r($data['in'], TRUE).'")');
		}
		
		$data = array('reports'=>$this->unit->result());
		$this->load->view('dev_tools/unit_testing/home', $data);
	}


	public function unit_testing($section = FALSE)
	{
		$this->load->helper('interface');
		if(!$section)
		{
			$this->load->view('dev_tools/unit_testing/home');
			return;
		}

		$this->load->helper('file');
		switch ($section)
		{
			case 'helpers':
				$root_path = APPPATH.'helpers';
				break;
			case 'models':
				$root_path = APPPATH.'models';
				break;
			case 'libraries':
				$root_path = APPPATH.'libraries';
				break;
			case 'plugins':
				$this->_plugins();
				return;
				break;
				
			default : redirect(base_url().'mainframe/dev_tools/index');
		}


		$paths = get_filenames($root_path, TRUE);
		$this->load->helper('string');
		foreach($paths as $key=>$path)
		{
			//Remove all non-php files from the array
			if(!ends_with($path, '.php'))
			{
				unset($paths[$key]);
			}

		}
		$paths = array_merge($paths, array());
		$root_path = str_replace('/', '\\', $_SERVER['DOCUMENT_ROOT'].'\\'.$root_path);

		foreach($paths as $path)
		{
			$path_key = str_replace($root_path.'\\', '', $path);
			$function[] = array('path'=>$path_key, 'functions'=>$this->_get_functions($path));
		}

		$data = array(
			'files'	=> $function,
		);

		$this->load->view('dev_tools/unit_testing/home', $data);
	}

	
	function _plugins(){
		$this->load->helper('file');
		$plugins = array();
		$functions = array();
		foreach(get_dir_file_info(APPPATH.'plugins', TRUE) as $key=>$plugin)
		{
			$plugins[$key] = get_filenames(APPPATH.'plugins/'.$key, TRUE);
		}


		foreach($plugins as $name=>&$files)
		{
			foreach($files as $key=>$file)
			{
				$plugin_path = str_replace('/', '\\', $_SERVER['DOCUMENT_ROOT'].'/'.APPPATH.'plugins/'.$name);

				//Remove all the files that are not libraries, models or helpers
				if(!(starts_with($file, $plugin_path.'\\libraries')||
				starts_with($file, $plugin_path.'\\models') ||
				starts_with($file, $plugin_path.'\\helpers')))
				{
					unset($files[$key]);
				}

				//Remove all non-php files
				if(!ends_with($file, '.php'))
				{
					unset($files[$key]);
				}

				if(isset ($files[$key]))
				{
					if(starts_with($files[$key], $plugin_path.'\\libraries'))
					{
						$type = 'library';
					}
					if(starts_with($files[$key], $plugin_path.'\\models'))
					{
						$type = 'model';
					}
					if(starts_with($files[$key], $plugin_path.'\\helpers'))
					{
						$type = 'helper';
					}

					$functions[$name][] = array(
						'type' => $type, 
						'file' => $file, 
						'functions' => $this->_get_functions($file)
					);
				}
			}
			$files = array_merge($files, array());
		}
		//Delete the reference just to be safe
		unset($files);

		//Make sure the keys are properly numbered
		$plugins = array_merge($plugins, array());
		$data = array(
			'files'		=>$plugins,
			'functions'	=>$functions	
		);
		$this->load->view('dev_tools/unit_testing/plugins', $data);
	}



	/**
	 * Returns an array that contains all the functions/methods that exists in the provided file
	 * 
	 * @param string 
	 * @return array
	 */
	function _get_functions($file)
	{
		$contents = read_file($file);
		$out = '';

		foreach(preg_split("/(\r?\n)/", $contents) as $line){
			if(preg_match('/function[\s\n]+(\S+)[\s\n]*\(/', $line))
			{
				$line = preg_replace('(\\{.*?\\})', '', trim($line));
				$out[] = preg_replace('/{/', '', $line);
			}
		}
		return $out;
	}
}