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
use phpbb\template\template;
use phpbb\user;

/**
 * @package Ultimate Points
 */
class points_info
{
	/** @var functions_points */
	protected $functions_points;

	/** @var auth */
	protected $auth;

	/** @var driver_interface */
	protected $db;

	/** @var template */
	protected $template;

	/** @var user */
	protected $user;

	/** @var config */
	protected $config;

	/** @var helper */
	protected $helper;

	/** @var string phpBB root path */
	protected $root_path;

	protected $points_values_table;

	/**
	 * Constructor
	 *
	 * @param auth $auth
	 * @param driver_interface $db
	 * @param template $template
	 * @param user $user
	 * @param config $config
	 * @param helper $helper
	 * @param string $root_path
	 * @param string $points_values_table
	 *
	 * @var functions_points $functions_points
	 */
	public function __construct(
		functions_points $functions_points,
		auth $auth,
		driver_interface $db,
		template $template,
		user $user,
		config $config,
		helper $helper,
		$root_path,
		$points_values_table
	)
	{
		$this->functions_points = $functions_points;
		$this->auth = $auth;
		$this->db = $db;
		$this->template = $template;
		$this->user = $user;
		$this->config = $config;
		$this->helper = $helper;
		$this->root_path = $root_path;
		$this->points_values_table = $points_values_table;
	}

	var $u_action;

	function main()
	{
		// Get all values
		$points_values = $this->functions_points->points_all_values();

		// Add part to bar
		$this->template->assign_block_vars('navlinks', [
			'U_VIEW_FORUM' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'info']),
			'FORUM_NAME' => sprintf($this->user->lang['POINTS_INFO'], $this->config['points_name']),
		]);

		// Read out all the need values
		$info_attach = ($points_values['points_per_attach'] == 0) ? sprintf($this->user->lang['INFO_NO_POINTS'], $this->config['points_name']) : sprintf($this->functions_points->number_format_points($points_values['points_per_attach']) . '&nbsp;' . $this->config['points_name']);
		$info_addtional_attach = ($points_values['points_per_attach_file'] == 0) ? sprintf($this->user->lang['INFO_NO_POINTS'], $this->config['points_name']) : sprintf($this->functions_points->number_format_points($points_values['points_per_attach_file']) . '&nbsp;' . $this->config['points_name']);
		$info_poll = ($points_values['points_per_poll'] == 0) ? sprintf($this->user->lang['INFO_NO_POINTS'], $this->config['points_name']) : sprintf($this->functions_points->number_format_points($points_values['points_per_poll']) . '&nbsp;' . $this->config['points_name']);
		$info_poll_option = ($points_values['points_per_poll_option'] == 0) ? sprintf($this->user->lang['INFO_NO_POINTS'], $this->config['points_name']) : sprintf($this->functions_points->number_format_points($points_values['points_per_poll_option']) . '&nbsp;' . $this->config['points_name']);
		$info_topic_word = ($points_values['points_per_topic_word'] == 0) ? sprintf($this->user->lang['INFO_NO_POINTS'], $this->config['points_name']) : sprintf($this->functions_points->number_format_points($points_values['points_per_topic_word']) . '&nbsp;' . $this->config['points_name']);
		$info_topic_character = ($points_values['points_per_topic_character'] == 0) ? sprintf($this->user->lang['INFO_NO_POINTS'], $this->config['points_name']) : sprintf($this->functions_points->number_format_points($points_values['points_per_topic_character']) . '&nbsp;' . $this->config['points_name']);
		$info_post_word = ($points_values['points_per_post_word'] == 0) ? sprintf($this->user->lang['INFO_NO_POINTS'], $this->config['points_name']) : sprintf($this->functions_points->number_format_points($points_values['points_per_post_word']) . '&nbsp;' . $this->config['points_name']);
		$info_post_character = ($points_values['points_per_post_character'] == 0) ? sprintf($this->user->lang['INFO_NO_POINTS'], $this->config['points_name']) : sprintf($this->functions_points->number_format_points($points_values['points_per_post_character']) . '&nbsp;' . $this->config['points_name']);
		$info_cost_warning = ($points_values['points_per_warn'] == 0) ? sprintf($this->user->lang['INFO_NO_COST'], $this->config['points_name']) : sprintf($this->functions_points->number_format_points($points_values['points_per_warn']) . '&nbsp;' . $this->config['points_name']);
		$info_reg_bonus = ($points_values['reg_points_bonus'] == 0) ? sprintf($this->user->lang['INFO_NO_POINTS'], $this->config['points_name']) : sprintf($this->functions_points->number_format_points($points_values['reg_points_bonus']) . '&nbsp;' . $this->config['points_name']);
		$info_points_bonus = ($points_values['points_bonus_chance'] == 0) ? sprintf($this->user->lang['INFO_NO_POINTS'], $this->config['points_name']) : sprintf($this->user->lang['INFO_BONUS_CHANCE_EXPLAIN'], $this->functions_points->number_format_points($points_values['points_bonus_chance']), $this->functions_points->number_format_points($points_values['points_bonus_min']), $this->functions_points->number_format_points($points_values['points_bonus_max']), $this->config['points_name']);
		$info_points_forum_topic = ($points_values['forum_topic'] == 0) ? sprintf($this->user->lang['INFO_NO_POINTS'], $this->config['points_name']) : sprintf($this->functions_points->number_format_points($points_values['forum_topic']) . '&nbsp;' . $this->config['points_name']);
		$info_points_forum_post = ($points_values['forum_post'] == 0) ? sprintf($this->user->lang['INFO_NO_POINTS'], $this->config['points_name']) : sprintf($this->functions_points->number_format_points($points_values['forum_post']) . '&nbsp;' . $this->config['points_name']);
		$info_points_forum_edit = ($points_values['forum_edit'] == 0) ? sprintf($this->user->lang['INFO_NO_POINTS'], $this->config['points_name']) : sprintf($this->functions_points->number_format_points($points_values['forum_edit']) . '&nbsp;' . $this->config['points_name']);
		$info_points_forum_att_download = ($points_values['forum_cost'] == 0) ? sprintf($this->user->lang['INFO_NO_POINTS'], $this->config['points_name']) : sprintf($this->functions_points->number_format_points($points_values['forum_cost']) . '&nbsp;' . $this->config['points_name']);
		$info_points_forum_cost_topic = ($points_values['forum_cost_topic'] == 0) ? sprintf($this->user->lang['INFO_NO_COST'], $this->config['points_name']) : sprintf($this->functions_points->number_format_points($points_values['forum_cost_topic']) . '&nbsp;' . $this->config['points_name']);
		$info_points_forum_cost_post = ($points_values['forum_cost_post'] == 0) ? sprintf($this->user->lang['INFO_NO_COST'], $this->config['points_name']) : sprintf($this->functions_points->number_format_points($points_values['forum_cost_post']) . '&nbsp;' . $this->config['points_name']);

		$this->template->assign_vars([
			'USER_POINTS' => sprintf($this->functions_points->number_format_points($this->user->data['user_points'])),
			'POINTS_NAME' => $this->config['points_name'],
			'LOTTERY_NAME' => $points_values['lottery_name'],
			'BANK_NAME' => $points_values['bank_name'],
			'POINTS_INFO_DESCRIPTION' => sprintf($this->user->lang['POINTS_INFO_DESCRIPTION'], $this->config['points_name']),

			'INFO_POINTS_ATTACH' => $info_attach,
			'INFO_POINTS_ADD_ATTACH' => $info_addtional_attach,
			'INFO_POINTS_POLL' => $info_poll,
			'INFO_POINTS_POLL_OPTION' => $info_poll_option,
			'INFO_POINTS_TOPIC_WORD' => $info_topic_word,
			'INFO_POINTS_TOPIC_CHARACTER' => $info_topic_character,
			'INFO_POINTS_POST_WORD' => $info_post_word,
			'INFO_POINTS_POST_CHARACTER' => $info_post_character,
			'INFO_POINTS_COST_WARNING' => $info_cost_warning,
			'INFO_POINTS_REG_BONUS' => $info_reg_bonus,
			'INFO_POINTS_BONUS' => $info_points_bonus,
			'INFO_POINTS_FORUM_TOPIC' => $info_points_forum_topic,
			'INFO_POINTS_FORUM_POST' => $info_points_forum_post,
			'INFO_POINTS_FORUM_EDIT' => $info_points_forum_edit,
			'INFO_POINTS_FORUM_ATT_DOWNLOAD' => $info_points_forum_att_download,
			'INFO_POINTS_FORUM_COST_TOPIC' => $info_points_forum_cost_topic,
			'INFO_POINTS_FORUM_COST_POST' => $info_points_forum_cost_post,
			'INFO_ATTACH' => sprintf($this->user->lang['INFO_ATTACH'], $this->config['points_name']),
			'INFO_ADD_ATTACH' => sprintf($this->user->lang['INFO_ADD_ATTACH'], $this->config['points_name']),
			'INFO_POLL' => sprintf($this->user->lang['INFO_POLL'], $this->config['points_name']),
			'INFO_POLL_OPTION' => sprintf($this->user->lang['INFO_POLL_OPTION'], $this->config['points_name']),
			'INFO_TOPIC_WORD' => sprintf($this->user->lang['INFO_TOPIC_WORD'], $this->config['points_name']),
			'INFO_TOPIC_CHARACTER' => sprintf($this->user->lang['INFO_TOPIC_CHARACTER'], $this->config['points_name']),
			'INFO_POST_WORD' => sprintf($this->user->lang['INFO_POST_WORD'], $this->config['points_name']),
			'INFO_POST_CHARACTER' => sprintf($this->user->lang['INFO_POST_CHARACTER'], $this->config['points_name']),
			'INFO_COST_WARNING' => sprintf($this->user->lang['INFO_COST_WARNING'], $this->config['points_name']),
			'INFO_REG_BONUS' => sprintf($this->user->lang['INFO_REG_BONUS'], $this->config['points_name']),
			'INFO_BONUS_CHANCE' => sprintf($this->user->lang['INFO_BONUS_CHANCE'], $this->config['points_name']),
			'INFO_FORUM_TOPIC' => sprintf($this->user->lang['INFO_FORUM_TOPIC'], $this->config['points_name']),
			'INFO_FORUM_POST' => sprintf($this->user->lang['INFO_FORUM_POST'], $this->config['points_name']),
			'INFO_FORUM_EDIT' => sprintf($this->user->lang['INFO_FORUM_EDIT'], $this->config['points_name']),
			'INFO_FORUM_ATT_DOWNLOAD' => sprintf($this->user->lang['INFO_FORUM_ATT_DOWNLOAD'], $this->config['points_name']),
			'INFO_FORUM_COST_TOPIC' => sprintf($this->user->lang['INFO_FORUM_COST_TOPIC'], $this->config['points_name']),
			'INFO_FORUM_COST_POST' => sprintf($this->user->lang['INFO_FORUM_COST_POST'], $this->config['points_name']),

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

		// Generate the page
		page_header($this->user->lang['POINTS_INFO']);

		// Generate the page template
		$this->template->set_filenames([
			'body' => 'points/points_info.html'
		]);

		page_footer();
	}
}
