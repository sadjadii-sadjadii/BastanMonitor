<?php
/**
 * @package     BastanMonitor
 * @copyright   Copyright (C) 2026 BastanGraphic. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;

class BastanmonitorTableSite extends Table {
    public function __construct(DatabaseDriver $db) {
        // Define the database table name and primary key (id) for Joomla
        parent::__construct('#__bastanmonitor_sites', 'id', $db);
    }
}