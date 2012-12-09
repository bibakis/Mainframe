<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * Functions related to user interface
 * These functions intentionaly echo their output most of the times
 * instead of returning it. This is because they are aimed exclusively 
 * at the user interface and should not be used to format text or presentation
 * of other parts of the application (for example email reports).
 */

/*
 * Shorthand for $this->load->view()
*/
function view($view, $params = FALSE){
	$ci =& get_instance();
	$ci->load->view($view, $params);
}

/*
 * Shorthand for $this->load->css()
*/
function css($css, $params = FALSE){
	$ci =& get_instance();
	$ci->load->css($css, $params);
}

/*
 * Shorthand for $this->load->less()
*/
function less($less, $params = FALSE){
	$ci =& get_instance();
	$ci->load->less($less, $params);
}

/*
 * Shorthand for $this->load->js()
*/
function js($js, $params = FALSE){
	$ci =& get_instance();
	$ci->load->js($js, $params);
}