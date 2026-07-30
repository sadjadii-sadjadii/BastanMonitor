<?php
/**
 * @package     BastanMonitor
 * @copyright   Copyright (C) 2026. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;

class BastanmonitorModelAsset extends AdminModel {
    
    public function getTable($type = 'Asset', $prefix = 'BastanmonitorTable', $config = array()) {
        // معرفی مسیر فایل‌های Table به جوملا
        Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_bastanmonitor/tables');
        return Table::getInstance($type, $prefix, $config);
    }

    public function getForm($data = array(), $loadData = true) {
        // فراخوانی فرم از مسیر models/forms/asset.xml
        $form = $this->loadForm('com_bastanmonitor.asset', 'asset', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }
        return $form;
    }

    protected function loadFormData() {
        // دریافت اطلاعات برای زمانی که کاربر روی دکمه ویرایش کلیک کرده است
        $data = Factory::getApplication()->getUserState('com_bastanmonitor.edit.asset.data', array());
        if (empty($data)) {
            $data = $this->getItem();
        }
        return $data;
    }
}