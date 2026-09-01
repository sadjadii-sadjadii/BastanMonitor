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
            ->from($db->quoteName('#__bastanmonitor_sites'))
            ->where($db->quoteName('state') . ' = 1');
        
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

        echo "Cron Job executed successfully. Total: " . count($sites) . " sites ($successCount success, $errorCount errors).";
        jexit();
    }

    /**
     * The core common method for processing and analyzing a site (With Auto-Discovery)
     */
    public function processSiteSync($siteId) {
        $lang = Factory::getLanguage();
        $lang->load('com_bastanmonitor', JPATH_ADMINISTRATOR);
        $db = Factory::getDbo();
        
        $query = $db->getQuery(true)->select('*')->from($db->quoteName('#__bastanmonitor_sites'))->where($db->quoteName('id') . ' = ' . (int)$siteId);
        $db->setQuery($query);
        $site = $db->loadObject();

        if (!$site || empty($site->url) || empty($site->agent_token)) {
            return false;
        }

        $baseUrl = rtrim(trim($site->url), '/');
        $agentToken = trim($site->agent_token);
        
        // 1. Build possible endpoint URLs for Joomla and WordPress
        $joomlaUrl = $baseUrl . '/index.php?option=com_ajax&plugin=bastanagent&format=json&token=' . urlencode($agentToken);
        $wpUrl = $baseUrl . '/wp-json/bastan/v1/monitor?token=' . urlencode($agentToken);

        // 2. Helper closure to send cURL requests
        $fetchData = function($url) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36');
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Cache-Control: no-cache'));
            
            $result = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);
            
            return ['code' => $httpCode, 'json' => $result, 'error' => $curlError];
        };

        // 3. First attempt: Assume the site is Joomla
        $response = $fetchData($joomlaUrl);
        $data = json_decode($response['json'], true);

        // 4. If not Joomla (404 error or invalid response), second attempt: Assume WordPress
        if ($response['code'] !== 200 || !$data || !isset($data['status']) || $data['status'] !== 'success') {
            $response = $fetchData($wpUrl);
            $data = json_decode($response['json'], true);
        }

        // 5. Final check of the received response validity
        if ($response['code'] !== 200 || empty($response['json'])) {
            $errorMsg = Text::sprintf('COM_BASTANMONITOR_ALERT_CURL_ERROR', $response['code']);
            if (!empty($response['error'])) {
                $errorMsg .= ' | cURL Error: ' . $response['error'];
            }
            $this->addAlert($siteId, $errorMsg, 'critical');
            return false;
        }

        if (!isset($data['status']) || $data['status'] !== 'success') {
            $this->addAlert($siteId, Text::_('COM_BASTANMONITOR_ALERT_INVALID_RESPONSE'), 'warning');
            return false;
        }

        // --- Continue with Health Score calculations ---
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
            ->set($db->quoteName('last_sync_data') . ' = ' . $db->quote($response['json']))
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
    
    /**
     * Execute remote extension update on the target site
     */
    public function remoteUpdate() {
        $app = Factory::getApplication();
        
        $siteId = $app->input->getInt('site_id', 0);
        $updateId = $app->input->getInt('update_id', 0);

        if (!$siteId || !$updateId) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid site or update ID.']);
            $app->close();
        }

        $db = Factory::getDbo();
        $query = $db->getQuery(true)->select('*')->from($db->quoteName('#__bastanmonitor_sites'))->where($db->quoteName('id') . ' = ' . $siteId);
        $db->setQuery($query);
        $site = $db->loadObject();

        if (!$site || empty($site->url) || empty($site->agent_token)) {
            echo json_encode(['status' => 'error', 'message' => 'Site not found or missing token.']);
            $app->close();
        }

        $baseUrl = rtrim(trim($site->url), '/');
        $agentToken = trim($site->agent_token);
        
        // Smart routing (similar to processSiteSync method)
        if (strpos($baseUrl, 'wp-json') !== false || strpos($baseUrl, 'index.php') !== false) {
            $separator = (strpos($baseUrl, '?') !== false) ? '&' : '?';
            $agentUrl = $baseUrl . $separator . 'token=' . urlencode($agentToken);
        } else {
            $agentUrl = $baseUrl . '/index.php?option=com_ajax&plugin=bastanagent&format=json&token=' . urlencode($agentToken);
        }

        // Append the update task and extension ID to the URL
        $agentUrl .= '&task=update&update_id=' . (int)$updateId;

        $ch = curl_init($agentUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        // Increase timeout because downloading and installing updates takes time
        curl_setopt($ch, CURLOPT_TIMEOUT, 60); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
        curl_setopt($ch, CURLOPT_USERAGENT, 'BastanMonitor-Updater');

        $json = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Set the response header to JSON
        header('Content-Type: application/json; charset=utf-8');

        if ($httpCode !== 200 || empty($json)) {
            echo json_encode(['status' => 'error', 'message' => 'Error connecting to target site (Code: ' . $httpCode . ')']);
            $app->close();
        }

        // Check if the target site returned a valid JSON response
        $decoded = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            // This block executes if the target site returned PHP errors or HTML
            echo json_encode([
                'status' => 'error', 
                'message' => 'Target site returned a structural error (Installer class might not be supported). Log: ' . strip_tags(substr($json, 0, 100))
            ]);
            $app->close();
        }

        // Pass the valid response from the target site to the browser
        echo $json;
        $app->close();
    }
}