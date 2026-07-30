<?php
/**
 * @package     BastanMonitor
 * @copyright   Copyright (C) 2026. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

namespace Bastanmonitor\Component\Bastanmonitor\Administrator\Task;
use Joomla\Component\Scheduler\Administrator\Task\ExecutionTaskInterface;
use Joomla\CMS\Factory;

class SyncTask implements ExecutionTaskInterface {
    
    public function execute(): ?bool {
        $db = Factory::getDbo();
        
        // خواندن تمام سایت‌های لایو (آنلاین)، فعال و دارای آدرس
        $query = $db->getQuery(true)
            ->select('id')
            ->from($db->quoteName('#__bastanmonitor_sites'))
            ->where($db->quoteName('state') . ' = 1')
            ->andWhere($db->quoteName('is_offline') . ' = 0') // چشم‌پوشی از دامنه‌های خام
            ->andWhere($db->quoteName('url') . ' IS NOT NULL')
            ->andWhere($db->quoteName('url') . ' != ' . $db->quote(''));
            
        $db->setQuery($query);
        $siteIds = $db->loadColumn();

        if (empty($siteIds)) {
            return true;
        }

        // لود کردن کنترلر سایت‌ها برای استفاده از متد پردازشگر مشترک
        require_once JPATH_ADMINISTRATOR . '/components/com_bastanmonitor/controllers/sites.php';
        
        // ارسال کانفیگ به کنترلر برای جلوگیری از ارور مسیرها در کرون‌جاب داخلی
        $config = array('base_path' => JPATH_ADMINISTRATOR . '/components/com_bastanmonitor');
        $controller = new \BastanmonitorControllerSites($config);

        foreach ($siteIds as $siteId) {
            // استفاده از همان متد قدرتمند processSiteSync
            $controller->processSiteSync((int) $siteId);
        }

        return true;
    }
}