<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Joomlapp
 * @author     Pieter <schaafsmapa@gmail.com>
 * @copyright  2016 Pieter
 * @license    GNU General Public License versie 2 of hoger; Zie LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * Class JoomlappFrontendHelper
 *
 * @since  1.6
 */
class JoomlappFrontendHelper
{
	/**
	 * Get an instance of the named model
	 *
	 * @param   string  $name  Model name
	 *
	 * @return null|object
	 */
	public static function getModel($name)
	{
		$model = null;

		// If the file exists, let's
		if (file_exists(JPATH_SITE . '/components/com_joomlapp/models/' . strtolower($name) . '.php'))
		{
			require_once JPATH_SITE . '/components/com_joomlapp/models/' . strtolower($name) . '.php';
			$model = JModelLegacy::getInstance($name, 'JoomlappModel');
		}

		return $model;
	}
}
