<?php

class dbtable {
	
	private $name, $fields;
	private $insertCond, $timestamp, $updated, $deleteNonUpdated, $cleanupAfter;
	
	public function __construct($name) {
		$this->name				= $name;
		$this->insertCond		= false;
		$this->timestamp		= false;
		$this->updated			= false;
		$this->deleteNonUpdated	= false;
		$this->cleanupAfter		= false;
		$this->catchAll			= "";
		$this->fields 			= array();
	}
	
	public function setValues( $set, $params )
	{
		foreach($this->fields as $field) {
			$field->setCVS($set);
			if (isset($set[$field->getCVS()])) {
				$cvsitem = $set[$field->getCVS()];
				$field->setValue($cvsitem->getValue(), $cvsitem->getFormat() );
			}
		}
		
		if (isset($this->fields[$this->catchAll])) {
			$this->fields[$this->catchAll]->setCatchAll($params);
		}
	}
	
	public function createSQL() {

		$updatetable = array();
		$inserttable = array();
		$insertvalues= array();
		$where		 = array();
		
		if ($this->trackUpdates)
		{
			// Any row with updated value 0 is manually added
			// Any row with updated value 1 is added by import function
			// During import, if a value is found or new, set the updated value to 2
			// After import, any row with value 1 is not updated
			$updatetable[] = "com_importer_updated=2";
			$inserttable[] = "com_importer_updated";
			$insertvalues[]= "2";
		}
		
		foreach($this->fields as $field)
		{
			$value	= $field->getValue();
			$name	= $field->getName();
			
			if ($field->isSK()) {
				if ($value == "''") throw new Exception("Select key has no value");
				$where[] = "$name = $value";
			}
			elseif (!$field->isPK())
			{
				$updatetable[] = $name."=".$value;
			}
			$inserttable[]	= $name;
			$insertvalues[]	= $value;
		}
		
		if (count($where)==0) throw new Exception("Where clause is empty");
		
		$queries['select'] = "SELECT COUNT(*) FROM $this->name WHERE ".implode(" AND ",$where);
		$queries['update'] = "UPDATE $this->name SET ".implode(",",$updatetable)." WHERE ".implode(" AND ",$where);
		$queries['insert'] = "INSERT INTO $this->name (".implode(",",$inserttable).") VALUES (".implode(",",$insertvalues).")";
		
		return $queries;
	}
	
	public function resetValues() {
		foreach($this->fields as $field) { $field->reset(); }
	}
	
	public function setInsertCond($cond) {
		$this->insertCond = true;
	}
	
	public function setTimestamp() {
		$this->timestamp = true;
	}
	
	public function setUpdated() {
		$this->updated = true;
	}
	
	public function setDeleteNonUpdated() {
		$this->deleteNonUpdated = true;
	}
	
	public function setCleanupAfer() {
		$this->cleanupAfter = true;
	}
	
	public function createField($db_name, $type, $default, $cvs_name, $foreignfield) {
		$this->fields["$db_name"] = new dbfield( $db_name, $type, $default, $cvs_name, $foreignfield);
		return $db_name;
	}
	
	public function setPrimKey($fieldId) {
		$this->fields["$fieldId"]->setPrimKey();
	}
	
	public function setSelectKey($fieldId) {
		$this->fields["$fieldId"]->setSelectKey();
	}
	
	public function setCatchAll($fieldId) {
		$this->catchAll = $fieldId;
	}
	
	public function setRequired($fieldId) {
		$this->fields["$fieldId"]->setRequired();
	}
	
}