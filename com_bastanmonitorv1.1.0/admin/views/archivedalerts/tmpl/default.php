<?php
/**
 * @package     BastanMonitor
 * @copyright   Copyright (C) 2026 BastanGraphic. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
?>
<div class="container-fluid pt-3 bastan-archived-wrap">
    <form action="index.php?option=com_bastanmonitor&view=archivedalerts" method="post" name="adminForm" id="adminForm">
        <div class="card">
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