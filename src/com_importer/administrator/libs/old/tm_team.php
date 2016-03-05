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

class TM_Team extends TM_Base {
	
	public function __construct() 
	{
		$this->pk		= array("teamcode");
		$this->table	= "#__teammanager_team";
		$this->tt = array(	"teamcode" => array("name"=>"teamcode","type"=>"i"), 
							"team" => array("name"=>"teamname","type"=>"s"));
		parent::__construct();
	}
}