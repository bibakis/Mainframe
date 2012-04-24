<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * ***** Assets configuration *****
 * 
 * Here goes everything related to CSS & Javascript loading
 */
$config['assets_pipeline']		= TRUE;

$config['compress_css']			= TRUE;
$config['compress_js']			= TRUE;

// Combines all files of the same type in one file
$config['combine_css']			= FALSE;
$config['combine_js']			= TRUE;

// The 'release' num. Used when encrypting css and javascript filenames to avoid caching
// It doesn't have to be numeric. Can have any possible value
$config['cjsuf']				= '5.1';

