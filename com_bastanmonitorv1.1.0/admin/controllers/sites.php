<?php
/**
 * @package    BastanMonitor
 * @copyright  Copyright (C) 2026 BastanGraphic. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Factory;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Language\Text;

class BastanmonitorControllerSites extends AdminController {
    
    public function getModel($name = 'Site', $prefix = 'BastanmonitorModel', $config = array('ignore_request' => true)) {
        return parent::getModel($name, $prefix, $config);
    }

    /**
     * Manual sync button from sites management
     */
    public function sync() {
        $this->checkToken();
        $ids = $this->input->get('cid', array(), 'array');
        
        if (empty($ids)) {
            $this->setMessage(Text::_('COM_BASTANMONITOR_SYNC_NO_SELECTION'), 'warning');
            $this->setRedirect('index.php?option=com_bastanmonitor&view=sites');
            return;
        }

        $successCount = 0;
        $errorCount = 0;

        foreach ($ids as $id) {
            $result = $this->processSiteSync((int) $id);
            if ($result) {
                $successCount++;
            } else {
                $errorCount++;
            }
        }

        $this->setMessage(Text::sprintf('COM_BASTANMONITOR_SYNC_SUCCESS_MSG', $successCount, $errorCount));
        $this->setRedirect('index.php?option=com_bastanmonitor&view=sites');
    }

    /**
     * Automatic execution via host Cron Job
     */
    public function cron() {
        $app = Factory::getApplication();
        $inputToken = $app->input->get('cron_token', '', 'string');
        $secretCronToken = 'BastanMonitorCronSecret2026';

        if ($inputToken !== $secretCronToken) {
            header('HTTP/1.1 403 Forbidden');
            echo "Access Denied: Invalid Cron Token.";
            jexit();
        }

        $db = Factory::getDbo();
        
        $query = $db->getQuery(true)
            ->select('id, title, url')
            ->from($db->quoteName('#__bastanmonitor_sites'));
        
        $db->setQuery($query);
        $sites = $db->loadObjectList();

        if (empty($sites)) {
            echo "No sites found for sync.";
            jexit();
        }

        $successCount = 0;
        $errorCount = 0;

        foreach ($sites as $site) {
            $result = $this->processSiteSync((int) $site->id);
            if ($result) {
                $successCount++;
            } else {
                $errorCount++;
            }
        }

        // Cron messages usually stay in the server log, and it's more standard for them to be in English
        echo "Cron Job executed successfully. Total: " . count($sites) . " sites ($successCount success, $errorCount errors).";
        jexit();
    }

    /**
     * The core common method for processing and analyzing a site
     */
    public function processSiteSync($siteId) {
        $db = Factory::getDbo();
        
        $query = $db->getQuery(true)->select('*')->from($db->quoteName('#__bastanmonitor_sites'))->where($db->quoteName('id') . ' = ' . $siteId);
        $db->setQuery($query);
        $site = $db->loadObject();

        if (!$site || empty($site->url) || empty($site->agent_token)) {
            return false;
        }

        $baseUrl = rtrim(trim($site->url), '/');
        $agentToken = trim($site->agent_token);
        
        // Smart URL Handler (پشتیبانی همزمان از جوملا و وردپرس)
        if (strpos($baseUrl, 'wp-json') !== false) {
            // WordPress REST API format
            $separator = (strpos($baseUrl, '?') !== false) ? '&' : '?';
            $agentUrl = $baseUrl . $separator . 'token=' . urlencode($agentToken);
        } elseif (strpos($baseUrl, 'index.php') !== false) {
            // Full custom Joomla URL provided manually
            $separator = (strpos($baseUrl, '?') !== false) ? '&' : '?';
            $agentUrl = $baseUrl . $separator . 'token=' . urlencode($agentToken);
        } else {
            // Standard Joomla base URL
            $agentUrl = $baseUrl . '/index.php?option=com_ajax&plugin=bastanagent&format=json&token=' . urlencode($agentToken);
        }

        // Initialize cURL with advanced settings to bypass firewalls and CDNs
        $ch = curl_init($agentUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        // --- Anti-Block / CDN Bypass Settings ---
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept: application/json',
            'Cache-Control: no-cache'
        ));
        // ----------------------------------------

        $json = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($httpCode !== 200 || empty($json)) {
            $errorMsg = Text::sprintf('COM_BASTANMONITOR_ALERT_CURL_ERROR', $httpCode);
            if (!empty($curlError)) {
                $errorMsg .= ' | cURL Error: ' . $curlError;
            }
            $this->addAlert($siteId, $errorMsg, 'critical');
            return false;
        }

        $data = json_decode($json, true);
        if (!isset($data['status']) || $data['status'] !== 'success') {
            $this->addAlert($siteId, Text::_('COM_BASTANMONITOR_ALERT_INVALID_RESPONSE'), 'warning');
            return false;
        }

        $healthScore = 100;
        
        if (!empty($data['last_backup'])) {
            $lastBackupDate = new Date($data['last_backup']);
            $now = new Date();
            $diffDays = floor(($now->toUnix() - $lastBackupDate->toUnix()) / 86400);
            
            if ($diffDays > 7) {
                $healthScore -= 20;
                $this->addAlert($siteId, Text::sprintf('COM_BASTANMONITOR_ALERT_OLD_BACKUP', $diffDays), 'warning');
            }
        } else {
            $healthScore -= 20;
            $this->addAlert($siteId, Text::_('COM_BASTANMONITOR_ALERT_NO_BACKUP'), 'warning');
        }

        if (!empty($data['pending_updates'])) {
            $updateCount = count($data['pending_updates']);
            $healthScore -= 10;
            $this->addAlert($siteId, Text::sprintf('COM_BASTANMONITOR_ALERT_PENDING_UPDATES', $updateCount), 'warning');
        }

        if (!empty($data['security_logs'])) {
            $healthScore -= 10;
            $this->addAlert($siteId, Text::_('COM_BASTANMONITOR_ALERT_SECURITY_LOGS'), 'warning');
        }

        $healthScore = max(0, $healthScore);
        $nowObj = Factory::getDate('now', new DateTimeZone('Asia/Tehran'));
        $offset = Factory::getApplication()->get('offset');
        if ($offset) {
            $nowObj->setTimezone(new DateTimeZone($offset));
        }
        $nowDate = $nowObj->toSql();
        
        $updateQuery = $db->getQuery(true)
            ->update($db->quoteName('#__bastanmonitor_sites'))
            ->set($db->quoteName('health_score') . ' = ' . $healthScore)
            ->set($db->quoteName('last_checked') . ' = ' . $db->quote($nowDate))
            ->set($db->quoteName('last_sync_data') . ' = ' . $db->quote($json))
            ->where($db->quoteName('id') . ' = ' . $siteId);
        
        $db->setQuery($updateQuery);
        $db->execute();

        return true;
    }

    private function addAlert($siteId, $message, $severity = 'warning') {
        $db = Factory::getDbo();
        
        $dateObj = Factory::getDate('now', new DateTimeZone('Asia/Tehran'));
        $date = $dateObj->toSql();
        
        $query = $db->getQuery(true)
            ->insert($db->quoteName('#__bastanmonitor_alerts'))
            ->columns(array($db->quoteName('site_id'), $db->quoteName('message'), $db->quoteName('severity'), $db->quoteName('status'), $db->quoteName('created_at')))
            ->values($siteId . ', ' . $db->quote($message) . ', ' . $db->quote($severity) . ', ' . $db->quote('pending') . ', ' . $db->quote($date));
        
        $db->setQuery($query);
        $db->execute();
    }    
}