<?php
/**
 * @package     BastanMonitor
 * @copyright   Copyright (C) 2026. All rights reserved.
 * @license     GNU/GPL
 */
defined('_JEXEC') or die;
use Joomla\CMS\MVC\Controller\BaseController;

// بارگذاری زبان افزونه
$lang = Joomla\CMS\Factory::getLanguage();
$lang->load('com_bastanmonitor', JPATH_ADMINISTRATOR);

// فراخوانی و اجرای کنترلر اصلی
$controller = BaseController::getInstance('Bastanmonitor');
$controller->execute(Joomla\CMS\Factory::getApplication()->input->get('task'));
$controller->redirect();