<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * ***** Assets configuration *****
 * 
 * Here goes everything related to CSS & Javascript loading
 */
$config['assets_pipeline']		= FALSE;

$config['compress_css']			= FALSE;
$config['compress_js']			= FALSE;

// Combines all files of the same type in one file
$config['combine_css']			= FALSE;
$config['combine_js']			= FALSE;	// We recommend that you use this feature with extreme caution as it can produce undesirable results
											// In particular it is known to break some jQuery plugins
