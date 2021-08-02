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

class points_transfer
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
	protected $points_log_table;

	protected $points_config_table;

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
	 * @param string $points_log_table
	 * @param string $points_config_table
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
		$points_log_table,
		$points_config_table,
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
		$this->points_log_table = $points_log_table;
		$this->points_config_table = $points_config_table;
		$this->points_values_table = $points_values_table;
	}

	var $u_action;

	function main($checked_user)
	{
		// Get all configs
		$points_config = $this->functions_points->points_all_configs();

		// Grab transfer fee
		$sql = 'SELECT transfer_fee
				FROM ' . $this->points_values_table;
		$result = $this->db->sql_query($sql);
		$transfer_fee = $this->db->sql_fetchfield('transfer_fee');
		$this->db->sql_freeresult($result);

		// Grab the variables
		$message = $this->request->variable('comment', '', true);
		$adm_points = $this->request->variable('adm_points', false);
		$transfer_id = $this->request->variable('i', 0);
		$post_id = $this->request->variable('post_id', 0);

		add_form_key('transfer_points');

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
			if (!check_form_key('transfer_points'))
			{
				trigger_error('FORM_INVALID');
			}

			// Get variables for transferring
			$am = round($this->request->variable('amount', 0.00), 2);
			$comment = $this->request->variable('comment', '', true);

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

			// Check, if the user is trying to send to himself
			if ($this->user->data['user_id'] == $checked_user['user_id'])
			{
				$message = sprintf($this->user->lang['TRANSFER_REASON_YOURSELF'], $this->config['points_name']) . '<br /><br /><a href="' . $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'transfer_user']) . '">&laquo; ' . $this->user->lang['BACK_TO_PREV'] . '</a>';
				trigger_error($message);
			}

			// Add cash to receiver
			$amount = (100 - $transfer_fee) / 100 * $am; // Deduct the transfer fee
			$this->functions_points->add_points($checked_user['user_id'], $amount);

			// Remove cash from sender
			$this->functions_points->substract_points($this->user->data['user_id'], $am);

			// Get current time for logs
			$current_time = time();

			// Add transfer information to the log
			$text = $message;

			$sql = 'INSERT INTO ' . $this->points_log_table . ' ' . $this->db->sql_build_array('INSERT', [
					'point_send' => (int) $this->user->data['user_id'],
					'point_recv' => (int) $checked_user['user_id'],
					'point_amount' => $am,
					'point_sendold' => $this->user->data['user_points'],
					'point_recvold' => $checked_user['user_points'],
					'point_comment' => $text,
					'point_type' => '1',
					'point_date' => $current_time,
				]);
			$this->db->sql_query($sql);

			// Update mChat with good transfer
			if ($this->phpbb_container->has('dmzx.mchat.settings') && $this->config['transfer_mchat_enable'])
			{
				$message = $this->user->lang['TRANSFER_MCHAT_GOOD'];
				$name = $this->config['points_name'];

				$this->functions_points->mchat_message($checked_user['user_id'], $this->functions_points->number_format_points($am), $message, $name);
			}

			// Send pm to user
			if (!$points_config['transfer_pm_enable'] == 0 && $checked_user['user_allow_pm'] == 1)
			{
				// Select the user data for the PM
				$sql_array = [
					'SELECT' => '*',
					'FROM' => [
						USERS_TABLE => 'u',
					],
					'WHERE' => 'user_id = ' . (int) $checked_user['user_id'],
				];
				$sql = $this->db->sql_build_query('SELECT', $sql_array);
				$result = $this->db->sql_query($sql);
				$user_row = $this->db->sql_fetchrow($result);
				$this->db->sql_freeresult($result);

				$points_name = $this->config['points_name'];
				$comment = $this->db->sql_escape($comment);
				$pm_subject = $this->user->lang['TRANSFER_PM_SUBJECT'];
				$pm_text = sprintf($this->user->lang['TRANSFER_PM_BODY'], $amount, $points_name, $text);

				include_once($this->root_path . 'includes/message_parser.' . $this->php_ext);

				$message_parser = new parse_message();
				$message_parser->message = $pm_text;
				$message_parser->parse(true, true, true, false, false, true, true);

				$pm_data = [
					'address_list' => ['u' => [$checked_user['user_id'] => 'to']],
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

			$message = sprintf($this->user->lang['TRANSFER_REASON_TRANSUCC'], $this->functions_points->number_format_points($am), $this->config['points_name'], $checked_user['username']) . '<br /><br />' . (($post_id) ? sprintf($this->user->lang['EDIT_P_RETURN_POST'], '<a href="' . append_sid("{$this->root_path}viewtopic.{$this->php_ext}", "p=" . $post_id) . '">', '</a>') : sprintf($this->user->lang['EDIT_P_RETURN_INDEX'], '<a href="' . append_sid("{$this->root_path}index.{$this->php_ext}") . '">', '</a>'));
			trigger_error($message);

			$this->template->assign_vars([
				'U_ACTION' => $this->u_action,
			]);
		}

		$username_full = get_username_string('full', $checked_user['user_id'], $checked_user['username'], $checked_user['user_colour']);

		$this->template->assign_vars([
			'L_TRANSFER_DESCRIPTION' => sprintf($this->user->lang['TRANSFER_DESCRIPTION'], $this->config['points_name']),
			'POINTS_NAME' => $this->config['points_name'],
			'POINTS_COMMENTS' => ($points_config['comments_enable']) ? true : false,
			'TRANSFER_FEE' => $transfer_fee,
			'U_TRANSFER_NAME' => sprintf($this->user->lang['TRANSFER_TO_NAME'], $username_full, $this->config['points_name']),
			'S_ALLOW_SEND_PM' => $this->auth->acl_get('u_sendpm'),
		]);

		// Generate the page
		page_header(sprintf($this->user->lang['TRANSFER_TITLE'], $this->config['points_name']));

		// Generate the page template
		$this->template->set_filenames([
			'body' => 'points/points_transfer.html',
		]);

		page_footer();
	}
}
