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

/**
 * @param	array	A named array
 * @return	array
 */
function ImporterBuildRoute(&$query) {
    $segments = array();

    if (isset($query['task'])) {
        $segments[] = implode('/', explode('.', $query['task']));
        unset($query['task']);
    }
    if (isset($query['view'])) {
        $segments[] = $query['view'];
        unset($query['view']);
    }
    if (isset($query['id'])) {
        $segments[] = $query['id'];
        unset($query['id']);
    }

    return $segments;
}

/**
 * @param	array	A named array
 * @param	array
 *
 * Formats:
 *
 * index.php?/importer/task/id/Itemid
 *
 * index.php?/importer/id/Itemid
 */
function ImporterParseRoute($segments) {
    $vars = array();

    // view is always the first element of the array
    $vars['view'] = array_shift($segments);

    while (!empty($segments)) {
        $segment = array_pop($segments);
        if (is_numeric($segment)) {
            $vars['id'] = $segment;
        } else {
            $vars['task'] = $vars['view'] . '.' . $segment;
        }
    }

    return $vars;
}
