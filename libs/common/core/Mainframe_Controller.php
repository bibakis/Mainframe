<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mainframe_Controller extends CI_Controller {

    function __construct(){
        parent::__construct();

        if(ENVIRONMENT !== 'development'){
            redirect();
        }
    }
}