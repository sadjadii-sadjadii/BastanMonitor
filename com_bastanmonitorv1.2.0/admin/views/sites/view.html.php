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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

class BastanmonitorViewSites extends HtmlView {
    protected $items;
    protected $pagination;
    protected $state;

    public function display($tpl = null) {
        // Fetch data from the model
        $this->items      = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state      = $this->get('State');

        // Check for errors in data retrieval
        if (count($errors = $this->get('Errors'))) {
            throw new \Exception(implode("\n", $errors), 500);
        }

        // Load the custom style file
        HTMLHelper::_('stylesheet', 'com_bastanmonitor/style.css', array('version' => 'auto', 'relative' => true));

        $this->addToolbar();
        
        parent::display($tpl);
    }

    protected function addToolbar() {
        ToolbarHelper::title(Text::_('COM_BASTANMONITOR_MENU_SITES'), 'list');
        ToolbarHelper::addNew('site.add');
        ToolbarHelper::editList('site.edit');
        ToolbarHelper::custom('sites.sync', 'sync', 'sync', Text::_('COM_BASTANMONITOR_BTN_SYNC'), true);
        ToolbarHelper::deleteList(Text::_('COM_BASTANMONITOR_SITES_DELETE_CONFIRM'), 'sites.delete');
    }
}