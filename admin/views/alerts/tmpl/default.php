<?php 
defined('_JEXEC') or die; 
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
?>

<div class="container-fluid pt-3 bastan-alerts-wrap">
    <form action="index.php?option=com_bastanmonitor&view=alerts" method="post" name="adminForm" id="adminForm">
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
        </table>
        
        <input type="hidden" name="task" value="">
        <input type="hidden" name="boxchecked" value="0">
        <?php echo HTMLHelper::_('form.token'); ?>
    </form>
</div>