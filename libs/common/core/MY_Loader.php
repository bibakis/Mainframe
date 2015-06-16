<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2015, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (http://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2015, British Columbia Institute of Technology (http://bcit.ca/)
 * @license	http://opensource.org/licenses/MIT	MIT License
 * @link	http://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Loader Class
 *
 * Loads framework components.
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Loader
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/loader.html
 */
class MY_Loader extends CI_Loader {
	
	var $template = '';
	var $data = array();
	var $return = FALSE;
	var $template_loaded;
	var $loading_theme = false;
	var $css_files = array();
	var $js_files = array();
	
	/**
	 * List of paths to load helpers from
	 *
	 * @var	array
	 */
	protected $_ci_helper_paths =	array(COMMONPATH, APPPATH, BASEPATH);
	
	/**
	 * Allows the loading of templates. Normaly you want pages with the same layout across your site.
	 * If you decide to load a template, then any of the views you load afterwards will be placed inside
	 * the template's code in the position with the $content variable
	 *
	 * @param $template		The filename of the template to use, in the style of loading a view
	 * @param $data			Any data you wish to pass to the template, in a data array just like the views
	 * @param $return		If you want to just get the template contents set to true
	 */
	function theme($template = '', $data = array(), $return = FALSE)
	{
		if ($template == '')
		{
			return FALSE;
		}
	
		$this->template = $template;
		$this->data = $this->_ci_object_to_array($data);
		$this->return = $return;
	}
	
	/**
	 * Checks if a template has already been loaded and then either loads the view directly or puts it inside
	 * the template and loads it with the view.
	 *
	 * @see /system/libraries/CI_Loader#view($view, $vars, $return)
	 */
	function view($view, $vars = array(), $return = FALSE){
		// this part loads a view
		if (($this->template == '') || ($this->template_loaded == TRUE)){
			$this->template_loaded = TRUE;
			return $this->_ci_load(array(
					'_ci_view' => $view,
					'_ci_vars' => $this->_ci_object_to_array($vars),
					'_ci_return' => $return)
			);
		}
		// this part loads the template
		else {
			$this->template_loaded = TRUE;
			$data = $this->_ci_object_to_array($vars);
				
	
			// adds the template $data
			if (count($this->data) > 0){
				foreach ($this->data as $key => $value){
					$data[$key] = $value;
				}
			}
				
			$data['content'] = $this->_ci_load(array(
					'_ci_view' => $this->template.'/'.$view.'.php',
					'_ci_vars' => $this->_ci_object_to_array($vars),
					'_ci_return' => TRUE)
			);
				
				
			$data['includes'] = '';
				
			$this->css_files = array_unique($this->css_files);
			$key = 0;
			foreach ($this->css_files as $css_file)
			{
				$data['includes'] .= '<link rel="stylesheet" type="text/css" href="'.$css_file[$key].'" media="screen" /> ';
				$key++;
			}
				
			$this->js_files = array_unique($this->js_files);
			$key = 0;
			foreach ($this->js_files as $js_file)
			{
				$data['includes'] .= '<script type="text/javascript" src="'.$js_file[$key].'"></script>';
				$key++;
			}
	
			$this->loading_theme = true;
				
			$themepath = '../../themes/'.$this->template.'/theme.php';
				
			return $this->_ci_load(array(
					'_ci_view' => $themepath,
					'_ci_vars' => $data,
					'_ci_return' => $return)
			);
		}
	}
	
	/*
	 * Loads a JavaScript file
	* @param	string		$filename		The JavaScript file to load
	* @param	string		$order			can be 'first','last',FALSE. use first for loading first, last for loading last,
	*			or ignore the parameter if you don't want to tweak the order of loading for this file
	*/
	function js($filename, $order = FALSE)
	{
		if (!$order)
		{
			$this->js_files['normal'][] = $filename;
		}
		elseif (($order === 'first') OR ($order === 'last'))
		{
			$this->js_files[$order][] = $filename;
		}
		else
		{
			log_message('debug', 'Invalid loading order set for file ' . $filename);
		}
	}
	
	
	/*
	 * Loads a CSS file
	* @param	string		$filename		The CSS file to load
	* @param	string		$order			can be 'first','last',FALSE. use first for loading first, last for loading last,
	*			or ignore the parameter if you don't want to tweak the order of loading for this file
	*/
	function css($filename, $order = FALSE)
	{
		if (!$order)
		{
			$this->css_files['normal'][] = $filename;
		}
		elseif (($order === 'first') OR ($order === 'last'))
		{
			$this->css_files[$order][] = $filename;
		}
		else
		{
			log_message('debug', 'Invalid loading order set for file ' . $filename);
		}
	
	}
	
	
	/*
	 * Loads and compiles a LESS file -> http://lesscss.org/
	* Compiled less files are stored in /themes/cache/$path/$name.css
	* This is done to avoid filename colissions and keep things tidy
	* 		For example if the original Less file was /themes/demo/less/styles.css
	* 		the new css file will be /themes/cache/themes/demo/less/styles.css
	* @param	string		$filename		The CSS file to load
	* @param	string		$order			can be 'first','last',FALSE. use first for loading first, last for loading last,
	* 			or ignore the parameter if you don't want to tweak the order of loading for this file
	*/
	function less($filename, $order = FALSE){
		// Block remote LESS files
		if ((substr($filename, 0,7) === 'http://') OR (substr($filename, 0,8) === 'https://'))
		{
			return false;
		}
	
		//Check if it's a LESS file
		if (substr($filename, -5) !== '.less')
		{
			return false;
		}
	
		// Make sure $filename path starts with a /
		if (substr($filename, 0, 1) !== '/')
		{
			$filename = '/'.$filename;
		}
	
		$lib = $_SERVER['DOCUMENT_ROOT'] . '/libs/php/lessc.inc.php';
		require_once ($lib);
	
		$css_filename = substr('/themes/cache' . $filename, 0, -4) . 'css';
		$less = $_SERVER['DOCUMENT_ROOT'] . $filename;
		$css = $_SERVER['DOCUMENT_ROOT'] . $css_filename;
			
		// Create the folder structure before attempting to call the less compressor for storing the file
		$css_parts = explode('/', $css);
		array_pop($css_parts);
		$css_path = implode('/', $css_parts);
		@mkdir($css_path, 0777, true);
			
		// Compile the $less file onto $css
		$lessc = new lessc;
		$lessc->checkedCompile($less, $css);
	
			
		if (!$order)
		{
			$this->css_files['normal'][] = $css_filename;
		}
		elseif (($order === 'first') OR ($order === 'last'))
		{
			$this->css_files[$order][] = $css_filename;
		}
		else
		{
			log_message('debug', 'Invalid loading order set for file ' . $filename);
		}
	
			
		/* Original lesscss.org code, left here for any possible future debugging
		 try {
		lessc::ccompile($less, $css);
		} catch (exception $ex) {
		exit('lessc fatal error:<br />'.$ex->getMessage());
		}
		*/
	}
	
	
	/*
	 * Returns a list of tags which load all CSS & Javascript files declared with $this->load->css() and $this->load->js()
	* Place $this->load->assets() in your theme where you want the tags to appear (normaly in the <head> section)
	*/
	function assets(){
		// reorder assets based on given order by the user
		$ordered_css = array();
		$ordered_js = array();
	
		if (array_key_exists('first', $this->css_files))
		{
			foreach ($this->css_files['first'] as $file)
			{
				$ordered_css[] = $file;
			}
		}
		if (array_key_exists('normal', $this->css_files))
		{
			foreach ($this->css_files['normal'] as $file)
			{
				$ordered_css[] = $file;
			}
		}
		if (array_key_exists('last', $this->css_files))
		{
			foreach ($this->css_files['last'] as $file)
			{
				$ordered_css[] = $file;
			}
		}
	
		if (array_key_exists('first', $this->js_files))
		{
			foreach ($this->js_files['first'] as $file)
			{
				$ordered_js[] = $file;
			}
		}
		if (array_key_exists('normal', $this->js_files))
		{
			foreach ($this->js_files['normal'] as $file)
			{
				$ordered_js[] = $file;
			}
		}
		if (array_key_exists('last', $this->js_files))
		{
			foreach ($this->js_files['last'] as $file)
			{
				$ordered_js[] = $file;
			}
		}
	
		$output = '';
		if ($this->config->item('assets_pipeline'))
		{
			$ci = &get_instance();
			$ci->load->library('assets_pipeline');
			$output .= $ci->assets_pipeline->render_css($ordered_css);
			$output .= $ci->assets_pipeline->render_js($ordered_js);
		}
		else
		{
			foreach ($ordered_css as $file){
				$output .= '<link rel="stylesheet" type="text/css" href="'.$file.'" media="screen" />';
			}
			foreach ($ordered_js as $file){
				$output .= '<script type="text/javascript" src="'.$file.'"></script>';
			}
		}
		return $output;
	}
	
}
