<?php
class dbfield {
	
	private $dbName, $type, $default, $cvsName;
	private $value, $format, $foreign;
	private $isPrimKey, $isSelectKey, $isRequired;
	
	public function __construct($dbName, $type, $default, $cvsName, $foreignKey) 
	{
		$this->dbName	= $dbName;
		$this->type		= $type;
		$this->foreign  = $foreignKey;
		
		if ($default!='') $this->default = $default;
		if (!empty($cvsName)) $this->cvsName = $cvsName;
		
		$this->isPrimKey	= false; 
		$this->isRequired	= false;
		$this->isPrimKey	= false;
		$this->isSelectKey	= false;
	}
	
	public function setValue($cvsValue, $cvsFormat) {
		$this->value = $cvsValue;
		if ($cvsFormat=="catchall") $this->format = $cvsFormat;
		if (!empty($cvsFormat)) $this->format = $cvsFormat;
	}

	public function setCVS($set) {
		$this->cvsSet = $set;
	}
	
	public function getCVSValue($field) {
		foreach($this->cvsSet as $cvsItem) {
			if ($cvsItem->getName() == $field) {
				return $cvsItem->getValue();
			}
		}
		throw new Exception( $field ." is required with no value provided");
	}
	
	public function setCatchAll($data) {
		$this->value = $data;
		$this->type = 'catchall';
	}
	
	public function setPrimKey() {
		$this->isPrimKey = true;
	}
	
	public function setSelectKey() {
		$this->isSelectKey = true;
	}
	
	public function setRequired() {
		$this->isRequired = true;
	}
	
	public function getName() { return $this->dbName; }
	public function isPK() { return $this->isPrimKey; }
	public function isSK() { return $this->isSelectKey; }
	
	public function getValue() {
		
		if ($this->foreign) {
			$table = $this->foreign['table'];
			$field = $this->foreign['field'];
			$match = $this->foreign['matchfield'];
			$cvs   = $this->foreign['cvsfield'];
			$format= isset($this->foreign['matchformat'])?$this->foreign['matchformat']:'';
			$type  = isset($this->foreign['matchtype'])?$this->foreign['matchtype']:'string';
			$cvsval= $this->getCVSvalue($cvs);
			$cvsval= $this->formatValue($cvsval, $type, $format);
			
			$value = " (SELECT $field FROM $table WHERE $match = $cvsval)";
		} else {
			
			if (!isset($this->value) || (string)$this->value=="") {
				if (isset($this->default)) {
					$this->value = (string)$this->default;
				} elseif ($this->isRequired) {
					throw new Exception("Field {$this->dbName} is required with no value provided");
				}
			}
			$value = $this->formatValue($this->value, $this->type, $this->format);
		}
		return $value;
	}

	private function formatValue($value, $type, $format)
	{
		switch($type)
		{
			case 'date':
				$format = isset($format)?$format:'%e-%c-%Y';
				$value	= "STR_TO_DATE('".$value."','".$format."')";
				break;
					
			case 'time':
				$format = isset($format)?$format:'%h:%i';
				$value	= "STR_TO_DATE('".$value."','".$format."')";
				break;
					
			case 'string':
				$value	= ($value=='')?"NULL":"'".$value."'";
				break;
					
			case 'int':
				$value	= is_numeric($value)?$value:"NULL";
				break;
					
			case 'catchall':
				$value = "'".json_encode($value)."'";
				break;
		}
		return $value;
	}
	
	
	public function getCVS() {
		return $this->cvsName;
	}
	
	public function reset() {
		unset($this->value);
	}
}