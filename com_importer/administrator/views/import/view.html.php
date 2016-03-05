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

jimport('joomla.application.component.view');

/**
 * View to import
 */
class ImporterViewImport extends JView
{
	protected $state;
	protected $form;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->form		= $this->get('Form');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
		JRequest::setVar('hidemainmenu', true);
		JToolBarHelper::title(JText::_('COM_IMPORTER_TITLE_IMPORT'), 'import.png');
		JToolBarHelper::custom( 'import.import', 'upload.png', 'upload.png', 'Import', false, false );
		JToolBarHelper::cancel('import.cancel', 'JTOOLBAR_CANCEL');
	}
}
