<?php 
defined('_JEXEC') or die; 
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
?>
<div class="container-fluid pt-3 bastan-assets-list-wrap">
    <form action="index.php?option=com_bastanmonitor&view=assets" method="post" name="adminForm" id="adminForm">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th width="1%" class="text-center">
                        <?php echo HTMLHelper::_('grid.checkall'); ?>
                    </th>
                    <th><?php echo Text::_('COM_BASTANMONITOR_FIELD_ASSET_TYPE'); ?></th>
                    <th><?php echo Text::_('COM_BASTANMONITOR_FIELD_RELATED_SITE'); ?></th>
                    <th><?php echo Text::_('COM_BASTANMONITOR_FIELD_HOST_COMPANY'); ?></th>
                    <th><?php echo Text::_('COM_BASTANMONITOR_FIELD_EXPIRATION_DATE'); ?></th>
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
                                <a href="index.php?option=com_bastanmonitor&task=asset.edit&id=<?php echo $row->id; ?>">
                                    <?php echo Text::_('COM_BASTANMONITOR_TYPE_' . strtoupper($row->type)); ?>
                                </a>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars((string)$row->site_title); ?></strong>
                            </td>
                            <td><?php echo htmlspecialchars((string)$row->host_company); ?></td>
                            <td dir="ltr" class="text-start">
                                <span class="badge bg-info text-dark"><?php echo htmlspecialchars((string)$row->expiration_date); ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="5" class="text-center p-4 text-muted"><?php echo Text::_('COM_BASTANMONITOR_ASSETS_EMPTY'); ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <input type="hidden" name="task" value="">
        <input type="hidden" name="boxchecked" value="0">
        <?php echo HTMLHelper::_('form.token'); ?>
    </form>
</div>