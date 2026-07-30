<?php
/**
 * @package     BastanMonitor
 * @copyright   Copyright (C) 2026. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Date\Date;
use Joomla\CMS\HTML\HTMLHelper;

HTMLHelper::_('bootstrap.modal');
?>

<div class="container-fluid pt-3 bastan-monitor-wrap">
    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-4">
        <?php if (!empty($this->items)) : ?>
            <?php foreach ($this->items as$site) : ?>
                <?php 
                    $data =$site->sync_data;
                    
                    // امتیاز سلامت
                    $score =$site->health_score;
                    $scoreClass = 'bg-success text-white';
                    if ($score < 50)$scoreClass = 'bg-danger text-white';
                    elseif ($score < 80)$scoreClass = 'bg-warning text-dark';

                    // بکاپ
                    $backupHtml = Text::_('COM_BASTANMONITOR_GLANCE_NO_BACKUP_INFO');$backupClass = 'bg-secondary text-white';
                    if ($data && !empty($data['last_backup'])) {
                        $backupDate = new Date($data['last_backup']);
                        $diffDays = floor((time() -$backupDate->toUnix()) / 86400);
                        if ($diffDays <= 7) {$backupHtml = Text::sprintf('COM_BASTANMONITOR_GLANCE_BACKUP_REGULAR', $diffDays);$backupClass = 'bg-success text-white';
                        } else {
                            $backupHtml = Text::sprintf('COM_BASTANMONITOR_GLANCE_BACKUP_OLD', $diffDays);$backupClass = 'bg-danger text-white';
                        }
                    }

                    // فایروال
                    $firewallHtml = Text::_('COM_BASTANMONITOR_GLANCE_FIREWALL_OFF');$firewallClass = 'bg-light text-dark border';
                    if ($data && !empty($data['security_logs'])) {$countLogs = count($data['security_logs']);$firewallHtml = Text::sprintf('COM_BASTANMONITOR_GLANCE_FIREWALL_BLOCKED', $countLogs);$firewallClass = 'bg-warning text-dark';
                    } elseif ($data && isset($data['has_firewall']) &&$data['has_firewall']) {
                        $firewallHtml = Text::_('COM_BASTANMONITOR_GLANCE_FIREWALL_ACTIVE');$firewallClass = 'bg-success text-white';
                    }

                    // آپدیت‌ها
                    $updateHtml = Text::_('COM_BASTANMONITOR_GLANCE_SYSTEM_UP_TO_DATE');$updateClass = 'bg-success text-white';
                    if ($data && !empty($data['pending_updates'])) {$upCount = count($data['pending_updates']);$updateHtml = Text::sprintf('COM_BASTANMONITOR_GLANCE_PENDING_UPDATES', $upCount);$updateClass = 'bg-info text-dark';
                    }
                ?>

                <div class="col">
                    <div class="card h-100 shadow-sm border-0 position-relative bm-card">
                        
                        <!-- امتیاز سلامت -->
                        <span class="position-absolute top-0 start-0 translate-middle-y badge rounded-pill <?php echo $scoreClass; ?> m-3 shadow-sm bm-badge-score">
                            <?php echo Text::_('COM_BASTANMONITOR_GLANCE_HEALTH_SCORE'); ?>: <?php echo $score; ?>
                        </span>

                        <div class="card-body text-center p-3 pt-4">
                            <div class="mb-2 mt-2 bm-icon-box">
                                <span class="icon-globe text-primary" style="font-size: 1.8rem;"></span>
                            </div>
                            
                            <h5 class="card-title fw-bold text-dark mb-1">
                                <a href="index.php?option=com_bastanmonitor&task=site.edit&id=<?php echo $site->id; ?>" class="text-decoration-none text-dark">
                                    <?php echo htmlspecialchars((string)$site->title); ?>
                                </a>
                            </h5>
                            
                            <!-- نسخه جوملا و PHP -->
                            <div class="mb-3 d-flex justify-content-center gap-1">
                                <span class="badge bg-secondary">Joomla: <?php echo $data['version'] ?? ($site->cms_version ?: Text::_('COM_BASTANMONITOR_UNKNOWN')); ?></span>
                                <span class="badge bg-dark">PHP: <?php echo $data['php_version'] ?? ($site->php_version ?: Text::_('COM_BASTANMONITOR_UNKNOWN')); ?></span>
                            </div>

                            <hr class="text-muted opacity-25">

                            <!-- دارایی‌ها -->
                            <div class="d-flex flex-column gap-1 mb-3 text-start">
                                <?php if (!empty($site->assets)) : ?>
                                    <?php foreach ($site->assets as$asset) : ?>
                                        <?php 
                                            $expDays = floor((strtotime($asset->expiration_date) - time()) / 86400);
                                            $badgeBg =$expDays > 30 ? 'bg-success text-white' : 'bg-danger text-white';
                                        ?>
                                        <div class="d-flex justify-content-between align-items-center bg-light p-1 px-2 rounded small">
                                            <span><?php echo $asset->type . ' (' .$asset->host_company . ')'; ?></span>
                                            <span class="badge <?php echo $badgeBg; ?>"><?php echo $expDays; ?> <?php echo Text::_('COM_BASTANMONITOR_DAYS'); ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <small class="text-muted text-center py-1"><?php echo Text::_('COM_BASTANMONITOR_GLANCE_NO_ASSETS'); ?></small>
                                <?php endif; ?>
                            </div>

                            <hr class="text-muted opacity-25">

                            <!-- وضعیت‌ها -->
                            <div class="d-flex flex-column gap-2 text-center">
                                <div class="p-2 rounded bm-box-item <?php echo $backupClass; ?>">
                                    ⚠️ <?php echo $backupHtml; ?>
                                </div>
                                <div class="p-2 rounded bm-box-item <?php echo $firewallClass; ?>">
                                    🛡️ <?php echo $firewallHtml; ?>
                                </div>
                                <div class="p-2 rounded bm-box-item <?php echo $updateClass; ?>">
                                    🔄 <?php echo $updateHtml; ?>
                                </div>
                                <div class="p-2 rounded bm-box-item bg-light text-secondary border">
                                    📁 <?php echo Text::_('COM_BASTANMONITOR_GLANCE_FIM_LABEL'); ?>
                                </div>
                            </div>

                        </div>

                        <!-- دکمه مودال جزئیات -->
                        <div class="card-footer bg-white border-0 text-center pb-3">
                            <button type="button" class="btn btn-sm btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#siteDetailsModal<?php echo $site->id; ?>">
                                🔍 <?php echo Text::_('COM_BASTANMONITOR_GLANCE_DETAILS_BTN'); ?>
                            </button>
                            <small class="text-muted d-block mt-2" style="font-size: 0.7rem;">
                                <?php echo Text::_('COM_BASTANMONITOR_GLANCE_LAST_CHECK'); ?>: <?php echo $site->last_checked ? $site->last_checked : Text::_('COM_BASTANMONITOR_NEVER'); ?>
                            </small>
                        </div>
                    </div>
                </div>

                <!-- مودال جزئیات -->
                <div class="modal fade" id="siteDetailsModal<?php echo $site->id; ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title"><?php echo Text::_('COM_BASTANMONITOR_MODAL_REPORT_TITLE'); ?>: <?php echo $site->title; ?></h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body text-start">
                                <h6 class="fw-bold text-primary">📦 <?php echo Text::_('COM_BASTANMONITOR_MODAL_PENDING_UPDATES'); ?>:</h6>
                                <?php if (!empty($data['pending_updates'])) : ?>
                                    <ul class="list-group mb-3">
                                        <?php foreach ($data['pending_updates'] as $extName =>$extVer) : ?>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span><?php echo $extName; ?></span>
                                                <span class="badge bg-info text-dark"><?php echo Text::_('COM_BASTANMONITOR_MODAL_NEW_VERSION'); ?>: <?php echo $extVer; ?></span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else : ?>
                                    <p class="text-success small"><?php echo Text::_('COM_BASTANMONITOR_MODAL_NO_UPDATES'); ?></p>
                                <?php endif; ?>

                                <h6 class="fw-bold text-primary mt-3">🛡️ <?php echo Text::_('COM_BASTANMONITOR_MODAL_FIREWALL_LOGS'); ?>:</h6>
                                <?php if (!empty($data['security_logs'])) : ?>
                                    <ul class="list-group mb-3">
                                        <?php foreach ($data['security_logs'] as$log) : ?>
                                            <li class="list-group-item small">
                                                <span>IP: <strong><?php echo $log['ip'] ?? Text::_('COM_BASTANMONITOR_UNKNOWN'); ?></strong></span> | 
                                                <span><?php echo Text::_('COM_BASTANMONITOR_DATE'); ?>: <?php echo $log['date'] ?? ''; ?></span>
                                                <div class="text-muted text-truncate"><?php echo $log['page'] ?? ''; ?></div>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else : ?>
                                    <p class="text-success small"><?php echo Text::_('COM_BASTANMONITOR_MODAL_NO_ATTACKS'); ?></p>
                                <?php endif; ?>

                                <h6 class="fw-bold text-primary mt-3">📁 <?php echo Text::_('COM_BASTANMONITOR_MODAL_FIM_STATUS'); ?>:</h6>
                                <ul class="list-group">
                                    <?php if (!empty($data['fim'])) : ?>
                                        <?php foreach ($data['fim'] as $file =>$hash) : ?>
                                            <li class="list-group-item d-flex justify-content-between align-items-center small">
                                                <code><?php echo $file; ?></code>
                                                <span class="badge <?php echo ($hash === 'missing') ? 'bg-danger text-white' : 'bg-secondary text-white'; ?>">
                                                    <?php echo ($hash === 'missing') ? Text::_('COM_BASTANMONITOR_FIM_MISSING') : Text::_('COM_BASTANMONITOR_FIM_VALID'); ?>
                                                </span>
                                            </li>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </ul>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal"><?php echo Text::_('COM_BASTANMONITOR_CLOSE'); ?></button>
                            </div>
                        </div>
                    </div>
                </div>

            <?php endforeach; ?>
        <?php else : ?>
            <div class="col-12 text-center py-5">
                <h4 class="text-muted"><?php echo Text::_('COM_BASTANMONITOR_GLANCE_NO_SITES'); ?></h4>
            </div>
        <?php endif; ?>
    </div>
</div>