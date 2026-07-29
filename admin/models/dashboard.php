<?php
defined('_JEXEC') or die;
use Joomla\CMS\MVC\Model\BaseModel;
use Joomla\CMS\Factory;

class BastanmonitorModelDashboard extends BaseModel {

    // ۱. تعداد کل سایت‌ها (بدون نیاز به ستون status)
    public function getTotalSites() {
        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from($db->quoteName('#__bastanmonitor_sites'));
        $db->setQuery($query);
        return (int) $db->loadResult();
    }

    // ۲. تعداد هشدارهای بحرانی (Critical)
    public function getCriticalAlerts() {
        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from($db->quoteName('#__bastanmonitor_alerts'))
            ->where($db->quoteName('severity') . ' = ' . $db->quote('critical'))
            ->andWhere($db->quoteName('is_archived') . ' = 0');
        $db->setQuery($query);
        return (int) $db->loadResult();
    }

    // ۳. میانگین امتیاز سلامت فقط برای سایت‌های لایو (دارای مامور)
    public function getAvgHealthScore() {
        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select('AVG(health_score)')
            ->from($db->quoteName('#__bastanmonitor_sites'))
            ->where($db->quoteName('is_offline') . ' = 0'); // شرط جدید برای حذف سایت‌های آفلاین از معدل‌گیری
        
        $db->setQuery($query);
        $avgScore = $db->loadResult();
        
        // اگر هیچ سایت آنلاینی وجود نداشت، پیش‌فرض ۱۰۰ برمی‌گردد تا ظاهر داشبورد به هم نریزد
        return $avgScore !== null ? round($avgScore) : 100;
    }

    // ۴. لیست سرویس‌ها و دامنه/بکاپ‌های در حال انقضا
    public function getExpiringAssets() {
        return array();
    }
}