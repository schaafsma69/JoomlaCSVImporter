<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Joomlapp
 * @author     Pieter <schaafsmapa@gmail.com>
 * @copyright  2016 Pieter
 * @license    GNU General Public License versie 2 of hoger; Zie LICENSE.txt
 */

defined('_JEXEC') or die;

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::register('JoomlappFrontendHelper', JPATH_COMPONENT . '/helpers/joomlapp.php');

// Execute the task.
$controller = JControllerLegacy::getInstance('Joomlapp');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
