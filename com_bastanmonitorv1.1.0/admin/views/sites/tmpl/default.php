<?php
/**
 * @package     BastanMonitor
 * @copyright   Copyright (C) 2026 BastanGraphic. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

// Load core Joomla JavaScript files to enable sorting clicks
HTMLHelper::_('behavior.core'); 

// Retrieve the search term and sort states from Joomla state
$saveSearch = $this->state ? $this->escape($this->state->get('filter.search')) : '';
$listOrder  = $this->state ? $this->escape($this->state->get('list.ordering', 'a.id')) : 'a.id';
$listDirn   = $this->state ? $this->escape($this->state->get('list.direction', 'DESC')) : 'DESC';
?>
<div class="container-fluid pt-3 bastan-sites-wrap">
    <form action="index.php?option=com_bastanmonitor&view=sites" method="post" name="adminForm" id="adminForm">
        
        <!-- Filters row: Search box and display limit box -->
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
                    <!-- Use grid.sort instead of searchtools.sort -->
                    <th><?php echo HTMLHelper::_('grid.sort', 'COM_BASTANMONITOR_FIELD_SITE_TITLE', 'a.title', $listDirn, $listOrder); ?></th>
                    <th><?php echo HTMLHelper::_('grid.sort', 'COM_BASTANMONITOR_FIELD_DOMAIN', 'a.domain', $listDirn, $listOrder); ?></th>
                    <th width="15%" class="text-center"><?php echo HTMLHelper::_('grid.sort', 'COM_BASTANMONITOR_FIELD_HEALTH_SCORE', 'a.health_score', $listDirn, $listOrder); ?></th>
                    
                    <th><?php echo Text::_('COM_BASTANMONITOR_FIELD_AGENT_URL'); ?></th>
                    <th width="8%" class="text-center"><?php echo Text::_('COM_BASTANMONITOR_FIELD_STATUS'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($this->items)) : ?>
                    <?php foreach ($this->items as $i => $row) : ?>
                        <tr>
                            <td class="text-center">
                                <?php echo HTMLHelper::_('grid.id', $i, $row->id); ?>
                            </td>
                            <td>
                                <a href="index.php?option=com_bastanmonitor&task=site.edit&id=<?php echo $row->id; ?>">
                                    <strong><?php echo htmlspecialchars((string)$row->title); ?></strong>
                                </a>
                            </td>
                            <td dir="ltr" class="text-start"><?php echo htmlspecialchars((string)$row->domain); ?></td>
                            
                            <!-- Health score column -->
                            <td class="text-center">
                                <?php if ($row->is_offline) : ?>
                                    <span class="badge bg-light text-secondary border"><?php echo Text::_('COM_BASTANMONITOR_NO_SCAN_NEEDED'); ?></span>
                                <?php else : ?>
                                    <?php 
                                    $score = (int) $row->health_score;
                                    $color = 'success'; 
                                    if ($score < 80 && $score >= 60) $color = 'warning'; 
                                    if ($score < 60) $color = 'danger'; 
                                    ?>
                                    <div class="progress position-relative shadow-sm" style="height: 22px; background-color: #e9ecef;">
                                        <div class="progress-bar bg-<?php echo $color; ?>" role="progressbar" style="width: <?php echo $score; ?>%;" aria-valuenow="<?php echo $score; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                        <span class="position-absolute w-100 text-center fw-bold" style="line-height: 22px; color: <?php echo ($score >= 60 && $score < 80) ? '#000' : '#fff'; ?>; text-shadow: <?php echo ($score >= 60 && $score < 80) ? 'none' : '1px 1px 2px rgba(0,0,0,0.6)'; ?>;">
                                            <?php echo $score; ?> / 100
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </td>

                            <td dir="ltr" class="text-start">
                                <?php if($row->is_offline): ?>
                                    <span class="badge bg-secondary"><?php echo Text::_('COM_BASTANMONITOR_OFFLINE_AGENT'); ?></span>
                                <?php else: ?>
                                    <small class="text-muted"><?php echo htmlspecialchars((string)$row->url); ?></small>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if ($row->state == 1) : ?>
                                    <span class="badge bg-success"><?php echo Text::_('COM_BASTANMONITOR_ACTIVE'); ?></span>
                                <?php else : ?>
                                    <span class="badge bg-danger"><?php echo Text::_('COM_BASTANMONITOR_INACTIVE'); ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="6" class="text-center p-4">
                            <?php echo Text::_('COM_BASTANMONITOR_SITES_EMPTY'); ?>
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
        <!-- Hidden fields for sorting state -->
        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>">
        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>">
        <?php echo HTMLHelper::_('form.token'); ?>
    </form>
</div>