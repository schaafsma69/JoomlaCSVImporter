<?php
/**
 * @version     1.0.0
 * @package     com_teammanager
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Pieter <joomla@jort.net> - http://
 */

// No direct access
defined('_JEXEC') or die;

class TM_Base {
	
	protected $tt = array();
	protected $pkval = array();
	protected $pk, $table, $db;
	protected $trackUpdates = true;

	public function __construct() 
	{
		$this->db	=& JFactory::getDBO();
		$this->reset();
	}
	
	public function doNotTrack() {
		$this->trackUpdates = false;
	}
	
	public function reset()
	{
		$this->pkval = array();
		if (count($this->tt)>0)
		{
			foreach($this->tt as $item)
			{
				$name = $item['name'];
				$this->$name = "";
			}
		}
	}
	
	public function isProperty($item) 
	{
		$item	= trim(strtolower($item));
		return array_key_exists($item, $this->tt);
	}
	
	public function setProperty($key, $value)
	{
		$key = trim(strtolower($key));
		// Fix for not having a Player role. Empty is player
		if ($key=="teamrol" && $value=="") $value="Speler";
		
		if (isset($this->tt[$key]))
		{
			$name = $this->tt[$key]['name'];
			$this->$name = $value;
			if (in_array($name,$this->pk))
			{
				$this->pkval[$name] = $value;
			}
		}	
	}

	public function update() 
	{
		$updatetable = array();
		$inserttable = array();
		$insertvaluetable = array();
		if ($this->trackUpdates)
		{
			// Any row with updated value 0 is manually added
			// Any row with updated value 1 is added by import function
			// During import, if a value is found or new, set the updated value to 2
			// After import, any row with value 1 is not updated
			$updatetable = array("updated=2");
			$inserttable = array("updated");
			$insertvaluetable = array("2");
		}
		
		foreach($this->tt as $col)
		{
			$colname = $col['name'];
			$coltype = $col['type'];
			$colval  = "";
			
			switch($coltype)
			{
				case 'd':
					$format = isset($col['format'])?$col['format']:'%e-%c-%Y';
					$colval = "STR_TO_DATE('".$this->$colname."','".$format."')";
					break;
					
				case 't':
					$format = isset($col['format'])?$col['format']:'%h:%i';
					$colval = "STR_TO_DATE('".$this->$colname."','".$format."')";
					break;
							
				case 's':
					$colval = $this->db->Quote($this->$colname);
					break;
					
				case 'i':
					$colval = is_numeric($this->$colname)?$this->$colname:"NULL";
					break;
			}
			
			if (!in_array($colname,$this->pk))
			{
				$updatetable[] = "$colname=$colval";
			}
			else
			{
				$this->pkval[$colname] = $colval;
			}
			$inserttable[] = $colname;
			$insertvaluetable[] = $colval;
		}
		
		$where = array();
		$skip = false;
		
		foreach($this->pkval as $key=>$val) {
			if ($val=="''") 
			{
				$skip = true;
			}
			else
			{
				$where[] = "$key=$val";
			}
		}
		
		if (!$skip)
		{
			$query_select = "SELECT COUNT(*) FROM ".$this->table." WHERE ".implode(" AND ",$where);
			$query_update = "UPDATE ".$this->table." SET ".implode(",",$updatetable)." WHERE ".implode(" AND ",$where);
			$query_insert = "INSERT INTO ".$this->table." (".implode(",",$inserttable).") VALUES (".implode(",",$insertvaluetable).")";
				
			$this->db->setQuery($query_select);
			$num_rows = $this->db->loadResult();
			if ($num_rows>=1)
			{
				$this->db->setQuery($query_update);
			}
			else
			{
				$this->db->setQuery($query_insert);
			}
			$this->db->query();
		}
	}
	
	public function cleanUpdate()
	{
		// Remove all rows that have not been updated (updated value is 1)
		$query = "DELETE FROM $this->table WHERE updated=1";
		$this->db->setQuery($query);
		$this->db->query();
		// Set all rows with updated value 2 to 1.
		// Remaining values are 0:manually added and 1:added by import
		$query = "UPDATE $this->table SET updated=1 WHERE updated=2";
		$this->db->setQuery($query);
		$this->db->query();
	}
}