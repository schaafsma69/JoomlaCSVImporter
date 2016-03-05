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

class TM_Member extends TM_Base {
	
	public function __construct() 
	{
		$this->defineTable();
		$this->tt = array(
				"relatienr" => array("name"=>"relationid","type"=>"s", ), 
				"teamcode" => array("name"=>"teamcode","type"=>"i", ),
				"teamrol" => array("name"=>"rolename","type"=>"s", ),
				);
		parent::__construct();
	}
	
	public function defineTable()
	{
		$this->table	= "#__teammanager_teammember";
		$this->fields	= array(
				"relationid"=> array("required"=>true, "type"=>"s", ), 
				"teamcode"	=> array("required"=>true, "type"=>"i", ),
				"rolename"	=> array("required"=>true, "type"=>"s", ),
		);
		$this->pk		= array("relationid", "teamname", "rolename", );
	}
	
}