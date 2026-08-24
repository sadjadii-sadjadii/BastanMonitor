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

class BastanmonitorViewAlerts extends HtmlView {
    public $items;
    public $pagination;
    public $state;

    public function display($tpl = null) {
        $this->items      = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state      = $this->get('State');
        
        // Load separate style file
        HTMLHelper::_('stylesheet', 'com_bastanmonitor/style.css', array('version' => 'auto', 'relative' => true));
        
        // Create top page buttons using language keys
        ToolbarHelper::title(Text::_('COM_BASTANMONITOR_ALERTS_TITLE'), 'alert');
        ToolbarHelper::custom('alerts.archive', 'archive', 'archive', Text::_('COM_BASTANMONITOR_BTN_ARCHIVE'), true);
        ToolbarHelper::deleteList(Text::_('COM_BASTANMONITOR_ALERTS_DELETE_CONFIRM'), 'alerts.delete');
        ToolbarHelper::link('index.php?option=com_bastanmonitor&view=archivedalerts', Text::_('COM_BASTANMONITOR_BTN_VIEW_ARCHIVE'), 'archive');
        
        parent::display($tpl);
    }
}