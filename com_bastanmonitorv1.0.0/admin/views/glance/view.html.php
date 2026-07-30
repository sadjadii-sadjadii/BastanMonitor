<?php
/**
 * @package     BastanMonitor
 * @copyright   Copyright (C) 2026. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

class BastanmonitorViewGlance extends HtmlView {
    public $items;

    public function display($tpl = null) {
        $this->items =$this->get('Items');
        
        $model =$this->getModel();
        if (!empty($this->items)) {
            foreach ($this->items as$item) {
                $item->assets =$model->getSiteAssets($item->id);$item->sync_data = !empty($item->last_sync_data) ? json_decode($item->last_sync_data, true) : null;
            }
        }

        // بارگذاری فایل استایل جداگانه صفحه glance
        HTMLHelper::_('stylesheet', 'com_bastanmonitor/glance.css', array('version' => 'auto', 'relative' => true));

        ToolbarHelper::title(Text::_('COM_BASTANMONITOR_GLANCE_TITLE'), 'eye');
        parent::display($tpl);
    }
}