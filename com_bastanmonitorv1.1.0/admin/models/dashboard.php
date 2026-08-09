<?php
/**
 * @package    BastanMonitor
 * @copyright  Copyright (C) 2026 BastanGraphic. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\BaseModel;
use Joomla\CMS\Factory;

class BastanmonitorModelDashboard extends BaseModel {

    // 1. Total number of sites (no need for status column)
    public function getTotalSites() {
        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from($db->quoteName('#__bastanmonitor_sites'));
        $db->setQuery($query);
        return (int) $db->loadResult();
    }

    // 2. Number of critical alerts
    public function getCriticalAlerts() {
        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from($db->quoteName('#__bastanmonitor_alerts'))
            ->where($db->quoteName('severity') . ' = ' . $db->quote('critical'))
            ->andWhere($db->quoteName('is_archived') . ' = 0');
        $db->setQuery($query);
        return (int) $db->loadResult();
    }

    // 3. Average health score only for live sites (with an agent)
    public function getAvgHealthScore() {
        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select('AVG(health_score)')
            ->from($db->quoteName('#__bastanmonitor_sites'))
            ->where($db->quoteName('is_offline') . ' = 0'); // New condition to exclude offline sites from the average calculation
        
        $db->setQuery($query);
        $avgScore = $db->loadResult();
        
        // If no online sites exist, return a default of 100 to prevent breaking the dashboard layout
        return $avgScore !== null ? round($avgScore) : 100;
    }

    // 4. List of expiring services and domains/backups
    public function getExpiringAssets() {
        return array();
    }
}