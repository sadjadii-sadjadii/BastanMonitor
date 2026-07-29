<?php
defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

class BastanmonitorViewAssets extends HtmlView {
    protected $items;
    protected $pagination;

    public function display($tpl = null) {
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');

        // بارگذاری فایل استایل اختصاصی
        HTMLHelper::_('stylesheet', 'com_bastanmonitor/assets.css', array('version' => 'auto', 'relative' => true));

        $this->addToolbar();
        parent::display($tpl);
    }

    protected function addToolbar() {
        ToolbarHelper::title(Text::_('COM_BASTANMONITOR_MENU_ASSETS'), 'calendar');
        ToolbarHelper::addNew('asset.add');
        ToolbarHelper::editList('asset.edit');
        ToolbarHelper::deleteList(Text::_('COM_BASTANMONITOR_ASSETS_DELETE_CONFIRM'), 'assets.delete');
    }
}