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

jimport('joomla.application.component.controllerform');

/**
 * Team controller class.
 */
class ImporterControllerImport extends JControllerForm
{

    function __construct() 
    {
        $this->view_list = 'import';
        parent::__construct();
    }
    
    function import()
    {
		jimport('joomla.filesystem.file');
		include_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'libs'.DS.'tm_base.php');
		include_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'libs'.DS.'tm_person.php');
		include_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'libs'.DS.'tm_team.php');
		include_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'libs'.DS.'tm_role.php');
		include_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'libs'.DS.'tm_member.php');
		
		$file		= JRequest::getVar('jform', null, 'files', 'array');
		$filename	= JFile::makeSafe($file['name']['importfile']);
		$src		= $file['tmp_name']['importfile'];
		
		if (strtolower(JFile::getExt($filename)) != 'csv') {
			$this->setMessage( 'Upload geweigerd. Geen CSV bestand.', 'error' );
			$this->setRedirect( JRoute::_('index.php?option=com_importer&view=import', false));
			return false;
		}
		
		if (($handle = fopen($src, "r")) !== FALSE) 
		{
			$person		= new TM_Person();
			$team		= new TM_Team();
			$role		= new TM_Role();
			$member 	= new TM_Member();
			$errorlog	= "";
			$row		= 0;
			
			while (($data = fgetcsv($handle, 0, ";")) !== FALSE) 
			{
				$row++;
				if ($row==1)
				{
					$colcount	= count($data);
					$columndef	= $data;
				}
				else
				{
					$person->reset();
					$team->reset();
					$role->reset();
					$member->reset();
					$columns = count($data);
					if ($columns==$colcount)
					{
						for($i=0;$i<$colcount;$i++)
						{
							$colname = $columndef[$i];
							if ($person->isProperty($colname)) $person->setProperty($colname, $data[$i]);
							if ($team->isProperty($colname)) $team->setProperty($colname, $data[$i]);
							if ($role->isProperty($colname)) $role->setProperty($colname, $data[$i]);
							if ($member->isProperty($colname)) $member->setProperty($colname, $data[$i]);
						}
						$person->update();
						$team->update();
						$role->update();
						$member->update();
					}
					else
					{
						$errorlog .= "Ignored row $row. Excpected $colcount columns, got $columns.</br>";
					}
				}
		    }
		    fclose($handle);
			//$person->cleanUpdate();
			//$team->cleanUpdate();
			//$role->cleanUpdate();
			//$member->cleanUpdate();
			
			$this->setMessage( 'Upload ready</br>'.$errorlog );
	    	$redirectTo = JRoute::_('index.php?option='.JRequest::getVar('option').'&controller=com_importer&view=tasks',false);
	    	$this->setRedirect( $redirectTo );
		}
		else
		{
			$this->setMessage( 'Kan bestand niet benaderen', 'error' );
			$this->setRedirect( JRoute::_('index.php?option='.JRequest::getVar('option').'&controller=com_importer&view=import',false));
			return false;
		}
    }
	
	function starttask()
	{
		$redirectTo = JRoute::_('index.php?option='.JRequest::getVar('option').'&controller=com_importer&view=import',false);
		$this->setRedirect( $redirectTo );	
	}
	
	function cancel()
	{
		$this->setMessage( 'Action canceled' );
		$redirectTo = JRoute::_('index.php?option='.JRequest::getVar('option').'&controller=com_importer&view=tasks',false);
		$this->setRedirect( $redirectTo );
	}
}