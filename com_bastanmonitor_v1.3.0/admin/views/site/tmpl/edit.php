<?php
/**
 * @package     BastanMonitor
 * @copyright   Copyright (C) 2026 BastanGraphic. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const titleField = document.querySelector('[name="jform[title]"]');
    const domainField = document.querySelector('[name="jform[domain]"]');
    const urlField = document.querySelector('[name="jform[url]"]'); 

    if (titleField && domainField && urlField) {
        
        if (domainField.closest('.control-group')) domainField.closest('.control-group').style.display = 'none';
        if (urlField.closest('.control-group')) urlField.closest('.control-group').style.display = 'none';

        titleField.addEventListener('input', function() {
            let val = this.value.trim();
            
            domainField.value = val;
            
            if (val !== '') {
                if (!/^https?:\/\//i.test(val)) {
                    urlField.value = 'https://' + val;
                } else {
                    urlField.value = val;
                }
            } else {
                urlField.value = '';
            }
        });

        titleField.dispatchEvent(new Event('input'));
    }
});
</script>