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
use phpbb\pagination;
use phpbb\request\request;
use phpbb\template\template;
use phpbb\user;
use Symfony\Component\DependencyInjection\Container;

class points_lottery
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

	/** @var pagination */
	protected $pagination;

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

	protected $points_values_table;

	protected $points_lottery_history_table;

	protected $points_lottery_tickets_table;

	/**
	 * Constructor
	 *
	 * @param template $template
	 * @param user $user
	 * @param driver_interface $db
	 * @param request $request
	 * @param config $config
	 * @param helper $helper
	 * @param pagination $pagination
	 * @param Container $phpbb_container
	 * @param string $php_ext
	 * @param string $root_path
	 * @param string $points_config_table
	 * @param string $points_values_table
	 * @param string $points_lottery_history_table ,
	 * @param string $points_lottery_tickets_table ,
	 *
	 */
	public function __construct(
		functions_points $functions_points,
		auth $auth,
		template $template,
		user $user,
		driver_interface $db,
		request	$request,
		config $config,
		helper $helper,
		pagination $pagination,
		Container $phpbb_container,
		$php_ext,
		$root_path,
		$points_config_table,
		$points_values_table,
		$points_lottery_history_table,
		$points_lottery_tickets_table
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
		$this->pagination = $pagination;
		$this->phpbb_container = $phpbb_container;
		$this->php_ext = $php_ext;
		$this->root_path = $root_path;
		$this->points_config_table = $points_config_table;
		$this->points_values_table = $points_values_table;
		$this->points_lottery_history_table = $points_lottery_history_table;
		$this->points_lottery_tickets_table = $points_lottery_tickets_table;
	}

	var $u_action;

	function main($checked_user)
	{
		// Get all values
		$points_values = $this->functions_points->points_all_values();

		// Get all configs
		$points_config = $this->functions_points->points_all_configs();

		// Set some variables
		$start = $this->request->variable('start', 0);
		$number = $points_values['number_show_per_page'];
		add_form_key('lottery_tickets');

		// Check, if lottery is enabled
		if (!$points_config['lottery_enable'])
		{
			$message = $this->user->lang['LOTTERY_DISABLED'] . '<br /><br /><a href="' . $this->helper->route('dmzx_ultimatepoints_controller') . '">&laquo; ' . $this->user->lang['BACK_TO_PREV'] . '</a>';
			trigger_error($message);
		}

		// Check, if user is allowed to use the lottery
		if (!$this->auth->acl_get('u_use_lottery'))
		{
			$message = $this->user->lang['NOT_AUTHORISED'] . '<br /><br /><a href="' . $this->helper->route('dmzx_ultimatepoints_controller') . '">&laquo; ' . $this->user->lang['BACK_TO_PREV'] . '</a>';
			trigger_error($message);
		}

		// Add part to bar
		$this->template->assign_block_vars('navlinks', [
			'U_VIEW_FORUM' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'lottery']),
			'FORUM_NAME' => $points_values['lottery_name'],
		]);

		// Add lottery base amount in description
		$this->template->assign_vars([
			'L_LOTTERY_BASE_AMOUNT' => sprintf($this->user->lang['LOTTERY_DESCRIPTION'], sprintf($this->functions_points->number_format_points($points_values['lottery_base_amount'])), $this->config['points_name']),
		]);

		// Recheck, if lottery was run, for those boards only having one user per day and which don't call the index page first
		if ($points_values['lottery_draw_period'] != 0 && time() > $points_values['lottery_last_draw_time'] + $points_values['lottery_draw_period'])
		{
			$this->functions_points->run_lottery();
		}

		// Check, if user has purchased tickets
		if ($this->request->variable('purchase_ticket', false) && $this->user->data['user_id'] != ANONYMOUS)
		{
			if (!check_form_key('lottery_tickets'))
			{
				trigger_error('FORM_INVALID');
			}

			// How many tickets have been bought?
			$total_tickets_bought = $this->request->variable('total_tickets', 0);

			// Check, if user already bought tickets
			$sql_array = [
				'SELECT' => 'COUNT(ticket_id) AS number_of_tickets',
				'FROM' => [
					$this->points_lottery_tickets_table => 't',
				],
				'WHERE' => 'user_id = ' . (int) $this->user->data['user_id'],
			];
			$sql = $this->db->sql_build_query('SELECT', $sql_array);
			$result = $this->db->sql_query($sql);
			$number_tickets = $this->db->sql_fetchfield('number_of_tickets');
			$this->db->sql_freeresult($result);

			// Check, if the user tries to buy more tickets than allowed
			if ($total_tickets_bought > $points_values['lottery_max_tickets'])
			{
				$message = sprintf($this->user->lang['LOTTERY_MAX_TICKETS_REACH'], $points_values['lottery_max_tickets']) . '<br /><br /><a href="' . $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'lottery']) . '">&laquo; ' . $this->user->lang['BACK_TO_PREV'] . '</a>';
				trigger_error($message);
			}

			// Check in user try to buy negative tickets
			if ($total_tickets_bought <= 0)
			{
				$message = $this->user->lang['LOTTERY_NEGATIVE_TICKETS'] . '<br /><br /><a href="' . $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'lottery']) . '">&laquo; ' . $this->user->lang['BACK_TO_PREV'] . '</a>';
				trigger_error($message);
			}

			// Check, if the already bought tickets and the new request are higher than the max set number of tickets
			if (($number_tickets + $total_tickets_bought) > $points_values['lottery_max_tickets'])
			{
				$message = sprintf($this->user->lang['LOTTERY_MAX_TICKETS_LEFT'], ($points_values['lottery_max_tickets'] - $number_tickets)) . '<br /><br /><a href="' . $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'lottery']) . '">&laquo; ' . $this->user->lang['BACK_TO_PREV'] . '</a>';
				trigger_error($message);
			}

			// Check, if the user sent an empty value
			if (!$total_tickets_bought)
			{
				$message = $this->user->lang['LOTTERY_INVALID_INPUT'] . '<br /><br /><a href="' . $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'lottery']) . '">&laquo; ' . $this->user->lang['BACK_TO_PREV'] . '</a>';
				trigger_error($message);
			}

			// Check. if lottery is enabled
			if ($points_config['lottery_enable'] != 0 && $points_values['lottery_ticket_cost'] != 0)
			{
				// Grab users total cash
				$sql_array = [
					'SELECT' => '*',
					'FROM' => [
						USERS_TABLE => 'u',
					],
					'WHERE' => 'user_id = ' . (int) $this->user->data['user_id'],
				];
				$sql = $this->db->sql_build_query('SELECT', $sql_array);
				$result = $this->db->sql_query($sql);
				$purchaser = $this->db->sql_fetchrow($result);
				$this->db->sql_freeresult($result);

				// Check, if the user has enough cash to buy tickets
				if ($points_values['lottery_ticket_cost'] * $total_tickets_bought > $purchaser['user_points'])
				{
					$message = $this->user->lang['LOTTERY_LACK_FUNDS'] . '<br /><br /><a href="' . $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'lottery']) . '">&laquo; ' . $this->user->lang['BACK_TO_PREV'] . '</a>';
					trigger_error($message);
				}
			}

			// Loop through total purchased tickets and create insert array
			for ($i = 0, $total_tickets_bought; $i < $total_tickets_bought; $i++)
			{
				$sql_insert_ary[] = [
					'user_id' => $this->user->data['user_id'],
				];
			}
			$this->db->sql_multi_insert($this->points_lottery_tickets_table, $sql_insert_ary);

			// Check again, if lottery is enabled
			if ($points_config['lottery_enable'] != 0)
			{
				// Deduct cost
				$viewer_cash = $purchaser['user_points'] - ($points_values['lottery_ticket_cost'] * $total_tickets_bought);
				$this->functions_points->set_points($this->user->data['user_id'], $viewer_cash);

				// Update jackpot
				$this->functions_points->set_points_values('lottery_jackpot', $points_values['lottery_jackpot'] + ($points_values['lottery_ticket_cost'] * $total_tickets_bought));
			}

			// Update mChat with lottery ticket buy
			if ($this->phpbb_container->has('dmzx.mchat.settings') && $this->config['lottery_mchat_enable'])
			{
				$message = $this->user->lang['LOTTERY_MCHAT_PURCHASE'];
				$name = $points_values['lottery_name'];

				$this->functions_points->mchat_message($this->user->data['user_id'], $total_tickets_bought, $message, $name);
			}

			$message = $this->user->lang['LOTTERY_TICKET_PURCHASED'] . '<br /><br /><a href="' . $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'lottery']) . '">&laquo; ' . $this->user->lang['BACK_TO_PREV'] . '</a>';
			trigger_error($message);

			$this->template->assign_vars([
				'U_ACTION' => $this->u_action,
			]);
		}

		// Display main page
		$history_mode = $this->request->variable('history', '');

		if ($history_mode)
		{
			// If no one has ever won, why bother doing anything else?
			if ($points_values['points_winners_total'] = 0)
			{
				$message = $this->user->lang['LOTTERY_NO_WINNERS'] . '<br /><br /><a href="' . $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'lottery']) . '">&laquo; ' . $this->user->lang['BACK_TO_PREV'] . '</a>';
				trigger_error($message);
			}

			$total_wins = $points_values['points_winners_total'];

			// Check, if no entries returned, only self search would turn up empty at this point
			if ($history_mode == 'ego')
			{
				$sql_array = [
					'SELECT' => 'COUNT(id) AS viewer_history',
					'FROM' => [
						$this->points_lottery_history_table => 'h',
					],
					'WHERE' => 'user_id = ' . (int) $this->user->data['user_id'],
				];
				$sql = $this->db->sql_build_query('SELECT', $sql_array);
				$result = $this->db->sql_query($sql);
				$total_wins = (int) $this->db->sql_fetchfield('viewer_history');
				$this->db->sql_freeresult($result);

				if ($total_wins == 0)
				{
					$message = sprintf($this->user->lang['LOTTERY_NEVER_WON'], $points_values['lottery_name']) . '<br /><br /><a href="' . $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'lottery']) . '">&laquo; ' . $this->user->lang['BACK_TO_PREV'] . '</a>';
					trigger_error($message);
				}

				$this->template->assign_vars([
					'U_VIEW_HISTORY' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'lottery', 'history' => 'all']),
					'U_TRANSFER_USER' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'transfer_user']),
					'U_LOGS' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'logs']),
					'U_LOTTERY' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'lottery']),
					'U_BANK' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'bank']),
					'U_ROBBERY' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'robbery']),
				]);
			}

			// Check, if user is viewing all or own entries
			if ($history_mode == 'all')
			{
				$sql_array = [
					'SELECT' => 'COUNT(id) AS total_entries',
					'FROM' => [
						$this->points_lottery_history_table => 'h',
					],
				];
				$sql = $this->db->sql_build_query('SELECT', $sql_array);
				$result = $this->db->sql_query($sql);
				$total_entries = (int) $this->db->sql_fetchfield('total_entries');
				$this->db->sql_freeresult($result);

				$sql_array = [
					'SELECT' => 'h.*, u.*',
					'FROM' => [
						$this->points_lottery_history_table => 'h',
					],
					'LEFT_JOIN' => [
						[
							'FROM' => [USERS_TABLE => 'u'],
							'ON' => 'h.user_id = u.user_id'
						],
					],
					'ORDER_BY' => 'time DESC',
				];
			}
			else
			{
				$sql_array = [
					'SELECT' => 'COUNT(id) AS total_entries',
					'FROM' => [
						$this->points_lottery_history_table => 'h',
					],
					'WHERE' => 'user_id = ' . (int) $this->user->data['user_id'],
				];
				$sql = $this->db->sql_build_query('SELECT', $sql_array);
				$result = $this->db->sql_query($sql);
				$total_entries = (int) $this->db->sql_fetchfield('total_entries');
				$this->db->sql_freeresult($result);

				$sql_array = [
					'SELECT' => 'h.*, u.*',
					'FROM' => [
						$this->points_lottery_history_table => 'h',
					],
					'LEFT_JOIN' => [
						[
							'FROM' => [USERS_TABLE => 'u'],
							'ON' => 'h.user_id = u.user_id'
						],
					],
					'WHERE' => 'h.user_id = ' . (int) $this->user->data['user_id'],
					'ORDER_BY' => 'time DESC',
				];
			}

			$sql = $this->db->sql_build_query('SELECT', $sql_array);
			$result = $this->db->sql_query_limit($sql, $number, $start);
			$row_color = $start;

			while ($row = $this->db->sql_fetchrow($result))
			{
				$row_color++;

				// Check, if winner is user
				if ($row['user_id'] != 0)
				{
					$history_member = get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']);
				} else
				{
					$history_member = $this->user->lang['LOTTERY_NO_WINNER'];
				}

				$this->template->assign_block_vars('history_row', [
					'NUMBER' => $row_color,
					'U_WINNER_PROFILE' => $history_member,
					'WINNER_PROFILE' => $history_member,
					'USERNAME' => $row['username'],
					'WINNINGS' => sprintf($this->functions_points->number_format_points($row['amount'])),
					'DATE' => $this->user->format_date($row['time']),
					'ROW_COLOR' => $row_color,
				]);

				$this->template->assign_vars([
					'U_VIEW_HISTORY' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'lottery', 'history' => 'all']),
					'U_VIEW_SELF_HISTORY' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'lottery', 'history' => 'ego']),
					'U_INFO' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'info']),
					'U_TRANSFER_USER' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'transfer_user']),
					'U_LOGS' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'logs']),
					'U_LOTTERY' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'lottery']),
					'U_BANK' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'bank']),
					'U_ROBBERY' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'robbery']),
				]);
			}

			//Start pagination
			$this->pagination->generate_template_pagination($this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'lottery', 'history' => $history_mode]), 'pagination', 'start', $total_entries, $number, $start);

			// Viewing a history page
			$this->template->assign_vars([
				'CASH_NAME' => $this->config['points_name'],
				'PAGINATION' => $this->user->lang('POINTS_LOG_COUNT', $total_entries),
				'LOTTERY_NAME' => $points_values['lottery_name'],
				'BANK_NAME' => $points_values['bank_name'],
				'S_VIEW_HISTORY' => true,
				'U_BACK_TO_LOTTERY' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'lottery']),
				'U_VIEW_SELF_HISTORY' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'lottery', 'history' => 'ego']),
				'U_TRANSFER_USER' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'transfer_user']),
				'U_LOGS' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'logs']),
				'U_LOTTERY' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'lottery']),
				'U_BANK' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'bank']),
				'U_ROBBERY' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'robbery']),
			]);

		} else
		{
			// Show main lottery page
			$viewer_total_tickets = '';
			if ($this->user->data['user_id'] != ANONYMOUS)
			{
				//Select total tickets viewer owns
				$sql_array = [
					'SELECT' => 'COUNT(ticket_id) AS num_tickets',
					'FROM' => [
						$this->points_lottery_tickets_table => 'h',
					],
					'WHERE' => 'user_id = ' . (int) $this->user->data['user_id'],
				];
				$sql = $this->db->sql_build_query('SELECT', $sql_array);
				$result = $this->db->sql_query($sql);
				$viewer_total_tickets = (int) $this->db->sql_fetchfield('num_tickets');
				$this->db->sql_freeresult($result);
			}

			// User color selection
			$sql_array = [
				'SELECT' => 'user_id, username, user_colour',
				'FROM' => [
					USERS_TABLE => 'u',
				],
				'WHERE' => 'user_id = ' . (int) $points_values['lottery_prev_winner_id'],
			];
			$sql = $this->db->sql_build_query('SELECT', $sql_array);
			$result = $this->db->sql_query($sql);
			$row = $this->db->sql_fetchrow($result);

			if ($row == null)
			{
				$username_colored = $this->user->lang['LOTTERY_NO_WINNER'];
			} else
			{
				$username_colored = get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']);
			}

			// Check, if previous winner is a user
			if ($points_values['lottery_prev_winner_id'] != 0)
			{
				$link_member = append_sid("{$this->root_path}memberlist.{$this->php_ext}", "mode=viewprofile&amp;u=" . $points_values['lottery_prev_winner_id']);
			} else
			{
				$link_member = '';
			}

			// Select the total number of tickets
			$sql_array = [
				'SELECT' => 'COUNT(ticket_id) AS no_of_tickets',
				'FROM' => [
					$this->points_lottery_tickets_table => 't',
				],
			];
			$sql = $this->db->sql_build_query('SELECT', $sql_array);
			$result = $this->db->sql_query($sql);
			$row = $this->db->sql_fetchrow($result);
			$no_of_tickets = $row['no_of_tickets'];
			$this->db->sql_freeresult($result);

			// Select the total number of players
			$sql_array = [
				'SELECT' => 'user_id',
				'FROM' => [
					$this->points_lottery_tickets_table => 't',
				],
			];
			$sql = $this->db->sql_build_query('SELECT_DISTINCT', $sql_array);
			$result = $this->db->sql_query($sql);
			$no_of_players = 0;

			while ($row = $this->db->sql_fetchrow($result))
			{
				$no_of_players += 1;
			}
			$this->db->sql_freeresult($result);

			$this->template->assign_vars([
				'JACKPOT' => sprintf($this->functions_points->number_format_points($points_values['lottery_jackpot']), $this->config['points_name']),
				'POINTS_NAME' => $this->config['points_name'],
				'TICKET_COST' => sprintf($this->functions_points->number_format_points($points_values['lottery_ticket_cost'])),
				'PREVIOUS_WINNER' => $username_colored,
				'NEXT_DRAWING' => $this->user->format_date($points_values['lottery_last_draw_time'] + $points_values['lottery_draw_period'], false, true),
				'LOTTERY_NAME' => $points_values['lottery_name'],
				'BANK_NAME' => $points_values['bank_name'],
				'VIEWER_TICKETS_TOTAL' => $viewer_total_tickets,
				'LOTTERY_TICKETS' => $no_of_tickets,
				'LOTTERY_PLAYERS' => $no_of_players,
				'MAX_TICKETS' => $points_values['lottery_max_tickets'],
				'S_PURCHASE_SINGLE' => (($viewer_total_tickets == 0) && ($points_config['lottery_multi_ticket_enable'] == 0) && ($points_config['lottery_enable'] == 1)) ? true : false,
				'S_PURCHASE_MULTI' => (($viewer_total_tickets < $points_values['lottery_max_tickets']) && ($points_config['lottery_multi_ticket_enable'] == 1) && ($points_config['lottery_enable'] == 1)) ? true : false,
				'S_MULTI_TICKETS' => ($points_config['lottery_multi_ticket_enable'] == 1) ? true : false,
				'S_LOTTERY_ENABLE' => ($points_config['lottery_enable'] == 1) ? true : false,
				'S_DRAWING_ENABLED' => ($points_values['lottery_draw_period']) ? true : false,
				'U_PREVIOUS_WINNER' => $link_member,
				'U_VIEW_HISTORY' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'lottery', 'history' => 'all']),
				'U_VIEW_SELF_HISTORY' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'lottery', 'history' => 'ego']),
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
				'USER_POINTS' => sprintf($this->functions_points->number_format_points($checked_user['user_points'])),
				'LOTTERY_INFO' => sprintf($this->user->lang['LOTTERY_INFO'], $points_values['lottery_name']),
				'LOTTERY_LAST_WINNER' => sprintf($this->user->lang['LOTTERY_LAST_WINNER'], $points_values['lottery_name']),
			]);
		}

		// Generate the page header
		page_header($points_values['lottery_name']);

		// Generate the page template
		$this->template->set_filenames([
			'body' => 'points/points_lottery.html',
		]);

		page_footer();
	}
}
