<?php
/**
 * @package     BastanMonitor
 * @copyright   Copyright (C) 2026 BastanGraphic. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

class BastanmonitorViewAsset extends HtmlView {
    protected $form;
    protected $item;

    public function display($tpl = null) {
        $this->form = $this->get('Form');
        $this->item = $this->get('Item');

        // Load the custom style file
        HTMLHelper::_('stylesheet', 'com_bastanmonitor/style.css', array('version' => 'auto', 'relative' => true));

        $this->addToolbar();
        parent::display($tpl);
    }

    protected function addToolbar() {
        Factory::getApplication()->input->set('hidemainmenu', true);
        $isNew = ($this->item->id == 0);
        
        ToolbarHelper::title($isNew ? Text::_('COM_BASTANMONITOR_ASSET_NEW') : Text::_('COM_BASTANMONITOR_ASSET_EDIT'), 'calendar-plus');
        ToolbarHelper::apply('asset.apply', 'JTOOLBAR_APPLY');
        ToolbarHelper::save('asset.save', 'JTOOLBAR_SAVE');
        ToolbarHelper::cancel('asset.cancel', 'JTOOLBAR_CANCEL');
    }
}