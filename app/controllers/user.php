<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Demo Authentication Controller
 * 
 * This controller contains some basic login and register functionality.
 * This is to be used during protoryping and should by no means be considered
 * as a complete authentication system.
 * 
 * In order to use this functionality you need to:
 * 
 * 1) Set up a database in /app/config/database.php
 * 2) Create a new database containing a table named 'users'.
 * 3) Create the following structure for the users table
  CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_on` datetime NOT NULL,
  `last_seen` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
 *
 * By default this controller uses email and password to authenticate.
 * Username is optional and is generated just case you want to use it
 * for URLs or something similar. 
 */

class User extends CI_Controller {

	function __construct(){
		parent::__construct();
		
		$this->load->database();
		$this->load->library('session');
		
		$this->load->theme('user');
	}
	
	/*
	 * Redirects to either the home page, or the login screen
	 * depending on whether the user is already logged in or not
	 */
	public function index()
	{
		if (session('user_id'))
		{
			redirect();
		}
		else 
		{
			redirect('user/login');
		}
	}
	
	
	/*
	 * Authenticates a user
	 */
	public function login()
	{
		$rules = array('username', 'password');
		
		// If the form was submited
		if (validate($rules))
		{
			// These are purposely not on a model. Remember this is only for prototyping.
			$db_data = array(
				'username'	=> post('username'),
				'password'	=> sha1(post('password'))
			);
			$query = $this->db->get_where('users', $db_data);
			
			// Time to log in  the user if credentials are correct
			if ($query->num_rows() == 1)
			{
				$this->session->set_userdata('user_id', $query->row()->id);
				redirect();
			}
			else 
			{
				$data = array('error' => true);
				$this->load->view('login', $data);
			}
		}
		// Otherwise just show the form
		else 
		{
			$this->load->view('login');
		}
	}
	
	
	/*
	 * Creates a new user account
	*/
	public function register()
	{
		$rules = array('username', 'password');
		if (validate($rules))
		{
			// Check for unique username & email
			$db_data = array(
				'email'			=> post('username'),
				'username'		=> post('username'),
				'password'		=> sha1(post('password')),
				'created_on'	=> now_mysql(),
				'last_seen'		=> now_mysql(),
			);
			$query = $this->db->get_where('users', array('username'	=> post('username')));
			
			if ($query->num_rows() == 1)
			{
				$data = array('error' => true);
				$this->load->view('register', $data);
			}
			else
			{
				// Create the user, create the session and redirect to home page
				$this->db->insert('users', $db_data);
				$id = $this->db->insert_id();
				
				$this->session->set_userdata('user_id', $id);
				redirect();
			}
		}
		else 
		{
			$this->load->view('register');
		}
	}
	
	
	/*
	 * Logs out the user and redirects to home page
	 */
	public function logout()
	{
		$this->session->sess_destroy();
		redirect();
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */