<?php
/**
*
* @package phpBB Extension - Ultimate Points
* @copyright (c) 2016 dmzx & posey - https://www.dmzx-web.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace dmzx\ultimatepoints\migrations;

class ultimatepoints_install extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v310\gold');
	}

	public function update_data()
	{
		return array(
			// Add config
			array('config.add', array('ultimate_points_version', '1.1.6')),
			array('config.add', array('points_notification_id', 0)),
			// Add permission
			array('permission.add', array('u_use_points', true)),
			array('permission.add', array('u_use_bank', true)),
			array('permission.add', array('u_use_logs', true)),
			array('permission.add', array('u_use_robbery', true)),
			array('permission.add', array('u_use_lottery', true)),
			array('permission.add', array('u_use_transfer', true)),
			array('permission.add', array('f_pay_attachment', false)),
			array('permission.add', array('f_pay_topic', false)),
			array('permission.add', array('f_pay_post', false)),
			array('permission.add', array('m_chg_points', true)),
			array('permission.add', array('m_chg_bank', true)),
			array('permission.add', array('a_points', true)),
			// Set permission
			array('permission.permission_set', array('REGISTERED', 'u_use_points', 'group')),
			array('permission.permission_set', array('REGISTERED', 'u_use_bank', 'group')),
			array('permission.permission_set', array('REGISTERED', 'u_use_logs', 'group')),
			array('permission.permission_set', array('REGISTERED', 'u_use_robbery', 'group')),
			array('permission.permission_set', array('REGISTERED', 'u_use_lottery', 'group')),
			array('permission.permission_set', array('REGISTERED', 'u_use_transfer', 'group')),

			// ACP module
			array('module.add', array(
				'acp',
				'ACP_CAT_DOT_MODS',
				'ACP_POINTS'
			)),
			array('module.add', array(
				'acp',
				'ACP_POINTS',
				array(
					'module_basename'	=> '\dmzx\ultimatepoints\acp\acp_ultimatepoints_module',
					'modes' 			=> array('points', 'forumpoints', 'bank', 'lottery', 'robbery'),
				),
			)),

			// UCP module
			array('module.add', array(
				'ucp',
				0,
				'UCP_ULTIMATEPOINTS_TITLE'
			)),
			array('module.add', array(
				'ucp',
				'UCP_ULTIMATEPOINTS_TITLE',
				array(
					'module_basename'	=> '\dmzx\ultimatepoints\ucp\ucp_ultimatepoints_module',
					'auth'				=> 'ext_dmzx/ultimatepoints',
					'modes'				=> array('lottery', 'bank', 'robbery', 'transfer'),
				),
			)),
			// Insert sample data
			array('custom', array(
				array(&$this, 'insert_sample_data')
			)),
			// Insert config data
			array('custom', array(
				array(&$this, 'insert_config_data')
			)),
		);
	}

	public function update_schema()
	{
		return array(
			'add_tables'	=> array(
				$this->table_prefix . 'points_bank'	=> array(
					'COLUMNS'	=> array(
						'id'			=> array('UINT:10', null, 'auto_increment'),
						'user_id'		=> array('UINT:10', 0),
						'holding'		=> array('DECIMAL:20', 0.00),
						'totalwithdrew'	=> array('DECIMAL:20', 0.00),
						'totaldeposit'	=> array('DECIMAL:20', 0.00),
						'opentime'		=> array('UINT:10', 0),
						'fees'			=> array('CHAR:5', 'on'),
					),
					'PRIMARY_KEY'	=> 'id',
				),

				$this->table_prefix . 'points_config'	=> array(
					'COLUMNS'	=> array(
						'config_name'		=> array('VCHAR', ''),
						'config_value'		=> array('VCHAR_UNI', ''),
					),
					'PRIMARY_KEY'	=> 'config_name',
				),

				$this->table_prefix . 'points_log'	=> array(
					'COLUMNS'	=> array(
						'id'			=> array('UINT:11', null, 'auto_increment'),
						'point_send'	=> array('UINT:11', null, ''),
						'point_recv'	=> array('UINT:11', null, ''),
						'point_amount'	=> array('DECIMAL:20', 0.00),
						'point_sendold'	=> array('DECIMAL:20', 0.00),
						'point_recvold'	=> array('DECIMAL:20', 0.00),
						'point_comment'	=> array('MTEXT_UNI', ''),
						'point_type'	=> array('UINT:11', null, ''),
						'point_date'	=> array('UINT:11', null, ''),
					),
					'PRIMARY_KEY'	=> 'id',
				),

				$this->table_prefix . 'points_lottery_history'	=> array(
					'COLUMNS'	=> array(
						'id'		=> array('UINT:11', null, 'auto_increment'),
						'user_id'	=> array('UINT', 0),
						'user_name'	=> array('VCHAR', ''),
						'time'		=> array('UINT:11', 0),
						'amount'	=> array('DECIMAL:20', 0.00),
					),
					'PRIMARY_KEY'	=> 'id',
				),

				$this->table_prefix . 'points_lottery_tickets'	=> array(
					'COLUMNS'	=> array(
						'ticket_id'	=> array('UINT:11', null, 'auto_increment'),
						'user_id'	=> array('UINT:11', 0),
					),
					'PRIMARY_KEY'	=> 'ticket_id',
				),

				$this->table_prefix . 'points_values'	=> array(
					'COLUMNS'	=> array(
						'bank_cost'						=> array('DECIMAL:10', 0.00),
						'bank_fees'						=> array('DECIMAL:10', 0.00),
						'bank_interest'					=> array('DECIMAL:10', 0.00),
						'bank_interestcut'				=> array('DECIMAL:20', 0.00),
						'bank_last_restocked'			=> array('UINT:11', null),
						'bank_min_deposit'				=> array('DECIMAL:10', 0.00),
						'bank_min_withdraw'				=> array('DECIMAL:10', 0.00),
						'bank_name'						=> array('VCHAR:100', null),
						'bank_pay_period'				=> array('UINT:10', 2592000),
						'lottery_base_amount'			=> array('DECIMAL:10', 0.00),
						'lottery_chance'				=> array('DECIMAL', 50.00),
						'lottery_draw_period'			=> array('UINT:10', 3600),
						'lottery_jackpot'				=> array('DECIMAL:20', 50.00),
						'lottery_last_draw_time'		=> array('UINT:11', null),
						'lottery_max_tickets'			=> array('UINT:10', 10),
						'lottery_name'					=> array('VCHAR:100', ''),
						'lottery_prev_winner'			=> array('VCHAR', ''),
						'lottery_prev_winner_id'		=> array('UINT:10', 0),
						'lottery_ticket_cost'			=> array('DECIMAL:10', 0.00),
						'lottery_winners_total'			=> array('UINT', 0),
						'number_show_per_page'			=> array('UINT:10', 0),
						'number_show_top_points'		=> array('UINT', 0),
						'points_bonus_chance'			=> array('DECIMAL:10', 0.00),
						'points_bonus_min'				=> array('DECIMAL:10', 0.00),
						'points_bonus_max'				=> array('DECIMAL:10', 0.00),
						'points_per_attach'				=> array('DECIMAL:10', 0.00),
						'points_per_attach_file'		=> array('DECIMAL:10', 0.00),
						'points_per_poll'				=> array('DECIMAL:10', 0.00),
						'points_per_poll_option'		=> array('DECIMAL:10', 0.00),
						'points_per_post_character'		=> array('DECIMAL:10', 0.00),
						'points_per_post_word'			=> array('DECIMAL:10', 0.00),
						'points_per_topic_character'	=> array('DECIMAL:10', 0.00),
						'points_per_topic_word'			=> array('DECIMAL:10', 0.00),
						'points_per_warn'				=> array('DECIMAL:10', 0.00),
						'reg_points_bonus'				=> array('DECIMAL:10', 0.00),
						'robbery_chance'				=> array('DECIMAL:5', 0.00),
						'robbery_loose'					=> array('DECIMAL:5', 0.00),
						'robbery_max_rob'				=> array('DECIMAL:5', 10.00),
						'transfer_fee'					=> array('UINT:10', 0),
						'lottery_pm_from'				=> array('UINT:10', 0),
						'forum_topic'					=> array('DECIMAL:10', 0.00),
						'forum_post'					=> array('DECIMAL:10', 0.00),
						'forum_edit'					=> array('DECIMAL:10', 0.00),
						'forum_cost'					=> array('DECIMAL:10', 0.00),
						'forum_cost_topic' 				=> array('DECIMAL:10', 0.00),
						'forum_cost_post' 				=> array('DECIMAL:10', 0.00),
					),
				),
			),
			'add_columns'	=> array(
				$this->table_prefix . 'users' => array(
					'user_points' 					=> array('DECIMAL:20', 0.00),
				),

				$this->table_prefix . 'posts' => array(
					'points_received'				=> array('DECIMAL:20', 0.00),
					'points_poll_received'			=> array('DECIMAL:20', 0.00),
					'points_attachment_received'	=> array('DECIMAL:20', 0.00),
					'points_topic_received'			=> array('DECIMAL:20', 0.00),
					'points_post_received'			=> array('DECIMAL:20', 0.00),
				),

				$this->table_prefix . 'forums' => array(
					'forum_perpost' 				=> array('DECIMAL:10', 5.00),
					'forum_peredit' 				=> array('DECIMAL:10', 0.05),
					'forum_pertopic' 				=> array('DECIMAL:10', 15.00),
					'forum_cost'					=> array('DECIMAL:10', 0.00),
					'forum_cost_topic'				=> array('DECIMAL:10', 0.00),
					'forum_cost_post'				=> array('DECIMAL:10', 0.00),
				),
			),
		);
	}

	public function revert_schema()
	{
		return 	array(
			'drop_tables' => array(
				$this->table_prefix . 'points_bank',
				$this->table_prefix . 'points_config',
				$this->table_prefix . 'points_log',
				$this->table_prefix . 'points_lottery_history',
				$this->table_prefix . 'points_lottery_tickets',
				$this->table_prefix . 'points_values',
			),
			'drop_columns' => array(
				$this->table_prefix . 'users'	=> array(
					'user_points',
				),

				$this->table_prefix . 'posts'	=> array(
					'points_received',
					'points_poll_received',
					'points_attachment_received',
					'points_topic_received',
					'points_post_received',
				),

				$this->table_prefix . 'forums'	=> array(
					'forum_perpost',
					'forum_peredit',
					'forum_pertopic',
					'forum_cost',
					'forum_cost_topic',
					'forum_cost_post',
				),
			),
		);
	}

	public function insert_sample_data()
	{
		if ($this->db_tools->sql_table_exists($this->table_prefix . 'points_values'))
		{
			$sample_data = array(
				array(
					'number_show_per_page' => '15',
					'number_show_top_points' => '10',
					'reg_points_bonus' => '50',
					'lottery_jackpot' => '50',
					'lottery_winners_total' => '0',
					'lottery_prev_winner' => '0',
					'lottery_prev_winner_id' => '0',
					'lottery_last_draw_time' => '0',
					'bank_last_restocked' => '0',
					'lottery_base_amount' => '50.00',
					'lottery_draw_period' => '3600',
					'lottery_ticket_cost' => '10',
					'bank_fees' => '0',
					'bank_interest' => '0',
					'bank_pay_period' => '2592000',
					'bank_min_withdraw' => '0',
					'bank_min_deposit' => '0',
					'bank_interestcut' => '0',
					'points_bonus_chance' => '0',
					'points_bonus_min' => '10.00',
					'points_bonus_max' => '50.00',
					'points_per_poll_option' => '0',
					'points_per_poll' => '0',
					'points_per_attach_file' => '0',
					'points_per_attach' => '0',
					'points_per_post_word' => '0',
					'points_per_post_character' => '0',
					'points_per_topic_word' => '0',
					'points_per_topic_character' => '0',
					'points_per_warn' => '0',
					'robbery_chance' => '50',
					'robbery_loose' => '50',
					'transfer_fee' => '10',
					'bank_cost' => '0',
					'bank_name' => 'BANK NAME',
					'lottery_name' => 'LOTTERY NAME',
				),
			);
		}

		// Insert sample data
		$this->db->sql_multi_insert($this->table_prefix . 'points_values', $sample_data);
	}

	public function insert_config_data()
	{
		if ($this->db_tools->sql_table_exists($this->table_prefix . 'points_config'))
		{
			// Define sample rule data
			$config_data = array(
				array(
					'config_name' 	=> 'transfer_enable',
					'config_value'	=> '1',
					),
				array(
					'config_name' 	=> 'transfer_pm_enable',
					'config_value'	=> '1',
				),
				array(
					'config_name' 	=> 'comments_enable',
					'config_value'	=> '1',
				),
				array(
					'config_name' 	=> 'logs_enable',
					'config_value'	=> '1',
				),
				array(
					'config_name' 	=> 'images_topic_enable',
					'config_value'	=> '1',
				),
				array(
					'config_name' 	=> 'images_memberlist_enable',
					'config_value'	=> '1',
				),
				array(
					'config_name' 	=> 'lottery_enable',
					'config_value'	=> '1',
				),
				array(
					'config_name' 	=> 'bank_enable',
					'config_value'	=> '1',
				),
				array(
					'config_name' 	=> 'robbery_enable',
					'config_value'	=> '1',
				),
				array(
					'config_name' 	=> 'points_disablemsg',
					'config_value'	=> 'Ultimate Points is currently disabled!',
				),
				array(
					'config_name' 	=> 'stats_enable',
					'config_value'	=> '1',
				),
				array(
					'config_name' 	=> 'lottery_multi_ticket_enable',
					'config_value'	=> '1',
				),
				array(
					'config_name' 	=> 'robbery_notify',
					'config_value'	=> '1',
				),
				array(
					'config_name' 	=> 'display_lottery_stats',
					'config_value'	=> '1',
				),
				array(
					'config_name' 	=> 'uplist_enable',
					'config_value'	=> '1',
				),
			);
		}
		// Insert sample data
		$this->db->sql_multi_insert($this->table_prefix . 'points_config', $config_data);
	}
}
