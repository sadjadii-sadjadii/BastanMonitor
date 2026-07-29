<?php
defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

class BastanmonitorViewAlerts extends HtmlView {
    public $items;
    public $pagination;

    public function display($tpl = null) {
        $this->items = $this->get('Items');
        
        // بارگذاری فایل استایل جداگانه
        HTMLHelper::_('stylesheet', 'com_bastanmonitor/alerts.css', array('version' => 'auto', 'relative' => true));
        
        // ساخت دکمه‌های بالای صفحه با کلیدهای زبانی
        ToolbarHelper::title(Text::_('COM_BASTANMONITOR_ALERTS_TITLE'), 'alert');
        ToolbarHelper::custom('alerts.archive', 'archive', 'archive', Text::_('COM_BASTANMONITOR_BTN_ARCHIVE'), true);
        ToolbarHelper::deleteList(Text::_('COM_BASTANMONITOR_ALERTS_DELETE_CONFIRM'), 'alerts.delete');
        ToolbarHelper::link('index.php?option=com_bastanmonitor&view=archivedalerts', Text::_('COM_BASTANMONITOR_BTN_VIEW_ARCHIVE'), 'archive');
        
        parent::display($tpl);
    }
}