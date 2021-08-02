<?php
/**
 *
 * @package phpBB Extension - Ultimate Points
 * @copyright (c) 2016 dmzx & posey - https://www.dmzx-web.net
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace dmzx\ultimatepoints\controller;

use dmzx\ultimatepoints\core\functions_points;
use dmzx\ultimatepoints\core\points_bank;
use dmzx\ultimatepoints\core\points_bank_edit;
use dmzx\ultimatepoints\core\points_info;
use dmzx\ultimatepoints\core\points_logs;
use dmzx\ultimatepoints\core\points_lottery;
use dmzx\ultimatepoints\core\points_main;
use dmzx\ultimatepoints\core\points_points_edit;
use dmzx\ultimatepoints\core\points_robbery;
use dmzx\ultimatepoints\core\points_robbery_user;
use dmzx\ultimatepoints\core\points_transfer;
use dmzx\ultimatepoints\core\points_transfer_user;
use phpbb\auth\auth;
use phpbb\config\config;
use phpbb\controller\helper;
use phpbb\db\driver\driver_interface;
use phpbb\request\request;
use phpbb\template\template;
use phpbb\user;

class main
{
	/** @var functions_points */
	protected $functions_points;

	/** @var points_main */
	protected $points_main;

	/** @var points_info */
	protected $points_info;

	/** @var points_transfer_user */
	protected $points_transfer_user;

	/** @var points_bank */
	protected $points_bank;

	/** @var points_logs */
	protected $points_logs;

	/** @var points_bank_edit */
	protected $points_bank_edit;

	/** @var points_lottery */
	protected $points_lottery;

	/** @var points_points_edit */
	protected $points_points_edit;

	/** @var points_robbery */
	protected $points_robbery;

	/** @var points_robbery_user */
	protected $points_robbery_user;

	/** @var points_transfer */
	protected $points_transfer;

	/** @var template */
	protected $template;

	/** @var user */
	protected $user;

	/** @var auth */
	protected $auth;

	/** @var driver_interface */
	protected $db;

	/** @var request */
	protected $request;

	/** @var config */
	protected $config;

	/** @var helper */
	protected $helper;

	/** @var string phpBB root path */
	protected $root_path;

	/** @var string php_ext */
	protected $php_ext;

	/**
	 * The database tables
	 *
	 * @var string
	 */
	protected $points_config_table;

	protected $points_values_table;

	/**
	 * Constructor
	 *
	 * @param template $template
	 * @param user $user
	 * @param auth $auth
	 * @param driver_interface $db
	 * @param request $request
	 * @param config $config
	 * @param helper $helper
	 * @param string $root_path
	 * @param string $php_ext
	 * @param string $points_config_table
	 * @param string $points_values_table
	 *
	 * @var functions_points $functions_points
	 * @var points_main $points_main
	 * @var points_info $points_info
	 * @var points_transfer_user $points_transfer_user
	 * @var points_bank $points_bank
	 * @var points_logs $points_logs
	 * @var points_bank_edit $points_bank_edit
	 * @var points_lottery $points_lottery
	 * @var points_points_edit $points_points_edit
	 * @var points_robbery $points_robbery
	 * @var points_robbery_user $points_robbery_user
	 * @var points_transfer $points_transfer
	 */
	public function __construct(
		functions_points $functions_points,
		points_main $points_main,
		points_info $points_info,
		points_transfer_user $points_transfer_user,
		points_bank $points_bank,
		points_logs $points_logs,
		points_bank_edit $points_bank_edit,
		points_lottery $points_lottery,
		points_points_edit $points_points_edit,
		points_robbery $points_robbery,
		points_robbery_user $points_robbery_user,
		points_transfer $points_transfer,
		template $template,
		user $user,
		auth $auth,
		driver_interface $db,
		request $request,
		config $config,
		helper $helper,
		$root_path,
		$php_ext,
		$points_config_table,
		$points_values_table
	)
	{
		$this->functions_points = $functions_points;
		$this->points_main = $points_main;
		$this->points_info = $points_info;
		$this->points_transfer_user = $points_transfer_user;
		$this->points_bank = $points_bank;
		$this->points_logs = $points_logs;
		$this->points_bank_edit = $points_bank_edit;
		$this->points_lottery = $points_lottery;
		$this->points_points_edit = $points_points_edit;
		$this->points_robbery = $points_robbery;
		$this->points_robbery_user = $points_robbery_user;
		$this->points_transfer = $points_transfer;
		$this->template = $template;
		$this->user = $user;
		$this->auth = $auth;
		$this->db = $db;
		$this->request = $request;
		$this->config = $config;
		$this->helper = $helper;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
		$this->points_config_table = $points_config_table;
		$this->points_values_table = $points_values_table;
	}

	public function handle_ultimatepoints()
	{
		include($this->root_path . 'includes/functions_user.' . $this->php_ext);
		include($this->root_path . 'includes/functions_module.' . $this->php_ext);
		include($this->root_path . 'includes/functions_display.' . $this->php_ext);
		include($this->root_path . 'includes/functions_privmsgs.' . $this->php_ext);

		$mode = $this->request->variable('mode', '');

		$this->functions_points->assign_authors();

		// Get all configs
		$points_config = $this->functions_points->points_all_configs();

		// Exclude Bots
		if ($this->user->data['is_bot'])
		{
			redirect(append_sid("{$this->root_path}index.{$this->php_ext}"));
		}

		// Check if you are locked or not
		if (!$this->auth->acl_get('u_use_points'))
		{
			trigger_error('NOT_AUTHORISED');
		}

		// Get user's information
		$check_user = $this->request->variable('i', 0);
		$check_user = ($check_user == 0) ? $this->user->data['user_id'] : $check_user;

		$sql_array = [
			'SELECT' => '*',
			'FROM' => [
				USERS_TABLE => 'u',
			],
			'WHERE' => 'u.user_id = ' . (int) $check_user,
		];
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$checked_user = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if (!$checked_user)
		{
			trigger_error('POINTS_NO_USER');
		}

		// Check if points system is enabled
		if (!$this->config['points_enable'])
		{
			trigger_error($points_config['points_disablemsg']);
		}

		// Add the base entry into the Nav Bar at top
		$this->template->assign_block_vars('navlinks', [
			'U_VIEW_FORUM' => $this->helper->route('dmzx_ultimatepoints_controller'),
			'FORUM_NAME' => sprintf($this->user->lang['POINTS_TITLE_MAIN'], $this->config['points_name']),
		]);

		$this->template->assign_vars(array_change_key_case($checked_user, CASE_UPPER));

		$this->template->assign_vars(array_merge(array_change_key_case($points_config, CASE_UPPER), [
			'USER_POINTS' => $this->functions_points->number_format_points($this->user->data['user_points']),
			'U_USE_POINTS' => $this->auth->acl_get('u_use_points'),
			'U_CHG_POINTS' => $this->auth->acl_get('m_chg_points'),
			'POINT_VERS' => $this->config['ultimate_points_version'],
			'U_USE_TRANSFER' => $this->auth->acl_get('u_use_transfer'),
			'U_USE_LOGS' => $this->auth->acl_get('u_use_logs'),
			'U_USE_LOTTERY' => $this->auth->acl_get('u_use_lottery'),
			'U_USE_BANK' => $this->auth->acl_get('u_use_bank'),
			'U_USE_ROBBERY' => $this->auth->acl_get('u_use_robbery'),
		]));

		$this->template->assign_var('ULTIMATEPOINTS_FOOTER_VIEW', true);

		switch ($mode)
		{
			case 'transfer_user':
				$this->points_transfer_user->main($checked_user);
				break;

			case 'logs':
				$this->points_logs->main($checked_user);
				break;

			case 'lottery':
				$this->points_lottery->main($checked_user);
				break;

			case 'transfer':
				$this->points_transfer->main($checked_user);
				break;

			case 'robbery':
				$this->points_robbery->main($checked_user);
				break;

			case 'robbery_user':
				$this->points_robbery_user->main($checked_user);
				break;

			case 'points_edit':
				$this->points_points_edit->main();
				break;

			case 'bank':
				$this->points_bank->main();
				break;

			case 'bank_edit':
				$this->points_bank_edit->main();
				break;

			case 'info':
				$this->points_info->main();
				break;

			default:
				$this->points_main->main($checked_user);
				break;
		}
	}
}
