<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * ***** Assets configuration *****
 * 
 * Here goes everything related to CSS & Javascript loading
 */
$config['assets_pipeline'] = false;

$config['compress_css'] = false;
$config['compress_js'] = false;

// Combines all files of the same type in one file
$config['combine_assets'] = true;

// The 'release' num. Used as suffix for css and javascript files to avoid caching
$config['cjsuf'] = '5.1'; 