<?php
/**
 * @package    BastanMonitor
 * @copyright  Copyright (C) 2026 BastanGraphic. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Bastanmonitor\Component\Bastanmonitor\Administrator\Task;

\defined('_JEXEC') or die;

use Joomla\Component\Scheduler\Administrator\Task\ExecutionTaskInterface;
use Joomla\CMS\Factory;

class SyncTask implements ExecutionTaskInterface {
    
    public function execute(): ?bool {
        $db = Factory::getDbo();
        
        // Read all live (online), active sites with a URL
        $query = $db->getQuery(true)
            ->select('id')
            ->from($db->quoteName('#__bastanmonitor_sites'))
            ->where($db->quoteName('state') . ' = 1')
            ->andWhere($db->quoteName('is_offline') . ' = 0') // Ignore raw domains
            ->andWhere($db->quoteName('url') . ' IS NOT NULL')
            ->andWhere($db->quoteName('url') . ' != ' . $db->quote(''));
            
        $db->setQuery($query);
        $siteIds = $db->loadColumn();

        if (empty($siteIds)) {
            return true;
        }

        // Load the sites controller to use the shared processor method
        require_once JPATH_ADMINISTRATOR . '/components/com_bastanmonitor/controllers/sites.php';
        
        // Send config to the controller to prevent path errors in the internal cron job
        $config = array('base_path' => JPATH_ADMINISTRATOR . '/components/com_bastanmonitor');
        $controller = new \BastanmonitorControllerSites($config);

        foreach ($siteIds as $siteId) {
            // Use the same powerful processSiteSync method
            $controller->processSiteSync((int) $siteId);
        }

        return true;
    }
}