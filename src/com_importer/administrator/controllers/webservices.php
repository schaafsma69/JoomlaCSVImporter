<?php
/**
 * @version     1.0.0
 * @package     com_importer
 * @copyright   Copyright (C) 2015. Alle rechten voorbehouden.
 * @license     GNU General Public License versie 2 of hoger; Zie LICENSE.txt
 * @author      Pieter <schaafsma69@gmail.com> - http://joomla.s-core.nl
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

/**
 * Tasks list controller class.
 */
class ImporterControllerWebservices extends JControllerLegacy
{
	/**
	 * Proxy for getModel.
	 * @since	1.6
	public function getModel($name = 'task', $prefix = 'ImporterModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
	 */
    
	public function csv2array()
	{
		$data	= $_POST['data'];
		$lines	= explode("\n", $data);
		$linenum= 0;
		foreach($lines as $line) { 
			$respons[$linenum] = str_getcsv($line);
			$linenum++;
		}
		$respons = array('respons'=>'ok', 'data'=>$respons);
		
		header('Content-Type: application/json');
		echo json_encode($respons);
		JFactory::getApplication()->close();
	}
	
	public function getDBTables() 
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select(array('table_name'))
				->from($db->quoteName('INFORMATION_SCHEMA.TABLES'))
				->where($db->quoteName('table_type')."=".$db->quote('BASE TABLE'));
		$db->setQuery($query);
		$respons = $db->loadObjectList();		
		$respons = array('respons'=>'ok', 'data'=>$respons);
		
		header('Content-Type: application/json');
		echo json_encode($respons);
		JFactory::getApplication()->close();
	}
	
	public function getDBColumns()
	{
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$data	= $_POST['data'];
		$dlist	= implode(",", $db->quote($data) );
		
		$query->select('*')
				->from($db->quoteName('INFORMATION_SCHEMA.COLUMNS'))
				->where($db->quoteName('table_name')." IN (".$dlist.")");
		$db->setQuery($query);
		$respons = $db->loadObjectList();		
		$respons = array('respons'=>'ok', 'data'=>$respons);
		
		header('Content-Type: application/json');
		echo json_encode($respons);
		JFactory::getApplication()->close();
	}
}