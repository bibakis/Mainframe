<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter Minify
 *
 * @package ci-minify
 * @author Eric Barnes
 * @copyright Copyright (c) Eric Barnes
 * @since Version 1.0
 * @link http://ericlbarnes.com
 */

// ------------------------------------------------------------------------

/**
 * Example Controller
 *
 * @subpackage	Controllers
 */
class Example extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->driver('minify');
	}

	function index()
	{
		$file = 'libs/ci-minify/test/js/test1.js';
		$file = 'libs/ci-minify/test/css/mainframe-main.css';
		//damn ($file);
		echo $this->minify->js->min($file);
	}

	public function combine()
	{
		echo $this->minify->combine_directory('libs/ci-minify/test/css');
	}
}

/* End of file example.php */
/* Location: ./application/controllers/example.php */