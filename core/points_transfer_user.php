<?php
/**
 *
 * @package phpBB Extension - Ultimate Points
 * @copyright (c) 2016 dmzx & posey - https://www.dmzx-web.net
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace dmzx\ultimatepoints\core;

use parse_message;
use phpbb\auth\auth;
use phpbb\config\config;
use phpbb\controller\helper;
use phpbb\db\driver\driver_interface;
use phpbb\request\request;
use phpbb\template\template;
use phpbb\user;
use Symfony\Component\DependencyInjection\Container;

class points_transfer_user
{
	/** @var functions_points */
	protected $functions_points;

	/** @var auth */
	protected $auth;

	/** @var template */
	protected $template;

	/** @var user */
	protected $user;

	/** @var driver_interface */
	protected $db;

	/** @var request */
	protected $request;

	/** @var config */
	protected $config;

	/** @var helper */
	protected $helper;

	/** @var Container */
	protected $phpbb_container;

	/** @var string */
	protected $php_ext;

	/** @var string phpBB root path */
	protected $root_path;

	/**
	 * The database tables
	 *
	 * @var string
	 */
	protected $points_config_table;

	protected $points_log_table;

	protected $points_values_table;

	/**
	 * Constructor
	 *
	 * @param template $template
	 * @param user $user
	 * @param driver_interface $db
	 * @param request $request
	 * @param config $config
	 * @param helper $helper
	 * @param Container $phpbb_container
	 * @param string $php_ext
	 * @param string $root_path
	 * @param string $points_config_table
	 * @param string $points_log_table
	 * @param string $points_values_table
	 *
	 */
	public function __construct(
		functions_points $functions_points,
		auth $auth,
		template $template,
		user $user,
		driver_interface $db,
		request $request,
		config $config,
		helper $helper,
		Container $phpbb_container,
		$php_ext,
		$root_path,
		$points_config_table,
		$points_log_table,
		$points_values_table
	)
	{
		$this->functions_points = $functions_points;
		$this->auth = $auth;
		$this->template = $template;
		$this->user = $user;
		$this->db = $db;
		$this->request = $request;
		$this->config = $config;
		$this->helper = $helper;
		$this->phpbb_container = $phpbb_container;
		$this->php_ext = $php_ext;
		$this->root_path = $root_path;
		$this->points_config_table = $points_config_table;
		$this->points_log_table = $points_log_table;
		$this->points_values_table = $points_values_table;
	}

	var $u_action;

	function main($checked_user)
	{
		add_form_key('transfer_user');

		// Get all values
		$points_values = $this->functions_points->points_all_values();

		// Get all configs
		$points_config = $this->functions_points->points_all_configs();

		// Grab the message variable
		$message = $this->request->variable('comment', '', true);

		// Check, if transferring is allowed
		if (!$points_config['transfer_enable'])
		{
			$message = $this->user->lang['TRANSFER_REASON_TRANSFER'] . '<br /><br /><a href="' . $this->helper->route('dmzx_ultimatepoints_controller') . '">&laquo; ' . $this->user->lang['BACK_TO_PREV'] . '</a>';
			trigger_error($message);
		}

		// Check, if user is allowed to use the transfer module
		if (!$this->auth->acl_get('u_use_transfer'))
		{
			$message = $this->user->lang['NOT_AUTHORISED'] . '<br /><br /><a href="' . $this->helper->route('dmzx_ultimatepoints_controller') . '">&laquo; ' . $this->user->lang['BACK_TO_PREV'] . '</a>';
			trigger_error($message);
		}

		// Add part to bar
		$this->template->assign_block_vars('navlinks', [
			'U_VIEW_FORUM' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'transfer_user']),
			'FORUM_NAME' => sprintf($this->user->lang['TRANSFER_TITLE'], $this->config['points_name']),
		]);

		if ($this->request->is_set_post('submit'))
		{
			if (!check_form_key('transfer_user'))
			{
				trigger_error('FORM_INVALID');
			}

			// Grab needed variables for the transfer
			$am = round($this->request->variable('amount', 0.00), 2);
			$comment = $this->request->variable('comment', '', true);
			$username1 = $this->request->variable('username', '', true);
			$username = strtolower($username1);

			// Select the user data to transfer to
			$sql_array = [
				'SELECT' => '*',
				'FROM' => [
					USERS_TABLE => 'u',
				],
				'WHERE' => 'username_clean = "' . $this->db->sql_escape(utf8_clean_string($username)) . '"',
			];
			$sql = $this->db->sql_build_query('SELECT', $sql_array);
			$result = $this->db->sql_query($sql);
			$transfer_user = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);

			if ($transfer_user == null)
			{
				$message = $this->user->lang['TRANSFER_NO_USER_RETURN'] . '<br /><br /><a href="' . $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'transfer_user']) . '">&laquo; ' . $this->user->lang['BACK_TO_PREV'] . '</a>';
				trigger_error($message);
			}

			// Select the old user_points from user_id to transfer to
			$sql_array = [
				'SELECT' => 'user_points',
				'FROM' => [
					USERS_TABLE => 'u',
				],
				'WHERE' => 'user_id = ' . (int) $transfer_user['user_id'],
			];
			$sql = $this->db->sql_build_query('SELECT', $sql_array);
			$result = $this->db->sql_query($sql);
			$transfer_user_old_points = (int) $this->db->sql_fetchfield('user_points');
			$this->db->sql_freeresult($result);

			// Check, if the sender has enough cash
			if ($this->user->data['user_points'] < $am)
			{
				$message = sprintf($this->user->lang['TRANSFER_REASON_MINPOINTS'], $this->config['points_name']) . '<br /><br /><a href="' . $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'transfer_user']) . '">&laquo; ' . $this->user->lang['BACK_TO_PREV'] . '</a>';
				trigger_error($message);
			}

			// Check, if the amount is 0 or below
			if ($am <= 0)
			{
				$message = sprintf($this->user->lang['TRANSFER_REASON_UNDERZERO'], $this->config['points_name']) . '<br /><br /><a href="' . $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'transfer_user']) . '">&laquo; ' . $this->user->lang['BACK_TO_PREV'] . '</a>';
				trigger_error($message);
			}

			// Check, if user is trying to send to himself
			if ($this->user->data['user_id'] == $transfer_user['user_id'])
			{
				$message = sprintf($this->user->lang['TRANSFER_REASON_YOURSELF'], $this->config['points_name']) . '<br /><br /><a href="' . $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'transfer_user']) . '">&laquo; ' . $this->user->lang['BACK_TO_PREV'] . '</a>';
				trigger_error($message);
			}

			// Add cash to receiver
			$amount = (100 - $points_values['transfer_fee']) / 100 * $am; // Deduct transfer fee
			$this->functions_points->add_points($transfer_user['user_id'], $amount);

			// Remove cash from sender
			$this->functions_points->substract_points($this->user->data['user_id'], $am);

			// Get current time for log
			$current_time = time();

			// Add transferring information to the log
			$text = $message;

			$sql = 'INSERT INTO ' . $this->points_log_table . ' ' . $this->db->sql_build_array('INSERT', [
					'point_send' => (int) $this->user->data['user_id'],
					'point_recv' => (int) $transfer_user['user_id'],
					'point_amount' => $am,
					'point_sendold' => $this->user->data['user_points'],
					'point_recvold' => $transfer_user_old_points,
					'point_comment' => $text,
					'point_type' => '1',
					'point_date' => $current_time,
				]);
			$this->db->sql_query($sql);

			// Update mChat with good transfer
			if ($this->phpbb_container->has('dmzx.mchat.settings') || $this->config['transfer_mchat_enable'])
			{
				$message = $this->user->lang['TRANSFER_MCHAT_GOOD'];
				$name = $this->config['points_name'];

				$this->functions_points->mchat_message($checked_user['user_id'], $this->functions_points->number_format_points($am), $message, $name);
			}

			// Send pm to receiver, if PM is enabled
			if (!$points_config['transfer_pm_enable'] == 0 && $transfer_user['user_allow_pm'])
			{

				$points_name = $this->config['points_name'];
				$comment = $this->db->sql_escape($comment);
				$pm_subject = $this->user->lang['TRANSFER_PM_SUBJECT'];
				$pm_text = sprintf($this->user->lang['TRANSFER_PM_BODY_USER'], $amount, $points_name);

				include_once($this->root_path . 'includes/message_parser.' . $this->php_ext);

				$message_parser = new parse_message();
				$message_parser->message = $pm_text;
				$message_parser->parse(true, true, true, false, false, true, true);

				$pm_data = [
					'address_list' => ['u' => [$transfer_user['user_id'] => 'to']],
					'from_user_id' => $this->user->data['user_id'],
					'from_username' => $this->user->data['username'],
					'icon_id' => 0,
					'from_user_ip' => '',
					'enable_bbcode' => true,
					'enable_smilies' => true,
					'enable_urls' => true,
					'enable_sig' => true,
					'message' => $message_parser->message,
					'bbcode_bitfield' => $message_parser->bbcode_bitfield,
					'bbcode_uid' => $message_parser->bbcode_uid,
				];

				submit_pm('post', $pm_subject, $pm_data, false);
			}

			// Change $username back to regular username
			$sql_array = [
				'SELECT' => 'username',
				'FROM' => [
					USERS_TABLE => 'u',
				],
				'WHERE' => 'user_id = ' . (int) $transfer_user['user_id'],
			];
			$sql = $this->db->sql_build_query('SELECT', $sql_array);

			$result = $this->db->sql_query($sql);
			$show_user = $this->db->sql_fetchfield('username');
			$this->db->sql_freeresult($result);

			// Show the successful transfer message
			$message = sprintf($this->user->lang['TRANSFER_REASON_TRANSUCC'], $this->functions_points->number_format_points($am), $this->config['points_name'], $show_user) . '<br /><br /><a href="' . $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'transfer_user']) . '">&laquo; ' . $this->user->lang['BACK_TO_PREV'] . '</a>';
			trigger_error($message);

			$this->template->assign_vars([
				'U_ACTION' => $this->u_action,
			]);
		}

		$this->template->assign_vars([
			'USER_POINTS' => sprintf($this->functions_points->number_format_points($checked_user['user_points'])),
			'POINTS_NAME' => $this->config['points_name'],
			'POINTS_COMMENTS' => ($points_config['comments_enable']) ? true : false,
			'TRANSFER_FEE' => $points_values['transfer_fee'],
			'LOTTERY_NAME' => $points_values['lottery_name'],
			'BANK_NAME' => $points_values['bank_name'],
			'L_TRANSFER_DESCRIPTION' => sprintf($this->user->lang['TRANSFER_DESCRIPTION'], $this->config['points_name']),
			'U_TRANSFER_USER' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'transfer_user']),
			'U_LOGS' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'logs']),
			'U_LOTTERY' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'lottery']),
			'U_BANK' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'bank']),
			'U_ROBBERY' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'robbery']),
			'U_INFO' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'info']),
			'U_FIND_USERNAME' => append_sid("{$this->root_path}memberlist.{$this->php_ext}", "mode=searchuser&amp;form=post&amp;field=username"),
			'U_USE_TRANSFER' => $this->auth->acl_get('u_use_transfer'),
			'U_USE_LOGS' => $this->auth->acl_get('u_use_logs'),
			'U_USE_LOTTERY' => $this->auth->acl_get('u_use_lottery'),
			'U_USE_BANK' => $this->auth->acl_get('u_use_bank'),
			'U_USE_ROBBERY' => $this->auth->acl_get('u_use_robbery'),
			'S_ALLOW_SEND_PM' => $this->auth->acl_get('u_sendpm'),
		]);

		// Generate the page
		page_header(sprintf($this->user->lang['TRANSFER_TITLE'], $this->config['points_name']));

		// Generate the page template
		$this->template->set_filenames([
			'body' => 'points/points_transfer_user.html',
		]);

		page_footer();
	}
}
