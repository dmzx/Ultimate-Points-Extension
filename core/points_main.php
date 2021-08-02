<?php
/**
 *
 * @package phpBB Extension - Ultimate Points
 * @copyright (c) 2016 dmzx & posey - https://www.dmzx-web.net
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace dmzx\ultimatepoints\core;

use phpbb\auth\auth;
use phpbb\config\config;
use phpbb\controller\helper;
use phpbb\db\driver\driver_interface;
use phpbb\template\template;
use phpbb\user;

class points_main
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

	/** @var config */
	protected $config;

	/** @var helper */
	protected $helper;

	/** @var string phpBB root path */
	protected $root_path;

	/**
	 * The database tables
	 *
	 * @var string
	 */
	protected $points_bank_table;

	protected $points_values_table;

	protected $points_log_table;

	protected $points_lottery_tickets_table;

	protected $points_lottery_history_table;

	/**
	 * Constructor
	 *
	 * @param auth $auth
	 * @param template $template
	 * @param user $user
	 * @param driver_interface $db
	 * @param config $config
	 * @param helper $helper
	 * @param string $root_path
	 * @param string $points_bank_table
	 * @param string $points_values_table
	 * @param string $points_log_table
	 * @param string $points_lottery_tickets_table
	 * @param string $points_lottery_history_table
	 *
	 * @var functions_points $functions_points
	 */
	public function __construct(
		functions_points $functions_points,
		auth $auth,
		template $template,
		user $user,
		driver_interface $db,
		config $config,
		helper $helper,
		$root_path,
		$points_bank_table,
		$points_values_table,
		$points_log_table,
		$points_lottery_tickets_table,
		$points_lottery_history_table
	)
	{
		$this->functions_points = $functions_points;
		$this->auth = $auth;
		$this->template = $template;
		$this->user = $user;
		$this->db = $db;
		$this->config = $config;
		$this->helper = $helper;
		$this->root_path = $root_path;
		$this->points_bank_table = $points_bank_table;
		$this->points_values_table = $points_values_table;
		$this->points_log_table = $points_log_table;
		$this->points_lottery_tickets_table = $points_lottery_tickets_table;
		$this->points_lottery_history_table = $points_lottery_history_table;
	}

	var $u_action;

	function main($checked_user)
	{
		// Get all values
		$points_values = $this->functions_points->points_all_values();
		// Get all config values
		$points_config = $this->functions_points->points_all_configs();

		// Select user's bank holding
		$sql_array = [
			'SELECT' => '*',
			'FROM' => [
				$this->points_bank_table => 'b',
			],
			'WHERE' => 'user_id = ' . (int) $this->user->data['user_id'],
		];
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		// Select user's lottery tickets
		$viewer_total_tickets = '';

		if ($this->user->data['user_id'] != ANONYMOUS)
		{
			$sql_array = [
				'SELECT' => 'COUNT(ticket_id) AS num_tickets',
				'FROM' => [
					$this->points_lottery_tickets_table => 't',
				],
				'WHERE' => 'user_id = ' . (int) $this->user->data['user_id'],
			];
			$sql = $this->db->sql_build_query('SELECT', $sql_array);
			$result = $this->db->sql_query($sql);
			$viewer_total_tickets = (int) $this->db->sql_fetchfield('num_tickets');
			$this->db->sql_freeresult($result);
		}

		// Generate the page header
		page_header(sprintf($this->user->lang['POINTS_TITLE_MAIN'], $this->config['points_name']));

		$user_name = get_username_string('full', $checked_user['user_id'], $checked_user['username'], $checked_user['user_colour']);

		// Generate some language stuff, dependig on the fact, if user has a bank account or not
		if ((is_array($row) && $row['user_id'] != $this->user->data['user_id']) || (is_array($row) && $row['holding'] < 1))
		{
			$this->template->assign_vars([
				'L_MAIN_ON_HAND' => sprintf($this->user->lang['MAIN_ON_HAND'], $this->functions_points->number_format_points($checked_user['user_points']), $this->config['points_name']),
				'L_MAIN_HELLO_USERNAME' => sprintf($this->user->lang['MAIN_HELLO_USERNAME'], $user_name),
				'L_MAIN_LOTTERY_TICKETS' => sprintf($this->user->lang['MAIN_LOTTERY_TICKETS'], $viewer_total_tickets),
			]);
		} else
		{
			$this->template->assign_vars([
				'L_MAIN_ON_HAND' => $this->auth->acl_get('u_use_points') ? sprintf($this->user->lang['MAIN_ON_HAND'], $this->functions_points->number_format_points($checked_user['user_points']), $this->config['points_name']) : '',
				'L_MAIN_HELLO_USERNAME' => sprintf($this->user->lang['MAIN_HELLO_USERNAME'], $user_name),
				'L_MAIN_LOTTERY_TICKETS' => $this->auth->acl_get('u_use_lottery') ? sprintf($this->user->lang['MAIN_LOTTERY_TICKETS'], $viewer_total_tickets) : '',
			]);

			if ($this->auth->acl_get('u_use_bank'))
			{
				$this->template->assign_block_vars('has_bank_account', [
					'L_MAIN_BANK_HAVE' => sprintf($this->user->lang['MAIN_BANK_HAVE'], $this->functions_points->number_format_points(is_array($row) && $row['holding']), $this->config['points_name']),
				]);
			}
		}

		// Generate richest users
		$limit = $points_values['number_show_top_points'];
		$sql_array = [
			'SELECT' => 'user_id, username, user_colour, user_points',
			'FROM' => [
				USERS_TABLE => 'u',
			],
			'WHERE' => 'user_points > 0',
			'ORDER_BY' => 'user_points DESC, username_clean ASC'
		];
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query_limit($sql, $limit);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->template->assign_block_vars('points', [
				'USERNAME' => get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
				'POINT' => sprintf($this->functions_points->number_format_points($row['user_points'])),
			]);
		}
		$this->db->sql_freeresult($result);

		// Richest Banker
		$sql_array = [
			'SELECT' => 'b.user_id, b.holding, u.user_id, u.username, u.user_colour',

			'FROM' => [
				$this->points_bank_table => 'b',
			],

			'LEFT_JOIN' => [
				[
					'FROM' => [USERS_TABLE => 'u'],
					'ON' => 'u.user_id = b.user_id',
				]
			],

			'ORDER_BY' => 'b.holding DESC',
		];

		// Build the query...
		$sql = $this->db->sql_build_query('SELECT', $sql_array);

		// Run the query...
		$result = $this->db->sql_query_limit($sql, 5); // only get 5 richest..

		// rb_ is Richest Banker
		while ($rb_row = $this->db->sql_fetchrow($result))
		{
			$rb_username = get_username_string('full', $rb_row['user_id'], $rb_row['username'], $rb_row['user_colour']);

			if ($rb_row['holding'] != 0.00) // Empty bank accounts
			{
				$this->template->assign_block_vars('richest_banker', [
					'USER' => $rb_username,
					'HOLDING' => $rb_row['holding'],
				]);
			}
		}
		$this->db->sql_freeresult($result); // Free the results

		// Most Donations Given
		$sql_array = [
			'SELECT' => 'lt.point_send, u.user_id, u.username, u.user_colour, SUM(lt.point_amount) AS total_donated',

			'FROM' => [
				$this->points_log_table => 'lt',
			],

			'LEFT_JOIN' => [
				[
					'FROM' => [USERS_TABLE => 'u'],
					'ON' => 'u.user_id = lt.point_send',
				]
			],

			'GROUP_BY' => 'lt.point_send',

			'ORDER_BY' => 'total_donated DESC',
		];

		// Build the query...
		$sql = $this->db->sql_build_query('SELECT', $sql_array);

		// Run the query...
		$result = $this->db->sql_query_limit($sql, 5); // only get 5 most generous users..

		// md_ is Most Donated
		while ($md_row = $this->db->sql_fetchrow($result))
		{
			$md_username = get_username_string('full', $md_row['user_id'], $md_row['username'], $md_row['user_colour']);

			$this->template->assign_block_vars('most_donated', [
				'USER' => $md_username,
				'DONATED' => $md_row['total_donated'],
			]);
		}
		$this->db->sql_freeresult($result); // Free the results

		// Most Lotteries Won
		$sql_array = [
			'SELECT' => 'lh.user_id, COUNT(lh.id) AS total_won, SUM(lh.amount) AS total_prize, u.user_id, u.username, u.user_colour',

			'FROM' => [
				$this->points_lottery_history_table => 'lh',
			],

			'LEFT_JOIN' => [
				[
					'FROM' => [USERS_TABLE => 'u'],
					'ON' => 'u.user_id = lh.user_id',
				]
			],

			'GROUP_BY' => 'lh.user_id',

			'ORDER_BY' => 'total_won DESC',
		];

		// Build the query...
		$sql = $this->db->sql_build_query('SELECT', $sql_array);

		// Run the query...
		$result = $this->db->sql_query_limit($sql, 5); // only get 5 luckiest users..

		// lw_ is Lotteries Won
		while ($lw_row = $this->db->sql_fetchrow($result))
		{
			if ($lw_row['user_id'] != 0) // 0 means there was no winner..
			{
				$lw_username = get_username_string('full', $lw_row['user_id'], $lw_row['username'], $lw_row['user_colour']);

				$this->template->assign_block_vars('lotteries_won', [
					'USER' => $lw_username,
					'TOTAL_WON' => $lw_row['total_won'],
					'TOTAL_PRIZE' => $lw_row['total_prize'],
				]);
			}
		}
		$this->db->sql_freeresult($result); // Free the results

		$this->template->assign_vars([
			'LOTTERY_NAME' => $points_values['lottery_name'],
			'BANK_NAME' => $points_values['bank_name'],
			'S_DISPLAY_INDEX' => ($points_values['number_show_top_points'] > 0) ? true : false,
			'U_TRANSFER_USER' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'transfer_user']),
			'U_LOGS' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'logs']),
			'U_LOTTERY' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'lottery']),
			'U_BANK' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'bank']),
			'U_ROBBERY' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'robbery']),
			'U_INFO' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'info']),
			'U_USE_TRANSFER' => $this->auth->acl_get('u_use_transfer'),
			'U_USE_LOGS' => $this->auth->acl_get('u_use_logs'),
			'U_USE_LOTTERY' => $this->auth->acl_get('u_use_lottery'),
			'U_USE_BANK' => $this->auth->acl_get('u_use_bank'),
			'U_USE_ROBBERY' => $this->auth->acl_get('u_use_robbery'),
			'S_BANK_ENABLE' => ($points_config['bank_enable']) ? true : false,
			'S_LOTTERY_INFO' => ($points_config['lottery_enable']) ? true : false,
			'POINTS_MOST_RICH_USERS' => sprintf($this->user->lang['POINTS_MOST_RICH_USERS'], $points_values['number_show_top_points']),
		]);

		// Generate the page template
		$this->template->set_filenames([
			'body' => 'points/points_main.html',
		]);

		page_footer();
	}
}
