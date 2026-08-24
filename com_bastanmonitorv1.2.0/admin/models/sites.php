<?php
/**
 * @package     BastanMonitor
 * @copyright   Copyright (C) 2026 BastanGraphic. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;

class BastanmonitorModelSites extends ListModel {
    
    public function __construct($config = array()) {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'a.id',
                'title', 'a.title',
                'domain', 'a.domain',
                'health_score', 'a.health_score',
                'state', 'a.state'
            );
        }
        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state.
     */
    protected function populateState($ordering = 'a.id', $direction = 'DESC') {
        // Get search keyword
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        // Get sort column and direction explicitly
        $listOrder = $this->getUserStateFromRequest($this->context . '.filter_order', 'filter_order', $ordering);
        $listDirn  = $this->getUserStateFromRequest($this->context . '.filter_order_Dir', 'filter_order_Dir', $direction);

        $this->setState('list.ordering', $listOrder);
        $this->setState('list.direction', $listDirn);

        parent::populateState($listOrder, $listDirn);
    }

    protected function getListQuery() {
        $db = $this->getDatabase();
        $query = $db->getQuery(true);

        // Select all fields from the sites table
        $query->select($db->quoteName(array(
            'a.id', 'a.title', 'a.domain', 'a.url', 'a.agent_token',
            'a.is_offline', 'a.cms_version', 'a.php_version',
            'a.health_score', 'a.state', 'a.created_at', 'a.last_checked'
        )))
        ->from($db->quoteName('#__bastanmonitor_sites', 'a'));

        // Apply search filter if needed
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            $search = $db->quote('%' . $db->escape($search, true) . '%');
            $query->where('(' . $db->quoteName('a.title') . ' LIKE ' . $search . ' OR ' . $db->quoteName('a.domain') . ' LIKE ' . $search . ')');
        }

        // Get sorting information
        $orderCol = $this->state->get('list.ordering', 'a.id');
        $orderDirn = $this->state->get('list.direction', 'DESC');
        
        // Security check for order direction
        $orderDirn = strtoupper($orderDirn);
        if (!in_array($orderDirn, array('ASC', 'DESC'))) {
            $orderDirn = 'DESC';
        }
        
        // --- Added new and intelligent sorting rules ---
        if ($orderCol === 'a.health_score') {
            // 1. First, separate online sites (0) from offline sites (1) so that offline sites always go to the bottom of the list regardless of their score
            $query->order($db->quoteName('a.is_offline') . ' ASC');
            
            // 2. Then sort by health score
            $query->order($db->quoteName('a.health_score') . ' ' . $orderDirn);
            
            // 3. If scores are equal (e.g., several sites have 100), sort them alphabetically so their position doesn't jump around
            $query->order($db->quoteName('a.domain') . ' ASC');
        } else {
            // For sorting other columns (like domain or title)
            $query->order($db->escape($orderCol) . ' ' . $orderDirn);
            // Sorting stability for other columns in case of ties
            $query->order($db->quoteName('a.id') . ' DESC');
        }

        return $query;
    }
}