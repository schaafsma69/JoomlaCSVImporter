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

class TM_Role extends TM_Base 
{
	public function __construct() 
	{
		$this->pk		= array("rolename");
		$this->table	= "#__teammanager_teamrole";
		$this->tt = array( "teamrol" => array("name"=>"rolename","type"=>"s"));
		parent::__construct();
	}
}
