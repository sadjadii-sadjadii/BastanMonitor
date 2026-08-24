<?php
/**
 * @package     BastanMonitor
 * @copyright   Copyright (C) 2026 BastanGraphic. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

// Load base script for grid sorting
HTMLHelper::_('behavior.core');

$saveSearch = $this->state ? $this->escape($this->state->get('filter.search')) : '';
$listOrder  = $this->escape($this->state->get('list.ordering'));
$listDirn   = $this->escape($this->state->get('list.direction'));
?>
<div class="container-fluid pt-3 bastan-archived-wrap">
    <form action="index.php?option=com_bastanmonitor&view=archivedalerts" method="post" name="adminForm" id="adminForm">
        
        <!-- Search bar and limit box -->
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

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h4 class="card-title mb-4"><?php echo Text::_('COM_BASTANMONITOR_ARCHIVED_ALERTS_HEADING'); ?></h4>
                <table class="table table-striped table-hover align-middle">
                    <thead>
                        <tr>
                            <th width="1%" class="text-center">
                                <?php echo HTMLHelper::_('grid.checkall'); ?>
                            </th>
                            <th width="15%">
                                <?php echo HTMLHelper::_('grid.sort', Text::_('COM_BASTANMONITOR_FIELD_SITE_NAME'), 'site_title', $listDirn, $listOrder); ?>
                            </th>
                            <th><?php echo Text::_('COM_BASTANMONITOR_FIELD_ALERT_MESSAGE'); ?></th>
                            <th width="12%" class="text-center">
                                <?php echo HTMLHelper::_('grid.sort', Text::_('COM_BASTANMONITOR_FIELD_SEVERITY'), 'severity', $listDirn, $listOrder); ?>
                            </th>
                            <th width="15%" class="text-center">
                                <?php echo HTMLHelper::_('grid.sort', Text::_('COM_BASTANMONITOR_FIELD_CREATED_AT'), 'created_at', $listDirn, $listOrder); ?>
                            </th>
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
                                        <span class="badge bg-secondary"><?php echo Text::_('COM_BASTANMONITOR_BADGE_ARCHIVED'); ?></span>
                                    </td>
                                    <td dir="ltr" class="text-center text-muted">
                                        <small><?php echo htmlspecialchars((string)$row->created_at); ?></small>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="5" class="text-center p-5 text-muted">
                                    <?php echo Text::_('COM_BASTANMONITOR_ARCHIVED_EMPTY'); ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5">
                                <?php echo $this->pagination->getListFooter(); ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        
        <input type="hidden" name="task" value="">
        <input type="hidden" name="boxchecked" value="0">
        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>">
        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>">
        <?php echo HTMLHelper::_('form.token'); ?>
    </form>
</div>