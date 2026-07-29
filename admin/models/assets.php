<?php
defined('_JEXEC') or die;
use Joomla\CMS\MVC\Model\ListModel;

class BastanmonitorModelAssets extends ListModel {
    protected function getListQuery() {
        $db = $this->getDatabase();
        $query = $db->getQuery(true);
        
        // انتخاب اطلاعات از جدول دارایی‌ها
        $query->select($db->quoteName(array('a.id', 'a.site_id', 'a.type', 'a.host_company', 'a.expiration_date')));
        $query->from($db->quoteName('#__bastanmonitor_assets', 'a'));
        
        // اتصال به جدول سایت‌ها برای گرفتن نام سایت به جای آیدی
        $query->select($db->quoteName('s.title', 'site_title'));
        $query->join('LEFT', $db->quoteName('#__bastanmonitor_sites', 's') . ' ON (' . $db->quoteName('a.site_id') . ' = ' . $db->quoteName('s.id') . ')');
        
        // مرتب‌سازی بر اساس تاریخ انقضا (نزدیک‌ترین‌ها در بالا)
        $query->order($db->quoteName('a.expiration_date') . ' ASC');
        
        return $query;
    }
}