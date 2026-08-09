<?php
/**
 * @package     BastanMonitor
 * @copyright   Copyright (C) 2026 BastanGraphic. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Factory;

class BastanmonitorModelArchivedalerts extends ListModel {
    
    public function __construct($config = array()) {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'a.id',
                'site_title', 's.title',
                'severity', 'a.severity',
                'created_at', 'a.created_at'
            );
        }
        parent::__construct($config);
    }

    protected function getListQuery() {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);

        $query->select($db->quoteName(array(
            'a.id', 'a.site_id', 'a.severity', 'a.message', 
            'a.status', 'a.created_at'
        )))
        ->select($db->quoteName('s.title', 'site_title'))
        ->from($db->quoteName('#__bastanmonitor_alerts', 'a'))
        ->leftJoin($db->quoteName('#__bastanmonitor_sites', 's') . ' ON ' . $db->quoteName('a.site_id') . ' = ' . $db->quoteName('s.id'))
        ->where($db->quoteName('a.is_archived') . ' = 1');

        // Dynamically apply sorting based on user selection
        $orderCol = $this->state->get('list.ordering', 'a.created_at');
        $orderDirn = $this->state->get('list.direction', 'DESC');
        
        // Prevent SQL Injection using standard escaping
        $query->order($db->escape($orderCol . ' ' . $orderDirn));

        return $query;
    }
}