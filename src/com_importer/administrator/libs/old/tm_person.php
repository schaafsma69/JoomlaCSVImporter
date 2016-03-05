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

class TM_Person extends TM_Base {
	
	public function __construct() { 
		
		$this->pk		= array("relationid");
		$this->table	= "#__teammanager_person";
		$this->tt = array(
			"relatienr" => array("name"=>"relationid","type"=>"s"),
			"geslacht" => array("name"=>"sex","type"=>"s"),
			"roepnaam" => array("name"=>"firstname","type"=>"s"),
			"voorletters" => array("name"=>"initials","type"=>"s"),
			"tussenvoegsel" => array("name"=>"infix","type"=>"s"),
			"achternaam" => array("name"=>"lastname","type"=>"s"),
			"geb datum" => array("name"=>"birthday","type"=>"d"),
			"telefoon" => array("name"=>"phonenumber","type"=>"s"),
			"mobiel" => array("name"=>"mobilenumber","type"=>"s"),
			"2e mobiel" => array("name"=>"mobilenumber2","type"=>"s"),
			"e-mail" => array("name"=>"email","type"=>"s"),
			"2e e-mail" => array("name"=>"email2","type"=>"s"),
		);
		parent::__construct();
	}
}