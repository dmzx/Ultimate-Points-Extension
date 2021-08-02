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

class points_logs
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

	protected $points_values_table;

	/**
	 * Constructor
	 *
	 * @param auth $auth
	 * @param template $template
	 * @param user $user
	 * @param driver_interface $db
	 * @param request $request
	 * @param config $config
	 * @param helper $helper
	 * @param pagination $pagination
	 * @param string $php_ext
	 * @param string $root_path
	 * @param string $points_log_table
	 * @param string $points_values_table
	 *
	 * @var functions_points $functions_points
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
		pagination $pagination,
		$php_ext,
		$root_path,
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
		$this->pagination = $pagination;
		$this->php_ext = $php_ext;
		$this->root_path = $root_path;
		$this->points_log_table = $points_log_table;
		$this->points_values_table = $points_values_table;
	}

	var $u_action;

	function main($checked_user)
	{
		// Get all values
		$points_values = $this->functions_points->points_all_values();

		// Get all configs
		$points_config = $this->functions_points->points_all_configs();

		// Check, if logs are enabled
		if (!$points_config['logs_enable'])
		{
			$message = $this->user->lang['LOGS_DISABLED'] . '<br /><br /><a href="' . $this->helper->route('dmzx_ultimatepoints_controller') . '">&laquo; ' . $this->user->lang['BACK_TO_PREV'] . '</a>';
			trigger_error($message);
		}

		// Check if user is allowed to use the logs
		if (!$this->auth->acl_get('u_use_logs'))
		{
			$message = $this->user->lang['NOT_AUTHORISED'] . '<br /><br /><a href="' . append_sid("{$this->root_path}ultimatepoints") . '">&laquo; ' . $this->user->lang['BACK_TO_PREV'] . '</a>';
			trigger_error($message);
		}

		// Add part to bar
		$this->template->assign_block_vars('navlinks', [
			'U_VIEW_FORUM' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'logs']),
			'FORUM_NAME' => sprintf($this->user->lang['LOGS_TITLE'], $this->config['points_name']),
		]);

		// Preparing the sort order
		$start = $this->request->variable('start', 0);
		$number = $points_values['number_show_per_page'];

		$sort_days = $this->request->variable('st', 0);
		$sort_key = $this->request->variable('sk', 'date');
		$sort_dir = $this->request->variable('sd', 'd');
		$limit_days = [0 => $this->user->lang['ALL_POSTS'], 1 => $this->user->lang['1_DAY'], 7 => $this->user->lang['7_DAYS'], 14 => $this->user->lang['2_WEEKS'], 30 => $this->user->lang['1_MONTH'], 90 => $this->user->lang['3_MONTHS'], 180 => $this->user->lang['6_MONTHS'], 365 => $this->user->lang['1_YEAR']];

		$sort_by_text = ['date' => $this->user->lang['LOGS_SORT_DATE'], 'to' => $this->user->lang['LOGS_SORT_TONAME'], 'from' => $this->user->lang['LOGS_SORT_FROMNAME'], 'comment' => $this->user->lang['LOGS_SORT_COMMENT']];
		$sort_by_sql = ['date' => 'point_date', 'to' => 'point_recv', 'from' => 'point_send', 'comment' => 'point_comment'];

		$s_limit_days = $s_sort_key = $s_sort_dir = $u_sort_param = '';
		gen_sort_selects($limit_days, $sort_by_text, $sort_days, $sort_key, $sort_dir, $s_limit_days, $s_sort_key, $s_sort_dir, $u_sort_param);
		$sql_sort_order = $sort_by_sql[$sort_key] . ' ' . (($sort_dir == 'd') ? 'DESC' : 'ASC');

		// The different log types
		$types = [
			0 => '--',
			1 => $this->user->lang['LOGS_RECV'],
			2 => $this->user->lang['LOGS_SENT'],
			3 => $this->user->lang['LOGS_ROBBERY_WON'],
			4 => $this->user->lang['LOGS_ROBBERY_LOST'],
		];

		// Grab the total amount of logs for this user
		$sql_array = [
			'SELECT' => 'COUNT(*) AS total',
			'FROM' => [
				$this->points_log_table => 'l',
			],
			'WHERE' => 'point_send = ' . (int) $this->user->data['user_id'] . '
				OR point_recv = ' . (int) $this->user->data['user_id'],
		];
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$max = (int) $this->db->sql_fetchfield('total');

		// Grab the actual logs based on all account movements
		$sql_array = [
			'SELECT' => '*',
			'FROM' => [
				$this->points_log_table => 'l',
			],
			'WHERE' => 'point_send = ' . (int) $this->user->data['user_id'] . '
				OR point_recv = ' . (int) $this->user->data['user_id'],
			'ORDER_BY' => $sql_sort_order,
		];
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query_limit($sql, $number, $start);

		// Start looping all the logs
		while ($row = $this->db->sql_fetchrow($result))
		{
			switch ($row['point_type'])
			{
				case 1: //Transfer
					$transfer_user = ($row['point_send'] == $checked_user['user_id']) ? $row['point_recv'] : $row['point_send'];
					$sql_array = [
						'SELECT' => '*',
						'FROM' => [
							USERS_TABLE => 'u',
						],
						'WHERE' => 'user_id = ' . (int) $transfer_user,
					];
					$sql = $this->db->sql_build_query('SELECT', $sql_array);
					$result1 = $this->db->sql_query($sql);
					$opponent = $this->db->sql_fetchrow($result1);
					$this->db->sql_freeresult($result1);

					if ($row['point_send'] == $checked_user['user_id'])
					{
						$who = get_username_string('full', $checked_user['user_id'], $checked_user['username'], $checked_user['user_colour']) . "<br />(" . $this->functions_points->number_format_points($row['point_sendold']) . "->" . $this->functions_points->number_format_points($row['point_sendold'] - $row['point_amount']) . ")";
						$to = get_username_string('full', $opponent['user_id'], $opponent['username'], $opponent['user_colour']) . "<br />(" . $this->functions_points->number_format_points($row['point_recvold']) . "->" . $this->functions_points->number_format_points($row['point_recvold'] + ((100 - $points_values['transfer_fee']) / 100 * $row['point_amount'])) . ")";
						$rows = 2;
					} else
					{
						$to = get_username_string('full', $checked_user['user_id'], $checked_user['username'], $checked_user['user_colour']) . "<br />(" . $this->functions_points->number_format_points($row['point_recvold']) . "->" . $this->functions_points->number_format_points($row['point_recvold'] + ((100 - $points_values['transfer_fee']) / 100 * $row['point_amount'])) . ")";
						$who = get_username_string('full', $opponent['user_id'], $opponent['username'], $opponent['user_colour']) . "<br />(" . $this->functions_points->number_format_points($row['point_sendold']) . "->" . $this->functions_points->number_format_points($row['point_sendold'] - $row['point_amount']) . ")";
						$rows = 1;
					}
					$who .= " (-" . $this->functions_points->number_format_points($row['point_amount']) . ")";
					$to .= " (+" . $this->functions_points->number_format_points((100 - $points_values['transfer_fee']) / 100 * $row['point_amount']) . ")";
					break;

				case 2: //Locked
					$who = get_username_string('full', $opponent['user_id'], $opponent['username'], $opponent['user_colour']);
					$to = "--";
					break;

				case 3: //Robbery
					$transfer_user = ($row['point_send'] == $checked_user['user_id']) ? $row['point_recv'] : $row['point_send'];
					$sql_array = [
						'SELECT' => '*',
						'FROM' => [
							USERS_TABLE => 'u',
						],
						'WHERE' => 'user_id = ' . (int) $transfer_user,
					];
					$sql = $this->db->sql_build_query('SELECT', $sql_array);
					$result1 = $this->db->sql_query($sql);
					$opponent = $this->db->sql_fetchrow($result1);
					$this->db->sql_freeresult($result1);

					if ($row['point_send'] == $checked_user['user_id'])
					{
						$who = get_username_string('full', $checked_user['user_id'], $checked_user['username'], $checked_user['user_colour']) . "<br />(" . $this->functions_points->number_format_points($row['point_sendold']) . "->" . $this->functions_points->number_format_points($row['point_sendold'] + $row['point_amount']) . ")";
						$to = get_username_string('full', $opponent['user_id'], $opponent['username'], $opponent['user_colour']) . "<br />(" . $this->functions_points->number_format_points($row['point_recvold']) . "->" . $this->functions_points->number_format_points($row['point_recvold'] - $row['point_amount']) . ")";
						$rows = 3;
					}
					else
					{
						$to = get_username_string('full', $checked_user['user_id'], $checked_user['username'], $checked_user['user_colour']) . "<br />(" . $this->functions_points->number_format_points($row['point_recvold']) . "->" . $this->functions_points->number_format_points($row['point_recvold'] - $row['point_amount']) . ")";
						$who = get_username_string('full', $opponent['user_id'], $opponent['username'], $opponent['user_colour']) . "<br />(" . $this->functions_points->number_format_points($row['point_sendold']) . "->" . $this->functions_points->number_format_points($row['point_sendold'] + $row['point_amount']) . ")";
						$rows = 4;
					}
					$who .= " (+" . $this->functions_points->number_format_points($row['point_amount']) . ")";
					$to .= " (-" . $this->functions_points->number_format_points($row['point_amount']) . ")";
					break;
			}

			// Add the items to the template
			$this->template->assign_block_vars('logs', [
				'DATE' => $this->user->format_date($row['point_date']),
				'COMMENT' => nl2br($row['point_comment']),
				'TYPE' => $types[$rows],
				'ROW' => $rows,
				'WHO' => $who,
				'TO' => $to,
			]);
		}
		$this->db->sql_freeresult($result);

		//Start pagination
		$this->pagination->generate_template_pagination($this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'logs', 'sk' => $sort_key, 'sd' => $sort_dir]), 'pagination', 'start', $max, $number, $start);

		// Generate the page template
		$this->template->assign_vars([
			'PAGINATION' => $this->user->lang('POINTS_LOG_COUNT', $max),
			'LOTTERY_NAME' => $points_values['lottery_name'],
			'BANK_NAME' => $points_values['bank_name'],
			'S_LOGS_ACTION' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'logs']),
			'S_SELECT_SORT_DIR' => $s_sort_dir,
			'S_SELECT_SORT_KEY' => $s_sort_key,
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

		// Generate the page header
		page_header(sprintf($this->user->lang['LOGS_TITLE'], $checked_user['username']));

		// Generate the page template
		$this->template->set_filenames([
			'body' => 'points/points_logs.html',
		]);

		page_footer();
	}
}
