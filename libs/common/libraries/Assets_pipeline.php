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
	 * If needed also merges all files into one, and even minifies the code
	*/
	function render_css($css_files = array())
	{
		$output = '';
		
		// Load the necessary compression libraries if needed
		if ($this->ci->config->item('compress_css'))
		{
			require_once './libs/php/cssmin.php';
			$CSSmin = new CSSmin;
		}
		
		// If CSS needs to be combined
		if ($this->ci->config->item('combine_css')){
				
			// Determine the file name for combined file
			
			$contents = array();
			foreach ($css_files as $file)
			{
				// Compress only local files, ignore the rest
				if (substr($file,0,4) !== 'http') // Local file
				{
					$content = file_get_contents($_SERVER['DOCUMENT_ROOT'].$file);
					$contents[$file] = ($this->ci->config->item('compress_css'))?$CSSmin->run($content):$content;
				}
				else // Handle remote files here
				{
					$output .= '<link rel="stylesheet" type="text/css" href="'. $file .'" media="screen" />';
				}
				
			}
			$css_code = implode('', $contents);
			$hash = sha1($css_code);
			$final_css_file = $_SERVER['DOCUMENT_ROOT'].'/themes/cache/'.$hash.'.css';		
				
			if (!file_exists($final_css_file))
			{		
				file_put_contents($final_css_file, $css_code);
			}
				
			$output .= '<link rel="stylesheet" type="text/css" href="'. base_url() . 'themes/cache/' . $hash . '.css" media="screen" />';
		
		}
		// If CSS files should be handled separately
		else {
			foreach ($css_files as $file)
			{
				// Compress only local files, ignore the rest
				if (substr($file,0,4) !== 'http') // Local file
				{
					$hash = sha1_file($_SERVER['DOCUMENT_ROOT'].$file);
					
					$final_css_file = $_SERVER['DOCUMENT_ROOT'].'/themes/cache/'.$hash.'.css';
					
					if ($this->ci->config->item('compress_css'))
					{
						if (!file_exists($final_css_file))
						{
							file_put_contents($final_css_file, $CSSmin->run(file_get_contents($_SERVER['DOCUMENT_ROOT'].$file)));
						}
						$output .= '<link rel="stylesheet" type="text/css" href="'. base_url() . 'themes/cache/'.$hash.'.css" media="screen" />';
					}
					else
					{
						$output .= '<link rel="stylesheet" type="text/css" href="'. substr(base_url(),0,-1) . $file .'" media="screen" />';
					}
				}
				else // Remote file
				{
					$output .= '<link rel="stylesheet" type="text/css" href="'. $file .'" media="screen" />';
				}
			}
		
		}
		
		return $output;
	}
	
	
	
	/*
	 * Returns the tags needed for included JS loaded by $this->load->js() from various parts of the application
	 * If needed also merges all files into one, and even minifies the code
	*/
	function render_js($js_files = array())
	{
		$output = '';
		
		// Load the necessary compression libraries if needed
		if ($this->ci->config->item('compress_js'))
		{
			require_once './libs/php/JSMin.php';
		}
	
		
		// If JS needs to be combined
		if ($this->ci->config->item('combine_js')){
		
			// Determine the file name for combined file
				
			$contents = array();
			foreach ($js_files as $file)
			{
				// Compress only local files, ignore the rest
				if (substr($file,0,4) !== 'http') // Local file
				{
					$content = file_get_contents($_SERVER['DOCUMENT_ROOT'].$file);
					//$contents[$file] = ($this->ci->config->item('compress_js'))?JSMin::minify($content):$content;
					$contents[$file] = $content;
				}
				else // Handle remote files here
				{
					$output .= '<script type="text/javascript" src="'. $file .'"></script>';
				}
		
			}
			$js_code = implode('', $contents);
			$hash = sha1($js_code);
			
			
			
		
			if ($this->ci->config->item('compress_js'))
			{
				$final_js_file = $_SERVER['DOCUMENT_ROOT'].'/themes/cache/'.$hash.'.js';
				
				if (!file_exists($final_js_file))
				{
					file_put_contents($final_js_file, JSMin::minify($js_code));
				}
				$output .= '<script type="text/javascript" src="'. base_url() . 'themes/cache/' . $hash . '.js"></script>';
			}
			else
			{
				$final_u_js_file = $_SERVER['DOCUMENT_ROOT'].'/themes/cache/u_'.$hash.'.js';
				file_put_contents($final_u_js_file, $js_code);
				$output .= '<script type="text/javascript" src="'. base_url() . 'themes/cache/u_' . $hash . '.js"></script>';
			}
			
			
		
			
		
		}
		// If JS files should be handled separately
		else {
			foreach ($js_files as $file)
			{	
				// Compress only local files, ignore the rest
				if (substr($file,0,4) !== 'http') // Local file
				{
					$hash = sha1_file($_SERVER['DOCUMENT_ROOT'].$file);
						
					$final_js_file = $_SERVER['DOCUMENT_ROOT'].'/themes/cache/'.$hash.'.js';
						
					if ($this->ci->config->item('compress_js'))
					{
						if (!file_exists($final_js_file))
						{
							file_put_contents($final_js_file, JSMin::minify(file_get_contents($_SERVER['DOCUMENT_ROOT'].$file)));
						}
						$output .= '<script type="text/javascript" src="'. base_url() . 'themes/cache/'.$hash.'.js"></script>';
					}
					else
					{
						$output .= '<script type="text/javascript" src="'. substr(base_url(),0,-1) . $file .'"></script>';
					}
				}
				else // Remote file
				{
					$output .= '<script type="text/javascript" src="'. $file .'"></script>';
				}
			}
		
		}
		
		return $output;
	}
	
	
	
	
}