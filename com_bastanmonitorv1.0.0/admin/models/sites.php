<?php
/**
 * @package     BastanMonitor
 * @copyright   Copyright (C) 2026. All rights reserved.
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

    protected function getListQuery() {
        $db = $this->getDatabase();
        $query = $db->getQuery(true);

        // انتخاب تمام فیلدهای جدول سایت‌ها
        $query->select($db->quoteName(array(
            'a.id', 'a.title', 'a.domain', 'a.url', 'a.agent_token',
            'a.is_offline', 'a.cms_version', 'a.php_version',
            'a.health_score', 'a.state', 'a.created_at', 'a.last_checked'
        )))
        ->from($db->quoteName('#__bastanmonitor_sites', 'a'));

        // اعمال فیلتر جستجو یا وضعیت در صورت نیاز
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            $search = $db->quote('%' . $db->escape($search, true) . '%');
            $query->where('(' . $db->quoteName('a.title') . ' LIKE ' . $search . ' OR ' . $db->quoteName('a.domain') . ' LIKE ' . $search . ')');
        }

        // مرتب‌سازی پیش‌فرض
        $orderCol = $this->state->get('list.ordering', 'a.id');
        $orderDirn = $this->state->get('list.direction', 'DESC');
        $query->order($db->escape($orderCol . ' ' . $orderDirn));

        return $query;
    }
}