<?php
/**
 * @package     BastanMonitor
 * @copyright   Copyright (C) 2026 BastanGraphic. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;

class BastanmonitorTableAsset extends Table {
    public function __construct(DatabaseDriver $db) {
        parent::__construct('#__bastanmonitor_assets', 'id', $db);
    }
}