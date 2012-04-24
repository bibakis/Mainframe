<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Temporary controller, can be safely deleted at anytime
 */
class Assets extends CI_Controller {

	public function index()
	{
	}
	
	function css ()
	{
		$css_file = '/themes/demo/css/styles.css';
		$css_code = file_get_contents($_SERVER['DOCUMENT_ROOT'] . $css_file);
		
		require_once '/libs/php/cssmin.php';
		
		$CSSmin = new CSSmin;
		
		$css_code = $CSSmin->run($css_code);
		
		echo $css_code;
	}
	
	function js()
	{
		$js_file = '/libs/js/jquery.url.js';
		$js_code = file_get_contents($_SERVER['DOCUMENT_ROOT'] . $js_file);
		
		require_once '/libs/php/JSMin.php';
		
		$js_code = JSMin::minify($js_code);
		
		echo $js_code;
	}
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */