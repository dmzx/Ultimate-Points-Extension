<?php
/**
 *
 * @package phpBB Extension - Ultimate Points
 * @copyright (c) 2016 dmzx & posey - https://www.dmzx-web.net
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace dmzx\ultimatepoints\ucp;

class ucp_ultimatepoints_module
{
	public $u_action;

	function main($id, $mode)
	{
		global $phpbb_container, $config, $template;

		$this->points_lottery_history_table = $phpbb_container->getParameter('dmzx.ultimatepoints.table.points.lottery.history');
		$this->points_bank_table = $phpbb_container->getParameter('dmzx.ultimatepoints.table.points.bank');
		$this->points_log_table = $phpbb_container->getParameter('dmzx.ultimatepoints.table.points.log');
		$this->points_config_table = $phpbb_container->getParameter('dmzx.ultimatepoints.table.points.config');
		$this->points_values_table = $phpbb_container->getParameter('dmzx.ultimatepoints.table.points.values');
		$this->functions_points = $phpbb_container->get('dmzx.ultimatepoints.core.functions.points');

		$points_config = $this->config_info();

		$this->functions_points->assign_authors();

		if ($config['points_enable'])
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
			}
		} else
		{
			trigger_error($points_config['points_disablemsg']);
		}

		$template->assign_var('ULTIMATEPOINTS_FOOTER_VIEW', true);
	}

	public function lottery_info()
	{
		global $db, $user, $template;

		$points_values = $this->values_info();
		$points_config = $this->config_info();

		$sql = 'SELECT *
			FROM ' . $this->points_lottery_history_table . ' p
				LEFT JOIN ' . USERS_TABLE . ' u
				ON p.user_name = u.username
			WHERE p.user_id = ' . (int) $user->data['user_id'] . '
			ORDER BY p.amount DESC';
		$result = $db->sql_query($sql);

		while ($row = $db->sql_fetchrow())
		{
			$template->assign_block_vars('ucp_ultimatepoints_lottery', [
				'LOTTERY_USERNAME' => get_username_string('full', $row['user_id'], $row['user_name'], $row['user_colour']),
				'LOTTERY_AMOUNT' => $row['amount'],
				'LOTTERY_TIME' => $user->format_date($row['time']),
			]);
		}
		$db->sql_freeresult($result);

		$this->tpl_name = 'points/ucp_ultimatepoints';
		$this->page_title = $user->lang['UCP_ULTIMATEPOINTS_TITLE'];

		$template->assign_vars([
			'S_LOTTERY_INFO' => true,
			'LOTTERY_NAME' => $points_values['lottery_name'],
			'S_LOTTERY_ENABLE' => $points_config['lottery_enable'],
		]);
	}

	public function bank_info()
	{
		global $db, $user, $template;

		$points_values = $this->values_info();
		$points_config = $this->config_info();

		$sql = 'SELECT *
			FROM ' . $this->points_bank_table . ' b
				LEFT JOIN ' . USERS_TABLE . ' u
				ON b.user_id = u.user_id
			WHERE b.user_id = ' . (int) $user->data['user_id'] . '
			ORDER BY b.holding DESC';
		$result = $db->sql_query($sql);

		while ($row = $db->sql_fetchrow())
		{
			$template->assign_block_vars('ucp_ultimatepoints_bank', [
				'BANK_USERNAME' => get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
				'BANK_AMOUNT' => $row['holding'],
				'BANK_TIME' => $user->format_date($row['opentime']),
			]);
		}
		$db->sql_freeresult($result);

		$this->tpl_name = 'points/ucp_ultimatepoints';
		$this->page_title = $user->lang['UCP_ULTIMATEPOINTS_TITLE'];

		$template->assign_vars([
			'S_BANK_INFO' => true,
			'BANK_NAME' => $points_values['bank_name'],
			'BANK_BALANCE' => sprintf($user->lang['BANK_INFO'], $points_values['bank_name']),
			'BANK_ACCOUNT_OPENED' => sprintf($user->lang['BANK_ACCOUNT_OPENED'], $points_values['bank_name']),
			'BANK_TO_ACCOUNT' => sprintf($user->lang['BANK_TO_ACCOUNT'], $points_values['bank_name']),
			'S_BANK_ENABLE' => $points_config['bank_enable'],
		]);
	}

	public function robbery_info()
	{
		global $db, $user, $template;

		$points_config = $this->config_info();

		$sql = 'SELECT *
			FROM ' . $this->points_log_table . ' l
				LEFT JOIN ' . USERS_TABLE . ' u
				ON l.point_send = u.user_id
			WHERE l.point_recv = ' . (int) $user->data['user_id'] . '
			AND l.point_type = 3
			ORDER BY l.point_date DESC';
		$result = $db->sql_query($sql);

		while ($row = $db->sql_fetchrow())
		{
			$template->assign_block_vars('ucp_ultimatepoints_robbery', [
				'ROBBERY_USERNAME' => get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
				'ROBBERY_AMOUNT' => $row['point_amount'],
				'ROBBERY_TIME' => $user->format_date($row['point_date']),
			]);
		}
		$db->sql_freeresult($result);

		$this->tpl_name = 'points/ucp_ultimatepoints';
		$this->page_title = $user->lang['UCP_ULTIMATEPOINTS_TITLE'];

		$template->assign_vars([
			'S_ROBBERY_INFO' => true,
			'S_ROBBERY_ENABLE' => $points_config['robbery_enable'],
		]);
	}

	public function transfer_info()
	{
		global $db, $user, $template, $config;

		$points_config = $this->config_info();

		$sql = 'SELECT *
			FROM ' . $this->points_log_table . ' l
				LEFT JOIN ' . USERS_TABLE . ' u
				ON l.point_send = u.user_id
			WHERE l.point_recv = ' . (int) $user->data['user_id'] . '
			AND l.point_type = 1
			ORDER BY l.point_date DESC';
		$result = $db->sql_query($sql);

		while ($row = $db->sql_fetchrow())
		{
			$template->assign_block_vars('ucp_ultimatepoints_transfer', [
				'TRANSFER_USERNAME' => get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
				'TRANSFER_AMOUNT' => $row['point_amount'],
				'TRANSFER_TIME' => $user->format_date($row['point_date']),
			]);
		}
		$db->sql_freeresult($result);

		$this->tpl_name = 'points/ucp_ultimatepoints';
		$this->page_title = $user->lang['UCP_ULTIMATEPOINTS_TITLE'];

		$template->assign_vars([
			'S_TRANSFER_INFO' => true,
			'S_TRANSFER_ENABLE' => $points_config['transfer_enable'],
			'L_TRANSFER_RECEIVED' => sprintf($user->lang['TRANSFER_RECEIVED'], $config['points_name'])
		]);
	}

	public function config_info()
	{
		global $db;

		// Read out config data
		$sql_array = [
			'SELECT' => 'config_name, config_value',
			'FROM' => [
				$this->points_config_table => 'c',
			],
		];
		$sql = $db->sql_build_query('SELECT', $sql_array);
		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			$points_config[$row['config_name']] = $row['config_value'];
		}
		$db->sql_freeresult($result);

		return $points_config;
	}

	public function values_info()
	{
		global $db;

		// Read out config values
		$sql = 'SELECT *
			FROM ' . $this->points_values_table;
		$result = $db->sql_query($sql);
		$points_values = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		return $points_values;
	}
}
