<?php
/**
 * Front-end Entry Point for BastanMonitor Cron Job
 */
defined('_JEXEC') or die;

// Get the token from the URL
$app = \Joomla\CMS\Factory::getApplication();
$token = $app->input->get('cron_token', '', 'string');

// Security check (the same token defined in the controller)
if ($token !== 'BastanMonitorCronSecret2026') {
    header('HTTP/1.1 403 Forbidden');
    die('Access Denied: Invalid Cron Token.');
}

// Load the admin controller from its main path
require_once JPATH_ADMINISTRATOR . '/components/com_bastanmonitor/controllers/sites.php';

// Execute the cron method
$controller = new BastanmonitorControllerSites();
$controller->cron();