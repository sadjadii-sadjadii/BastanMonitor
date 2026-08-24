<?php
/**
 * @package     BastanMonitor
 * @copyright   Copyright (C) 2026 BastanGraphic. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
use Joomla\CMS\MVC\Controller\BaseController;

// Load the extension language
$lang = Joomla\CMS\Factory::getLanguage();
$lang->load('com_bastanmonitor', JPATH_ADMINISTRATOR);

// Call and execute the main controller
$controller = BaseController::getInstance('Bastanmonitor');
$controller->execute(Joomla\CMS\Factory::getApplication()->input->get('task'));
$controller->redirect();