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

class ultimatepoints_1_2_9 extends migration
{
	static public function depends_on()
	{
		return [
			'\dmzx\ultimatepoints\migrations\ultimatepoints_1_2_8',
		];
	}

	public function update_data()
	{
		return [
			['config.update', ['ultimate_points_version', '1.2.9']],

			['permission.add', ['u_use_bounty', true]],
			['permission.permission_set', ['REGISTERED', 'u_use_bounty', 'group']],

			['config.add', ['bounty_mchat_enable', 0]],

			['module.add', [
				'acp',
				'ACP_POINTS',
				[
					'module_basename' => '\dmzx\ultimatepoints\acp\acp_ultimatepoints_module',
					'modes' => ['bounty'],
				],
			]],

			['module.add', [
				'ucp',
				'UCP_ULTIMATEPOINTS_TITLE',
				[
					'module_basename' => '\dmzx\ultimatepoints\ucp\ucp_ultimatepoints_module',
					'auth' => 'ext_dmzx/ultimatepoints',
					'modes' => ['bounty'],
				],
			]],

			['custom', [
				[&$this, 'insert_bounty_config_data'],
			]],
		];
	}

	public function update_schema()
	{
		return [
			'add_tables' => [
				$this->table_prefix . 'points_bounties' => [
					'COLUMNS' => [
						'bounty_id' => ['UINT:11', null, 'auto_increment'],
						'poster_id' => ['UINT:11', 0],
						'claimer_id' => ['UINT:11', 0],
						'title' => ['VCHAR', ''],
						'description' => ['MTEXT_UNI', ''],
						'reward' => ['DECIMAL:20', 0.00],
						'status' => ['TINT:2', 0],
						'created_time' => ['UINT:11', 0],
						'claimed_time' => ['UINT:11', 0],
						'submitted_time' => ['UINT:11', 0],
						'paid_time' => ['UINT:11', 0],
					],
					'PRIMARY_KEY' => 'bounty_id',
				],
			],
			'add_columns' => [
				$this->table_prefix . 'points_values' => [
					'bounty_min_reward' => ['DECIMAL:10', 10.00],
					'bounty_max_reward' => ['DECIMAL:10', 1000.00],
					'bounty_claim_time_limit' => ['UINT:10', 72],
				],
			],
		];
	}

	public function revert_schema()
	{
		return [
			'drop_tables' => [
				$this->table_prefix . 'points_bounties',
			],
			'drop_columns' => [
				$this->table_prefix . 'points_values' => [
					'bounty_min_reward',
					'bounty_max_reward',
					'bounty_claim_time_limit',
				],
			],
		];
	}

	public function insert_bounty_config_data()
	{
		if ($this->db_tools->sql_table_exists($this->table_prefix . 'points_config'))
		{
			$config_data = [
				[
					'config_name' => 'bounty_enable',
					'config_value' => '1',
				],
				[
					'config_name' => 'bounty_require_approval',
					'config_value' => '1',
				],
			];

			$this->db->sql_multi_insert($this->table_prefix . 'points_config', $config_data);
		}
	}
}
