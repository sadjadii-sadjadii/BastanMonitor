<?php
/**
 * @package     BastanMonitor
 * @copyright   Copyright (C) 2026 BastanGraphic. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
use Joomla\CMS\MVC\Model\ListModel;

class BastanmonitorModelAssets extends ListModel {

    protected function populateState($ordering = 'a.expiration_date', $direction = 'ASC') {
        // Get the search keyword and store it in State for correct pagination functionality
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);
        // Initialize standard list state
        parent::populateState($ordering, $direction);
    }

    protected function getListQuery() {
        $db = $this->getDatabase();
        $query = $db->getQuery(true);
        // Select information from the assets table
        $query->select($db->quoteName(array('a.id', 'a.site_id', 'a.type', 'a.host_company', 'a.expiration_date')))
              ->from($db->quoteName('#__bastanmonitor_assets', 'a'));
         // Join with the sites table to get the site name instead of the ID
        $query->select($db->quoteName('s.title', 'site_title'))
              ->join('LEFT', $db->quoteName('#__bastanmonitor_sites', 's') . ' ON (' . $db->quoteName('a.site_id') . ' = ' . $db->quoteName('s.id') . ')');
        // Handle search filter (searches in asset type, host company, and related site title)
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            $search = $db->quote('%' . $db->escape(trim($search), true) . '%');
            $query->where(
                '(' . $db->quoteName('a.type') . ' LIKE ' . $search . 
                ' OR ' . $db->quoteName('a.host_company') . ' LIKE ' . $search . 
                ' OR ' . $db->quoteName('s.title') . ' LIKE ' . $search . ')'
            );
        }
        // Apply sorting based on state ordering and direction
        $orderCol = $this->state->get('list.ordering', 'a.expiration_date');
        $orderDir = $this->state->get('list.direction', 'ASC');
        $query->order($db->escape($orderCol . ' ' . $orderDir));

        return $query;
    }
}