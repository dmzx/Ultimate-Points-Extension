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
use phpbb\log\log;
use phpbb\notification\manager;
use phpbb\request\request;
use phpbb\template\template;
use phpbb\user;

class points_bank
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

	/** @var log */
	protected $log;

	/** @var manager */
	protected $notification_manager;

	/** @var string */
	protected $php_ext;

	/** @var string phpBB root path */
	protected $root_path;

	/**
	 * The database tables
	 *
	 * @var string
	 */
	protected $points_bank_table;

	protected $points_config_table;

	protected $points_values_table;

	/**
	 * Constructor
	 *
	 * @param functions_points $functions_points
	 * @param auth $auth
	 * @param template $template
	 * @param user $user
	 * @param driver_interface $db
	 * @param request $request
	 * @param config $config
	 * @param helper $helper
	 * @param log $log
	 * @param manager $notification_manager
	 * @param string $php_ext
	 * @param string $root_path
	 * @param string $points_bank_table
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
		log $log,
		manager $notification_manager,
		$php_ext,
		$root_path,
		$points_bank_table,
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
		$this->log = $log;
		$this->notification_manager = $notification_manager;
		$this->php_ext = $php_ext;
		$this->root_path = $root_path;
		$this->points_bank_table = $points_bank_table;
		$this->points_config_table = $points_config_table;
		$this->points_values_table = $points_values_table;
	}

	var $u_action;

	function main()
	{
		// Get all values
		$points_values = $this->functions_points->points_all_values();

		// Check Points Config Table if Bank is Enabled
		$sql = 'SELECT config_value
			FROM ' . $this->points_config_table . '
			WHERE config_name = "bank_enable"';
		$result = $this->db->sql_query($sql);
		$is_bank_enabled = $this->db->sql_fetchfield('config_value');
		$this->db->sql_freeresult($result);

		// Check if bank is enabled
		if (1 > $points_values['bank_pay_period'])
		{
			$message = $this->user->lang['BANK_ERROR_PAYOUTTIME_SHORT'] . '<br /><br /><a href="' . $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'bank']) . '">&laquo; ' . $this->user->lang['BACK_TO_PREV'] . '</a>';
			trigger_error($message);
		}

		if ($is_bank_enabled != 1)
		{
			$message = $this->user->lang['BANK_DISABLED'] . '<br /><br /><a href="' . $this->helper->route('dmzx_ultimatepoints_controller') . '">&laquo; ' . $this->user->lang['BACK_TO_PREV'] . '</a>';
			trigger_error($message);
		}

		if (!$this->auth->acl_get('u_use_bank'))
		{
			$message = $this->user->lang['NOT_AUTHORISED'] . '<br /><br /><a href="' . $this->helper->route('dmzx_ultimatepoints_controller') . '">&laquo; ' . $this->user->lang['BACK_TO_PREV'] . '</a>';
			trigger_error($message);
		}

		$withdrawtotal_check = '';

		// Add part to bar
		$this->template->assign_block_vars('navlinks', [
			'U_VIEW_FORUM' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'bank']),
			'FORUM_NAME' => $points_values['bank_name'],
		]);

		// Check, if it's time to pay users
		if ((time() - $points_values['bank_last_restocked']) > $points_values['bank_pay_period'])
		{
			$this->functions_points->run_bank();
		}

		$sql_array = [
			'SELECT' => '*',
			'FROM' => [
				$this->points_bank_table => 'u',
			],
			'WHERE' => 'user_id = ' . (int) $this->user->data['user_id'],
		];
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);

		$action = $this->request->variable('action', '');
		add_form_key('bank_action');

		// Default bank info page
		if (empty($action))
		{
			$this->template->set_filenames([
				'body' => 'points/points_bank.html'
			]);

			if (!isset($row['holding']) && $this->user->data['user_id'] > 0 && $this->user->data['username'] != ANONYMOUS)
			{
				$this->template->assign_block_vars('no_account', [
					'USER_NO_ACCOUNT' => sprintf($this->user->lang['BANK_USER_NO_ACCOUNT'], $points_values['bank_name']),
					'OPEN_ACCOUNT' => sprintf($this->user->lang['BANK_OPEN_ACCOUNT'], '<a href="' . $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'bank', 'action' => 'createaccount']) . '" title="' . $this->user->lang['BANK_OPEN_ACCOUNT'] . '!">', '</a>')
				]);
			}
			else if ($this->user->data['user_id'] > 0 && $this->user->data['username'] != ANONYMOUS)
			{
				$this->template->assign_block_vars('has_account', []);
			}

			$sql_array = [
				'SELECT' => 'SUM(holding) AS total_holding, count(user_id) AS total_users',
				'FROM' => [
					$this->points_bank_table => 'u',
				],
				'WHERE' => 'id > 0',
			];
			$sql = $this->db->sql_build_query('SELECT', $sql_array);
			$result = $this->db->sql_query($sql);
			$b_row = $this->db->sql_fetchrow($result);

			$bankholdings = ($b_row['total_holding']) ? $b_row['total_holding'] : 0;
			$bankusers = $b_row['total_users'];

			$fees = (is_array($row) && $row['fees']);
			$holding = (is_array($row) && $row['holding']);

			$withdrawtotal = ($fees == 'on') ? $holding - (round($holding / 100 * $points_values['bank_fees'])) : $holding;

			if ($fees == 'on' && $this->user->lang['BANK_WITHDRAW_RATE'])
			{
				$this->template->assign_block_vars('switch_withdraw_fees', []);
			}

			if ($points_values['bank_min_withdraw'])
			{
				$this->template->assign_block_vars('switch_min_with', []);
			}

			if ($points_values['bank_min_deposit'])
			{
				$this->template->assign_block_vars('switch_min_depo', []);
			}

			$banklocation = ' -> <a href="' . $this->helper->route('dmzx_ultimatepoints_controller') . '" class="nav">' . $points_values['bank_name'] . '</a>';

			$title = $points_values['bank_name'] . '; ' . ((!is_numeric($holding)) ? $this->user->lang['BANK_ACCOUNT_OPENING'] : $this->user->lang['BANK_DEPOSIT_WITHDRAW'] . ' ' . $this->config['points_name']);

			page_header($points_values['bank_name']);

			$bank_enable = $is_bank_enabled;

			$this->template->assign_vars([
				'BANK_NAME' => $points_values['bank_name'],
				'BANK_INFO' => sprintf($this->user->lang['BANK_INFO'], $points_values['bank_name']),
				'BANK_BALANCE' => sprintf($this->user->lang['BANK_INFO'], $points_values['bank_name']),
				'BANKLOCATION' => $banklocation,
				'BANK_OPENED' => $this->user->format_date($bank_enable),
				'BANK_HOLDINGS' => sprintf($this->functions_points->number_format_points($bankholdings)),
				'BANK_ACCOUNTS' => $bankusers,
				'BANK_FEES' => $points_values['bank_fees'],
				'BANK_INTEREST' => $points_values['bank_interest'],
				'BANK_MIN_WITH' => sprintf($this->functions_points->number_format_points($points_values['bank_min_withdraw'])),
				'BANK_MIN_DEPO' => sprintf($this->functions_points->number_format_points($points_values['bank_min_deposit'])),
				'BANK_MAX_HOLD' => sprintf($this->functions_points->number_format_points($points_values['bank_interestcut'])),
				'BANK_TITLE' => $title,
				'POINTS_NAME' => $this->config['points_name'],
				'USER_BALANCE' => sprintf($this->functions_points->number_format_points($holding)),
				'USER_GOLD' => $this->user->data['user_points'],
				'USER_WITHDRAW' => sprintf(number_format($withdrawtotal, 2, '.', '')),

				'U_WITHDRAW' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'bank', 'action' => 'withdraw']),
				'U_DEPOSIT' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'bank', 'action' => 'deposit'])
			]);

		} // Start page, where an account is created
		else if ($action == 'createaccount')
		{
			if (!$this->user->data['is_registered'])
			{
				login_box();
			}

			$this->template->set_filenames([
				'body' => 'points/points_bank.html'
			]);
			$holding = (is_array($row) && $row['holding']);
			if (is_numeric($holding))
			{
				trigger_error(' ' . $this->user->lang['YES_ACCOUNT'] . '!<br /><br />' . sprintf($this->user->lang['BANK_BACK_TO_BANK'], '<a href="' . $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'bank']) . '">', '</a>') . sprintf('<br />' . $this->user->lang['BANK_BACK_TO_INDEX'], '<a href="' . append_sid("{$this->root_path}index.{$this->php_ext}") . '">', '</a>'));
			}
			else
			{
				$sql = 'INSERT INTO ' . $this->points_bank_table . ' ' . $this->db->sql_build_array('INSERT', [
						'user_id' => (int) $this->user->data['user_id'],
						'opentime' => time(),
						'fees' => 'on',
					]);
				$this->db->sql_query($sql);

				trigger_error(' ' . $this->user->lang['BANK_WELCOME_BANK'] . ' ' . $points_values['bank_name'] . '! <br />' . $this->user->lang['BANK_START_BALANCE'] . '<br />' . $this->user->lang['BANK_YOUR_ACCOUNT'] . '!<br /><br />' . sprintf($this->user->lang['BANK_BACK_TO_BANK'], '<a href="' . $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'bank']) . '">', '</a>') . sprintf('<br />' . $this->user->lang['BANK_BACK_TO_INDEX'], '<a href="' . append_sid("{$this->root_path}index.{$this->php_ext}") . '">', '</a>'));
			}
		} // Deposit points
		else if ($action == 'deposit')
		{
			if (!check_form_key('bank_action'))
			{
				trigger_error('FORM_INVALID');
			}

			$deposit = round($this->request->variable('deposit', 0.00), 2);

			if (!$this->user->data['is_registered'])
			{
				login_box();
			}

			if ($deposit < $points_values['bank_min_deposit'])
			{
				$message = sprintf($this->user->lang['BANK_DEPOSIT_SMALL_AMOUNT'], $points_values['bank_min_deposit'], $this->config['points_name']) . '<br /><br /><a href="' . $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'bank']) . '">&laquo; ' . $this->user->lang['BACK_TO_PREV'] . '</a>';
				trigger_error($message);
			}
			else if ($deposit < 1)
			{
				$message = $this->user->lang['BANK_ERROR_DEPOSIT'] . '<br /><br /><a href="' . $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'bank']) . '">&laquo; ' . $this->user->lang['BACK_TO_PREV'] . '</a>';
				trigger_error($message);
			}
			else if ($deposit > $this->user->data['user_points'])
			{
				$message = sprintf($this->user->lang['BANK_ERROR_NOT_ENOUGH_DEPOSIT'], $this->config['points_name']) . '<br /><br /><a href="' . $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'bank']) . '">&laquo; ' . $this->user->lang['BACK_TO_PREV'] . '</a>';
				trigger_error($message);
			}

			$this->functions_points->substract_points($this->user->data['user_id'], $deposit);

			$sql_array = [
				'SELECT' => 'holding, totaldeposit',
				'FROM' => [
					$this->points_bank_table => 'b',
				],
				'WHERE' => 'user_id = ' . (int) $this->user->data['user_id'],
			];
			$sql = $this->db->sql_build_query('SELECT', $sql_array);
			$result = $this->db->sql_query($sql);
			$user_bank = $this->db->sql_fetchrow($result);
			$user_holding = $user_bank['holding'];
			$user_totaldeposit = $user_bank['totaldeposit'];
			$this->db->sql_freeresult($result);

			$data = [
				'holding' => $user_holding + $deposit,
				'totaldeposit' => $user_totaldeposit + $deposit,
			];

			$sql = 'UPDATE ' . $this->points_bank_table . '
				SET ' . $this->db->sql_build_array('UPDATE', $data) . '
				WHERE user_id = ' . (int) $this->user->data['user_id'];
			$this->db->sql_query($sql);

			trigger_error(' ' . $this->user->lang['BANK_HAVE_DEPOSIT'] . ' ' . sprintf($this->functions_points->number_format_points($deposit)) . ' ' . $this->config['points_name'] . ' ' . sprintf($this->user->lang['BANK_TO_ACCOUNT'], $points_values['bank_name']) . '<br />' . $this->user->lang['BANK_NEW_BALANCE'] . ' ' . sprintf($this->functions_points->number_format_points(($row['holding'] + $deposit))) . '.<br />' . $this->user->lang['BANK_LEAVE_WITH'] . ' ' . (sprintf($this->functions_points->number_format_points($this->user->data['user_points'] - $deposit))) . ' ' . $this->config['points_name'] . ' ' . $this->user->lang['BANK_ON_HAND'] . '.<br /><br />' . sprintf($this->user->lang['BANK_BACK_TO_BANK'], '<a href="' . $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'bank']) . '">', '</a>') . sprintf('<br />' . $this->user->lang['BANK_BACK_TO_INDEX'], '<a href="' . append_sid("{$this->root_path}index.{$this->php_ext}") . '">', '</a>'));
		} // Withdraw points
		else if ($action == 'withdraw')
		{
			if (!check_form_key('bank_action'))
			{
				trigger_error('FORM_INVALID');
			}

			$withdraw = round($this->request->variable('withdraw', 0.00), 2);

			if (!$this->user->data['is_registered'])
			{
				login_box();
			}

			if ($withdraw < $points_values['bank_min_withdraw'])
			{
				$message = sprintf($this->user->lang['BANK_WITHDRAW_SMALL_AMOUNT'], $points_values['bank_min_withdraw'], $this->config['points_name']) . '<br /><br /><a href="' . $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'bank']) . '">&laquo; ' . $this->user->lang['BACK_TO_PREV'] . '</a>';
				trigger_error($message);
			}
			else if ($withdraw < 1)
			{
				$message = $this->user->lang['BANK_ERROR_WITHDRAW'] . '<br /><br /><a href="' . $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'bank']) . '">&laquo; ' . $this->user->lang['BACK_TO_PREV'] . '</a>';
				trigger_error($message);
			}

			if ($row['fees'] == 'on')
			{
				$withdrawtotal_check = ($row['fees'] == 'on') ? $row['holding'] - (round($row['holding'] / 100 * $points_values['bank_fees'])) : $row['holding'];
				$fees = round($row['holding'] / 100 * $points_values['bank_fees']);

				if ($withdraw == $withdrawtotal_check)
				{
					$withdrawtotal = $withdraw + $fees;
				}
				else
				{
					$withdrawtotal = (round((($withdraw / 100) * $points_values['bank_fees']))) + $withdraw;
				}
			}
			else
			{
				$withdrawtotal = 0;
			}

			if ($row['holding'] < $withdrawtotal)
			{
				$message = sprintf($this->user->lang['BANK_ERROR_NOT_ENOUGH_WITHDRAW'], $this->config['points_name']) . '<br /><br /><a href="' . $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'bank']) . '">&laquo; ' . $this->user->lang['BACK_TO_PREV'] . '</a>';
				trigger_error($message);
			}

			// Transfer points to users cash account
			$this->functions_points->add_points($this->user->data['user_id'], $withdraw);

			// Update users bank account
			$sql_array = [
				'SELECT' => 'holding, totalwithdrew',
				'FROM' => [
					$this->points_bank_table => 'b',
				],
				'WHERE' => 'user_id = ' . (int) $this->user->data['user_id'],
			];
			$sql = $this->db->sql_build_query('SELECT', $sql_array);
			$result = $this->db->sql_query($sql);
			$user_bank = $this->db->sql_fetchrow($result);
			$user_holding = $user_bank['holding'];
			$user_totalwithdrew = $user_bank['totalwithdrew'];
			$this->db->sql_freeresult($result);

			$data = [
				'holding' => $user_holding - $withdrawtotal,
				'totalwithdrew' => $user_totalwithdrew + $withdraw,
			];

			$sql = 'UPDATE ' . $this->points_bank_table . '
				SET ' . $this->db->sql_build_array('UPDATE', $data) . '
				WHERE user_id = ' . (int) $this->user->data['user_id'];
			$this->db->sql_query($sql);

			trigger_error(' ' . $this->user->lang['BANK_HAVE_WITHDRAW'] . ' ' . sprintf($this->functions_points->number_format_points($withdraw)) . ' ' . $this->config['points_name'] . ' ' . sprintf($this->user->lang['BANK_FROM_ACCOUNT'], $points_values['bank_name']) . '. <br />' . $this->user->lang['BANK_NEW_BALANCE'] . ' ' . sprintf($this->functions_points->number_format_points(($row['holding'] - $withdrawtotal))) . ' ' . $this->config['points_name'] . '.<br />' . $this->user->lang['BANK_NOW_HAVE'] . ' ' . (sprintf($this->functions_points->number_format_points($this->user->data['user_points'] + $withdraw))) . ' ' . $this->config['points_name'] . ' ' . $this->user->lang['BANK_ON_HAND'] . '.<br /><br />' . sprintf($this->user->lang['BANK_BACK_TO_BANK'], '<a href="' . $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'bank']) . '">', '</a>') . sprintf('<br />' . $this->user->lang['BANK_BACK_TO_INDEX'], '<a href="' . append_sid("{$this->root_path}index.{$this->php_ext}") . '">', '</a>'));
		}
		else
		{
			redirect($this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'bank']));
		}

		// Generate most rich banker to show
		$limit = $points_values['number_show_top_points'];
		$sql_array = [
			'SELECT' => 'u.user_id, u.username, u.user_colour, b.*',

			'FROM' => [
				USERS_TABLE => 'u',
			],

			'LEFT_JOIN' => [
				[
					'FROM' => [$this->points_bank_table => 'b'],
					'ON' => 'u.user_id = b.user_id'
				],
			],

			'WHERE' => 'b.holding > 0',
			'ORDER_BY' => 'b.holding DESC, u.username ASC',
		];

		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query_limit($sql, $limit);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->template->assign_block_vars('bank', [
				'USERNAME' => get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
				'POINT' => sprintf($this->functions_points->number_format_points($row['holding'])),
			]);
		}
		$this->db->sql_freeresult($result);

		// Generate the time format
		function time_format($secs, $filter = false)
		{
			global $user;
			$output = '';
			$filter = ($filter) ? explode('|', strtolower($filter)) : false;

			$time_array = [
				'year' => 60 * 60 * 24 * 365,
				'month' => 60 * 60 * 24 * 30,
				'week' => 60 * 60 * 24 * 7,
				'day' => 60 * 60 * 24,
				'hour' => 60 * 60,
				'minute' => 60,
				'second' => 0,
			];

			foreach ($time_array as $key => $value)
			{
				if ($filter && !in_array($key, $filter))
				{
					continue;
				}

				$item = ($value) ? intval(intval($secs) / $value) : intval($secs);
				if ($item > 0)
				{
					$secs = $secs - ($item * $value);
					$output .= ' ' . $item . ' ' . (($item > 1) ? $user->lang['TIME_' . strtoupper($key) . 'S'] : $user->lang['TIME_' . strtoupper($key)]);
				}
			}

			return $output;
		}

		$this->template->assign_vars([
			'BANK_INTEREST_PERIOD' => time_format($points_values['bank_pay_period']),
			'BANK_COST' => sprintf($this->functions_points->number_format_points($points_values['bank_cost'])),
			'LOTTERY_NAME' => $points_values['lottery_name'],
			'BANK_NAME' => $points_values['bank_name'],
			'BANK_NOBODY_IN_BANK' => sprintf($this->user->lang['BANK_NOBODY_IN_BANK'], $this->config['points_name'], $points_values['bank_name']),
			'S_DISPLAY_INDEX' => ($points_values['number_show_top_points'] > 0) ? true : false,
			'L_BANK_DESCRIPTION' => sprintf($this->user->lang['BANK_DESCRIPTION'], $this->config['points_name']),
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
		]);

		page_footer();
	}
}
