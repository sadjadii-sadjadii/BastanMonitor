<?php
defined('_JEXEC') or die;
use Joomla\CMS\MVC\Model\ListModel;

class BastanmonitorModelGlance extends ListModel {
    protected function getListQuery() {
        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select('a.*')
            ->from($db->quoteName('#__bastanmonitor_sites', 'a'))
            ->order($db->quoteName('title') . ' ASC');
        return $query;
    }
    
    // گرفتن دارایی‌ها (هاست، دامنه و...) برای نمایش در کارت هر سایت
    public function getSiteAssets($siteId) {
        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__bastanmonitor_assets'))
            ->where($db->quoteName('site_id') . ' = ' . (int) $siteId)
            ->order($db->quoteName('expiration_date') . ' ASC');
        $db->setQuery($query);
        return $db->loadObjectList();
    }
}