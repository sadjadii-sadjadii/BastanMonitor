<?php
/**
 * @package    BastanMonitor
 * @copyright  Copyright (C) 2026 BastanGraphic. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
?>

<div class="container-fluid pt-3 bastan-dashboard">
    <!-- First row: Statistics cards -->
    <div class="row mb-4">
        
        <!-- Total sites card -->
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white text-center shadow-sm h-100 bastan-stat-card">
                <div class="card-body d-flex flex-column justify-content-center">
                    <div class="bastan-stat-number"><?php echo (int) $this->totalSites; ?></div>
                    <h5 class="card-title bastan-card-title mt-2"><?php echo Text::_('COM_BASTANMONITOR_TOTAL_SITES'); ?></h5>
                </div>
            </div>
        </div>

        <!-- Critical alerts card -->
        <div class="col-md-3 mb-3">
            <div class="card bg-danger text-white text-center shadow-sm h-100 bastan-stat-card">
                <div class="card-body d-flex flex-column justify-content-center">
                    <div class="bastan-stat-number"><?php echo (int) $this->criticalAlerts; ?></div>
                    <h5 class="card-title bastan-card-title mt-2"><?php echo Text::_('COM_BASTANMONITOR_CRITICAL_ALERTS'); ?></h5>
                </div>
            </div>
        </div>

        <!-- Average health score card -->
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white text-center shadow-sm h-100 bastan-stat-card">
                <div class="card-body d-flex flex-column justify-content-center">
                    <div class="bastan-stat-number"><?php echo (int) $this->avgHealthScore; ?>%</div>
                    <h5 class="card-title bastan-card-title mt-2"><?php echo Text::_('COM_BASTANMONITOR_AVG_HEALTH_SCORE'); ?></h5>
                </div>
            </div>
        </div>

        <!-- Cron job status card -->
        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-dark text-center shadow-sm h-100 bastan-stat-card">
                <div class="card-body d-flex flex-column justify-content-center">
                    <div class="bastan-stat-number"><span class="icon-loop" aria-hidden="true"></span></div>
                    <h5 class="card-title bastan-card-title mt-2"><?php echo Text::_('COM_BASTANMONITOR_CRON_ACTIVE'); ?></h5>
                </div>
            </div>
        </div>
        
    </div>

    <!-- Second row: System status -->
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm border-secondary">
                <div class="card-header bg-dark text-white">
                    <span class="icon-dashboard" aria-hidden="true"></span> 
                    <?php echo Text::_('COM_BASTANMONITOR_SYSTEM_SUMMARY_TITLE'); ?>
                </div>
                <div class="card-body">
                    <div class="p-2 text-success text-center">
                        <strong><?php echo Text::_('COM_BASTANMONITOR_EXCELLENT'); ?></strong> <?php echo Text::_('COM_BASTANMONITOR_SYSTEM_HEALTHY_DESC'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>