<?php
/**
 * @package     BastanAgent
 * @copyright   Copyright (C) 2026. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Factory;

class PlgAjaxBastanagent extends CMSPlugin {
    
    public function onAjaxBastanagent() {
        $app = Factory::getApplication();
        $requestToken = $app->input->get('token', '', 'string');
        $savedToken = $this->params->get('agent_token', '');

        if (empty($savedToken) || $requestToken !== $savedToken) {
            header('HTTP/1.0 403 Forbidden');
            echo json_encode(['error' => 'Access Denied. Invalid or missing token.']);
            $app->close();
        }

        $db = Factory::getDbo();
        $response = [
            'status' => 'success',
            'cms' => 'Joomla',
            'url' => \Joomla\CMS\Uri\Uri::root(),
            'version' => (new \Joomla\CMS\Version())->getShortVersion(),
            'php_version' => PHP_VERSION,
            'plugins' => [],
            'themes' => [],
            'security_logs' => [],
            'pending_updates' => [],
            'last_backup' => null,
            'backup_debug' => null,
            'fim' => [],
            'has_firewall' => false
        ];

        // مانیتورینگ تغییرات فایل‌ها (FIM)
        $files_to_check = [
            JPATH_ROOT . '/configuration.php',
            JPATH_ROOT . '/index.php',
            JPATH_ROOT . '/.htaccess',
            JPATH_ADMINISTRATOR . '/index.php'
        ];
        foreach ($files_to_check as $file) {
            $key = str_replace(JPATH_ROOT . '/', '', $file);
            $response['fim'][$key] = file_exists($file) ? md5_file($file) : 'missing';
        }

        // خواندن افزونه‌ها
        try {
            $query = $db->getQuery(true)
                ->select($db->quoteName(['name', 'element', 'type', 'enabled', 'manifest_cache', 'protected']))
                ->from($db->quoteName('#__extensions'))
                ->where($db->quoteName('type') . ' IN ("component", "plugin", "module", "template", "package", "file")');
            
            $db->setQuery($query);
            $extensions = $db->loadObjectList();

            foreach ($extensions as $ext) {
                if ($ext->protected == 1 || $ext->enabled == 0) continue;
                $manifest = json_decode($ext->manifest_cache, true);
                $author = $manifest['author'] ?? '';
                if (stripos($author, 'Joomla! Project') !== false) continue;

                $item = [
                    'name' => $ext->name,
                    'element' => $ext->element,
                    'version' => $manifest['version'] ?? 'Unknown',
                    'status' => 'Active',
                    'type' => $ext->type
                ];

                if ($ext->type === 'template') {
                    $response['themes'][] = $item;
                } else {
                    $response['plugins'][] = $item;
                }
            }
        } catch (Exception $e) {}

        // بررسی لاگ‌های RSFirewall
        try {
            $query = $db->getQuery(true)
                ->select($db->quoteName(['date', 'ip', 'page']))
                ->from($db->quoteName('#__rsfirewall_logs'))
                ->order($db->quoteName('id') . ' DESC');
            $db->setQuery($query, 0, 5);
            $logs = $db->loadAssocList();
            if ($logs) {
                $response['has_firewall'] = true;
                $response['security_logs'] = $logs;
            }
        } catch (Exception $e) {}

        // بررسی آپدیت‌های منتظر
        try {
            $query = $db->getQuery(true)
                ->select($db->quoteName(['element', 'version']))
                ->from($db->quoteName('#__updates'));
            $db->setQuery($query);
            $updates = $db->loadAssocList();
            if ($updates) {
                foreach ($updates as $upd) {
                    if (!empty($upd['element'])) {
                        $response['pending_updates'][$upd['element']] = $upd['version'];
                    }
                }
            }
        } catch (Exception $e) {}

        // بررسی وضعیت Akeeba Backup
        $backup_found = false;
        try {
            $query = $db->getQuery(true)
                ->select('*')
                ->from($db->quoteName('#__akeebabackup_backups'))
                ->where($db->quoteName('status') . ' IN ("ok", "obsolete", "published", "complete")')
                ->order($db->quoteName('id') . ' DESC');
            $db->setQuery($query, 0, 1);
            $res = $db->loadAssoc();

            if ($res) {
                $val = $res['backupstart'] ?? $res['start_timestamp'] ?? $res['end_timestamp'] ?? $res['creation_date'] ?? null;
                $response['last_backup'] = is_numeric($val) ? date('Y-m-d H:i:s', $val) : $val;
                $backup_found = true;
            }
        } catch (Exception $e) {}

        if (!$backup_found) {
            try {
                $query = $db->getQuery(true)
                    ->select('*')
                    ->from($db->quoteName('#__ak_stats'))
                    ->where($db->quoteName('status') . ' IN ("ok", "obsolete", "published", "complete")')
                    ->order($db->quoteName('id') . ' DESC');
                $db->setQuery($query, 0, 1);
                $res2 = $db->loadAssoc();

                if ($res2) {
                    $val2 = $res2['backupend'] ?? $res2['startdate'] ?? null;
                    $response['last_backup'] = is_numeric($val2) ? date('Y-m-d H:i:s', $val2) : $val2;
                }
            } catch (Exception $e) {}
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $app->close();
    }
}