<?php
/**
 * @package     BastanMonitor
 * @copyright   Copyright (C) 2026 BastanGraphic. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;

class BastanmonitorModelAssets extends ListModel {
    protected function getListQuery() {
        $db = $this->getDatabase();
        $query = $db->getQuery(true);
        
       // Select information from the assets table
        $query->select($db->quoteName(array('a.id', 'a.site_id', 'a.type', 'a.host_company', 'a.expiration_date')));
        $query->from($db->quoteName('#__bastanmonitor_assets', 'a'));
        
        // Join with the sites table to get the site name instead of the ID
        $query->select($db->quoteName('s.title', 'site_title'));
        $query->join('LEFT', $db->quoteName('#__bastanmonitor_sites', 's') . ' ON (' . $db->quoteName('a.site_id') . ' = ' . $db->quoteName('s.id') . ')');
        
        // Sort by expiration date (nearest first)
        $query->order($db->quoteName('a.expiration_date') . ' ASC');
        
        return $query;
    }
}