<?php

/**
 * @version     1.0.0
 * @package     com_importer
 * @copyright   Copyright (C) 2015. Alle rechten voorbehouden.
 * @license     GNU General Public License versie 2 of hoger; Zie LICENSE.txt
 * @author      Pieter <schaafsma69@gmail.com> - http://joomla.s-core.nl
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

class ImporterController extends JControllerLegacy {

    /**
     * Method to display a view.
     *
     * @param	boolean			$cachable	If true, the view output will be cached
     * @param	array			$urlparams	An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     *
     * @return	JController		This object to support chaining.
     * @since	1.5
     */
    public function display($cachable = false, $urlparams = false) {
        require_once JPATH_COMPONENT . '/helpers/importer.php';

        $view = JFactory::getApplication()->input->getCmd('view', 'tasks');
        JFactory::getApplication()->input->set('view', $view);

        parent::display($cachable, $urlparams);

        return $this;
    }

}
