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

jimport('joomla.application.component.controllerform');

/**
 * Task controller class.
 */
class ImporterControllerTask extends JControllerForm
{

    function __construct() {
        $this->view_list = 'tasks';
        parent::__construct();
    }
    
    function save() {
    	$data  = $this->input->post->get('jform', array(), 'array');
    	$this->input->post->set('jform', $data);
    	parent::save();
    }
}