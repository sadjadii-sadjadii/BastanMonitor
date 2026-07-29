<?php
defined('_JEXEC') or die;
use Joomla\CMS\MVC\Controller\AdminController;

class BastanmonitorControllerAssets extends AdminController {
    public function getModel($name = 'Asset', $prefix = 'BastanmonitorModel', $config = array('ignore_request' => true)) {
        return parent::getModel($name, $prefix, $config);
    }
}