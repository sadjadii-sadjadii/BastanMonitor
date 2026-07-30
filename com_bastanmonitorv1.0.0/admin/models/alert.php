<?php
/**
 * @package     BastanMonitor
 * @copyright   Copyright (C) 2026. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\AdminModel;

class BastanmonitorModelAlert extends AdminModel {
    public function getTable($type = 'Alert', $prefix = 'BastanmonitorTable', $config = array()) {
        return Joomla\CMS\Table\Table::getInstance($type, $prefix, $config);
    }
    public function getForm($data = array(), $loadData = true) {
        return false;
    }
}