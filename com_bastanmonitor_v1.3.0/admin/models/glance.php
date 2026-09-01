<?php
/**
 * @package     BastanMonitor
 * @copyright   Copyright (C) 2026 BastanGraphic. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;

class BastanmonitorModelGlance extends ListModel {
    protected function getListQuery() {
        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select('a.*')
            ->from($db->quoteName('#__bastanmonitor_sites', 'a'))
            ->where($db->quoteName('state') . ' = 1')
            ->order($db->quoteName('title') . ' ASC');
        return $query;
    }
    
    // Fetching assets (host, domain, etc.) to display in each site's card
    public function getSiteAssets($siteId) {
        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__bastanmonitor_assets'))
            ->where($db->quoteName('site_id') . ' = ' . (int) $siteId)
            ->order($db->quoteName('expiration_date') . ' ASC');
        $db->setQuery($query);
        return $db->loadObjectList();
    }
}