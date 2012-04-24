<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Assets_pipeline 
{

	function __construct()
	{
		$this->ci = &get_instance();
		log_message('debug', 'Assets_pipeline class Initialized');
	}
	
	/*
	 * Parses all assets filenames, evaluates them and returns the appropriate tags
	 */
	function render($css_files = array(), $js_files = array()){

		$output = '';
		
		// Load the necessary compression libraries if needed
		if ($this->ci->config->item('compress_css'))
		{
			require_once '/libs/php/cssmin.php';
			$CSSmin = new CSSmin;
		}
		if ($this->ci->config->item('compress_js'))
		{
			require_once '/libs/php/JSMin.php';
		}
		
		

		if ($this->ci->config->item('combine_css')){
			
			// Determine the file names for combined file
			$hash = $this->ci->config->item('cjsuf');
			foreach ($css_files as $file)
			{
				$hash .= $file;
			}
			
			$hash = sha1($hash);
			$final_css_file = $_SERVER['DOCUMENT_ROOT'].'/themes/cache/'.sha1($hash).'.css';
			
			if (!file_exists($final_css_file))
			{
				$css_code = '';
				foreach ($css_files as $file)
				{
					if ($this->ci->config->item('compress_css'))
					{
						$css_code .= $CSSmin->run(file_get_contents($_SERVER['DOCUMENT_ROOT'] . $file));
					}
					else 
					{
						$css_code .= file_get_contents($_SERVER['DOCUMENT_ROOT'] . $file);
					}
					
				}


				file_put_contents($final_css_file, $css_code);
			}
			
			$output .= '<link rel="stylesheet" type="text/css" href="'. base_url() . 'themes/cache/'.sha1($hash).'.css" media="screen" />';			

		}
		else {
			foreach ($css_files as $file)
			{
				$hash = sha1($this->ci->config->item('cjsuf') . $file);
				$final_css_file = $_SERVER['DOCUMENT_ROOT'].'/themes/cache/'.$hash.'.css';
				
				if ($this->ci->config->item('compress_css') && !file_exists($final_css_file))
				{
					file_put_contents($final_css_file, $CSSmin->run(file_get_contents($_SERVER['DOCUMENT_ROOT'].$file)));
					$output .= '<link rel="stylesheet" type="text/css" href="'. base_url() . 'themes/cache/'.$hash.'.css" media="screen" />';
				}
				else
				{
					$output .= '<link rel="stylesheet" type="text/css" href="'. substr(base_url(),-1) . $file .'" media="screen" />';
				}
			}

		}
		
		

		return $output;
	}
	
	/*
	 * Returns the tags needed for included CSS loaded by $this->load->css() from various parts of the application
	 * If needed also merges all file into one, or even minifies the code
	*/
	function render_css($css_files = array())
	{
		$output = '';
		
		// Load the necessary compression libraries if needed
		if ($this->ci->config->item('compress_css'))
		{
			require_once '/libs/php/cssmin.php';
			$CSSmin = new CSSmin;
		}		
		
		if ($this->ci->config->item('combine_css')){
				
			// Determine the file names for combined file
			$hash = $this->ci->config->item('cjsuf');
			foreach ($css_files as $file)
			{
				$hash .= $file;
			}
				
			$hash = sha1($hash);
			$final_css_file = $_SERVER['DOCUMENT_ROOT'].'/themes/cache/'.sha1($hash).'.css';
				
			if (!file_exists($final_css_file))
			{
				$css_code = '';
				foreach ($css_files as $file)
				{
					if ($this->ci->config->item('compress_css'))
					{
						$css_code .= $CSSmin->run(file_get_contents($_SERVER['DOCUMENT_ROOT'] . $file));
					}
					else
					{
						$css_code .= file_get_contents($_SERVER['DOCUMENT_ROOT'] . $file);
					}
						
				}
		
		
				file_put_contents($final_css_file, $css_code);
			}
				
			$output .= '<link rel="stylesheet" type="text/css" href="'. base_url() . 'themes/cache/'.sha1($hash).'.css" media="screen" />';
		
		}
		else {
			foreach ($css_files as $file)
			{
				$hash = sha1($this->ci->config->item('cjsuf') . $file);
				$final_css_file = $_SERVER['DOCUMENT_ROOT'].'/themes/cache/'.$hash.'.css';
		
				if ($this->ci->config->item('compress_css') && !file_exists($final_css_file))
				{
					file_put_contents($final_css_file, $CSSmin->run(file_get_contents($_SERVER['DOCUMENT_ROOT'].$file)));
					$output .= '<link rel="stylesheet" type="text/css" href="'. base_url() . 'themes/cache/'.$hash.'.css" media="screen" />';
				}
				else
				{
					$output .= '<link rel="stylesheet" type="text/css" href="'. substr(base_url(),0,-1) . $file .'" media="screen" />';
				}
			}
		
		}
		
		return $output;
	}
	
	
	
	/*
	 * Returns the tags needed for included JS loaded by $this->load->js() from various parts of the application
	*/
	function render_js($js_files = array())
	{
		if ($this->ci->config->item('compress_js'))
		{
			require_once '/libs/php/JSMin.php';
		}
		
		
	}
	
	
	
	// OLD
	function render_combined($css_files = array(), $js_files = array()){
		$this->ci->load->helper('file_helper');
		$local_list = array('css' => array(), 'js' => array());
		$remote_list = array('css' => array(), 'js' => array());
		
		$css_count = 0;
		$js_count = 0;
		
		foreach ($css_files as $file){
			// Is it a local file ?
			if ((substr($file, 0,7) != 'http://') && (substr($file, 0,8) != 'https://'))
			{
				if ($contents = file_get_contents($file))
				{
				
				}
			}
			// If it's a remote file add it to the remote queue
			else 
			{
				$remote_list['css'][] = $file;
			}
			
			$css_count++;
		}
		
		
		foreach ($js_files as $file){
			// Is it a local file ?
			if ((substr($file, 0,7) != 'http://') && (substr($file, 0,8) != 'https://'))
			{
				if ($contents = file_get_contents($file))
				{

				}
			}
			// If it's a remote file add it to the remote queue
			else 
			{
				$remote_list['js'][] = $file;
			}
			
			$js_count++;
		}
	
		$output = '';
		// Process the queue
		// Remote files are processed first as they are commonly CDN hosted essential files like jQuery
		foreach ($remote_list['css'] as $remote_file){
			$output .= '<link rel="stylesheet" type="text/css" href="'.$remote_file.'" media="screen" />';
		}
		
		foreach ($remote_list['js'] as $remote_file){
			$output .= '<script type="text/javascript" src="'.$remote_file.'"></script>';
		}

		foreach ($local_list['css'] as $type => $local_file){
			$output .= '<link rel="stylesheet" type="text/css" href="'.base_url().$local_file.'" media="screen" />';
		}
			
		foreach ($local_list['js'] as $type => $local_file){
			$output .= '<script type="text/javascript" src="'.base_url().$local_file.'"></script>';
		}
			
		log_message('debug', $css_count." CSS files loaded by the loader class.");
		log_message('debug', $js_count." Javascript files loaded by the loader class.");
			
		return $output;

	}
	
	// OLD
	function render_seperate($css_files = array(), $js_files = array()){
		$this->ci->load->helper('file_helper');
		$local_list = array('css' => array(), 'js' => array());
		$remote_list = array('css' => array(), 'js' => array());
	
		$css_count = 0;
		$js_count = 0;
	
		foreach ($css_files as $file){
			// Is it a local file ?
			if ((substr($file, 0,7) != 'http://') && (substr($file, 0,8) != 'https://'))
			{
				if ($contents = file_get_contents($file))
				{
					$hash = sha1($file.$this->ci->config->item('cjsuf'));
						
					// Is the target file already in place ?
					$target_path = 'themes/cache/'.$hash.'.css';
					if (file_exists($target_path))
					{
						$local_list['css'][] = $target_path;
					}
					// If not, create it
					else
					{
						if (write_file($target_path, $contents))
						{
							$local_list['css'][] = $target_path;
						}
						// Target file could not be created, show the original file and log the error
						else
						{
							$local_list['css'][] = $file;
							log_message('error', "CSS file failed to write to cache, original file loaded: ".$css_file);
						}
	
					}
				}
				else
				{
					log_message('error', "CSS file failed to load: ".$css_file);
				}
			}
			// If it's a remote file add it to the remote queue
			else
			{
				$remote_list['css'][] = $file;
			}
				
			$css_count++;
		}
	
	
		foreach ($js_files as $file){
			// Is it a local file ?
			if ((substr($file, 0,7) != 'http://') && (substr($file, 0,8) != 'https://'))
			{
				if ($contents = file_get_contents($file))
				{
					$hash = sha1($file.$this->ci->config->item('cjsuf'));
						
					// Is the target file already in place ?
					$target_path = 'themes/cache/'.$hash.'.js';
					if (file_exists($target_path))
					{
						$local_list['js'][] = $target_path;
					}
					// If not, create it
					else
					{
						if (write_file($target_path, $contents))
						{
							$local_list['js'][] = $target_path;
						}
						// Target file could not be created, show the original file and log the error
						else
						{
							$local_list['js'][] = $file;
							log_message('error', "Javascript file failed to write to cache, original file loaded: ".$js_file);
						}
	
					}
				}
				else
				{
					log_message('error', "Javascript file failed to load: ".$js_file);
				}
			}
			// If it's a remote file add it to the remote queue
			else
			{
				$remote_list['js'][] = $file;
			}
				
			$js_count++;
		}
	
		$output = '';
		// Process the queue
		// Remote files are processed first as they are commonly CDN hosted essential files like jQuery
		foreach ($remote_list['css'] as $remote_file){
			$output .= '<link rel="stylesheet" type="text/css" href="'.$remote_file.'" media="screen" />';
		}
	
		foreach ($remote_list['js'] as $remote_file){
			$output .= '<script type="text/javascript" src="'.$remote_file.'"></script>';
		}
	
		foreach ($local_list['css'] as $type => $local_file){
			$output .= '<link rel="stylesheet" type="text/css" href="'.base_url().$local_file.'" media="screen" />';
		}
			
		foreach ($local_list['js'] as $type => $local_file){
			$output .= '<script type="text/javascript" src="'.base_url().$local_file.'"></script>';
		}
			
		log_message('debug', $css_count." CSS files loaded by the loader class.");
		log_message('debug', $js_count." Javascript files loaded by the loader class.");
			
		return $output;
	}
	
	
}
