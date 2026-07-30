/*
 * @package     BastanMonitor
 * @copyright   Copyright (C) 2026. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

-- جدول اصلی سایت‌ها
CREATE TABLE IF NOT EXISTS `#__bastanmonitor_sites` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `domain` varchar(255) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `agent_token` varchar(100) DEFAULT NULL,
  `is_offline` tinyint(1) NOT NULL DEFAULT 0,
  `cms_version` varchar(50) DEFAULT NULL,
  `php_version` varchar(20) DEFAULT NULL,
  `health_score` int(3) DEFAULT 100,
  `state` tinyint(3) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_checked` datetime DEFAULT NULL,
  `last_sync_data` MEDIUMTEXT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

-- جدول مدیریت مالی و دارایی‌ها (سرویس‌ها)
CREATE TABLE IF NOT EXISTS `#__bastanmonitor_assets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `site_id` int(10) unsigned NOT NULL,
  `type` varchar(100) NOT NULL,
  `host_company` varchar(255) DEFAULT NULL,
  `expiration_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_site_id` (`site_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

-- جدول سیستم هشدار مرکزی (مجهز به ستون آرشیو)
CREATE TABLE IF NOT EXISTS `#__bastanmonitor_alerts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `site_id` int(10) unsigned NOT NULL,
  `severity` varchar(50) NOT NULL DEFAULT 'warning',
  `message` text NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `is_acknowledged` tinyint(1) NOT NULL DEFAULT 0,
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_site_id` (`site_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;