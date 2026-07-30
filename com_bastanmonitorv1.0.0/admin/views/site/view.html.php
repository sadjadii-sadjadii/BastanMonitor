<?php
/**
 * @package     BastanMonitor
 * @copyright   Copyright (C) 2026. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

class BastanmonitorViewSite extends HtmlView {
    protected $form;
    protected $item;

    public function display($tpl = null) {
        $this->form = $this->get('Form');
        $this->item = $this->get('Item');

        // بارگذاری فایل استایل اختصاصی فرم سایت
        HTMLHelper::_('stylesheet', 'com_bastanmonitor/site.css', array('version' => 'auto', 'relative' => true));

        $this->addToolbar();
        parent::display($tpl);
    }

    protected function addToolbar() {
        Factory::getApplication()->input->set('hidemainmenu', true);
        $isNew = ($this->item->id == 0);
        
        ToolbarHelper::title($isNew ? Text::_('COM_BASTANMONITOR_SITE_NEW') : Text::_('COM_BASTANMONITOR_SITE_EDIT'), 'pencil-2');
        ToolbarHelper::apply('site.apply', 'JTOOLBAR_APPLY');
        ToolbarHelper::save('site.save', 'JTOOLBAR_SAVE');
        ToolbarHelper::cancel('site.cancel', 'JTOOLBAR_CANCEL');
    }
}