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
use parse_message;
use phpbb\auth\auth;
use phpbb\config\config;
use phpbb\db\driver\driver_interface;
use phpbb\log\log;
use phpbb\request\request;
use phpbb\template\template;
use phpbb\user;
use Symfony\Component\DependencyInjection\Container;

class admin_controller
{
	/** @var functions_points */
	protected $functions_points;

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

	/** @var log */
	protected $log;

	/** @var Container */
	protected $phpbb_container;

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

	protected $points_log_table;

	protected $points_lottery_history_table;

	/**
	 * Constructor
	 *
	 * @param template $template
	 * @param user $user
	 * @param auth $auth
	 * @param driver_interface $db
	 * @param request $request
	 * @param config $config
	 * @param log $log
	 * @param Container $phpbb_container
	 * @param string $root_path
	 * @param string $php_ext
	 * @param string $points_config_table
	 * @param string $points_values_table
	 * @param string $points_log_table
	 * @param string $points_lottery_history_table
	 *
	 * @var functions_points $functions_points
	 */
	public function __construct(
		functions_points $functions_points,
		template $template,
		user $user,
		auth $auth,
		driver_interface $db,
		request $request,
		config $config,
		log $log,
		Container $phpbb_container,
		$root_path,
		$php_ext,
		$points_config_table,
		$points_values_table,
		$points_log_table,
		$points_lottery_history_table
	)
	{
		$this->functions_points = $functions_points;
		$this->template = $template;
		$this->user = $user;
		$this->auth = $auth;
		$this->db = $db;
		$this->request = $request;
		$this->config = $config;
		$this->log = $log;
		$this->phpbb_container = $phpbb_container;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
		$this->points_config_table = $points_config_table;
		$this->points_values_table = $points_values_table;
		$this->points_log_table = $points_log_table;
		$this->points_lottery_history_table = $points_lottery_history_table;
	}

	public function display_points()
	{
		// Get all configs
		$points_config = $this->functions_points->points_all_configs();

		$this->template->assign_vars(array_change_key_case($points_config, CASE_UPPER));

		// Get all values
		$points_values = $this->functions_points->points_all_values();

		// Form key
		add_form_key('acp_points');

		$this->template->assign_vars([
			'BASE' => $this->u_action,
		]);

		if ($this->request->is_set_post('submit'))
		{
			if (!check_form_key('acp_points'))
			{
				trigger_error($this->user->lang['FORM_INVALID'] . adm_back_link($this->u_action), E_USER_WARNING);
			}

			// Set the options the user configured
			$this->set_options();

			// Values for phpbb_points_values
			$sql_ary = [
				'transfer_fee' => $this->request->variable('transfer_fee', 0),
				'number_show_per_page' => $this->request->variable('number_show_per_page', 0),
				'number_show_top_points' => $this->request->variable('number_show_top_points', 0),
				'points_per_attach' => round($this->request->variable('points_per_attach', 0.00), 2),
				'points_per_attach_file' => round($this->request->variable('points_per_attach_file', 0.00), 2),
				'points_per_poll' => round($this->request->variable('points_per_poll', 0.00), 2),
				'points_per_poll_option' => round($this->request->variable('points_per_poll_option', 0.00), 2),
				'points_per_topic_word' => round($this->request->variable('points_per_topic_word', 0.00), 2),
				'points_per_topic_character' => round($this->request->variable('points_per_topic_character', 0.00), 2),
				'points_per_post_word' => round($this->request->variable('points_per_post_word', 0.00), 2),
				'points_per_post_character' => round($this->request->variable('points_per_post_character', 0.00), 2),
				'reg_points_bonus' => round($this->request->variable('reg_points_bonus', 0.00), 2),
				'points_bonus_chance' => round($this->request->variable('points_bonus_chance', 0.00), 2),
				'points_bonus_min' => round($this->request->variable('points_bonus_min', 0.00), 2),
				'points_bonus_max' => round($this->request->variable('points_bonus_max', 0.00), 2),
				'points_per_warn' => round($this->request->variable('points_per_warn', 0.00), 2),
			];

			// Check if number_show_per_page is at least 5
			$per_page_check = $this->request->variable('number_show_per_page', 0);

			if ($per_page_check < 5)
			{
				trigger_error($this->user->lang['POINTS_SHOW_PER_PAGE_ERROR'] . adm_back_link($this->u_action), E_USER_WARNING);
			}

			// Check if Transfer Fee percent is not more than 100%
			if ($sql_ary['transfer_fee'] > 100)
			{
				trigger_error($this->user->lang['POINTS_TRANSFER_FEE_ERROR'] . adm_back_link($this->u_action), E_USER_WARNING);
			}

			// Update values in phpbb_points_values
			$sql = 'UPDATE ' . $this->points_values_table . ' SET ' . $this->db->sql_build_array('UPDATE', $sql_ary);
			$this->db->sql_query($sql);

			// Add logs
			$this->log->add('admin', $this->user->data['user_id'], $this->user->data['user_ip'], 'LOG_MOD_POINTS_SETTINGS');
			trigger_error($this->user->lang['POINTS_CONFIG_SUCCESS'] . adm_back_link($this->u_action));
		}
		else
		{
			$this->template->assign_vars([
				'POINTS_NAME' => $this->config['points_name'],
				'POINTS_NAME_UPLIST' => $this->config['points_name_uplist'],
				'POINTS_ICON_MAINICON' => $this->config['points_icon_mainicon'],
				'POINTS_ICON_UPLIST' => $this->config['points_icon_uplist'],
				'POINTS_PER_ATTACH' => $points_values['points_per_attach'],
				'POINTS_PER_ATTACH_FILE' => $points_values['points_per_attach_file'],
				'POINTS_PER_POLL' => $points_values['points_per_poll'],
				'POINTS_PER_POLL_OPTION' => $points_values['points_per_poll_option'],
				'POINTS_PER_TOPIC_WORD' => $points_values['points_per_topic_word'],
				'POINTS_PER_TOPIC_CHARACTER' => $points_values['points_per_topic_character'],
				'POINTS_PER_POST_WORD' => $points_values['points_per_post_word'],
				'POINTS_PER_POST_CHARACTER' => $points_values['points_per_post_character'],
				'POINTS_PER_WARN' => $points_values['points_per_warn'],
				'REG_POINTS_BONUS' => $points_values['reg_points_bonus'],
				'POINTS_BONUS_CHANCE' => $points_values['points_bonus_chance'],
				'POINTS_BONUS_MIN' => $points_values['points_bonus_min'],
				'POINTS_BONUS_MAX' => $points_values['points_bonus_max'],
				'NUMBER_SHOW_TOP_POINTS' => $points_values['number_show_top_points'],
				'NUMBER_SHOW_PER_PAGE' => $points_values['number_show_per_page'],
				'TRANSFER_FEE' => $points_values['transfer_fee'],
				'POINTS_ENABLE' => ($this->config['points_enable']) ? true : false,
				'ULTIMATEPOINTS_VERSION' => $this->config['ultimate_points_version'],
			]);
		}

		// Delete all userlogs
		$reset_pointslogs = (isset($_POST['action_points_logs'])) ? true : false;

		if ($reset_pointslogs)
		{
			if (confirm_box(true))
			{
				if (!$this->auth->acl_get('a_points'))
				{
					trigger_error($this->user->lang['NO_AUTH_OPERATION'] . adm_back_link($this->u_action), E_USER_WARNING);
				}

				$sql_layer = $this->db->get_sql_layer();
				switch ($sql_layer)
				{
					case 'sqlite':
					case 'firebird':
						$this->db->sql_query('DELETE FROM ' . $this->points_log_table);
						break;

					default:
						$this->db->sql_query('DELETE FROM ' . $this->points_log_table);
						break;
				}

				$this->log->add('admin', $this->user->data['user_id'], $this->user->data['user_ip'], 'LOG_RESYNC_POINTSLOGSCOUNTS');
				trigger_error($this->user->lang['LOG_RESYNC_POINTSLOGSCOUNTS'] . adm_back_link($this->u_action));
			} // Create a confirmbox with yes and no.
			else
			{
				$s_hidden_fields = build_hidden_fields([
					'action_points_logs' => true,
				]);

				// Display mode
				confirm_box(false, $this->user->lang['RESYNC_POINTSLOGS_CONFIRM'], $s_hidden_fields);
			}
		}

		// Delete all userpoints
		$reset_points_user = (isset($_POST['action_points'])) ? true : false;

		if ($reset_points_user)
		{
			if (confirm_box(true))
			{

				if (!$this->auth->acl_get('a_points'))
				{
					trigger_error($this->user->lang['NO_AUTH_OPERATION'] . adm_back_link($this->u_action), E_USER_WARNING);
				}

				$this->db->sql_query('UPDATE ' . USERS_TABLE . ' SET user_points = 0');

				$this->log->add('admin', $this->user->data['user_id'], $this->user->data['user_ip'], 'LOG_RESYNC_POINTSCOUNTS');
				trigger_error($this->user->lang['LOG_RESYNC_POINTSCOUNTS'] . adm_back_link($this->u_action));
			} // Create a confirmbox with yes and no.
			else
			{
				$s_hidden_fields = build_hidden_fields([
					'action_points' => true,
				]);

				// Display mode
				confirm_box(false, $this->user->lang['RESYNC_POINTS_CONFIRM'], $s_hidden_fields);
			}
		}

		// Transfer or set points for groups
		$group_transfer = (isset($_POST['group_transfer'])) ? true : false;
		$group_transfer_points = $this->request->variable('group_transfer_points', 0.00);
		$func = $this->request->variable('func', '');
		$group_id = $this->request->variable('group_id', 0);
		$pm_subject = $this->request->variable('pm_subject', '', true);
		$pm_text = $this->request->variable('pm_text', '', true);

		$sql_array = [
			'SELECT' => 'group_id, group_name, group_type',
			'FROM' => [
				GROUPS_TABLE => 'g',
			],
			'ORDER_BY' => 'group_name',
		];
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$total_groups = $this->db->sql_affectedrows($result);
		$this->db->sql_freeresult($result);

		$this->template->assign_vars([
			'U_SMILIES' => append_sid("{$this->root_path}posting.{$this->php_ext}", 'mode=smilies'),
			'S_GROUP_OPTIONS' => group_select_options($total_groups),
			'U_ACTION' => $this->u_action
		]);

		// Update the points
		if ($group_transfer)
		{
			if (!check_form_key('acp_points'))
			{
				trigger_error($this->user->lang['FORM_INVALID'] . adm_back_link($this->u_action), E_USER_WARNING);
			}

			$sql_array = [
				'SELECT' => 'group_type, group_name',
				'FROM' => [
					GROUPS_TABLE => 'g',
				],
				'WHERE' => 'group_id = ' . (int) $group_id,
			];
			$sql = $this->db->sql_build_query('SELECT', $sql_array);
			$result = $this->db->sql_query($sql);
			$row = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);

			$group_name = ($row['group_type'] == GROUP_SPECIAL) ? $this->user->lang['G_' . $row['group_name']] : $row['group_name'];

			// Check if we try transfering to BOTS or GUESTS
			if ($row['group_name'] == 'BOTS' || $row['group_name'] == 'GUESTS')
			{
				trigger_error($this->user->lang['POINTS_GROUP_TRANSFER_SEL_ERROR'] . adm_back_link($this->u_action), E_USER_WARNING);
			}

			$sql_array = [
				'SELECT' => 'user_id',
				'FROM' => [
					USER_GROUP_TABLE => 'g',
				],
				'WHERE' => 'user_pending <> ' . true . '
					AND group_id = ' . (int) $group_id,
			];
			$sql = $this->db->sql_build_query('SELECT', $sql_array);
			$result = $this->db->sql_query($sql);

			$user_ids = [];

			while ($row = $this->db->sql_fetchrow($result))
			{
				$user_ids[] = $row['user_id'];
			}

			$this->db->sql_freeresult($result);

			if (sizeof($user_ids))
			{
				$userdata_group = implode(', ', $user_ids);

				if ($func == 'add')
				{
					$sql = "UPDATE " . USERS_TABLE . "
						SET user_points = user_points + $group_transfer_points
						WHERE user_id IN ($userdata_group)";
					$this->log->add('admin', $this->user->data['user_id'], $this->user->data['user_ip'], 'LOG_GROUP_TRANSFER_ADD');
				}

				if ($func == 'substract')
				{
					$sql = "UPDATE " . USERS_TABLE . "
						SET user_points = user_points - $group_transfer_points
						WHERE user_id IN ($userdata_group)";
					$this->log->add('admin', $this->user->data['user_id'], $this->user->data['user_ip'], 'LOG_GROUP_TRANSFER_ADD');
				}

				if ($func == 'set')
				{
					$sql = "UPDATE " . USERS_TABLE . "
						SET user_points = $group_transfer_points
						WHERE user_id IN ($userdata_group)";
					$this->log->add('admin', $this->user->data['user_id'], $this->user->data['user_ip'], 'LOG_GROUP_TRANSFER_SET');
				}

				$result = $this->db->sql_query($sql);

				// Send PM, if pm subject and pm comment is entered
				if ($pm_subject != '' || $pm_text != '')
				{
					if ($pm_subject == '' || $pm_text == '')
					{
						trigger_error($this->user->lang['POINTS_GROUP_TRANSFER_PM_ERROR'] . adm_back_link($this->u_action), E_USER_WARNING);
					} else
					{
						$sql_array = [
							'SELECT' => 'user_id, group_id',
							'FROM' => [
								USER_GROUP_TABLE => 'g',
							],
							'WHERE' => 'user_pending <> ' . true . '
								AND group_id = ' . (int) $group_id,
						];
						$sql = $this->db->sql_build_query('SELECT', $sql_array);
						$result = $this->db->sql_query($sql);
						$group_to = [];

						while ($row = $this->db->sql_fetchrow($result))
						{
							$group_to[$row['group_id']] = 'to';
						}

						// and notify PM to recipient of rating:
						require_once($this->root_path . 'includes/functions_privmsgs.' . $this->php_ext);
						include_once($this->root_path . 'includes/message_parser.' . $this->php_ext);

						$message_parser = new parse_message();
						$message_parser->message = $pm_text;
						$message_parser->parse(true, true, true, false, false, true, true);

						$pm_data = [
							'address_list' => ['g' => $group_to],
							'from_user_id' => $this->user->data['user_id'],
							'from_username' => 'Points Transfer',
							'icon_id' => 0,
							'from_user_ip' => $this->user->data['user_ip'],

							'enable_bbcode' => true,
							'enable_smilies' => true,
							'enable_urls' => true,
							'enable_sig' => true,

							'message' => $message_parser->message,
							'bbcode_bitfield' => $message_parser->bbcode_bitfield,
							'bbcode_uid' => $message_parser->bbcode_uid,
						];
						submit_pm('post', $pm_subject, $pm_data, false);

						$this->db->sql_freeresult($result);
					}
					$message = $this->user->lang['POINTS_GROUP_TRANSFER_PM_SUCCESS'] . adm_back_link($this->u_action);
					trigger_error($message);
				}
				else
				{
					$message = $this->user->lang['POINTS_GROUP_TRANSFER_SUCCESS'] . adm_back_link($this->u_action);
					trigger_error($message);
				}
			}
		}

		if ($this->phpbb_container->has('dmzx.mchat.settings'))
		{
			$this->template->assign_var('TRANSFER_MCHAT_VIEW', true);
		}

		$this->template->assign_vars([
			'S_POINTS_MAIN' => true,
			'S_POINTS_ACTIVATED' => ($this->config['points_enable']) ? true : false,
			'TRANSFER_MCHAT_ENABLE' => $this->config['transfer_mchat_enable'],
			'U_ACTION' => $this->u_action
		]);
	}

	protected function set_options()
	{
		// Update values in phpbb_config
		$this->config->set('points_name', $this->request->variable('points_name', '', true));
		$this->config->set('points_enable', $this->request->variable('points_enable', 0));
		$this->config->set('points_icon_mainicon', $this->request->variable('points_icon_mainicon', '', true));
		$this->config->set('points_icon_uplist', $this->request->variable('points_icon_uplist', '', true));
		$this->config->set('points_name_uplist', $this->request->variable('points_name_uplist', '', true));
		$this->config->set('transfer_mchat_enable', $this->request->variable('transfer_mchat_enable', '', true));

		// Update values in phpbb_points_config
		$this->functions_points->set_points_config('points_disablemsg', $this->request->variable('points_disablemsg', '', true));
		$this->functions_points->set_points_config('transfer_enable', $this->request->variable('transfer_enable', 0));
		$this->functions_points->set_points_config('transfer_pm_enable', $this->request->variable('transfer_pm_enable', 0));
		$this->functions_points->set_points_config('comments_enable', $this->request->variable('comments_enable', 0));
		$this->functions_points->set_points_config('uplist_enable', $this->request->variable('uplist_enable', 0));
		$this->functions_points->set_points_config('stats_enable', $this->request->variable('stats_enable', 0));
		$this->functions_points->set_points_config('logs_enable', $this->request->variable('logs_enable', 0));
		$this->functions_points->set_points_config('images_topic_enable', $this->request->variable('images_topic_enable', 0));
		$this->functions_points->set_points_config('images_memberlist_enable', $this->request->variable('images_memberlist_enable', 0));
	}

	public function display_lottery()
	{
		// Get all configs
		$points_config = $this->functions_points->points_all_configs();

		$this->template->assign_vars(array_change_key_case($points_config, CASE_UPPER));

		// Get all values
		$points_values = $this->functions_points->points_all_values();

		// Form key
		add_form_key('acp_points');

		$this->template->assign_vars([
			'BASE' => $this->u_action,
		]);

		if ($this->request->is_set_post('submit'))
		{
			if (!check_form_key('acp_points'))
			{
				trigger_error($this->user->lang['FORM_INVALID'] . adm_back_link($this->u_action), E_USER_WARNING);
			}

			// Get current lottery_base_amount
			$current_lottery_jackpot = $points_values['lottery_jackpot'];
			$current_lottery_base_amount = $points_values['lottery_base_amount'];

			// Values for phpbb_points_config
			$lottery_enable = $this->request->variable('lottery_enable', 0);
			$lottery_multi_ticket_enable = $this->request->variable('lottery_multi_ticket_enable', 0);
			$display_lottery_stats = $this->request->variable('display_lottery_stats', 0);

			// Values for phpbb_points_values
			$lottery_base_amount = round($this->request->variable('lottery_base_amount', 0.00), 2);
			$lottery_draw_period = $this->request->variable('lottery_draw_period', 0) * 3600;
			$lottery_ticket_cost = round($this->request->variable('lottery_ticket_cost', 0.00), 2);
			$lottery_name = $this->request->variable('lottery_name', '', true);
			$lottery_chance = round($this->request->variable('lottery_chance', 0.00), 2);
			$lottery_max_tickets = round($this->request->variable('lottery_max_tickets', 0.00), 2);
			$lottery_pm_from = $this->request->variable('lottery_pm_from', 0);

			// Check entered lottery chance - has to be max 100
			if ($lottery_chance > 100)
			{
				trigger_error($this->user->lang['LOTTERY_CHANCE_ERROR'] . adm_back_link($this->u_action), E_USER_WARNING);
			}

			// If base amount increases, increase jackpot
			if ($lottery_base_amount > $current_lottery_base_amount)
			{
				$this->functions_points->set_points_values('lottery_jackpot', ($current_lottery_jackpot . '+' . $lottery_base_amount . '-' . $current_lottery_base_amount));
				//set_points_values('lottery_base_amount', $lottery_base_amount);
			}

			// Update values in phpbb_points_config
			if ($lottery_enable != $points_config['lottery_enable'])
			{
				$this->functions_points->set_points_config('lottery_enable', $lottery_enable);
			}
			if ($lottery_multi_ticket_enable != $points_config['lottery_multi_ticket_enable'])
			{
				$this->functions_points->set_points_config('lottery_multi_ticket_enable', $lottery_multi_ticket_enable);
			}
			if ($display_lottery_stats != $points_config['display_lottery_stats'])
			{
				$this->functions_points->set_points_config('display_lottery_stats', $display_lottery_stats);
			}

			// Update values in phpbb_config
			$this->config->set('points_icon_lotteryicon', $this->request->variable('points_icon_lotteryicon', '', true));
			$this->config->set('lottery_mchat_enable', $this->request->variable('lottery_mchat_enable', 0));

			// Update values in phpbb_points_values
			$this->functions_points->set_points_values('lottery_base_amount', $lottery_base_amount);

			// Check if 0 is entered. Must be > 0
			if ($lottery_draw_period < 0)
			{
				trigger_error($this->user->lang['LOTTERY_DRAW_PERIOD_SHORT'] . adm_back_link($this->u_action), E_USER_WARNING);
			}
			else
			{
				$this->functions_points->set_points_values('lottery_draw_period', $lottery_draw_period);
			}

			$this->functions_points->set_points_values('lottery_ticket_cost', $lottery_ticket_cost);
			$this->functions_points->set_points_values('lottery_name', ("'" . $this->db->sql_escape($lottery_name) . "'"));
			$this->functions_points->set_points_values('lottery_chance', $lottery_chance);
			$this->functions_points->set_points_values('lottery_max_tickets', $lottery_max_tickets);

			// Check, if the entered user_id really exists
			$sql_array = [
				'SELECT' => 'user_id',
				'FROM' => [
					USERS_TABLE => 'u',
				],
				'WHERE' => 'user_id = ' . (int) $lottery_pm_from,
			];
			$sql = $this->db->sql_build_query('SELECT', $sql_array);
			$result = $this->db->sql_query($sql);
			$id_exist = $this->db->sql_fetchfield('user_id');
			$this->db->sql_freeresult($result);

			if ($lottery_pm_from == 0)
			{
				$this->functions_points->set_points_values('lottery_pm_from', $lottery_pm_from);
			}
			else if (empty($id_exist))
			{
				trigger_error($this->user->lang['NO_USER'] . adm_back_link($this->u_action), E_USER_WARNING);
			}
			else
			{
				$this->functions_points->set_points_values('lottery_pm_from', $lottery_pm_from);
			}

			// Set last draw time to current time, if draw period activated
			if ($points_values['lottery_last_draw_time'] == 0 && $points_values['lottery_draw_period'] != 0)
			{
				$this->functions_points->set_points_values('lottery_last_draw_time', time());
			}

			// Set last draw time to 0, if draw period deactivated
			if ($points_values['lottery_draw_period'] == 0)
			{
				$this->functions_points->set_points_values('lottery_last_draw_time', 0);
			}

			// Add logs
			$this->log->add('admin', $this->user->data['user_id'], $this->user->data['user_ip'], 'LOG_MOD_POINTS_LOTTERY');
			trigger_error($this->user->lang['CONFIG_UPDATED'] . adm_back_link($this->u_action));
		}

		// Delete lottery history
		$reset_lottery_history = (isset($_POST['action_lottery_history'])) ? true : false;

		if ($reset_lottery_history)
		{
			if (confirm_box(true))
			{
				if (!$this->auth->acl_get('a_points'))
				{
					trigger_error($this->user->lang['NO_AUTH_OPERATION'] . adm_back_link($this->u_action), E_USER_WARNING);
				}
				$sql_layer = $this->db->get_sql_layer();

				switch ($sql_layer)
				{
					case 'sqlite':
					case 'firebird':
						$this->db->sql_query('DELETE FROM ' . $this->points_lottery_history_table);
						break;

					default:
						$this->db->sql_query('DELETE FROM ' . $this->points_lottery_history_table);
						break;
				}

				$this->log->add('admin', $this->user->data['user_id'], $this->user->data['user_ip'], 'LOG_RESYNC_LOTTERY_HISTORY');
				trigger_error($this->user->lang['LOG_RESYNC_LOTTERY_HISTORY'] . adm_back_link($this->u_action));
			} // Create a confirmbox with yes and no.
			else
			{
				$s_hidden_fields = build_hidden_fields([
					'action_lottery_history' => true,
				]);

				// Display mode
				confirm_box(false, $this->user->lang['RESYNC_LOTTERY_HISTORY_CONFIRM'], $s_hidden_fields);
			}
		}

		if ($this->phpbb_container->has('dmzx.mchat.settings'))
		{
			$this->template->assign_var('LOTTERY_MCHAT_VIEW', true);
		}

		$this->template->assign_vars([
			'LOTTERY_BASE_AMOUNT' => $points_values['lottery_base_amount'],
			// Convert to hours
			'LOTTERY_DRAW_PERIOD' => ($points_values['lottery_draw_period'] == 0) ? $points_values['lottery_draw_period'] : $points_values['lottery_draw_period'] / 3600,
			'LOTTERY_TICKET_COST' => $points_values['lottery_ticket_cost'],
			'LOTTERY_CASH_NAME' => $this->config['points_name'],
			'LOTTERY_MCHAT_ENABLE' => $this->config['lottery_mchat_enable'],
			'LOTTERY_NAME' => $points_values['lottery_name'],
			'LOTTERY_CHANCE' => $points_values['lottery_chance'],
			'LOTTERY_MAX_TICKETS' => $points_values['lottery_max_tickets'],
			'LOTTERY_PM_FROM' => $points_values['lottery_pm_from'],
			'S_LOTTERY_ENABLE' => ($points_config['lottery_enable']) ? true : false,
			'S_LOTTERY_MULTI_TICKET_ENABLE' => ($points_config['lottery_multi_ticket_enable']) ? true : false,
			'S_DISPLAY_LOTTERY_STATS' => ($points_config['display_lottery_stats']) ? true : false,
			'S_LOTTERY' => true,
			'POINTS_ICON_MAINICON' => $this->config['points_icon_mainicon'],
			'POINTS_ICON_LOTTERYICON' => $this->config['points_icon_lotteryicon'],
			'U_ACTION' => $this->u_action
		]);
	}

	public function display_bank()
	{
		// Get all configs
		$points_config = $this->functions_points->points_all_configs();

		$this->template->assign_vars(array_change_key_case($points_config, CASE_UPPER));

		// Get all values
		$points_values = $this->functions_points->points_all_values();

		// Form key
		add_form_key('acp_points');

		$this->template->assign_vars([
			'BASE' => $this->u_action,
		]);

		if ($this->request->is_set_post('submit'))
		{
			if (!check_form_key('acp_points'))
			{
				trigger_error($this->user->lang['FORM_INVALID'] . adm_back_link($this->u_action), E_USER_WARNING);
			}

			// Values for phpbb_points_config
			$bank_enable = $this->request->variable('bank_enable', 0);

			// Values for phpbb_points_values
			$bank_interest = round($this->request->variable('bank_interest', 0.00), 2);
			$bank_fees = round($this->request->variable('bank_fees', 0.00), 2);
			$bank_pay_period = round($this->request->variable('bank_pay_period', 0.00), 2) * 86400;
			$bank_min_withdraw = round($this->request->variable('bank_min_withdraw', 0.00), 2);
			$bank_min_deposit = round($this->request->variable('bank_min_deposit', 0.00), 2);
			$bank_interestcut = round($this->request->variable('bank_interestcut', 0.00), 2);
			$bank_cost = round($this->request->variable('bank_cost', 0.00), 2);
			$bank_name = $this->request->variable('bank_name', '', true);

			// Check entered bank interesst - has to be max 100
			if ($bank_interest > 100)
			{
				trigger_error($this->user->lang['BANK_INTEREST_ERROR'] . adm_back_link($this->u_action), E_USER_WARNING);
			}

			// Check entered bank fees - has to be max 100
			if ($bank_fees > 100)
			{
				trigger_error($this->user->lang['BANK_FEES_ERROR'] . adm_back_link($this->u_action), E_USER_WARNING);
			}

			// Update values in phpbb_points_config
			if ($bank_enable != $points_config['bank_enable'])
			{
				$this->functions_points->set_points_config('bank_enable', $bank_enable);
			}

			$this->config->set('points_icon_bankicon', $this->request->variable('points_icon_bankicon', '', true));

			// Update values in phpbb_points_values
			$this->functions_points->set_points_values('bank_interest', $bank_interest);
			$this->functions_points->set_points_values('bank_fees', $bank_fees);
			$this->functions_points->set_points_values('bank_pay_period', $bank_pay_period);
			$this->functions_points->set_points_values('bank_min_withdraw', $bank_min_withdraw);
			$this->functions_points->set_points_values('bank_min_deposit', $bank_min_deposit);
			$this->functions_points->set_points_values('bank_interestcut', $bank_interestcut);
			$this->functions_points->set_points_values('bank_cost', $bank_cost);
			$this->functions_points->set_points_values('bank_name', ("'" . $this->db->sql_escape($bank_name) . "'"));

			// Add logs
			$this->log->add('admin', $this->user->data['user_id'], $this->user->data['user_ip'], 'LOG_MOD_POINTS_BANK');

			trigger_error($this->user->lang['CONFIG_UPDATED'] . adm_back_link($this->u_action));
		}

		$this->template->assign_vars([
			// Convert to days
			'BANK_PAY_PERIOD' => ($points_values['bank_pay_period'] == 0) ? $points_values['bank_pay_period'] : $points_values['bank_pay_period'] / 86400,
			'BANK_POINTS_NAME' => $this->config['points_name'],
			'BANK_FEES' => $points_values['bank_fees'],
			'BANK_INTEREST' => $points_values['bank_interest'],
			'BANK_MIN_WITHDRAW' => $points_values['bank_min_withdraw'],
			'BANK_MIN_DEPOSIT' => $points_values['bank_min_deposit'],
			'BANK_INTERESTCUT' => $points_values['bank_interestcut'],
			'BANK_COST' => $points_values['bank_cost'],
			'BANK_NAME' => $points_values['bank_name'],
			'S_BANK_ENABLE' => ($points_config['bank_enable']) ? true : false,
			'S_POINTS_BANK' => true,
			'POINTS_ICON_BANKICON' => $this->config['points_icon_bankicon'],
			'POINTS_ICON_MAINICON' => $this->config['points_icon_mainicon'],
			'U_ACTION' => $this->u_action
		]);
	}

	public function display_robbery()
	{
		// Get all configs
		$points_config = $this->functions_points->points_all_configs();

		$this->template->assign_vars(array_change_key_case($points_config, CASE_UPPER));

		// Get all values
		$points_values = $this->functions_points->points_all_values();

		// Form key
		add_form_key('acp_points');

		$this->template->assign_vars([
			'BASE' => $this->u_action,
		]);

		if ($this->request->is_set_post('submit'))
		{
			if (!check_form_key('acp_points'))
			{
				trigger_error($this->user->lang['FORM_INVALID'] . adm_back_link($this->u_action), E_USER_WARNING);
			}

			// Values for phpbb_points_config
			$robbery_enable = $this->request->variable('robbery_enable', 0);
			$robbery_notify = $this->request->variable('robbery_notify', 0);

			// Values for phpbb_points_values
			$robbery_chance = round($this->request->variable('robbery_chance', 0.00), 2);
			$robbery_loose = round($this->request->variable('robbery_loose', 0.00), 2);
			$robbery_max_rob = round($this->request->variable('robbery_max_rob', 0.00), 2);

			// Check, if entered robbery chance is 0 or below
			if ($robbery_chance <= 0)
			{
				trigger_error($this->user->lang['ROBBERY_CHANCE_MINIMUM'] . adm_back_link($this->u_action), E_USER_WARNING);
			}

			// Check entered robbery chance - has to be max 100
			if ($robbery_chance > 100)
			{
				trigger_error($this->user->lang['ROBBERY_CHANCE_ERROR'] . adm_back_link($this->u_action), E_USER_WARNING);
			}

			// Check, if entered robbery loose is 0 or below
			if ($robbery_loose <= 0)
			{
				trigger_error($this->user->lang['ROBBERY_LOOSE_MINIMUM'] . adm_back_link($this->u_action), E_USER_WARNING);
			}

			// Check entered robbery loose - has to be max 100
			if ($robbery_loose > 100)
			{
				trigger_error($this->user->lang['ROBBERY_LOOSE_ERROR'] . adm_back_link($this->u_action), E_USER_WARNING);
			}

			// Check, if entered robbery is 0 or below
			if ($robbery_max_rob <= 0)
			{
				trigger_error($this->user->lang['ROBBERY_MAX_ROB_MINIMUM'] . adm_back_link($this->u_action), E_USER_WARNING);
			}

			// Check entered robbery max rob value - has to be max 100
			if ($robbery_max_rob > 100)
			{
				trigger_error($this->user->lang['ROBBERY_MAX_ROB_ERROR'] . adm_back_link($this->u_action), E_USER_WARNING);
			}

			// Update values in phpbb_points_config
			if ($robbery_enable != $points_config['robbery_enable'])
			{
				$this->functions_points->set_points_config('robbery_enable', $robbery_enable);
			}
			if ($robbery_notify != $points_config['robbery_notify'])
			{
				$this->functions_points->set_points_config('robbery_notify', $robbery_notify);
			}

			$this->config->set('robbery_mchat_enable', $this->request->variable('robbery_mchat_enable', 0));

			// Update values in phpbb_points_values
			$this->functions_points->set_points_values('robbery_chance', $robbery_chance);
			$this->functions_points->set_points_values('robbery_loose', $robbery_loose);
			$this->functions_points->set_points_values('robbery_max_rob', $robbery_max_rob);

			// Add logs
			$this->log->add('admin', $this->user->data['user_id'], $this->user->data['user_ip'], 'LOG_MOD_POINTS_ROBBERY');
			trigger_error($this->user->lang['CONFIG_UPDATED'] . adm_back_link($this->u_action));
		}

		if ($this->phpbb_container->has('dmzx.mchat.settings'))
		{
			$this->template->assign_var('ROBBERY_MCHAT_VIEW', true);
		}

		$this->template->assign_vars([
			'ROBBERY_CHANCE' => $points_values['robbery_chance'],
			'ROBBERY_LOOSE' => $points_values['robbery_loose'],
			'ROBBERY_MAX_ROB' => $points_values['robbery_max_rob'],
			'S_ROBBERY_ENABLE' => ($points_config['robbery_enable']) ? true : false,
			'S_ROBBERY_NOTIFY' => ($points_config['robbery_notify']) ? true : false,
			'ROBBERY_MCHAT_ENABLE' => $this->config['robbery_mchat_enable'],
			'S_ROBBERY' => true,
			'POINTS_ICON_MAINICON' => $this->config['points_icon_mainicon'],
			'U_ACTION' => $this->u_action
		]);
	}

	public function display_forumpoints()
	{
		// Grab some vars
		$action = $this->request->variable('action', '');
		$mode = $this->request->variable('mode', '');

		// Get all configs
		$points_config = $this->functions_points->points_all_configs();

		$this->template->assign_vars(array_change_key_case($points_config, CASE_UPPER));

		// Get all values
		$points_values = $this->functions_points->points_all_values();

		// Form key
		add_form_key('acp_points');

		$this->template->assign_vars([
			'BASE' => $this->u_action,
		]);

		$set_point_values = $this->request->variable('action_point_values', '');

		// Update forum points values
		if ($set_point_values)
		{
			if (confirm_box(true))
			{
				$forum_topic = round($this->request->variable('forum_topic', 0.00), 2);
				$forum_post = round($this->request->variable('forum_post', 0.00), 2);
				$forum_edit = round($this->request->variable('forum_edit', 0.00), 2);
				$forum_cost = round($this->request->variable('forum_cost', 0.00), 2);
				$forum_cost_t = round($this->request->variable('forum_cost_topic', 0.00), 2);
				$forum_cost_p = round($this->request->variable('forum_cost_post', 0.00), 2);

				// Update values in phpbb_points_values
				$this->functions_points->set_points_values('forum_topic', $forum_topic);
				$this->functions_points->set_points_values('forum_post', $forum_post);
				$this->functions_points->set_points_values('forum_edit', $forum_edit);
				$this->functions_points->set_points_values('forum_cost', $forum_cost);
				$this->functions_points->set_points_values('forum_cost_topic', $forum_cost_t);
				$this->functions_points->set_points_values('forum_cost_post', $forum_cost_p);

				// Update all forum points
				$data = [
					'forum_pertopic' => $forum_topic,
					'forum_perpost' => $forum_post,
					'forum_peredit' => $forum_edit,
					'forum_cost' => $forum_cost,
					'forum_cost_topic' => $forum_cost_t,
					'forum_cost_post' => $forum_cost_p
				];

				$sql = 'UPDATE ' . FORUMS_TABLE . ' SET ' . $this->db->sql_build_array('UPDATE', $data);
				$this->db->sql_query($sql);

				// Add logs
				$this->log->add('admin', $this->user->data['user_id'], $this->user->data['user_ip'], 'LOG_MOD_POINTS_FORUM');

				trigger_error($this->user->lang['FORUM_POINT_SETTINGS_UPDATED'] . adm_back_link($this->u_action));
			}
			else
			{
				$s_hidden_fields = build_hidden_fields([
					'forum_topic' => $this->request->variable('forum_topic', 0.00),
					'forum_post' => $this->request->variable('forum_post', 0.00),
					'forum_edit' => $this->request->variable('forum_edit', 0.00),
					'forum_cost' => $this->request->variable('forum_cost', 0.00),
					'forum_cost_topic' => $this->request->variable('forum_cost_topic', 0.00),
					'forum_cost_post' => $this->request->variable('forum_cost_post', 0.00),
					'mode' => $mode,
					'action' => $action,
					'action_point_values' => true,
				]);
				confirm_box(false, 'FORUM_POINT_UPDATE', $s_hidden_fields);
			}
		}

		$this->template->assign_vars([
			'FORUM_POINTS_NAME' => $this->config['points_name'],
			'FORUM_TOPIC' => $points_values['forum_topic'],
			'FORUM_POST' => $points_values['forum_post'],
			'FORUM_EDIT' => $points_values['forum_edit'],
			'FORUM_COST' => $points_values['forum_cost'],
			'FORUM_COST_TOPIC' => $points_values['forum_cost_topic'],
			'FORUM_COST_POST' => $points_values['forum_cost_post'],
			'S_FORUMPOINTS' => true,
			'POINTS_ICON_MAINICON' => $this->config['points_icon_mainicon'],
			'U_ACTION' => $this->u_action
		]);
	}

	/**
	 * Set page url
	 *
	 * @param string $u_action Custom form action
	 * @return null
	 * @access public
	 */
	public function set_page_url($u_action)
	{
		$this->u_action = $u_action;
	}
}
