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

        // Load the custom style file
        HTMLHelper::_('stylesheet', 'com_bastanmonitor/style.css', array('version' => 'auto', 'relative' => true));

        ToolbarHelper::title(Text::_('COM_BASTANMONITOR_DASHBOARD_TITLE'), 'dashboard');

        parent::display($tpl);
    }
}