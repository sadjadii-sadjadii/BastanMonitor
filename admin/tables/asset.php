<?php
defined('_JEXEC') or die;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;

class BastanmonitorTableAsset extends Table {
    public function __construct(DatabaseDriver $db) {
        parent::__construct('#__bastanmonitor_assets', 'id', $db);
    }
}