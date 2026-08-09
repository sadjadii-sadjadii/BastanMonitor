<?php
/**
 * @package     BastanMonitor
 * @copyright   Copyright (C) 2026 BastanGraphic. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\AdminController;

class BastanmonitorControllerAssets extends AdminController {
    public function getModel($name = 'Asset', $prefix = 'BastanmonitorModel', $config = array('ignore_request' => true)) {
        return parent::getModel($name, $prefix, $config);
    }
}