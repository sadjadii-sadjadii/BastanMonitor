<?php
/**
 * @package     BastanMonitor
 * @copyright   Copyright (C) 2026. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;

class BastanmonitorController extends BaseController {
    // اگر کاربر روی منوی اصلی کلیک کرد، به صورت پیش‌فرض کدام صفحه باز شود؟
    protected $default_view = 'dashboard';
}