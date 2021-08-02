<?php
/**
 *
 * @package phpBB Extension - Ultimate Points
 * @copyright (c) 2016 dmzx & posey - https://www.dmzx-web.net
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace dmzx\ultimatepoints\notification;

use phpbb\notification\type\base;
use phpbb\controller\helper;
use phpbb\user_loader;

class points extends base
{
	protected $user_loader;

	protected $helper;

	public function set_user_loader(user_loader $user_loader)
	{
		$this->user_loader = $user_loader;
	}

	public function set_helper(helper $helper)
	{
		$this->helper = $helper;
	}

	public function get_type()
	{
		return 'dmzx.ultimatepoints.notification.type.points';
	}

	public static $notification_option = [
		'lang' => 'NOTIFICATION_POINTS_UCP',
		'group' => 'NOTIFICATION_GROUP_MISCELLANEOUS',
	];

	public function is_available()
	{
		return true;
	}

	public static function get_item_id($data)
	{
		return (int) $data['points_notify_id'];
	}

	public static function get_item_parent_id($data)
	{
		return 0;
	}

	public function find_users_for_notification($data, $options = [])
	{
		$users = [];
		$users[$data['receiver']] = [''];
		$this->user_loader->load_users(array_keys($users));

		return $this->check_user_notification_options(array_keys($users), $options);
	}

	public function users_to_query()
	{
		return [];
	}

	public function get_avatar()
	{
		return $this->user_loader->get_avatar($this->get_data('sender'));
	}

	public function get_title()
	{
		$users = [];
		$users = [$this->get_data('sender')];
		$this->user_loader->load_users($users);
		$username = $this->user_loader->get_username($this->get_data('sender'), 'no_profile');

		return $username . '&nbsp;' . $this->get_data('points_notify_msg');
	}

	public function get_url()
	{
		return $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => $this->get_data('mode')]);
	}

	public function get_email_template()
	{
		return false;
	}

	public function get_email_template_variables()
	{
		return [];
	}

	public function create_insert_array($data, $pre_create_data = [])
	{
		$this->set_data('points_notify_id', $data['points_notify_id']);
		$this->set_data('points_notify_msg', $data['points_notify_msg']);
		$this->set_data('sender', $data['sender']);
		$this->set_data('receiver', $data['receiver']);
		$this->set_data('mode', $data['mode']);

		parent::create_insert_array($data, $pre_create_data);
	}
}
