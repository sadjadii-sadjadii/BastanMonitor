<?php 
defined('_JEXEC') or die; 
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
?>
<div class="container-fluid pt-3 bastan-sites-wrap">
    <form action="index.php?option=com_bastanmonitor&view=sites" method="post" name="adminForm" id="adminForm">
        <table class="table table-striped table-hover align-middle">
            <thead>
                <tr>
                    <th width="1%" class="text-center">
                        <?php echo HTMLHelper::_('grid.checkall'); ?>
                    </th>
                    <th><?php echo Text::_('COM_BASTANMONITOR_FIELD_SITE_TITLE'); ?></th>
                    <th><?php echo Text::_('COM_BASTANMONITOR_FIELD_DOMAIN'); ?></th>
                    <th width="15%" class="text-center"><?php echo Text::_('COM_BASTANMONITOR_FIELD_HEALTH_SCORE'); ?></th>
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
                            
                            <!-- ستون امتیاز سلامت -->
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
        </table>
        
        <input type="hidden" name="task" value="">
        <input type="hidden" name="boxchecked" value="0">
        <?php echo HTMLHelper::_('form.token'); ?>
    </form>
</div>