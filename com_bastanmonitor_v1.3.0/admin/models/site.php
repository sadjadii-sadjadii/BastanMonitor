<?php
/**
 * @package     BastanMonitor
 * @copyright   Copyright (C) 2026 BastanGraphic. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;

class BastanmonitorModelSite extends AdminModel {
    
    /**
     * This function tells Joomla the exact location and name of the table file
     */
    public function getTable($type = 'Site', $prefix = 'BastanmonitorTable', $config = array()) {
        // Add the tables directory path to the system
        Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_bastanmonitor/tables');
        return Table::getInstance($type, $prefix, $config);
    }

    public function getForm($data = array(), $loadData = true) {
        $form = $this->loadForm('com_bastanmonitor.site', 'site', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) { return false; }
        return $form;
    }

    protected function loadFormData() {
        $data = Factory::getApplication()->getUserState('com_bastanmonitor.edit.site.data', array());
        if (empty($data)) { $data = $this->getItem(); }
        return $data;
    }
}