<?php
/**
 * @package     BastanMonitor
 * @copyright   Copyright (C) 2026 BastanGraphic. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

// Load core script for sorting
HTMLHelper::_('behavior.core');

$saveSearch = $this->state ? $this->escape($this->state->get('filter.search')) : '';
$listOrder  = $this->state ? $this->escape($this->state->get('list.ordering', 'a.expiration_date')) : 'a.expiration_date';
$listDirn   = $this->state ? $this->escape($this->state->get('list.direction', 'ASC')) : 'ASC';
?>
<div class="container-fluid pt-3 bastan-assets-list-wrap">
    <form action="index.php?option=com_bastanmonitor&view=assets" method="post" name="adminForm" id="adminForm">
        
        <!-- Filters row: Search box and display limit box (Added mb-3 for spacing) -->
        <div class="row align-items-center mb-3 bastan-filter-bar">
            <div class="col-md-6 col-12 mb-2 mb-md-0">
                <div class="input-group bastan-search-group">
                    <input type="text" name="filter[search]" id="filter_search" class="form-control bastan-search-input" placeholder="<?php echo Text::_('JSEARCH_FILTER'); ?>" value="<?php echo $saveSearch; ?>">
                    <button type="submit" class="btn btn-primary bastan-search-btn" title="<?php echo Text::_('JSEARCH_FILTER_SUBMIT'); ?>">
                        <span class="icon-search" aria-hidden="true"></span> <?php echo Text::_('JSEARCH_FILTER_SUBMIT'); ?>
                    </button>
                    <button type="button" class="btn btn-secondary bastan-clear-btn" title="<?php echo Text::_('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('filter_search').value='';this.form.submit();">
                        <span class="icon-times" aria-hidden="true"></span> <?php echo Text::_('JSEARCH_FILTER_CLEAR'); ?>
                    </button>
                </div>
            </div>
            <div class="col-md-6 col-12 text-md-end">
                <div class="d-inline-block bastan-limit-box">
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
                    <th><?php echo HTMLHelper::_('grid.sort', Text::_('COM_BASTANMONITOR_FIELD_ASSET_TYPE'), 'a.type', $listDirn, $listOrder); ?></th>
                    <th><?php echo HTMLHelper::_('grid.sort', Text::_('COM_BASTANMONITOR_FIELD_RELATED_SITE'), 'site_title', $listDirn, $listOrder); ?></th>
                    <th><?php echo HTMLHelper::_('grid.sort', Text::_('COM_BASTANMONITOR_FIELD_HOST_COMPANY'), 'a.host_company', $listDirn, $listOrder); ?></th>
                    <th><?php echo HTMLHelper::_('grid.sort', Text::_('COM_BASTANMONITOR_FIELD_EXPIRATION_DATE'), 'a.expiration_date', $listDirn, $listOrder); ?></th>
                    <th class="text-center"><?php echo Text::_('COM_BASTANMONITOR_FIELD_DAYS_LEFT'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($this->items)) : ?>
                    <?php 
                    // Get today's date and set its time to zero for accurate calculation
                    $today = new DateTime('today'); 
                    ?>
                    <?php foreach ($this->items as $i => $row) : ?>
                        <?php
                        // Calculate exact difference in days
                        $expDate = new DateTime($row->expiration_date);
                        $expDate->setTime(0, 0, 0); // Set expiration date time to zero
                        $diff = $today->diff($expDate);
                        $daysLeft = (int) $diff->format('%R%a'); // Output: positive for the future, negative for the past
                        
                        // Determine color and text based on remaining days
                        if ($daysLeft > 30) {
                            $badgeClass = 'bg-success';
                            $daysText = $daysLeft . ' days left';
                        } elseif ($daysLeft > 0 && $daysLeft <= 30) {
                            $badgeClass = 'bg-warning text-dark';
                            $daysText = $daysLeft . ' days left';
                        } elseif ($daysLeft === 0) {
                            $badgeClass = 'bg-danger';
                            $daysText = 'Expires today';
                        } else {
                            $badgeClass = 'bg-dark';
                            $daysText = abs($daysLeft) . ' days ago';
                        }
                        ?>
                        <tr>
                            <td class="text-center">
                                <?php echo HTMLHelper::_('grid.id', $i, $row->id); ?>
                            </td>
                            <td>
                                <a href="index.php?option=com_bastanmonitor&task=asset.edit&id=<?php echo $row->id; ?>">
                                    <?php echo Text::_('COM_BASTANMONITOR_TYPE_' . strtoupper($row->type)); ?>
                                </a>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars((string)$row->site_title); ?></strong>
                            </td>
                            <td><?php echo htmlspecialchars((string)$row->host_company); ?></td>
                            <td dir="ltr" class="text-start">
                                <?php echo htmlspecialchars((string)$row->expiration_date); ?>
                            </td>
                            <td class="text-center">
                                <span class="badge <?php echo $badgeClass; ?> px-2 py-1" style="font-size: 13px;">
                                    <?php echo $daysText; ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="6" class="text-center p-4 text-muted"><?php echo Text::_('COM_BASTANMONITOR_ASSETS_EMPTY'); ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
            
            <!-- Footer section added to display pagination at the bottom of the table -->
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
        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>">
        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>">
        <?php echo HTMLHelper::_('form.token'); ?>
    </form>
</div>