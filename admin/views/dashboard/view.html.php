<?php
defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

class BastanmonitorViewDashboard extends HtmlView {
    public $totalSites;
    public $criticalAlerts;
    public $avgHealthScore;
    public $expiringAssets;

    public function display($tpl = null) {
        $this->totalSites     = $this->get('TotalSites');
        $this->criticalAlerts = $this->get('CriticalAlerts');
        $this->avgHealthScore = $this->get('AvgHealthScore');
        $this->expiringAssets = $this->get('ExpiringAssets');

        // بارگذاری فایل استایل اختصاصی داشبورد
        HTMLHelper::_('stylesheet', 'com_bastanmonitor/dashboard.css', array('version' => 'auto', 'relative' => true));

        ToolbarHelper::title(Text::_('COM_BASTANMONITOR_DASHBOARD_TITLE'), 'dashboard');

        parent::display($tpl);
    }
}