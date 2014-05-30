<?php defined('BASEPATH') OR exit('No direct script access allowed');
class DB_Tools{

    public $messages         		= array();
    public $exported_files   		= array();
    public $execution_times  		= array();
    private $import_export_errors   = FALSE;

    public $total_tables 	 		= array();
    public $file_changes	 		= array();
    public $export_changes			= array();
    public $export_from_importer 	= FALSE;
    private $schema_filename 		= 'latest_schema.xml';
    private $latest_updates_filename   = 'latest_updates.json';

	public $truncated				= FALSE;

	public $populate_data			= array();

    private $output_path     		= '';
    private $db              		= NULL;

    public function __construct($params){
        $this->db = $params['db'];
        $this->output_path = $params['output_path'];

        // initialize the db forge class
        // and add a refference to this instance
        $ci =& get_instance();
        $ci->load->dbforge();
        $this->dbforge =& $ci->dbforge;
    }


    /*********************************************************************************************************
     * IMPORT & EXPORT                                                                                       *
     *********************************************************************************************************/


    /**
     * Checks the contents of the $this->output_path folder and imports all the files that are not yet imported
     * @return boolean
     */
    public function import()
    {
        $update_start = microtime(TRUE);

        $this->export_from_importer = $this->export();

        // Adding to the public variable an array that includes the total tables of the database
        // and the total columns of each table. The function "$this->db->list_tables()" keeps only
        // the snapshot of the database at the time when the $this object was initiallized, so
        // after modifying a table, the data provided by that function will not be up to date.
   		foreach($this->db->list_tables() as $table_name)
        {
        	$this->total_tables[$table_name] = array();
        }
        foreach($this->total_tables as $table_name => $table)
        {
        	foreach($this->db->list_fields($table_name) as $key => $field)
        	{
        		$this->total_tables[$table_name][$field] = $field;
        	}
        }


        $last_executed_script_timestamp = 0;

        $executed_files = array();
        if($saved_schema = $this->get_saved_schema())
        {
            // Parse the schema and get all the xml filenames which have been already executed
            $executed_files = $saved_schema->documentElement->getAttribute('executed_files');
            $executed_files = explode(',', $executed_files);
        }

        $files = array();
        if ($handle = opendir($this->output_path)) {
            while (($entry = readdir($handle)) !== FALSE) {
                $files[] = $entry;
            }
            closedir($handle);

            asort($files);
			$non_executed_file_found = FALSE;
            // Gather all the filenames that are in the format timestamp.xml
            $files = array_filter($files, function($filename) use($executed_files, &$non_executed_file_found) {
                $timestamp = reset(explode('.', $filename));
                $extension = end(explode('.', $filename));

                // Add the filename to the array if:
                        // It's extension is XML
                if(strtolower($extension) === 'xml'
                    // The first part of it's name is a valid unix timestamp
                    && ((string) (int) $timestamp === $timestamp)
                    && ($timestamp <= PHP_INT_MAX)
                    && ($timestamp >= ~PHP_INT_MAX)
                    // If that file is not in the array with the files that have already been executed
                    && ((!in_array($timestamp, $executed_files))
                    // Or a file from the files checked before in the $files array, was never executed.
            		// If that's the case, all the files after that, will be executed in order.
            		|| $non_executed_file_found)
                ) {
        			$non_executed_file_found = TRUE;
        			return TRUE;
        		}
        		else
        		{
        			return FALSE;
        		}
            });

            // Since the new files will be executed, they are added to the array of the executed files.
            // Their names will be added to the latest_schema.xml
            foreach($files as $file)
            {
            	$file = explode('.', $file);
            	$executed_files[] = $file[0];
            }
            $executed_files = array_unique($executed_files);
            sort($executed_files);
            $executed_files = implode(',', $executed_files);

            $errors = $this->update_database_schema($files);

            if(!$errors)
            {
                $this->execution_times[] = (microtime(TRUE) - $update_start);

                // If the directory does not exist, create it
                if(!is_dir($this->output_path))
                {
                    // Create the directory
                    if(!mkdir($this->output_path))
                    {
                        // Something went wrong
                        $this->messages[] = 'Unable to create the folder '.$this->output_path;
                        return FALSE;
                    }
                }
                // A schema file does not exist yet. Create one that contains the current schema
                if(!$this->create_schema_file($executed_files))
                {
                    // Schema file could not be created
                    $this->messages[] = 'Could not create a schema file. Perhaps a permission problem?';
                    return FALSE;
                }
            }
            else
            {
                foreach ($errors as $error) {
                    $this->messages[] = $error;
                }
                return FALSE;
            }
        }
    }



    /**
     * Exports the current database schema in a batch of SQL files and saves them inside the specified folder
     * @return boolean
     */
    public function export()
    {
        // Checking if the directory where the output files will be written, is readable/writable
        if(!is_writable($this->output_path) || !is_readable($this->output_path)) {
            echo $this->output_path . ' is not a readable or writable directory';
            die();
        } else {
            // If it is, checking if the contained files are readable/writable
            $not_writable_files = FALSE;
            $files = scandir($this->output_path);
            foreach ($files as $file) {
                if ($file != "." && $file != "..") {
                    if (!is_writable($this->output_path .'/' . $file) || !is_readable($this->output_path .'/' . $file)) {
                        echo $this->output_path .'/' . $file . ' is not readable or writable<br>';
                        $not_writable_files = TRUE;
                    }
                }
            }
            if($not_writable_files) {
                die();
            }
        }

        $execution_time = microtime(TRUE);
        error_reporting (E_ALL);

        // Adding to the total_tables array the total tables of the database and the total columns of each table. 
        // The function "$this->db->list_tables()" keeps only a snapshot of the database at the time when the 
        // $this object was initiallized, so after modifying a table, the data provided by that function will not 
        // be up to date. The total_tables array will be manually kept up to date.
        foreach($this->db->list_tables() as $table_name)
        {
        	$this->total_tables[$table_name] = array();
        }
        foreach($this->total_tables as $table_name => $table)
        {
        	foreach($this->db->list_fields($table_name) as $key => $field)
        	{
        		$this->total_tables[$table_name][$field] = $field;
        	}
        }

        $diff = $this->get_changes();        
        $this->export_changes = $diff;
        // If there are any additions/changes or deletions, then an xml will be generated
        if(count($diff['modifications']) > 0 || count($diff['deletions']) > 0)
        {
            // Differences between saved schema and current schema exist. Generate scripts
            // that will update the database to the current schema
            $schema_dom = new DOMDocument();

            // schema_changes is the main element of the xml that includes all the other elements
            $schema_changes = $schema_dom->createElement('schema_changes');
            $schema_dom->appendChild($schema_changes);

			// If there are any additions/changes
            if(count($diff['modifications']) > 0)
            {
            	// For each table added/altered
            	foreach($diff['modifications'] as $table_name => $fields)
            	{
            		$xmls = array();

            		$out_fields = array();
            		foreach($fields as $field=>$properties)
            		{
            			$out_fields[]['column'] = $properties;
            		}

            		// The structure is:
            		// <table name="table_name"><table_updates><column>...</column></table_updates></table>
            		$xml = new SimpleXMLElement('<table name="'.$table_name.'"/>');
            		$table_updates = $xml->addChild('table_updates');
            		foreach($out_fields as $column)
            		{
            			$this->array_to_xml($column, $table_updates);
            		}

            		$dom_element = dom_import_simplexml($xml);
            		$dom_element = $schema_dom->importNode($dom_element, TRUE);
            		$schema_changes->appendChild($dom_element);
            	}
            }

            // If there are any deletions
            if(count($diff['deletions']) > 0)
            {
            	// For each table deleted/altered
            	foreach($diff['deletions'] as $table_name => $fields)
            	{
            		$xmls = array();

            		$out_fields = array();
            		if(!isset($fields['dropped']))
            		{
	            		foreach($fields as $field=>$properties)
	            		{
	            			$out_fields[]['column'] = $properties;
	            		}
            		}
            		else
            		{
            			$out_fields[]['dropped'] = 'true';
            		}

            		$tables = $schema_changes->getElementsByTagName('table');
            		$table_exists = FALSE;
            		foreach($tables as $table)
            		{
            			// If the table already exists in the xml (because it has been altered before)
            			// Add the element <table_deletions> in it and add all the dropped columns there
            			if($table->getAttribute('name') == $table_name)
            			{
            				$table_deletions = new SimpleXMLElement('<table_deletions/>');

            				foreach($out_fields as $column)
            				{
            					$this->array_to_xml($column, $table_deletions);
            				}

            				$dom_element = dom_import_simplexml($table_deletions);
            				$dom_element = $schema_dom->importNode($dom_element, TRUE);
            				$table->appendChild($dom_element);
            				$table_exists = TRUE;
            			}
            		}
            		// If the table doesn't exist, create a table element and then add the <table_deletions>
            		// element as its child
            		if(!$table_exists)
            		{
	            		$xml = new SimpleXMLElement('<table name="'.$table_name.'"/>');
	            		$table_deletions = $xml->addChild('table_deletions');

	            		foreach($out_fields as $column)
	            		{
	            			$this->array_to_xml($column, $table_deletions);
	            		}

	            		$dom_element = dom_import_simplexml($xml);
	            		$dom_element = $schema_dom->importNode($dom_element, TRUE);
	            		$schema_changes->appendChild($dom_element);
            		}
            	}
            }

            $schema_dom->formatOutput = TRUE;
            if($this->save_schema_xml($schema_dom)){
            	$executed_files = array();
            	if($saved_schema = $this->get_saved_schema())
            	{
            		// Parse the latest_schema.xml and get all the xml filenames which have been already executed
            		$executed_files = $saved_schema->documentElement->getAttribute('executed_files');
            		$executed_files = explode(',', $executed_files);
            	}
            	// Add the new xml to the the list of the files executed
            	$executed_files[] =  array_shift(explode(".", array_pop(explode("\\", array_pop(explode("/", $this->exported_files[0]))))));
	            $executed_files = array_unique($executed_files);
	            sort($executed_files);
            	$executed_files = implode(',', $executed_files);
            	$this->create_schema_file($executed_files);

                $this->execution_times[] = microtime(TRUE) - $execution_time;
                $this->messages[] = 'Scripts have been succesfully saved in the <em>'.$this->output_path.'</em>';
                return TRUE;
            }
            else
            {
                $this->execution_times[] = microtime(TRUE) - $execution_time;
                $this->messages[] = 'Something went wrong while saving the scripts '.$this->output_path;
                return FALSE;
            }
        }

        $this->execution_times[] = microtime(TRUE) - $execution_time;
        $this->messages[] = 'No changes to apply';
        return TRUE;
    }

    // Checks if scripts which have not been executed, exist
    public function new_scripts_exist() {
        $executed_files = array();
        if($saved_schema = $this->get_saved_schema())
        {
            // Parse the schema and get all the xml filenames which have been already executed
            $executed_files = $saved_schema->documentElement->getAttribute('executed_files');
            $executed_files = explode(',', $executed_files);
        }

        $files = array();
        if ($handle = opendir($this->output_path)) {
            while (($entry = readdir($handle)) !== FALSE) {
                $files[] = $entry;
            }
            closedir($handle);

            asort($files);
            $non_executed_file_found = FALSE;
            // Gather all the filenames that are in the format timestamp.xml
            $files = array_filter($files, function($filename) use($executed_files, &$non_executed_file_found) {
                $timestamp = reset(explode('.', $filename));
                $extension = end(explode('.', $filename));

                // Add the filename to the array if:
                        // It's extension is XML
                if(strtolower($extension) === 'xml'
                    // The first part of it's name is a valid unix timestamp
                    && ((string) (int) $timestamp === $timestamp)
                    && ($timestamp <= PHP_INT_MAX)
                    && ($timestamp >= ~PHP_INT_MAX)
                    // If that file is not in the array with the files that have already been executed
                    && ((!in_array($timestamp, $executed_files))
                    // Or a file from the files checked before in the $files array, was never executed.
                    // If that's the case, all the files after that, will be executed in order.
                    || $non_executed_file_found)
                ) {
                    $non_executed_file_found = TRUE;
                    return TRUE;
                }
                else
                {
                    return FALSE;
                }
            });
        }

        return !!$files;
    }


    /**
     * Goes through the provided list of files, and applies the necesary changes to the database
     * in order to make the schema match that of each XML
     *
     * The contents of the file array must be strings in the format {valid_timestamp}.xml
     *
     * @param array $files
     */
    private function update_database_schema($files){
        // Sort those files!
        usort($files, function($file_a, $file_b){
            return reset(explode('.', $file_a)) > reset(explode('.', $file_b));
        });

        $errors = array();
        // Checking for errors before updating
        foreach($files as $file)
        {
            $doc = DOMDocument::load($this->output_path.DIRECTORY_SEPARATOR.$file);
            $xpath = new DOMXPath($doc);
            // Get all the tables
            $tables = $xpath->query('table');

            for($table_index = 0; $table_index < $tables->length; $table_index++)
            {
                $table = $tables->item($table_index);
                $table_name = $table->getAttribute('name');
                // Check the tables and columns that changed on the existing tables
                if(array_key_exists($table_name, $this->total_tables))
                {
                    // We will go through table columns
                    $columns = $xpath->query('table_updates/column', $table);
                    foreach($columns as $column)
                    {
                        // Here we convert the DOM to an Array that can be used by the forge class
                        $column_array = $this->fix_column($column);
                        $column_name = $column_array['name'];

                        // Setting the null field
                        if(isset($column_array['null']))
                        {
                            $column_array['null'] = $column_array['null'] == 'yes';
                        }

                        // Does the column exist in the schema?
                        if(in_array($column_name, $this->total_tables[$table_name]))
                        {
                            // If the column does not support NULL values anymore, check if the db table has null values
                            // in it and display the appropriate error messages
                            if(isset($column_array['null']) && !$column_array['null'] && !isset($column_array['default']))
                            {
                                $this->db->where($column_name . ' IS NULL');
                                $null_values = $this->db->get($table_name)->num_rows();
                                if($null_values)
                                {
                                    $errors[] = 'Column ' . $column_name . ' on table ' . $table_name . ' was changed to "NOT NULL". Please fill out current "NULL" values and refresh this page';
                                }
                            }
                        }
                    }
                }
            }
        }

        if(!$errors)
        {
            // If there were no errors, proceed with the update of the schema
            foreach($files as $file)
            {
                $doc = DOMDocument::load($this->output_path.DIRECTORY_SEPARATOR.$file);
                $xpath = new DOMXPath($doc);
                // Get all the tables
                $tables = $xpath->query('table');

                for($table_index = 0; $table_index < $tables->length; $table_index++)
                {
                    $table = $tables->item($table_index);
                    $table_name = $table->getAttribute('name');
                    // First case: Does the table exist?
                    if(!array_key_exists($table_name, $this->total_tables))
                    {
                        // It doesn't. We will create it according to the contents of the xml element
                        $columns = $xpath->query('table_updates/column', $table);
                        if($columns->length)
                        {
                        	$this->total_tables[$table_name] = array();
    	                    $this->file_changes[$file]['tables_created'][$table_name] = array();
    	                    foreach($columns as $column)
    	                    {
    	                        // Here we convert the DOM to an Array that can be used by the forge class
    	                        $column_array = $this->fix_column($column);
    	                        $column_name = $column_array['name'];
    	                        unset($column_array['name']);

    	                        // Setting the null field
    	                        if(isset($column_array['null']))
    	                        {
    	                        	$column_array['null'] = $column_array['null'] == 'yes';
    	                        }

    	                        // Table ids are a special case, and will automatically defined as primary keys
    	                        // auto_incremented and NOT NULL
    	                        if($column_name === 'id')
    	                        {
    	                            $this->dbforge->add_field('id');
    	                        }
    	                        else
    	                        {
    	                            $this->dbforge->add_field(array($column_name => $column_array));
    	                        }

    	                        if($column_name !== 'id' && isset($column_array['primary_key']) && $column_array['primary_key'])
    	                        {
    	                            $this->dbforge->add_key($column_name, TRUE);
    	                        }

    	                        $this->file_changes[$file]['tables_created'][$table_name]['columns_created'][] = $column_name;
    							$this->total_tables[$table_name][$column_name] = $column_name;
    	                    }
    	                    $this->dbforge->create_table($table_name);

    	                    // Changing the collation manually, since dbforge doesn't do it
    	                    foreach($columns as $column)
    	                    {
    	                    	$column_array = $this->fix_column($column);
    		                    if(isset($column_array['collation'])) {
    	                        	$nullable = $column_array['null']? "NULL": "NOT NULL";
    	                        	$charset = array_shift(explode("_", $column_array['collation']));
    	                        	mysql_query("ALTER TABLE " . $table_name . " MODIFY " . $column_name . " " . $column_array['type'] . " CHARACTER SET " . $charset . " COLLATE " . $column_array['collation'] . " " . $nullable);
    		                    }
    	                    }
                        }
                    }
                    else
                    {
                        // The table exists. We will go through it's columns and update them if they don't match
                        $columns = $xpath->query('table_updates/column', $table);
                        foreach($columns as $column)
                        {
                            // Here we convert the DOM to an Array that can be used by the forge class
                            $column_array = $this->fix_column($column);
                            $column_name = $column_array['name'];

                            // Setting the null field
                            if(isset($column_array['null']))
                            {
                            	$column_array['null'] = $column_array['null'] == 'yes';
                            }
                            if(!isset($this->file_changes[$file]['tables_altered']) || (isset($this->file_changes[$file]['tables_altered']) && !array_key_exists($table_name, $this->file_changes[$file]['tables_altered'])))
                            {
                                $this->file_changes[$file]['tables_altered'][$table_name] = array();
                            }

                            // Does the column exist in the schema?
                            if(in_array($column_name, $this->total_tables[$table_name]))
                            {
                                if(isset($column_array['null']))
                                {
                            	    // If the column is altered so that it won't accept null values, but there are some null values currently in it, update them to the new default value
                            		if(!$column_array['null'] && isset($column_array['default']))
                            		{
                            			$this->db->where($column_name . ' IS NULL');
                            			$this->db->update($table_name, array($column_name => $column_array['default']));
                            		}
                            	}

                                // Alter the table with the new data
                                $this->dbforge->modify_column($table_name, array($column_name=>$column_array));
                                $this->file_changes[$file]['tables_altered'][$table_name]['columns_altered'][] = $column_name;
                            }
                            else
                            {
                                unset($column_array['name']);
                                // Add the new column
                                $this->dbforge->add_column($table_name, array($column_name=>$column_array));
                                $this->file_changes[$file]['tables_altered'][$table_name]['columns_created'][] = $column_name;
                                $this->total_tables[$table_name][$column_name] = $column_name;
                            }
                            // Changing the collation manually, since dbforge doesn't do it
                            if(isset($column_array['collation'])){
                            	$nullable = $column_array['null']? "NULL": "NOT NULL";
                            	$charset = array_shift(explode("_", $column_array['collation']));
                            	mysql_query("ALTER TABLE " . $table_name . " MODIFY " . $column_name . " " . $column_array['type'] . " CHARACTER SET " . $charset . " COLLATE " . $column_array['collation'] . " " . $nullable);
                            }

                        }
                        // If the whole table should be dropped, drop it
                        if($xpath->query('table_deletions/dropped', $table)->length)
                        {
                        	$this->dbforge->drop_table($table_name);
                        	$this->file_changes[$file]['tables_dropped'][$table_name] = array();
                        	unset($this->total_tables[$table_name]);
                        }
                        // Else check if there are any columns that should be dropped
    					else
    					{
    	                    $columns = $xpath->query('table_deletions/column', $table);
    	                    foreach($columns as $column)
    	                    {
    	                    	// Here we convert the DOM to an Array that can be used by the forge class
    	                    	$column_array = $this->fix_column($column);
    	                    	$column_name = $column_array['name'];
    	                    	// Does the column exist in the schema?
    	                    	if(array_key_exists($column_name, $this->total_tables[$table_name]))
    	                    	{
    	                    		$this->dbforge->drop_column($table_name, $column_name);
    	                    		if(!isset($this->file_changes[$file]['tables_altered']) || (isset($this->file_changes[$file]['tables_altered']) && !array_key_exists($table_name, $this->file_changes[$file]['tables_altered'])))
    	                    		{
    	                    			$this->file_changes[$file]['tables_altered'][$table_name] = array();
    	                    		}
    	                    		$this->file_changes[$file]['tables_altered'][$table_name]['columns_dropped'][] = $column_name;
    	                    		unset($this->total_tables[$table_name][$column_name]);
    	                    	}
    	                    }
    					}
                    }
                }
            }
            return NULL;
        }
        else
        {
            return $errors;
        }
    }

    /**
     * Returns the execution time of the last query
     * @return float
     */
    public function execution_time()
    {
        return end($this->execution_times);
    }


    /**
     * Applies some corrections to the column array in order for the dbforge class not to break in some extreme cases
     * @param array $column
     * @return array
     */
    private function fix_column($column)
    {

        $column_array = $this->xml2array(simplexml_import_dom($column));

        foreach($column_array as $name=>$value)
        {
            if(is_array($value) && empty($value))
            {
                unset($column_array[$name]);
            }
        }

        if($column_array['name'] === 'id')
        {
            $column_array['auto_increment'] = TRUE;
            $column_array['primary_key']    = TRUE;
            unset($column_array['default']);
        }

        // The dbforge doesn't seem to handle the max_length property well, therefore we add it to the type of the column
        if(array_key_exists('type', $column_array) && array_key_exists('max_length', $column_array))
        {
            $column_array['type'] = $column_array['type'].'('.$column_array['max_length'].')';
        }

        // Some default values are incorectly escaped by the dbforge class
        if(array_key_exists('default', $column_array))
        {
            if(strtolower($column_array['default']) === 'current_timestamp')
            {
                // CURRENT TIMESTAMP is one of them and needs to be appended to the column type
                $column_array['type'] .= ' DEFAULT CURRENT_TIMESTAMP';
                unset($column_array['default']);
            }
        }

        // If no null property exists, the dbforge class automatically assumes that the IS NULL should be appended in the query, which breaks some queries
        // Therefore we are setting the NULL value to TRUE as default
        if(!array_key_exists('null', $column_array)){
            $column_array['null'] = TRUE;
        }
        return $column_array;
    }


    /**
     * Adds the elements of the provided array to the provided SimpleXML object
     * @param array $array
     * @param SimpleXMLElement $xml
     */
    private function array_to_xml($array, &$xml) {
        foreach($array as $key => $value) {
            if(is_array($value)) {
                if(!is_numeric($key)){
                    $subnode = $xml->addChild("$key");
                    $this->array_to_xml($value, $subnode);
                }
                else{
                    $subnode = $xml->addChild("item$key");
                    $this->array_to_xml($value, $subnode);
                }
            }
            else {
                $xml->addChild("$key","$value");
            }
        }
    }


    /**
     * Saves the provided DOMDocument as an XML file in the format {timestamp}.xml in the set outpout directory
     *
     * @param DOMDocument $dom
     * @return boolean
     */
    private function save_schema_xml(DOMDocument $dom)
    {
        if(!is_dir($this->output_path))
        {
            $this->messages[] = 'The path '.$this->output_path.' does not exists';
            return FALSE;
        }

        //Generate a timestamp
        $now = new DateTime(); $now->setTimezone(new DateTimeZone('Europe/Athens'));
        $this->exported_files[] = $this->output_path.DIRECTORY_SEPARATOR.$now->format('U').'.xml';
        return (file_put_contents($this->output_path.DIRECTORY_SEPARATOR.$now->format('U').'.xml', $dom->saveXML()) !== FALSE);
    }



    /**
     * Returns the full path of the database schema
     * @return string
     */
    private function schema_full_path(){
        return implode(DIRECTORY_SEPARATOR, array($this->output_path, $this->schema_filename));
    }


    /**
     * Parses through the latest schema php file and returns the contents of that array
     * If case of failure returns a empty array
     *
     * @param  $as_array    boolean
     * @return DOMDocument
     */
    private function get_saved_schema($as_array = FALSE)
    {

        if(!file_exists($this->schema_full_path()))
        {
            // No schema is saved
            if($as_array)
            {
                return array();
            }
            else{
                return NULL;
            }
        }

        $doc = DOMDocument::load($this->schema_full_path());
        if(!$as_array)
        {
            return $doc;
        }

        $xpath = new DOMXPath($doc);
        // Get all the tables
        $tables = $xpath->query('table');

        $schema_array = array();
        foreach($tables as $table)
        {
            $table_name = $table->getAttribute('name');
            $schema_array[$table_name] = array();
            $columns = $xpath->query('column', $table);
            foreach($columns as $column)
            {

                $column_array = $this->xml2array(simplexml_import_dom($column));
                foreach($column_array as $name=>$value)
                {
                    if(is_array($value) && empty($value))
                    {
                        $column_array[$name] = 0;
                    }
                }

                $schema_array[$table_name][$column_array['name']]  = $column_array;
            }
        }

        return $schema_array;

    }

    /**
     * function xml2array
     *
     * This function is part of the PHP manual.
     *
     * The PHP manual text and comments are covered by the Creative Commons
     * Attribution 3.0 License, copyright (c) the PHP Documentation Group
     *
     * @author  k dot antczak at livedata dot pl
     * @date    2011-04-22 06:08 UTC
     * @link    http://www.php.net/manual/en/ref.simplexml.php#103617
     * @license http://www.php.net/license/index.php#doc-lic
     * @license http://creativecommons.org/licenses/by/3.0/
     * @license CC-BY-3.0 <http://spdx.org/licenses/CC-BY-3.0>
     */
    function xml2array ( $xmlObject, $out = array () )
    {
        foreach ( (array) $xmlObject as $index => $node )
            $out[$index] = ( is_object ( $node ) ) ? $this->xml2array ( $node ) : $node;

        return $out;
    }


    /**
     * Returns an array that represents the schema of the current database
     *
     * @return array
     */
    private function get_schema(){
        $schema = array();
        foreach($this->total_tables as $table_name => $table)
        {
            foreach($this->db->field_data($table_name) as $field)
            {
            	// If the field has a collation, it gets added to its data
            	if($field->type == 'varchar'
            	|| $field->type == 'text'
            	|| $field->type == 'char'
            	|| $field->type == 'tinytext'
            	|| $field->type == 'mediumtext'
            	|| $field->type == 'longtext')
            	{
            		$result = mysql_query('
	            		SELECT collation_name FROM information_schema.`COLUMNS` C
	            		WHERE table_schema = "' . $this->db->database . '"
	            		AND table_name = "' . $table_name . '"
	            		AND column_name = "' . $field->name . '"
            		');
            		$results = mysql_fetch_row($result);
            		$field->collation = $results[0];
            	}

            	// Checking if the values of the column can be NULL or not
            	$result = mysql_query('
            			SELECT is_nullable FROM information_schema.`COLUMNS` C
            			WHERE table_schema = "' . $this->db->database . '"
            			AND table_name = "' . $table_name . '"
            			AND column_name = "' . $field->name . '"
            			');
            	$results = mysql_fetch_row($result);
            	$field->null = strtolower($results[0]);
                
                // Fix enum types
                if($field->type == 'enum')
                {
                    $result = mysql_query('describe '.$table_name.' '.$field->name);
                    $results = mysql_fetch_row($result);
                    $field->type = $results[1];
                }

                foreach($field as $property => $value)
                {
                    $schema[$table_name][$field->name][$property] = $value;
                }
            }

        }
        return $schema;
    }


    private function get_enum_values( $table, $field )
    {
        $type = $this->db->query( "SHOW COLUMNS FROM {$table} WHERE Field = '{$field}'" )->row( 0 )->Type;
        preg_match('/^enum\((.*)\)$/', $type, $matches);
        foreach( explode(',', $matches[1]) as $value )
        {
             $enum[] = trim( $value, "'" );
        }
        return $enum;
    }


    /**
     * Creates an XML file that contains the current structure of the database
     *
     * @return boolean TRUE if succesful, FALSE otherwise
     */
    private function create_schema_file($executed_files)
    {
        $ci =& get_instance();

        $schema_dom = new DOMDocument();
        $schema_element = $schema_dom->createElement('schema');
        $schema_element->setAttribute('executed_files', $executed_files);
        $schema_dom->appendChild($schema_element);

        foreach($this->get_schema() as $table_name => $fields)
        {
            $out_fields = array();
            foreach($fields as $field=>$properties)
            {
            	// Removing the default, primary_key and null data, if they have the default values
            	if($properties['default'] == NULL)
            	{
            		unset($properties['default']);
            	}
            	if(isset($properties['primary_key']) && $properties['primary_key'] == 0)
            	{
            		unset($properties['primary_key']);
            	}
            	if(isset($properties['null']) && $properties['null'] == 'yes')
            	{
            		unset($properties['null']);
            	}
                $out_fields[]['column'] = $properties;
            }

            $xml = new SimpleXMLElement('<table name="'.$table_name.'"/>');
            foreach($out_fields as $column)
            {
                $this->array_to_xml($column, $xml);
            }

            $dom_element = dom_import_simplexml($xml);
            $dom_element = $schema_dom->importNode($dom_element, TRUE);
            $schema_dom->documentElement->appendChild($dom_element);
            $schema_dom->formatOutput = TRUE;
        }


        return (file_put_contents($this->schema_full_path(), $schema_dom->saveXML()) !== FALSE);
    }


    /**
     * Compares the db schema that is saved inside the schema file with the schema of the actual database, and returns an array with those changes
     * @return Array
     */
    private function get_changes(){
    	$saved_schema = $this->fix_saved_schema($this->get_saved_schema(TRUE));
        $changes['modifications'] = $this->array_diff_multi_modifications($this->get_schema(), $saved_schema);
        $changes['deletions'] = $this->array_diff_multi_deletions($saved_schema, $this->get_schema());
        return $changes;
    }


    /**
     * Compares two multidimentional arrays and returns an array
     * containing their differences
     * @param array $array1
     * @param array $array2
     * @return array
     */
    private function array_diff_multi_modifications($aArray1, $aArray2) {
        $aReturn = array();


        foreach ($aArray1 as $mKey => $mValue) {
            if (array_key_exists($mKey, $aArray2)) {
                if (is_array($mValue)) {
                    $aRecursiveDiff = $this->array_diff_multi_modifications($mValue, $aArray2[$mKey]);
                    if (count($aRecursiveDiff)) { $aReturn[$mKey] = $aRecursiveDiff; }
                } else {
                    if ($mValue != $aArray2[$mKey]) {
                        $aReturn[$mKey] = $mValue;
                        if($mKey != 'name')
                        {
                        	// Setting the name and the type and max_length even if they are not different, because
                        	// mysql requires them in order to change the other attributes
                        	// (the query has a part that includes: MODIFY $name $type($max_length) )
                        	if(isset($aArray1['name']))
                        	{
                        		$aReturn['name'] = $aArray1['name'];
                        	}
                        	if(isset($aArray1['type']))
                        	{
                        		$aReturn['type'] = $aArray1['type'];
                        	}
                        	if(isset($aArray1['max_length']))
                        	{
                        		$aReturn['max_length'] = $aArray1['max_length'];
                        	}
                        }
                    }
                }
            } else {
                $aReturn[$mKey] = $mValue;
            }
        }

        return $aReturn;
    }

    /**
     * Compares two multidimentional arrays and returns an array
     * containing their differences
     * @param array $array1
     * @param array $array2
     * @return array
     */
    private function array_diff_multi_deletions($aArray1, $aArray2) {
    	$aReturn = array();


    	foreach ($aArray1 as $mKey => $mValue) {
    		if (array_key_exists($mKey, $aArray2)) {
    			if (is_array($mValue)) {
    				$aRecursiveDiff = $this->array_diff_multi_deletions($mValue, $aArray2[$mKey]);
    				if (count($aRecursiveDiff)) {
    					$aReturn[$mKey] = $aRecursiveDiff;
    				}
    			} else {
    				if ($mValue != $aArray2[$mKey] && $mKey == 'name') {
    					$aReturn[$mKey] = $mValue;
    				}
    			}
    		} else {
    			// If it's a sub-array the one that doesn't exist in the other array
    			if (is_array($mValue)) {
    				// If the array has a name in its keys, it means that it's an array of columns
    				// Return the name so that it can be dropped by it.
    				if(isset($mValue['name']))
    				{
    					$aReturn[$mKey]['name'] = $mValue['name'];
    				}
    				// Otherwise, it's a table. Just drop it.
    				else
    				{
    					$aReturn[$mKey]['dropped'] = 'true';
    				}
    			}
    			else {
    				// If the key of the value that will be returned is "name", return it, otherwise,
    				// there is no point in returning anything
    				if($mKey == 'name')
    				{
    					$aReturn[$mKey] = $mValue;
    				}
    			}
    		}
    	}

    	return $aReturn;
    }

    /**
     * Restores all the missing information from the saved schema
     * Info may be missing because the attributes default, null and primary_key
     * get removed from the latest_schema.xml if they have the default values.
     *
     * @param array $schema
     * @return array
     */
    private function fix_saved_schema($schema)
    {

    	foreach($schema as $table_name => $columns)
    	{
    		foreach($columns as $column_name => $properties)
    		{
    			if(!isset($properties['default']))
    			{
    				$schema[$table_name][$column_name]['default'] = NULL;
    			}
    			if(!isset($properties['null']))
    			{
    				$schema[$table_name][$column_name]['null'] = 'yes';
    			}
    			if(!isset($properties['primary_key']))
    			{
    				$schema[$table_name][$column_name]['primary_key'] = 0;
    			}
    		}
    	}
    	return $schema;
    }

    /**
     * Echoes the changes made by the importer on the screen
     */
    function echo_import()
    {
        $output = array();
    	$messages = $this->messages;
    	$execution_times = $this->execution_times;
    	$file_changes = $this->file_changes;
    	$export = $this->export_from_importer;
    	if($file_changes)
    	{
    		$output[] = '<h2>Import complete</h2> <a href="'.base_url().'">Go to your app</a>';

            $exported_files = $export ? $this->exported_files : array();

            // If the message is just one (that there were not exported files), do not display it
            if( !(count($messages) == 1 && !count($exported_files)) ) {
        		foreach($messages as $message)
        		{
        			$output[] = '<p>' . $message . '</p>';
        		}
            }

			if(count($exported_files))
			{
				$output[] = 'Exported the database changes to the file <em>' . $exported_files[0] . '</em> before importing<br>';
			}

    		$output[] = '<h4>Total execution time</h4>';
    		$output[] = '<div>';
    		$output[] = '&nbsp;&nbsp;&nbsp;&nbsp;' . array_sum($execution_times) . ' seconds';
    		$output[] = '</div>';

    		// Separating the report by file first
    		foreach($file_changes as $file_name => $file_data)
    		{
    			$output[] = '<br><hr><h3>' . $file_name . '</h3><hr>';

    			// Separating the files by table along with the action applied to it
    			if(isset($file_data['tables_created']))
    			{
    				foreach($file_data['tables_created'] as $table_name => $table_data)
    				{
    					$output[] = '<h4>Created table <em>' . $table_name . '</em></h4>';
    					// Separating the tables by column and the actions applied to them
    					foreach($table_data as $action => $columns)
    					{
    						switch($action)
    						{
    							case 'columns_created':
    								foreach($columns as $column_name)
    								{
    									$output[] = '&nbsp;&nbsp;&nbsp;&nbsp;Created <em>' . $column_name . '</em><br>';
    								}
    								break;
    						}
    					}
    				}
    			}

    			// Separating the files by table along with the action applied to it
    			if(isset($file_data['tables_altered']))
    			{
    				foreach($file_data['tables_altered'] as $table_name => $table_data)
    				{
    					$output[] = '<h4>Altered table <em>' . $table_name . '</em></h4>';
    					// Separating the tables by column and the actions applied to them
    					foreach($table_data as $action => $columns)
    					{
    						switch($action)
    						{
    							case 'columns_created':
    								foreach($columns as $column_name)
    								{
    									$output[] = '&nbsp;&nbsp;&nbsp;&nbsp;Created <em>' . $column_name . '</em><br>';
    								}
    								break;
    							case 'columns_altered':
    								foreach($columns as $column_name)
    								{
    									$output[] = '&nbsp;&nbsp;&nbsp;&nbsp;Altered <em>' . $column_name . '</em><br>';
    								}
    								break;
    							case 'columns_dropped':
    								foreach($columns as $column_name)
    								{
    									$output[] = '&nbsp;&nbsp;&nbsp;&nbsp;Dropped <em>' . $column_name . '</em><br>';
    								}
    								break;
    						}
    					}
    				}
    			}

    			// Separating the files by table along with the action applied to it
    			if(isset($file_data['tables_dropped']))
    			{
    				foreach($file_data['tables_dropped'] as $table_name => $table_data)
    				{
    					$output[] = '<h4>Dropped table <em>' . $table_name . '</em></h4>';
    					// Separating the tables by column and the actions applied to them
    					foreach($table_data as $action => $columns)
    					{
    						switch($action)
    						{
    							case 'columns_dropped':
    								foreach($columns as $column_name)
    								{
    									$output[] = '&nbsp;&nbsp;&nbsp;&nbsp;Dropped <em>' . $column_name . '</em><br>';
    								}
    								break;
    						}
    					}
    				}
    			}
    		}
    	}
        // If no data changes but there are some messages by the exporter
        else if(count($messages)>1)
        {
            // Removing the "No changes to apply" exporter message
            array_shift($messages);
            // Echoing the messages
            foreach ($messages as $message)
            {
                $output[] = $message . '<br>';
            }
        }
    	else
    	{
    		$output[] = '<h2>Nothing to import</h2> <a href="'.base_url().'">Go to your app</a>';
    	}
        
        echo implode('', $output);
    }

    /**
     * Echoes the changes made by the exporter on the screen
     */
    function echo_export()
    {
    	$exported_files = $this->exported_files;
    	if(count($exported_files))
    	{
    		echo '<h2>Export complete</h2>';
    		echo '<h4>The following changes were exported to the file: ' . $exported_files[0] . '</h4>';
    		$changes = $this->export_changes;
    		if($changes['modifications'])
    		{
    			foreach($changes['modifications'] as $table_name => $columns)
    			{
    				echo "<h4>Updated table <em>" . $table_name . "</em></h4>";

    				foreach($columns as $column_name => $attribute)
    				{
    					echo '&nbsp;&nbsp;&nbsp;&nbsp;Updated <em>' . $column_name . '</em><br>';
    				}
    				if(isset($changes['deletions'][$table_name]))
    				{
    					foreach($changes['deletions'][$table_name] as $column_name => $attribute)
    					{
    						echo '&nbsp;&nbsp;&nbsp;&nbsp;Dropped <em>' . $column_name . '</em><br>';
    					}
    					unset($changes['deletions'][$table_name]);
    				}
    			}
    		}
    		if($changes['deletions'])
    		{
    			foreach($changes['deletions'] as $table_name => $columns)
    			{
    				if(!isset($table_name['dropped']))
    				{
    					echo "<h4>Updated table <em>" . $table_name . "</em></h4>";

    					foreach($columns as $column_name => $attribute)
    					{
    						echo '&nbsp;&nbsp;&nbsp;&nbsp;Dropped <em>' . $column_name . '</em><br>';
    					}
    				}
    				else
    				{
    					echo "<h4>Dropped table <em>" . $table_name . "</em></h4>";
    				}
    			}
    		}
    	}
    	else
    	{
    		echo '<h2>Nothing to export</h2>';
    	}
    }


    /*********************************************************************************************************
     * TRUNCATE                                                                                              *
     *********************************************************************************************************/

    /**
     * Truncates all the database tables
     */
    public function truncate()
    {
    	$this->truncated = FALSE;
    	foreach($this->db->list_tables() as $table_name)
    	{
    		$this->db->truncate($table_name);
    		$this->truncated = TRUE;
    	}

    }

    /**
     * Echoes whether the truncation was successful or not
     */
    function echo_truncate()
    {
    	if($this->truncated)
    	{
    		echo '<h4>All the tables of the database have been truncated</h4>';
    	}
    	else
    	{
    		echo '<h4>There are no tables to truncate</h4>';
    	}
    }


    /*********************************************************************************************************
     * POPULATE                                                                                              *
     *********************************************************************************************************/

    /**
     * Populates the database with test data
     */
    public function populate()
    {
    	// Inserting users
    	$users = array(
    			array('email' => 'vangelis@vangelis.com', 'password'=>123, 'username' => 'vangelis'),
    			array('email' => 'george@george.com',     'password'=>123, 'username' => 'george'),
    			array('email' => 'kostas@kostas.com',     'password'=>123, 'username' => 'kostas'),
                array('email' => 'dionisis@dionisis.com', 'password'=>123, 'username' => 'dionisis')
    	);
    	foreach($users as $user)
    	{
    		$existing_user = $this->db->get_where('users', array('email' => $user['email']));
    		if($existing_user->num_rows())
    		{
	    		$email = str_replace(' ', '_', strtolower($existing_user->row()->email));
				$user_ids[$email] = $existing_user->row()->id;
    		}
    		else
    		{
	    		$this->db->insert('users', $user);
	    		$email = str_replace(' ', '_', strtolower($user['email']));
	    		$user_ids[$email] = $this->db->insert_id();
	    		// Inserting the id in the array and making the id field the first column
	    		$user = array('id' => $user_ids[$email]) + $user;
	    		$this->populate_data['users'][] = $user;
    		}
    	}
    }

    /**
     * Echoes the results of the populate function
     */
    public function echo_populate()
    {
    	echo '<style type="text/css">
    			table, th, td {border: 1px solid black; border-collapse: collapse;}
    			td, th {padding: 5px;}
			  </style>';

    	echo '<h4>The database has been populated with test data</h4>';

    	foreach($this->populate_data as $table_name => $table_rows)
    	{
    		$table_columns = array_keys($table_rows[0]);
    		echo '<table><thead><tr>';
    		echo '<th colspan="' . count($table_columns) . '"><em>' . $table_name . '</em></th></tr><tr>';
    		foreach($table_columns as $column_name)
    		{
    			echo '<td><em>' . $column_name . '<em></td>';
    		}
    		echo '</tr></thead><tbody>';
			foreach($table_rows as $row_num => $row)
			{
				echo '<tr>';
				foreach($row as $column_data)
				{

					echo '<td>' . $column_data . '</td>';
				}
				echo '</tr>';
			}
			echo '</tbody></table><br>';
    	}
    }


    /*********************************************************************************************************
     * DATA IMPORT/EXPORT                                                                                    *
     *********************************************************************************************************/

    /**
     * Imports the data of the exported tables
     * @param array $table_names
     */
    public function import_data( $table_names ) {
        // Check for permissions and the existance of needed files
        $this->import_export_errors = $this->check_for_directory_errors() || $this->import_export_errors;
        
        // Checking if the selected tables exist
        foreach( $table_names as $table_name ) {
            if( !$this->db->table_exists( $table_name ) ) {
                $this->messages[] = 'Table ' . $table_name . ' does not exist. Create it before importing.';
                $this->import_export_errors = TRUE;
            }
        }

        // If there are errors, stop
        if( $this->import_export_errors ) return FALSE;


        // The path to the file that keeps the timestamps of the last updates of the tables
        $latest_updates_filename = $this->output_path . DIRECTORY_SEPARATOR . $this->latest_updates_filename;

        if( !file_exists( $latest_updates_filename ) ) {
            $latest_updates = array();
        } else {
            // Getting the timestamp of the last update of the tables
            $latest_updates = file_get_contents( $latest_updates_filename );
            $latest_updates = (array)json_decode( $latest_updates );
        }

        $scripts = $this->get_non_executed_scripts( $table_names );

        $tables_updated = array();
        foreach( $scripts as $script ) {
            // Truncate the table
            $this->db->truncate( $script['table'] );
            // Run the script
            $this->db->query( $script['query'] );
            $latest_updates[$script['table']] = $script['last_updated'];
            $tables_updated[] = $script['table'];
        }

        // If at least one table was updated
        if( $tables_updated ) {
            // Change the timestamps of the latest updates
            $latest_updates = json_encode( $latest_updates );
            file_put_contents( $latest_updates_filename, $latest_updates );

            // Log the actions taken
            foreach( $tables_updated as $table_updated ) {
                $this->messages[] = 'Updated table ' . $table_updated;
            }
        } else {
            $this->messages[] = 'No new scripts exist';
        }
    }

    /**
     * Exports the data of the selected tables
     * @param array $table_names
     */
    public function export_data( $table_names ) {
        // Check for permissions and the existance of needed files
        $this->import_export_errors = $this->check_for_directory_errors() || $this->import_export_errors;

        // Checking if the selected tables exist
        foreach( $table_names as $table_name ) {
            if( !$this->db->table_exists( $table_name ) ) {
                $this->messages[] = 'Table ' . $table_name . ' does not exist. Create it before exporting.';
                $this->import_export_errors = TRUE;
            }
        }

        if( $this->import_export_errors ) return FALSE;

        $non_executed_scripts = $this->get_non_executed_scripts( $table_names );
        // If non-executed scripts exist, warn the user
        if( $non_executed_scripts ) {
            $this->messages[] = 'Non-executed scripts found. If you have made any changes to the database<br>please, backup your data, run the non-executed scripts via the importer,<br>apply your changes again and then run the exporter.<br><br>Otherwhise, <a href="/mainframe/db/import_data">import</a> right now.';
            $this->import_export_errors = TRUE;
            return FALSE;
        }

        $tables = array();

        // Getting the table information from the db (the data stored and the types of the values)
        foreach( $table_names as $i => $table_name ) {
            // Getting the field data of the table (name, type etc)
            $field_data = $this->db->field_data($table_name);

            $tables[$table_name]['field_types'] = array();
            foreach ( $field_data as $key => $data ) {
                // Storing the field type for each field of the table
                $tables[$table_name]['field_types'][$data->name] = $data->type;
            }

            // Getting the data of the table
            $tables[$table_name]['rows'] = $this->db->get( $table_name )->result_array();
        }


        //Generate a timestamp
        $now = new DateTime(); 
        $now->setTimezone( new DateTimeZone('Europe/Athens') );

        // Creating the script based on the data
        foreach( $tables as $table_name => $table ) {
            // Adding the timestamp to the script
            $script = "-- Last updated on " . $now->format('d-m-Y H:i:s') . "\n";
            $script .= "-- " . $now->format('U') . "\n";

            // The insert statement for each row
            if( $table['rows'] ) {
                $column_names = array_keys( $table['rows'][0] );

                // The $row_data array stores the values of the table row, as it will be used in the query
                $row_data = array();
                // Looping through the rows
                foreach( $table['rows'] as $i => $row ) {
                    $values = array();
                    // Looping through the values of the row
                    foreach( $row as $j => $value ) {
                        // If the value is a varchar, it should be enclosed in quotes
                        if( $table['field_types'][$j] == 'varchar' ) {
                            $values[] = "'" . $value . "'";
                        } else {
                            $values[] = $value;
                        }
                    }
                    // Creating the string of all the values e.g. (value, 'string value', value)
                    $row_data[] = "(" . implode( ", ", $values ) . ")";
                }

                // Creating the insert statement
                $script .= "INSERT INTO `" . $table_name . "` (`" . implode( "`, `", $column_names ) . "`) VALUES\n";
                $script .= implode( ",\n", $row_data );
                $script .= ";";
            }

            // The path and name of the output file
            $sql_file = $this->output_path . DIRECTORY_SEPARATOR . $table_name . '.sql';
            // Exporting the file
            if( file_put_contents( $sql_file, $script ) !== FALSE ) {
                $this->messages[] = "Exported table " . $table_name;
            } else {
                $this->messages[] = "A problem occurred while exporting table " . $table_name;
            }
        }


        // Editing the file that keeps track of the scripts that have ran
        $latest_updates_filename = $this->output_path . DIRECTORY_SEPARATOR . $this->latest_updates_filename;

        // If the "scripts_ran" file doesn't exist, try to create it 
        if( !file_exists( $latest_updates_filename ) ) {
            if( file_put_contents( $latest_updates_filename, '' ) === FALSE ) {
                $this->messages[] = "A problem occurred while creating the <i>" . $this->latest_updates_filename . "</i> file";
                $this->import_export_errors = TRUE;
            }
        }

        // Insert the timestamp of the file in the scripts_ran file
        if( !$this->import_export_errors ) {
            $latest_updates = file_get_contents( $latest_updates_filename );
            $latest_updates = json_decode( $latest_updates );
            foreach( $table_names as $table_name ) {
                $latest_updates->$table_name = $now->format('U');
            }
            $latest_updates = json_encode( $latest_updates );
            file_put_contents( $latest_updates_filename, $latest_updates );
        }

    }

    /**
     * Echoes the messages produced during the export of the data of the database
     */
    public function echo_export_data() {
        if( $this->import_export_errors ) {
            echo '<h2>Data export failed</h2>';
        } else {
            echo '<h2>Data export complete</h2>';
        }
        foreach( $this->messages as $message ) {
            echo $message . "<br>";
        }
        die();
    }

    /**
     * Echoes the messages produced during the import of the data of the database
     */
    public function echo_import_data() {
        if( $this->import_export_errors ) {
            echo '<h2>Data import failed</h2>';
        } else {
            echo '<h2>Data import complete</h2>';
        }
        foreach( $this->messages as $message ) {
            echo $message . "<br>";
        }
        die();
    }


    /**
     * Checks for the existance and the permissions of the directories where the
     * files will be read/written
     */
    private function check_for_directory_errors() {
        $errors = FALSE;

        // If the output path doesn't exist
        if( !is_dir( $this->output_path ) )
        {
            $this->messages[] = 'The path ' . $this->output_path . ' does not exists';
            $errors = TRUE;
        }

        // Checking if the directory where the output files will be written, is readable/writable
        if(!is_writable($this->output_path) || !is_readable($this->output_path)) {
            $this->messages[] = $this->output_path . ' is not a readable or writable directory';
            $errors = TRUE;
        } else {
            // If it is, checking if the contained files are readable/writable
            $not_writable_files = FALSE;
            $files = scandir($this->output_path);
            foreach ($files as $file) {
                if ($file != "." && $file != "..") {
                    if (!is_writable($this->output_path .'/' . $file) || !is_readable($this->output_path .'/' . $file)) {
                        $this->messages[] = $this->output_path .'/' . $file . ' is not readable or writable<br>';
                        $errors = TRUE;
                    }
                }
            }
        }

        return $errors;
    }

    private function get_non_executed_scripts( $table_names ) {
        $all_scripts = array();
        $new_scripts = array();
        foreach( $table_names as $table_name ) {
            // The expected file path of the sql file for the current table
            $table_file_path = $this->output_path . DIRECTORY_SEPARATOR . $table_name . ".sql";
            // If the script exists for this table
            if( file_exists( $table_file_path ) ) {
                // Extract the data
                $script = file_get_contents( $table_file_path );
                $script_lines = explode("\n", $script);
                $last_updated = end( explode(" ", $script_lines[1] ) );

                $all_scripts[] = array(
                    'table'         => $table_name,
                    'last_updated'  => $last_updated,
                    'query'         => $script
                );
            }
        }

        // The path to the file that keeps the timestamps of the last updates of the tables
        $latest_updates_filename = $this->output_path . DIRECTORY_SEPARATOR . $this->latest_updates_filename;

        if( !file_exists( $latest_updates_filename ) ) {
            $latest_updates = array();
        } else {
            // Getting the timestamp of the last update of the tables
            $latest_updates = file_get_contents( $latest_updates_filename );
            $latest_updates = (array)json_decode( $latest_updates );
        }

        $tables_updated = array();
        foreach( $all_scripts as $script ) {
            // If the table hasn't been updated before or the last update script ran is older than the current one
            if( !array_key_exists( $script['table'], $latest_updates ) || (int)$latest_updates[$script['table']] < (int)$script['last_updated'] ) {
                $new_scripts[] = $script;
            }
        }

        return $new_scripts;
    }
}