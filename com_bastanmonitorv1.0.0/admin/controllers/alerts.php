<?php
/**
 * @package     BastanMonitor
 * @copyright   Copyright (C) 2026. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

class BastanmonitorControllerAlerts extends BaseController {

    // متد مستقیم برای آرشیو کردن هشدارها
    public function archive() {
        $this->checkToken();
        $ids = $this->input->get('cid', array(), 'array');
        $view = $this->input->get('view', 'alerts', 'cmd'); // تشخیص صفحه‌ای که در آن هستیم

        if (empty($ids)) {
            $this->setMessage(Text::_('COM_BASTANMONITOR_ALERTS_NO_SELECTION'), 'warning');
            $this->setRedirect('index.php?option=com_bastanmonitor&view=' . $view);
            return;
        }

        $db = Factory::getDbo();
        $idsString = implode(',', array_map('intval', $ids));

        $query = $db->getQuery(true)
            ->update($db->quoteName('#__bastanmonitor_alerts'))
            ->set($db->quoteName('is_archived') . ' = 1')
            ->where('id IN (' . $idsString . ')');

        $db->setQuery($query);
        $db->execute();

        $this->setMessage(Text::_('COM_BASTANMONITOR_ALERTS_ARCHIVED_SUCCESS'));
        $this->setRedirect('index.php?option=com_bastanmonitor&view=' . $view);
    }

    // متد جدید برای بازگردانی هشدارها از آرشیو
    public function restore() {
        $this->checkToken();
        $ids = $this->input->get('cid', array(), 'array');
        $view = $this->input->get('view', 'archivedalerts', 'cmd');

        if (empty($ids)) {
            $this->setMessage(Text::_('COM_BASTANMONITOR_ALERTS_NO_SELECTION'), 'warning');
            $this->setRedirect('index.php?option=com_bastanmonitor&view=' . $view);
            return;
        }

        $db = Factory::getDbo();
        $idsString = implode(',', array_map('intval', $ids));

        $query = $db->getQuery(true)
            ->update($db->quoteName('#__bastanmonitor_alerts'))
            ->set($db->quoteName('is_archived') . ' = 0')
            ->where('id IN (' . $idsString . ')');

        $db->setQuery($query);
        $db->execute();

        $this->setMessage(Text::_('COM_BASTANMONITOR_ALERTS_RESTORED_SUCCESS'));
        $this->setRedirect('index.php?option=com_bastanmonitor&view=' . $view);
    }

    // متد مستقیم برای حذف دائمی هشدارها
    public function delete() {
        $this->checkToken();
        $ids = $this->input->get('cid', array(), 'array');
        $view = $this->input->get('view', 'alerts', 'cmd');

        if (empty($ids)) {
            $this->setMessage(Text::_('COM_BASTANMONITOR_ALERTS_NO_SELECTION'), 'warning');
            $this->setRedirect('index.php?option=com_bastanmonitor&view=' . $view);
            return;
        }

        $db = Factory::getDbo();
        $idsString = implode(',', array_map('intval', $ids));

        $query = $db->getQuery(true)
            ->delete($db->quoteName('#__bastanmonitor_alerts'))
            ->where('id IN (' . $idsString . ')');

        $db->setQuery($query);
        $db->execute();

        $this->setMessage(Text::_('COM_BASTANMONITOR_ALERTS_DELETED_SUCCESS'));
        $this->setRedirect('index.php?option=com_bastanmonitor&view=' . $view);
    }
}