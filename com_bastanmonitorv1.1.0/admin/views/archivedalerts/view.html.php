<?php
/**
 * @package     BastanMonitor
 * @copyright   Copyright (C) 2026 BastanGraphic. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

class BastanmonitorViewArchivedalerts extends HtmlView {
    public $items;
    public $state;

    public function display($tpl = null) {
        $this->items = $this->get('Items');
        $this->state = $this->get('State');

        // Load the custom style file
        HTMLHelper::_('stylesheet', 'com_bastanmonitor/style.css', array('version' => 'auto', 'relative' => true));

        $this->addToolbar();
        parent::display($tpl);
    }

    protected function addToolbar() {
        ToolbarHelper::title(Text::_('COM_BASTANMONITOR_ARCHIVED_ALERTS_TITLE'), 'archive');
        
        // Restore button (refers to the restore method of the alerts controller)
        ToolbarHelper::custom('alerts.restore', 'undo', 'undo', Text::_('COM_BASTANMONITOR_BTN_RESTORE'), true);
        
        // Permanent delete button
        ToolbarHelper::deleteList(Text::_('COM_BASTANMONITOR_ALERTS_DELETE_CONFIRM'), 'alerts.delete');

        // Back button
        ToolbarHelper::back(Text::_('COM_BASTANMONITOR_BTN_BACK_TO_ALERTS'), 'index.php?option=com_bastanmonitor&view=alerts');
    }
}