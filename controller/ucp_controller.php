<?php
/**
 *
 * @package phpBB Extension - Ultimate Points
 * @copyright (c) 2026 dmzx https://www.dmzx-web.net
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace dmzx\ultimatepoints\controller;

use dmzx\ultimatepoints\core\functions_points;
use phpbb\config\config;
use phpbb\db\driver\driver_interface;
use phpbb\language\language;
use phpbb\template\template;
use phpbb\user;

class ucp_controller
{
	/** @var functions_points */
	protected $functions_points;

	/** @var driver_interface */
	protected $db;

	/** @var user */
	protected $user;

	/** @var language */
	protected $language;

	/** @var template */
	protected $template;

	/** @var config */
	protected $config;

	/**
	 * The database tables
	 *
	 * @var string
	 */
	protected $points_lottery_history_table;

	protected $points_bank_table;

	protected $points_log_table;

	protected $points_config_table;

	protected $points_values_table;

	protected $points_bounty_table;

	protected $points_duels_table;

	/**
	 * Constructor
	 *
	 * @param functions_points $functions_points
	 * @param driver_interface $db
	 * @param user $user
	 * @param language $language
	 * @param template $template
	 * @param config $config
	 * @param string $points_lottery_history_table
	 * @param string $points_bank_table
	 * @param string $points_log_table
	 * @param string $points_config_table
	 * @param string $points_values_table
	 * @param string $points_bounty_table
	 * @param string $points_duels_table
	 *
	 */
	public function __construct(
		functions_points $functions_points,
		driver_interface $db,
		user $user,
		language $language,
		template $template,
		config $config,
		$points_lottery_history_table,
		$points_bank_table,
		$points_log_table,
		$points_config_table,
		$points_values_table,
		$points_bounty_table,
		$points_duels_table
	)
	{
		$this->functions_points = $functions_points;
		$this->db = $db;
		$this->user = $user;
		$this->language = $language;
		$this->template = $template;
		$this->config = $config;
		$this->points_lottery_history_table = $points_lottery_history_table;
		$this->points_bank_table = $points_bank_table;
		$this->points_log_table = $points_log_table;
		$this->points_config_table = $points_config_table;
		$this->points_values_table = $points_values_table;
		$this->points_bounty_table = $points_bounty_table;
		$this->points_duels_table = $points_duels_table;
	}

	public function main($mode)
	{
		$points_config = $this->config_info();

		$this->functions_points->assign_authors();

		if ($this->config['points_enable'])
		{
			switch ($mode)
			{
				case 'lottery':
					$this->lottery_info();
					break;

				case 'bank':
					$this->bank_info();
					break;

				case 'robbery':
					$this->robbery_info();
					break;

				case 'transfer':
					$this->transfer_info();
					break;

				case 'bounty':
					$this->bounty_info();
					break;

				case 'duel':
					$this->duel_info();
					break;
			}
		} else
		{
			trigger_error($points_config['points_disablemsg']);
		}

		$this->template->assign_var('ULTIMATEPOINTS_FOOTER_VIEW', true);
	}

	protected function lottery_info()
	{
		$points_values = $this->values_info();
		$points_config = $this->config_info();

		$sql = 'SELECT *
			FROM ' . $this->points_lottery_history_table . ' p
				LEFT JOIN ' . USERS_TABLE . ' u
				ON p.user_name = u.username
			WHERE p.user_id = ' . (int) $this->user->data['user_id'] . '
			ORDER BY p.amount DESC';
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->template->assign_block_vars('ucp_ultimatepoints_lottery', [
				'LOTTERY_USERNAME' => get_username_string('full', $row['user_id'], $row['user_name'], $row['user_colour']),
				'LOTTERY_AMOUNT' => $row['amount'],
				'LOTTERY_TIME' => $this->user->format_date($row['time']),
			]);
		}
		$this->db->sql_freeresult($result);

		$this->template->assign_vars([
			'S_LOTTERY_INFO' => true,
			'LOTTERY_NAME' => $points_values['lottery_name'],
			'S_LOTTERY_ENABLE' => $points_config['lottery_enable'],
		]);
	}

	protected function bank_info()
	{
		$points_values = $this->values_info();
		$points_config = $this->config_info();

		$sql = 'SELECT *
			FROM ' . $this->points_bank_table . ' b
				LEFT JOIN ' . USERS_TABLE . ' u
				ON b.user_id = u.user_id
			WHERE b.user_id = ' . (int) $this->user->data['user_id'] . '
			ORDER BY b.holding DESC';
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->template->assign_block_vars('ucp_ultimatepoints_bank', [
				'BANK_USERNAME' => get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
				'BANK_AMOUNT' => $row['holding'],
				'BANK_TIME' => $this->user->format_date($row['opentime']),
			]);
		}
		$this->db->sql_freeresult($result);

		$this->template->assign_vars([
			'S_BANK_INFO' => true,
			'BANK_NAME' => $points_values['bank_name'],
			'BANK_BALANCE' => sprintf($this->language->lang('BANK_INFO'), $points_values['bank_name']),
			'BANK_ACCOUNT_OPENED' => sprintf($this->language->lang('BANK_ACCOUNT_OPENED'), $points_values['bank_name']),
			'BANK_TO_ACCOUNT' => sprintf($this->language->lang('BANK_TO_ACCOUNT'), $points_values['bank_name']),
			'S_BANK_ENABLE' => $points_config['bank_enable'],
		]);
	}

	protected function robbery_info()
	{
		$points_config = $this->config_info();

		$sql = 'SELECT *
			FROM ' . $this->points_log_table . ' l
				LEFT JOIN ' . USERS_TABLE . ' u
				ON l.point_send = u.user_id
			WHERE l.point_recv = ' . (int) $this->user->data['user_id'] . '
			AND l.point_type = 3
			ORDER BY l.point_date DESC';
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->template->assign_block_vars('ucp_ultimatepoints_robbery', [
				'ROBBERY_USERNAME' => get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
				'ROBBERY_AMOUNT' => $row['point_amount'],
				'ROBBERY_TIME' => $this->user->format_date($row['point_date']),
			]);
		}
		$this->db->sql_freeresult($result);

		$this->template->assign_vars([
			'S_ROBBERY_INFO' => true,
			'S_ROBBERY_ENABLE' => $points_config['robbery_enable'],
		]);
	}

	protected function transfer_info()
	{
		$points_config = $this->config_info();

		$sql = 'SELECT *
			FROM ' . $this->points_log_table . ' l
				LEFT JOIN ' . USERS_TABLE . ' u
				ON l.point_send = u.user_id
			WHERE l.point_recv = ' . (int) $this->user->data['user_id'] . '
			AND l.point_type = 1
			ORDER BY l.point_date DESC';
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->template->assign_block_vars('ucp_ultimatepoints_transfer', [
				'TRANSFER_USERNAME' => get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
				'TRANSFER_AMOUNT' => $row['point_amount'],
				'TRANSFER_TIME' => $this->user->format_date($row['point_date']),
			]);
		}
		$this->db->sql_freeresult($result);

		$this->template->assign_vars([
			'S_TRANSFER_INFO' => true,
			'S_TRANSFER_ENABLE' => $points_config['transfer_enable'],
			'L_TRANSFER_RECEIVED' => sprintf($this->language->lang('TRANSFER_RECEIVED'), $this->config['points_name'])
		]);
	}

	protected function bounty_info()
	{
		$points_config = $this->config_info();

		$status_labels = [
			0 => $this->language->lang('BOUNTY_STATUS_OPEN'),
			1 => $this->language->lang('BOUNTY_STATUS_CLAIMED'),
			2 => $this->language->lang('BOUNTY_STATUS_PENDING'),
			3 => $this->language->lang('BOUNTY_STATUS_COMPLETED'),
		];

		$sql = 'SELECT *
			FROM ' . $this->points_bounty_table . '
			WHERE poster_id = ' . (int) $this->user->data['user_id'] . '
				OR claimer_id = ' . (int) $this->user->data['user_id'] . '
			ORDER BY created_time DESC';
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$is_poster = (int) $row['poster_id'] === (int) $this->user->data['user_id'];

			$this->template->assign_block_vars('ucp_ultimatepoints_bounty', [
				'BOUNTY_TITLE' => $row['title'],
				'BOUNTY_AMOUNT' => $row['reward'],
				'BOUNTY_STATUS' => isset($status_labels[(int) $row['status']]) ? $status_labels[(int) $row['status']] : '',
				'BOUNTY_ROLE' => $is_poster ? $this->language->lang('BOUNTY_POSTED_BY_YOU') : $this->language->lang('BOUNTY_CLAIMED_BY_YOU'),
				'BOUNTY_TIME' => $this->user->format_date($row['created_time']),
			]);
		}
		$this->db->sql_freeresult($result);

		$this->template->assign_vars([
			'S_BOUNTY_INFO' => true,
			'S_BOUNTY_ENABLE' => $points_config['bounty_enable'],
		]);
	}

	protected function duel_info()
	{
		$points_config = $this->config_info();

		$status_labels = [
			0 => $this->language->lang('DUEL_STATUS_PENDING'),
			1 => $this->language->lang('DUEL_STATUS_ACTIVE'),
			2 => $this->language->lang('DUEL_STATUS_COMPLETED'),
			3 => $this->language->lang('DUEL_STATUS_COMPLETED'),
			4 => $this->language->lang('DUEL_STATUS_COMPLETED'),
			5 => $this->language->lang('DUEL_STATUS_COMPLETED'),
		];

		$sql = 'SELECT *
			FROM ' . $this->points_duels_table . '
			WHERE challenger_id = ' . (int) $this->user->data['user_id'] . '
				OR opponent_id = ' . (int) $this->user->data['user_id'] . '
			ORDER BY created_time DESC';
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$is_challenger = (int) $row['challenger_id'] === (int) $this->user->data['user_id'];
			$status = (int) $row['status'];

			if ($status === 2 && (int) $row['winner_id'] !== 0)
			{
				$won = (int) $row['winner_id'] === (int) $this->user->data['user_id'];
				$result_label = $won ? sprintf($this->language->lang('DUEL_RESULT_WON'), $row['wager']) : sprintf($this->language->lang('DUEL_RESULT_LOST'), $row['wager']);
			}
			else if ($status >= 3)
			{
				$result_label = $this->language->lang('DUEL_RESULT_REFUNDED');
			}
			else
			{
				$result_label = $status_labels[$status] ?? '';
			}

			$this->template->assign_block_vars('ucp_ultimatepoints_duel', [
				'DUEL_AMOUNT' => $row['wager'],
				'DUEL_STATUS' => $status_labels[$status] ?? '',
				'DUEL_RESULT' => $result_label,
				'DUEL_ROLE' => $is_challenger ? $this->language->lang('DUEL_ROLE_CHALLENGER_YOU') : $this->language->lang('DUEL_ROLE_OPPONENT_YOU'),
				'DUEL_TIME' => $this->user->format_date($row['created_time']),
			]);
		}
		$this->db->sql_freeresult($result);

		$this->template->assign_vars([
			'S_DUEL_INFO' => true,
			'S_DUEL_ENABLE' => $points_config['duel_enable'],
		]);
	}

	protected function config_info()
	{
		// Read out config data
		$sql_array = [
			'SELECT' => 'config_name, config_value',
			'FROM' => [
				$this->points_config_table => 'c',
			],
		];
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$points_config = [];
		while ($row = $this->db->sql_fetchrow($result))
		{
			$points_config[$row['config_name']] = $row['config_value'];
		}
		$this->db->sql_freeresult($result);

		return $points_config;
	}

	protected function values_info()
	{
		// Read out config values
		$sql = 'SELECT *
			FROM ' . $this->points_values_table;
		$result = $this->db->sql_query($sql);
		$points_values = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		return $points_values;
	}
}
