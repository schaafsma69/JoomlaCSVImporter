<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Joomlapp
 * @author     Pieter <schaafsmapa@gmail.com>
 * @copyright  2016 Pieter
 * @license    GNU General Public License versie 2 of hoger; Zie LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_joomlapp'))
{
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependancies
jimport('joomla.application.component.controller');

$controller = JControllerLegacy::getInstance('Joomlapp');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
