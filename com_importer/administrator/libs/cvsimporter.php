<?php
include ( __DIR__ . '\cvsitem.php');
include ( __DIR__ . '\dbtable.php');
include ( __DIR__ . '\dbfield.php');

class cvsimporter {
	
	private $dbtables, $cvsitems, $log, $db;
	
	public function __construct($test=false) {
		$this->dbtables = array();
		$this->cvsitems = array();
		$this->log		= array();
		$this->test		= $test;
		
		if (!$this->test)  $this->db =& JFactory::getDBO();
	}
	
	public function setup( $job_definition ) {
		$job = new SimpleXMLElement($job_definition);
		
		if ($job===false) return false;
		
		// Create array of cvs items
		foreach ( $job->input->fields->field as $field ) {
			$this->cvsitems[(string)$field["id"]] = 
				new cvsitem((string)$field->name,
							((string)$field->is_empty_allowed=="true"), 
				 			(string)$field->format );
		}
		
		// Create array of db items
		foreach ( $job->output->tables->table as $table ) {
			
			// Get name of table
			$name	= (string)$table["name"];
			// Create object for table with name $name
			$object = new dbtable($name);
			// If order is set, add to array
			$order = (!empty($table->order))?(int)$table->order:0;
			$this->dbtables[$name]["order"] = $order;
			
			$object->setInsertCond($table->cond_insert);
			
			if ($table->use_timestamp=="true")		$object->setTimestamp();
			if ($table->use_updated=="true")		$object->setUpdated();
			if ($table->delete_non_updated=="true")	$object->setDeleteNonUpdated();
			if ($table->cleanup_after=="true")		$object->setCleanupAfer();
			
			foreach( $table->fields->field as $field ) 
			{
				if ($field->foreignkey) { 
					$foreignfield = array(
						'table'		=> (string)$field->foreignkey->foreigntable,
						'field'		=> (string)$field->foreignkey->foreignid,
						'matchfield'=> (string)$field->foreignkey->foreignmatch,
						'cvsfield'	=> (string)$field->foreignkey->tablematch,
						'type'		=> (string)$field->foreignkey->matchtype,
						'format'	=> (string)$field->foreignkey->matchformat
					);
				} 
				else 
				{
					$foreignfield = false;
				}
				
				$newField = $object->createField( (string)$field->db_name, 
												  (string)$field->type, 
												  (string)$field->default, 
												  (string)$field->cvs_name,
												  $foreignfield );
				
				if ((string)$field->is_primkey=="true")		$object->setPrimKey($newField);
				if ((string)$field->is_required=="true")	$object->setRequired($newField);
				if ((string)$field->is_selectkey=="true")	$object->setSelectKey($newField);
				if ((string)$field->catchall=="true")		$object->setCatchAll($newField);
				
			}
			
			$this->dbtables[$name]['object'] = $object;
		}
		
		if (!$this->test) {
			foreach ($this->dbtables as $name->$table) {
				// Add update table if needed
				$query = "SELECT COUNT(*) FROM information_schema.COLUMNS 
						   WHERE TABLE_SCHEMA=DATABASE() 
							 AND COLUMN_NAME='com_import_update_col' 
							 AND TABLE_NAME='$name'";
				$this->db->setQuery($query);
				$count = $this->db->loadResult();
				if ($count==0) {
					$query = "ALTER TABLE '$name' ADD com_importer_updated INT(1) NOT NULL DEFAULT 0;";
					$this->db->setQuery($query);
					$this->db->query();
				} 
				
			}
		}	

	}
	
	public function show() {
		var_dump($this->cvsitems);
		var_dump($this->dbtables);
		print $this->getLog();
	}
	
	public function getLog() {
		return implode($this->log,"<br/>");
	}
	
	public function run( $cvs_data ) {
		// Get all data rows
		$rows	= str_getcsv( $cvs_data, "\n" );
		$row_nr	= 0;
		// Process rows
		foreach($rows as $row) {
			$row_nr++;
			$data = str_getcsv($row);
			if ($row_nr==1) {
				$header = $data;
				// Valudate defined fields
				foreach ($this->cvsitems as $cvsitem) {
					if (!in_array($cvsitem->getName(), $data)) {
						// Defined field not found. Stop processing.
						$this->log[] = $row_nr." : Field $key not found in file provided.";
						return false;
					}
				}
			}
			else {
				// Check row count
				if (count($header)==count($data)) {
					// Process data
					// First create / reset arrays for CatchAll and Querylist
					$params		= array();
					$querylist	= array();
					
					// Cleanup data arrays
					foreach ($this->cvsitems as $cvsitem) { $cvsitem->reset(); }
					// Make importable array
					$set = array_combine($header, $data);
					// Start copy row values to cvsitems
					foreach ($set as $key=>$value) {
						$found = false;
						foreach ($this->cvsitems as $cvsitem)
						{
							if ($cvsitem->getName()==$key)
							{
								if ($value!="" || $cvsitem->emptyAllowed()) {
									$cvsitem->setValue($value);
									$found = true;
									break 1;
								} else {
									$this->log[] = $row_nr." : Failed to import row, missing value.";
									// Skip this whole line
									// continue rows foreach loop
									continue 3;	
								}
							}
						}
						if (!$found) $params[$key] = $value;
					}
					
					// Use created list to populate Database tables
					// retrieve SQL Statements and execute
					foreach ($this->dbtables as $dbtableitem) {
						if (!isset($dbtableitem['object'])) {
							$this->log[] = "failed to find object : ".print_r($dbtableitem, true);
							continue;
						}
						$dbtable = $dbtableitem['object'];
						// Reset Table values
						$dbtable->resetValues();
						// Set Values of the table fields
						$dbtable->setValues( $this->cvsitems, $params );
						// Retrieve Queries
						try {
							$queries = $dbtable->createSQL();
						
							if (!$this->test) 
							{
								// Execute
								$this->db->setQuery($queries['select']);
								$num_rows = $this->db->loadResult();
								if ($num_rows>=1)
								{
									$this->db->setQuery($queries['update']);
								}
								else
								{
									$this->db->setQuery($queries['insert']);
								}
								$this->db->query();
							}
							
							$this->log[] = $queries['select'];
							$this->log[] = $queries['insert'];
							$this->log[] = $queries['update'];
						} catch (Exception $e) {
							$this->log[] = $row_nr." : ".$e->getMessage();
						}
					}
				} else {
					// Exception. Field count was wrong. Skip row.
					$this->log[] = $row_nr." : Failed to import row.";
				}
			}
		}
		return $this->log;
	}
}