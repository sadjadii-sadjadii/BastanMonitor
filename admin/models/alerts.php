<?php
defined('_JEXEC') or die;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Factory;

class BastanmonitorModelAlerts extends ListModel {
    
    public function __construct($config = array()) {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'a.id',
                'site_id', 'a.site_id',
                'severity', 'a.severity',
                'status', 'a.status',
                'created_at', 'a.created_at',
                'site_title', 's.title'
            );
        }
        parent::__construct($config);
    }

    protected function populateState($ordering = 'a.created_at', $direction = 'DESC') {
        $app = Factory::getApplication();
        $archived = $app->getUserStateFromRequest($this->context . '.filter.archived', 'filter_archived', 0, 'int');
        $this->setState('filter.archived', $archived);

        parent::populateState($ordering, $direction);
    }

    protected function getListQuery() {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);

        $query->select($db->quoteName(array(
            'a.id', 'a.site_id', 'a.severity', 'a.message', 
            'a.status', 'a.is_acknowledged', 'a.is_archived', 'a.created_at'
        )))
        ->select($db->quoteName('s.title', 'site_title'))
        ->from($db->quoteName('#__bastanmonitor_alerts', 'a'))
        ->leftJoin($db->quoteName('#__bastanmonitor_sites', 's') . ' ON ' . $db->quoteName('a.site_id') . ' = ' . $db->quoteName('s.id'));

        // فیلتر آرشیو
        $archived = (int) $this->getState('filter.archived', 0);
        if ($archived === 1) {
            $query->where($db->quoteName('a.is_archived') . ' = 1');
        } else {
            $query->where('(' . $db->quoteName('a.is_archived') . ' = 0 OR ' . $db->quoteName('a.is_archived') . ' IS NULL)');
        }

        // مرتب‌سازی
        $orderCol = $this->state->get('list.ordering', 'a.created_at');
        $orderDirn = $this->state->get('list.direction', 'DESC');
        $query->order($db->escape($orderCol . ' ' . $orderDirn));

        return $query;
    }
}