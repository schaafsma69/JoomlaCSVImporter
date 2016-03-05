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

jimport('joomla.application.component.controller');

/**
 * Class JoomlappController
 *
 * @since  1.6
 */
class JoomlappController extends JControllerLegacy
{
	/**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   mixed    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController   This object to support chaining.
	 *
	 * @since    1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		require_once JPATH_COMPONENT . '/helpers/joomlapp.php';

		$view = JFactory::getApplication()->input->getCmd('view', 'users');
		JFactory::getApplication()->input->set('view', $view);

		parent::display($cachable, $urlparams);

		return $this;
	}
}
