<?php
/**
 * @package     BastanMonitor
 * @copyright   Copyright (C) 2026 BastanGraphic. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;

class BastanmonitorModelAsset extends AdminModel {

    public function getTable($type = 'Asset', $prefix = 'BastanmonitorTable', $config = array()) {
        return parent::getTable($type, $prefix, $config);
    }

    public function getForm($data = array(), $loadData = true) {
        // Explicitly register the forms folder paths to prevent XML file not found errors
        Form::addFormPath(JPATH_ADMINISTRATOR . '/components/com_bastanmonitor/models/forms');
        Form::addFormPath(JPATH_ADMINISTRATOR . '/components/com_bastanmonitor/forms');

        $form = $this->loadForm('com_bastanmonitor.asset', 'asset', array('control' => 'jform', 'load_data' => $loadData));

        if (empty($form)) {
            return false;
        }

        return $form;
    }

    protected function loadFormData() {
        $data = Factory::getApplication()->getUserState('com_bastanmonitor.edit.asset.data', array());

        if (empty($data)) {
            $data = $this->getItem();
        }

        return $data;
    }
}