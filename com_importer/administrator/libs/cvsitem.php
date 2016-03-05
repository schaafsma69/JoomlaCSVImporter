<?php

class cvsitem {
	
	private $cvsname, $itemValue, $itemFormat, $emptyIsValue;
	
	public function __construct( $name, $eiv, $format) {
		$this->cvsname		= $name; 
		$this->emptyIsValue = $eiv;
		$this->itemFormat	= $format;
	}
	
	public function setValue($val) {
		if ($val=="" && !$this->emptyIsValue) return;
		$this->itemValue = $val;
	}
	
	public function getName() {
		return $this->cvsname;
	}
	
	public function getFormat() {
		return $this->itemFormat;
	}
	
	public function getValue() {
		return $this->itemValue;
	}
	
	public function reset() {
		unset($this->itemValue);
	}
	
	public function emptyAllowed() {
		return $this->emptyIsValue;
	}
	
}