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

jimport('joomla.application.component.controllerform');

/**
 * User controller class.
 *
 * @since  1.6
 */
class JoomlappControllerUser extends JControllerForm
{
	/**
	 * Constructor
	 *
	 * @throws Exception
	 */
	public function __construct()
	{
		$this->view_list = 'users';
		parent::__construct();
	}
}
