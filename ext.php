<?php
/**
 *
 * @package phpBB Extension - Ultimate Points
 * @copyright (c) 2016 dmzx & posey - https://www.dmzx-web.net
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace dmzx\ultimatepoints;

use phpbb\extension\base;

class ext extends base
{
	/**
	 * Enable extension if phpBB version requirement is met
	 *
	 * @return bool
	 * @access public
	 */
	public function is_enableable()
	{
		$config = $this->container->get('config');
		return version_compare($config['version'], '3.2.0', '>=');
	}

	protected static $notification_types = [
		'dmzx.ultimatepoints.notification.type.points',
	];

	/**
	 * Enable our notifications.
	 *
	 * @param mixed $old_state State returned by previous call of this method
	 * @return mixed Returns false after last step, otherwise temporary state
	 * @access public
	 */
	public function enable_step($old_state)
	{
		switch ($old_state)
		{
			case '': // Empty means nothing has run yet
				/* @var $phpbb_notifications manager */
				$phpbb_notifications = $this->container->get('notification_manager');
				foreach (self::$notification_types as $type)
				{
					$phpbb_notifications->enable_notifications($type);
				}
				return 'notifications';
				break;
			default:
				// Run parent enable step method
				return parent::enable_step($old_state);
				break;
		}
	}

	/**
	 * Disable our notifications.
	 *
	 * @param mixed $old_state State returned by previous call of this method
	 * @return mixed Returns false after last step, otherwise temporary state
	 * @access public
	 */
	public function disable_step($old_state)
	{
		switch ($old_state)
		{
			case '': // Empty means nothing has run yet
				/* @var $phpbb_notifications manager */
				$phpbb_notifications = $this->container->get('notification_manager');
				foreach (self::$notification_types as $type)
				{
					$phpbb_notifications->disable_notifications($type);
				}
				return 'notifications';
				break;
			default:
				// Run parent disable step method
				return parent::disable_step($old_state);
				break;
		}
	}

	/**
	 * Purge our notifications
	 *
	 * @param mixed $old_state State returned by previous call of this method
	 * @return mixed Returns false after last step, otherwise temporary state
	 * @access public
	 */
	public function purge_step($old_state)
	{
		switch ($old_state)
		{
			case '': // Empty means nothing has run yet
				/* @var $phpbb_notifications manager */
				$phpbb_notifications = $this->container->get('notification_manager');
				foreach (self::$notification_types as $type)
				{
					$phpbb_notifications->purge_notifications($type);
				}
				return 'notifications';
				break;
			default:
				// Run parent purge step method
				return parent::purge_step($old_state);
				break;
		}
	}
}
