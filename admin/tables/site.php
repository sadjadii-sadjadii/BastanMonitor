<?php
defined('_JEXEC') or die;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;

class BastanmonitorTableSite extends Table {
    public function __construct(DatabaseDriver $db) {
        // معرفی نام جدول دیتابیس و کلید اصلی (id) به جوملا
        parent::__construct('#__bastanmonitor_sites', 'id', $db);
    }
}