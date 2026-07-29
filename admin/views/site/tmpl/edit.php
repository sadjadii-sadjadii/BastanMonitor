<?php
defined('_JEXEC') or die;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
?>
<div class="container-fluid pt-3 bastan-site-form">
    <form action="<?php echo Route::_('index.php?option=com_bastanmonitor&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
        
        <div class="row">
            <div class="col-md-9">
                <div class="card">
                    <div class="card-body">
                        <?php echo $this->form->renderFieldset('details'); ?>
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" name="task" value="">
        <?php echo HTMLHelper::_('form.token'); ?>
    </form>
</div>