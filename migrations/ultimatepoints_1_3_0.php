<?php
/**
 *
 * @package phpBB Extension - Ultimate Points
 * @copyright (c) 2026 dmzx https://www.dmzx-web.net
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace dmzx\ultimatepoints\migrations;

use phpbb\db\migration\migration;

class ultimatepoints_1_3_0 extends migration
{
	static public function depends_on()
	{
		return [
			'\dmzx\ultimatepoints\migrations\ultimatepoints_1_2_9',
		];
	}

	public function update_data()
	{
		return [
			['config.update', ['ultimate_points_version', '1.3.0']],

			['permission.add', ['u_use_duel', true]],
			['permission.permission_set', ['REGISTERED', 'u_use_duel', 'group']],
			['permission.add', ['m_resolve_duel', true]],

			['config.add', ['duel_mchat_enable', 0]],

			['module.add', [
				'acp',
				'ACP_POINTS',
				[
					'module_basename' => '\dmzx\ultimatepoints\acp\acp_ultimatepoints_module',
					'modes' => ['duels'],
				],
			]],

			['module.add', [
				'ucp',
				'UCP_ULTIMATEPOINTS_TITLE',
				[
					'module_basename' => '\dmzx\ultimatepoints\ucp\ucp_ultimatepoints_module',
					'auth' => 'ext_dmzx/ultimatepoints',
					'modes' => ['duel'],
				],
			]],

			['custom', [
				[&$this, 'insert_duel_config_data'],
			]],
		];
	}

	public function update_schema()
	{
		return [
			'add_tables' => [
				$this->table_prefix . 'points_duels' => [
					'COLUMNS' => [
						'duel_id' => ['UINT:11', null, 'auto_increment'],
						'challenger_id' => ['UINT:11', 0],
						'opponent_id' => ['UINT:11', 0],
						'wager' => ['DECIMAL:20', 0.00],
						'resolution_type' => ['TINT:2', 0],
						'status' => ['TINT:2', 0],
						'winner_id' => ['UINT:11', 0],
						'created_time' => ['UINT:11', 0],
						'accepted_time' => ['UINT:11', 0],
						'resolved_time' => ['UINT:11', 0],
					],
					'PRIMARY_KEY' => 'duel_id',
				],
			],
			'add_columns' => [
				$this->table_prefix . 'points_values' => [
					'duel_min_wager' => ['DECIMAL:10', 10.00],
					'duel_max_wager' => ['DECIMAL:10', 500.00],
					'duel_accept_time_limit' => ['UINT:10', 24],
				],
			],
		];
	}

	public function revert_schema()
	{
		return [
			'drop_tables' => [
				$this->table_prefix . 'points_duels',
			],
			'drop_columns' => [
				$this->table_prefix . 'points_values' => [
					'duel_min_wager',
					'duel_max_wager',
					'duel_accept_time_limit',
				],
			],
		];
	}

	public function insert_duel_config_data()
	{
		if ($this->db_tools->sql_table_exists($this->table_prefix . 'points_config'))
		{
			$config_data = [
				[
					'config_name' => 'duel_enable',
					'config_value' => '1',
				],
				[
					'config_name' => 'duel_default_admin_resolve',
					'config_value' => '0',
				],
				[
					'config_name' => 'duel_max_open_per_user',
					'config_value' => '3',
				],
			];

			$this->db->sql_multi_insert($this->table_prefix . 'points_config', $config_data);
		}
	}
}
