<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Db extends Mainframe_Controller
{

	/**
	 * Initialization code for the DB tools controller 
	 */
	public function __construct()
	{
		parent::__construct();

		// If the controller is not called from the command line, we assume the root is the location of the index.php file
		$this->load->library('db_tools', array('db'=>$this->db, 'output_path'=> $_SERVER['DOCUMENT_ROOT'].'/libs/temp/db'));
	}

	/**
	 * Applies all the database changes that are defined in the files located in the {$document_root}/libs/temp/db
	 */
	function import(){
        $this->db_tools->import();
    	$this->db_tools->echo_import();
	}

	/**
	 * Compares the current database schema to the last imported schema and 
	 * saves the differences to an XML file that will be used on an import
	 */
	function export(){
		$this->db_tools->export();
		$this->db_tools->echo_export();
	}


	/**
	 * Empties all the database tables
	 */
	function truncate(){
		$this->db_tools->truncate();		
		$this->db_tools->echo_truncate();
	}

	/**
	 * Populates the database with dummy data
	 */
	function populate(){
		$this->db_tools->populate();
		$this->db_tools->echo_populate();
	}

	/**
	 * Exports the data of the db tables which are included in the config value "sync_tables"
	 */
	function export_data() {
		$sync_tables = $this->config->item('sync_tables');

		$this->db_tools->export_data( $sync_tables );
		$this->db_tools->echo_export_data();
	}

	/**
	 * Syncs the database according to the queries exported
	 */
	function import_data() {
		$sync_tables = $this->config->item('sync_tables');

		$this->db_tools->import_data( $sync_tables );
		$this->db_tools->echo_import_data();
	}
}