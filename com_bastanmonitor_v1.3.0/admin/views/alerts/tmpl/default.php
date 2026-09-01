<?php
/**
 * @package     BastanMonitor
 * @copyright   Copyright (C) 2026 BastanGraphic. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

// Get the search keyword
$saveSearch = $this->state ? $this->escape($this->state->get('filter.search')) : '';
?>

<div class="container-fluid pt-3 bastan-alerts-wrap">
    <form action="index.php?option=com_bastanmonitor&view=alerts" method="post" name="adminForm" id="adminForm">
        
        <!-- Filter and search bar -->
        <div class="row mb-3 align-items-center">
            <div class="col-md-6 col-12 mb-2 mb-md-0">
                <div class="input-group">
                    <input type="text" name="filter[search]" id="filter_search" class="form-control" placeholder="<?php echo Text::_('JSEARCH_FILTER'); ?>" value="<?php echo $saveSearch; ?>">
                    <button type="submit" class="btn btn-primary" title="<?php echo Text::_('JSEARCH_FILTER_SUBMIT'); ?>">
                        <span class="icon-search" aria-hidden="true"></span> <?php echo Text::_('JSEARCH_FILTER_SUBMIT'); ?>
                    </button>
                    <button type="button" class="btn btn-secondary" title="<?php echo Text::_('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('filter_search').value='';this.form.submit();">
                        <span class="icon-times" aria-hidden="true"></span> <?php echo Text::_('JSEARCH_FILTER_CLEAR'); ?>
                    </button>
                </div>
            </div>
            <div class="col-md-6 col-12 text-md-end">
                <div class="d-inline-block">
                    <?php echo $this->pagination->getLimitBox(); ?>
                </div>
            </div>
        </div>

        <table class="table table-striped table-hover align-middle">
            <thead>
                <tr>
                    <th width="1%" class="text-center">
                        <?php echo HTMLHelper::_('grid.checkall'); ?>
                    </th>
                    <th width="15%"><?php echo Text::_('COM_BASTANMONITOR_FIELD_SITE_NAME'); ?></th>
                    <th><?php echo Text::_('COM_BASTANMONITOR_FIELD_ALERT_MESSAGE'); ?></th>
                    <th width="12%" class="text-center"><?php echo Text::_('COM_BASTANMONITOR_FIELD_SEVERITY'); ?></th>
                    <th width="15%" class="text-center"><?php echo Text::_('COM_BASTANMONITOR_FIELD_CREATED_AT'); ?></th>
                    <th width="10%" class="text-center"><?php echo Text::_('COM_BASTANMONITOR_FIELD_STATUS'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($this->items)) : ?>
                    <?php foreach ($this->items as $i => $row) : ?>
                        <tr>
                            <td class="text-center">
                                <?php echo HTMLHelper::_('grid.id', $i, $row->id); ?>
                            </td>
                            <td><strong><?php echo htmlspecialchars((string)$row->site_title); ?></strong></td>
                            <td><?php echo htmlspecialchars((string)$row->message); ?></td>
                            
                            <td class="text-center">
                                <?php 
                                $badge = 'info'; $label = Text::_('COM_BASTANMONITOR_SEVERITY_INFO');
                                if ($row->severity == 'critical') { $badge = 'danger'; $label = Text::_('COM_BASTANMONITOR_SEVERITY_CRITICAL'); }
                                if ($row->severity == 'warning') { $badge = 'warning text-dark'; $label = Text::_('COM_BASTANMONITOR_SEVERITY_WARNING'); }
                                ?>
                                <span class="badge bg-<?php echo $badge; ?>"><?php echo $label; ?></span>
                            </td>
                            
                            <td dir="ltr" class="text-center text-muted">
                                <small><?php echo htmlspecialchars((string)$row->created_at); ?></small>
                            </td>
                            
                            <td class="text-center">
                                <?php if ($row->status == 'resolved') : ?>
                                    <span class="text-success"><span class="icon-check"></span> <?php echo Text::_('COM_BASTANMONITOR_STATUS_RESOLVED'); ?></span>
                                <?php else : ?>
                                    <span class="text-danger fw-bold"><span class="icon-pending"></span> <?php echo Text::_('COM_BASTANMONITOR_STATUS_PENDING'); ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="6" class="text-center p-5 text-success">
                            <span class="icon-check-circle alert-icon-box"></span>
                            <h5 class="fw-bold"><?php echo Text::_('COM_BASTANMONITOR_ALERTS_EMPTY_TITLE'); ?></h5>
                            <?php echo Text::_('COM_BASTANMONITOR_ALERTS_EMPTY_DESC'); ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6">
                        <?php echo $this->pagination->getListFooter(); ?>
                    </td>
                </tr>
            </tfoot>
        </table>
        
        <input type="hidden" name="task" value="">
        <input type="hidden" name="boxchecked" value="0">
        <?php echo HTMLHelper::_('form.token'); ?>
    </form>
</div>