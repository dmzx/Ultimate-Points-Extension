<?php
/**
*
* @package phpBB Extension - Ultimate Points
* @copyright (c) 2016 dmzx & posey - https://www.dmzx-web.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace dmzx\ultimatepoints\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class listener implements EventSubscriberInterface
{
	/** @var \dmzx\ultimatepoints\core\functions_points*/
	protected $functions_points;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \phpbb\cache\service */
	protected $cache;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var string */
	protected $root_path;

	/** @var string */
	protected $php_ext;

	/**
	* The database tables
	*
	* @var string
	*/
	protected $points_bank_table;

	protected $points_config_table;

	protected $points_values_table;

	/** @var \phpbb\files\factory */
	protected $files_factory;

	/**
	* Constructor
	*
	* @var \dmzx\ultimatepoints\core\functions_points	$functions_points
	* @param \phpbb\user								$user
	* @param \phpbb\template\template					$template
	* @param \phpbb\db\driver\driver_interface			$db
	* @param \phpbb\config\config						$config
	* @param \phpbb\auth\auth							$auth
	* @param \phpbb\controller\helper					$helper
	* @param \phpbb\cache\service		 				$cache
	* @param \phpbb\request\request		 				$request
	* @param string										$root_path
	* @param string										$php_ext
	* @param string 									$points_bank_table
	* @param string 									$points_config_table
	* @param string 									$points_values_table
	* @param \phpbb\files\factory						$files_factory
	*
	*/
	public function __construct(
		\dmzx\ultimatepoints\core\functions_points $functions_points,
		\phpbb\user $user,
		\phpbb\template\template $template,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\config\config $config,
		\phpbb\auth\auth $auth,
		\phpbb\controller\helper $helper,
		\phpbb\cache\service $cache,
		\phpbb\request\request $request,
		$root_path,
		$php_ext,
		$points_bank_table,
		$points_config_table,
		$points_values_table,
		\phpbb\files\factory $files_factory = null
	)
	{
		$this->functions_points 	= $functions_points;
		$this->user					= $user;
		$this->template				= $template;
		$this->db					= $db;
		$this->config				= $config;
		$this->auth 				= $auth;
		$this->helper 				= $helper;
		$this->cache 				= $cache;
		$this->request 				= $request;
		$this->root_path 			= $root_path;
		$this->php_ext 				= $php_ext;
		$this->points_bank_table 	= $points_bank_table;
		$this->points_config_table 	= $points_config_table;
		$this->points_values_table 	= $points_values_table;
		$this->files_factory 		= $files_factory;
	}

	static public function getSubscribedEvents()
	{
		return array(
			'core.user_setup'								=> 'load_language_on_setup',
			'core.common'									=> 'common_config',
			'core.index_modify_page_title'					=> 'index_modify_page_title',
			'core.memberlist_view_profile'					=> 'memberlist_view_profile',
			'core.viewtopic_assign_template_vars_before'	=> 'viewtopic_assign_template_vars_before',
			'core.parse_attachments_modify_template_data'	=> 'parse_attachments_modify_template_data',
			'core.viewtopic_get_post_data'					=> 'viewtopic_get_post_data',
			'core.viewtopic_post_rowset_data'				=> 'viewtopic_post_rowset_data',
			'core.viewtopic_modify_post_row'				=> 'viewtopic_modify_post_row',
			'core.page_header'								=> 'page_header',
			'core.viewonline_overwrite_location'			=> 'add_page_viewonline',
			'core.download_file_send_to_browser_before'		=> 'download_file_send_to_browser_before',
			'core.acp_manage_forums_request_data'			=> 'acp_manage_forums_request_data',
			'core.acp_manage_forums_initialise_data'		=> 'acp_manage_forums_initialise_data',
			'core.acp_manage_forums_display_form'			=> 'acp_manage_forums_display_form',
			'core.acp_users_overview_modify_data'			=> 'acp_users_overview_modify_data',
			'core.acp_users_display_overview'				=> 'acp_users_display_overview',
			'core.mcp_warn_user_before'						=> 'mcp_warn_user_before',
			'core.mcp_warn_user_after'						=> 'mcp_warn_user_after',
			'core.user_add_modify_data'						=> 'user_add_modify_data',
			'core.submit_post_end'							=> 'submit_post_end',
			'core.modify_posting_auth'						=> 'modify_posting_auth',
			'core.permissions'								=> 'permissions',
		);
	}

	public function load_language_on_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
			'ext_name' => 'dmzx/ultimatepoints',
			'lang_set' => 'common',
		);
		$event['lang_set_ext'] = $lang_set_ext;
	}

	// Let's put some common config values in cache..
	public function common_config($event)
	{
		// Get all configs
		$points_config = $this->functions_points->points_all_configs();

		$this->cache->put('points_config', $points_config);

		// Get all values
		$points_values = $this->functions_points->points_all_values();

		$this->cache->put('points_values', $points_values);
	}

	// Show some information on the index page: Point Statistics, Richest Banker, Lottery..
	public function index_modify_page_title($event)
	{
		if (isset($this->config['points_name']))
		{
			$points_config = $this->cache->get('points_config');
			$points_values = $this->cache->get('points_values');

			// Generate the bank statistics
			$sql_array = array(
				'SELECT'	=> 'SUM(holding) AS total_holding, count(user_id) AS total_users',
				'FROM'		=> array(
					$this->points_bank_table => 'b',
				),
				'WHERE'		=> 'id > 0',
			);
			$sql = $this->db->sql_build_query('SELECT', $sql_array);
			$result = $this->db->sql_query($sql);
			$b_row = $this->db->sql_fetchrow($result);
			$bankholdings = ($b_row['total_holding']) ? $b_row['total_holding'] : 0;
			$bankusers = $b_row['total_users'];

			// Create richest users - cash and bank
			$limit = $points_values['number_show_top_points'];
			$sql_array = array(
				'SELECT'	=> 'u.user_id, u.username, u.user_colour, u.user_points, u.user_avatar, u.user_avatar_type, u.user_avatar_height, u.user_avatar_width, b.holding',

				'FROM'		=> array(
					USERS_TABLE	=> 'u',
				),
				'LEFT_JOIN' => array(
					array(
						'FROM'	=> array($this->points_bank_table => 'b'),
						'ON'	=> 'u.user_id = b.user_id'
					)
				),
			);
			$sql = $this->db->sql_build_query('SELECT', $sql_array);
			$result = $this->db->sql_query($sql);

			// Create a new array for the users
			$rich_users = array();

			// Create sorting array
			$rich_users_sort = array();

			// Loop all users array to escape the 0 points users
			while ($row = $this->db->sql_fetchrow($result))
			{
				if ($row['user_points'] > 0 || $row['holding'] > 0) //let away beggars
				{
					$total_points = $row['user_points'] + $row['holding'];
					$index = $row['user_id'];
					$rich_users[$index] = array('total_points' => $total_points, 'user_avatar' => $row['user_avatar'], 'user_avatar_type' => $row['user_avatar_type'], 'user_avatar_height' => $row['user_avatar_height'], 'user_avatar_width' => $row['user_avatar_width'], 'username' => $row['username'], 'user_colour' => $row['user_colour'], 'user_id' => $index);
					$rich_users_sort[$index] = $total_points;
				}
			}

			$this->db->sql_freeresult($result);

			// Sort by points desc
			arsort($rich_users_sort);

			// Extract the user ids
			$rich_users_sort	= array_keys($rich_users_sort);

			// Create new sorted rich users array
			$rich_users_sorted = array();

			// Check, if number of users in array is below the set limit
			$new_limit = sizeof($rich_users) < $limit ? sizeof($rich_users) : $limit;

			for ($i = 0; $i < $new_limit; $i++)
			{
				$rich_users_sorted[] = $rich_users[$rich_users_sort[$i]];
			}

			// Send to template
			foreach ($rich_users_sorted as $var)
			{
				$this->template->assign_block_vars('rich_user', array(
					'USERNAME'		 	=> get_username_string('full', $var['user_id'], $var['username'], $var['user_colour']),
					'AVATAR'			=> phpbb_get_user_avatar($var),
					'SUM_POINTS'		=> $this->functions_points->number_format_points($var['total_points']),
					'SUM_POINTS_NAME'	=> $this->config['points_name'],
				));
			}

			//Generate the points statistics
			$sql_array = array(
				'SELECT'	=> 'SUM(user_points) AS total_points',
				'FROM'		=> array(
					USERS_TABLE => 'u',
				),
				'WHERE'		=> 'user_points > 0',
			);
			$sql = $this->db->sql_build_query('SELECT', $sql_array);
			$result = $this->db->sql_query($sql);
			$b_row = $this->db->sql_fetchrow($result);
			$totalpoints = ($b_row['total_points']) ? $b_row['total_points'] : 0;
			$lottery_time = $this->user->format_date(($points_values['lottery_last_draw_time'] + $points_values['lottery_draw_period']), false, true);

			// Run Lottery
			if ($points_values['lottery_draw_period'] != 0 && $points_values['lottery_last_draw_time'] + $points_values['lottery_draw_period'] - time() < 0)
			{
				if (!function_exists('send_pm'))
				{
					include($this->root_path . 'includes/functions_privmsgs.' . $this->php_ext);
				}
				$this->functions_points->run_lottery();
			}

			// Run Bank
			if ((time() - $points_values['bank_last_restocked']) > $points_values['bank_pay_period'])
			{
				$this->functions_points->run_bank();
			}

			// Lottery winner index
			$sql_array = array(
				'SELECT'	=> 'user_id, username, user_colour',
				'FROM'		=> array(
					USERS_TABLE => 'u',
				),
				'WHERE'		=> 'user_id = ' . (int) $points_values['lottery_prev_winner_id'],
			);
			$sql = $this->db->sql_build_query('SELECT', $sql_array);
			$result = $this->db->sql_query($sql);
			$row = $this->db->sql_fetchrow($result);

			if ($row == null)
			{
				$username_colored = $this->user->lang['LOTTERY_NO_WINNER'];
			}
			else
			{
				$username_colored = get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']);
			}

			$this->template->assign_vars(array(
				'TOTAL_BANK_USER'			=> sprintf($this->user->lang['POINTS_BUPOINTS_TOTAL'], $points_values['bank_name'], $bankusers),
				'TOTAL_BANK_POINTS'			=> sprintf($this->user->lang['POINTS_BPOINTS_TOTAL'], $points_values['bank_name'], $this->functions_points->number_format_points($bankholdings), $this->config['points_name'], $points_values['bank_name']),
				'TOTAL_POINTS_USER'			=> sprintf($this->user->lang['POINTS_TOTAL'], $this->functions_points->number_format_points($totalpoints), $this->config['points_name']),
				'LOTTERY_TIME'				=> sprintf($this->user->lang['POINTS_LOTTERY_TIME'], $points_values['lottery_name'], $lottery_time),
				'S_DISPLAY_LOTTERY'			=> ($points_config['display_lottery_stats']) ? true : false,
				'S_DISPLAY_POINTS_STATS'	=> ($points_config['stats_enable']) ? true : false,
				'S_DISPLAY_INDEX'			=> ($points_values['number_show_top_points'] > 0) ? true : false,
				'U_USE_POINTS'				=> $this->auth->acl_get('u_use_points'),
				'L_PREVIOUS_WINNER'	 		=> sprintf($this->user->lang['LOTTERY_WINNER_INDEX'], $points_values['lottery_name'], $username_colored),
				'POINTS_MOST_RICH_USERS'	=> sprintf($this->user->lang['POINTS_MOST_RICH_USERS'], $points_values['number_show_top_points']),
			));
		}
	}

	// Show the points in a user's profile
	public function memberlist_view_profile($event)
	{
		$member = $event['member'];
		$user_id = $member['user_id'];
		$points_config = $this->cache->get('points_config');
		$points_values = $this->cache->get('points_values');

		// Grab user's points on hand
		$user_points = $member['user_points'];

		// Grab user's bank holdings
		$sql = 'SELECT holding
			FROM ' . $this->points_bank_table . '
			WHERE user_id = '. $user_id;
		$result = $this->db->sql_query($sql);
		$holding = $this->db->sql_fetchfield('holding');

		$this->template->assign_vars(array(
			'USER_PROF_POINTS'		=> $this->functions_points->number_format_points($user_points),
			'USER_LOCK'				=> !$this->auth->acl_get('u_use_points'),
			'USER_BANK_LOCK'		=> !$this->auth->acl_get('u_use_bank'),
			'USER_BANK_ACC'			=> ($holding) ? true : false,
			'USER_BANK_POINTS'		=> $this->functions_points->number_format_points($holding),
			'L_USER_NO_BANK_ACC'	=> sprintf($this->user->lang['BANK_NO_ACCOUNT'], $points_values['bank_name']),
			'L_MOD_USER_POINTS'		=> ($this->auth->acl_get('a_') && $this->auth->acl_get('m_chg_points')) ? sprintf($this->user->lang['POINTS_MODIFY']) : '',
			'U_POINTS_MODIFY'		=> ($this->auth->acl_get('a_') && $this->auth->acl_get('m_chg_points')) ? $this->helper->route('dmzx_ultimatepoints_controller', array('mode' => 'points_edit', 'user_id' => $user_id, 'adm_points' => '1')) : '',
			'L_MOD_USER_BANK'		=> ($this->auth->acl_get('a_') && $this->auth->acl_get('m_chg_bank')) ? sprintf($this->user->lang['POINTS_MODIFY']) : '',
			'U_BANK_MODIFY'			=> ($this->auth->acl_get('a_') && $this->auth->acl_get('m_chg_points')) ? $this->helper->route('dmzx_ultimatepoints_controller', array('mode' => 'bank_edit', 'user_id' => $user_id, 'adm_points' => '1')) : '',
			'L_DONATE'				=> ($this->auth->acl_get('u_use_points')) ? sprintf($this->user->lang['POINTS_DONATE']) : '',
			'U_POINTS_DONATE'		=> ($this->auth->acl_get('u_use_points')) ? $this->helper->route('dmzx_ultimatepoints_controller', array('mode' => 'transfer', 'i' => $user_id, 'adm_points' => '1')) : '',
			'P_NAME'				=> $this->config['points_name'],
			'USE_POINTS'			=> $this->config['points_enable'],
			'USE_IMAGES_POINTS'		=> $points_config['images_memberlist_enable'],
			'USE_BANK'				=> $points_config['bank_enable'],
			'S_IS_OWN_PROF'			=> ($user_id == $this->user->data['user_id']) ? true : false,
			'U_ROBBERY'				=> $this->helper->route('dmzx_ultimatepoints_controller', array('mode' => 'robbery_user', 'user_id' => $user_id)),
			'U_USE_ROBBERY'			=> $this->auth->acl_get('u_use_robbery') && $points_config['robbery_enable'],
			'U_USE_TRANSFER'		=> $this->auth->acl_get('u_use_transfer') && $points_config['transfer_enable'],
			'U_USE_POINTS'			=> $this->auth->acl_get('u_use_points'),
			'L_ROBBERY'				=> $this->user->lang['ROBBERY_USER'],
			'BANK_BALANCE'			=> sprintf($this->user->lang['BANK_BALANCE'], $points_values['bank_name']),
		));
	}

	public function viewtopic_assign_template_vars_before($event)
	{
		$points_config = $this->cache->get('points_config');
		$points_values = $this->cache->get('points_values');

		$this->template->assign_vars(array(
			'P_NAME'			=> $this->config['points_name'],
			'USE_POINTS'		=> $this->config['points_enable'],
			'USE_IMAGES_POINTS'	=> $points_config['images_topic_enable'],
			'USE_BANK'			=> $points_config['bank_enable'],
			'BANK_BALANCE'		=> sprintf($this->user->lang['BANK_BALANCE'], $points_values['bank_name']),
			'U_USE_POINTS'		=> $this->auth->acl_get('u_use_points'),
		));
	}

	public function parse_attachments_modify_template_data($event)
	{
		$block_array = $event['block_array'];
		$forum_id = (int) $event['forum_id'];
		$display_cat = (int) $event['display_cat'];

		$sql = 'SELECT forum_cost
			FROM ' . FORUMS_TABLE . '
			WHERE forum_id = ' . (int) $forum_id;
		$result = $this->db->sql_query($sql);
		$forum_cost = $this->db->sql_fetchfield('forum_cost');
		$this->db->sql_freeresult($result);

		if ($forum_cost > 0 && $this->auth->acl_get('f_pay_attachment', (int) $forum_id) && $display_cat != 1)
		{
			$this->template->assign_vars(array(
				'L_DOWNLOAD_COST'		=> $this->user->lang['POINTS_DOWNLOAD_COST'],
				'DOWNLOAD_COST'			=> $forum_cost,
			));
		}
	}

	public function viewtopic_get_post_data($event)
	{
		$sql_ary = $event['sql_ary'];
		$sql_ary['SELECT'] .= ', pb.id AS pb_id, pb.holding AS pb_holding';

		$sql_ary['LEFT_JOIN'][] = array(
			'FROM'	=> array(
				$this->points_bank_table		=> 'pb',),
			'ON'	=> 'pb.user_id = p.poster_id'
		);
		$event['sql_ary'] = $sql_ary;
	}

	public function viewtopic_post_rowset_data($event)
	{
		$rowset_data = $event['rowset_data'];
		$row = $event['row'];
		$poster_id = $event['row']['poster_id'];
		$points_config = $this->cache->get('points_config');

		$holding = (empty($holding)) ? array() : $holding;
		if (empty($holding[$poster_id]))
		{
			$sql = "SELECT holding
				FROM " . $this->points_bank_table . "
				WHERE user_id = '$poster_id'";
			$result = $this->db->sql_query($sql);
			$bank_row = $this->db->sql_fetchrow($result);
			$holding[$poster_id] = (!empty($bank_row['holding'])) ? !empty($bank_row['holding']) : '0';
			$bank_row = '';
		}

		$has_account = true;
		$holding = (empty($holding)) ? array() : $holding;
		$pointslock = $banklock = '';

		if ($this->config['points_enable'])
		{
			// Get the points status
			$pointslock = !$this->auth->acl_get('u_use_points');

			// Get the bank status
			if ($points_config['bank_enable'])
			{
				$banklock = !$this->auth->acl_get('u_use_bank');
			}

			if (!isset($row['pb_holding']) && $poster_id > 0)
			{
				$has_account = false;
			}
			$holding[$poster_id] = ($row['pb_holding']) ? $row['pb_holding'] : '0';
		}
		else
		{
			$holding[$poster_id] = '0';
		}

		$rowset_data = array_merge($rowset_data, array(
			'points'			=> $row['user_points'],
			'bank_points'		=> $holding[$poster_id],
			'points_lock'		=> $pointslock,
			'bank_lock'			=> $banklock,
			'bank_account'		=> $has_account,
		));
		$event['rowset_data'] = $rowset_data;
	}

	public function viewtopic_modify_post_row($event)
	{
		$row = $event['row'];
		$user_poster_data = $event['user_poster_data'];
		$post_row = $event['post_row'];
		$post_id = (int) $row['post_id'];
		$poster_id = (int) $event['poster_id'];
		$points_config = $this->cache->get('points_config');
		$points_values = $this->cache->get('points_values');

		$post_row = array_merge($post_row, array(
			'POSTER_POINTS'			=> $this->functions_points->number_format_points($row['points']),
			'POSTER_LOCK'			=> $row['points_lock'],
			'POSTER_BANK_LOCK'		=> $row['bank_lock'],
			'USER_ID'				=> $poster_id,
			'BANK_GOLD'				=> $this->functions_points->number_format_points($row['bank_points']),
			'BANK_ACCOUNT'			=> $row['bank_account'],
			'L_MOD_USER_POINTS'		=> ($this->auth->acl_get('a_') && $this->auth->acl_get('m_chg_points')) ? sprintf($this->user->lang['POINTS_MODIFY']) : '',
			'U_POINTS_MODIFY'		=> ($this->auth->acl_get('a_') && $this->auth->acl_get('m_chg_points')) ? $this->helper->route('dmzx_ultimatepoints_controller', array('mode' => 'points_edit', 'user_id' => $poster_id, 'adm_points' => '1', 'post_id' => $row['post_id'])) : '',
			'L_BANK_USER_POINTS'	=> ($this->auth->acl_get('a_') && $this->auth->acl_get('m_chg_bank')) ? sprintf($this->user->lang['POINTS_MODIFY']) : '',
			'U_BANK_MODIFY'			=> ($this->auth->acl_get('a_') && $this->auth->acl_get('m_chg_bank')) ? $this->helper->route('dmzx_ultimatepoints_controller', array('mode' => 'bank_edit', 'user_id' => $poster_id, 'adm_points' => '1', 'post_id' => $row['post_id'])) : '',
			'L_DONATE'				=> ($this->auth->acl_get('u_use_points') && $points_config['transfer_enable']) ? sprintf($this->user->lang['POINTS_DONATE']) : '',
			'U_POINTS_DONATE'		=> ($this->auth->acl_get('u_use_points')) ? $this->helper->route('dmzx_ultimatepoints_controller', array('mode' => 'transfer', 'i' => $poster_id, 'adm_points' => '1', 'post_id' => $row['post_id'])) : '',
			'S_IS_OWN_POST'			=> ($poster_id == $this->user->data['user_id']) ? true : false,
			'U_ROBBERY'				=> $this->helper->route('dmzx_ultimatepoints_controller', array('mode' => 'robbery_user', 'user_id' => $poster_id)),
			'U_USE_ROBBERY'			=> $this->auth->acl_get('u_use_robbery') && $points_config['robbery_enable'],
			'U_USE_TRANSFER'		=> $this->auth->acl_get('u_use_transfer') && $points_config['transfer_enable'],
			'L_ROBBERY'				=> $this->user->lang['ROBBERY_USER'],
		));
		$event['post_row'] = $post_row;
	}

	// Adds the Points link in the header
	public function page_header($event)
	{
		if (isset($this->config['points_name']))
		{
			$points_config = $this->cache->get('points_config');

			$this->template->assign_vars(array(
				'U_POINTS'					=> $this->helper->route('dmzx_ultimatepoints_controller'),
				'U_POINTS_LIST'				=> $this->helper->route('dmzx_ultimatepoints_list_controller'),
				'POINTS_LINK'				=> $this->config['points_name'],
				'USER_POINTS'				=> sprintf($this->functions_points->number_format_points($this->user->data['user_points'])),
				'S_POINTS_ENABLE'			=> $this->config['points_enable'],
				'S_UPLIST_ENABLE'			=> $points_config['uplist_enable'],
				'S_USE_POINTS'				=> $this->auth->acl_get('u_use_points'),
				'POINTS_ICON_MAINICON'		=> $this->config['points_icon_mainicon'],
				'POINTS_ICON_UPLIST'		=> $this->config['points_icon_uplist'],
				'POINTS_LIST_TOTAL'			=> $this->config['points_name_uplist'],
				'POINTS_ICON_BANKICON'		=> $this->config['points_icon_bankicon'],
				'POINTS_ICON_LOTTERYICON'	=> $this->config['points_icon_lotteryicon'],
				'S_BANK_ENABLE'				=> $points_config['bank_enable'],
				'USE_BANK'					=> $this->auth->acl_get('u_use_bank'),
				'S_LOTTERY_ENABLE'			=> $points_config['lottery_enable'],
				'USE_LOTTERY'				=> $this->auth->acl_get('u_use_lottery'),
				'PHPBB_IS_32'				=> ($this->files_factory !== null) ? true : false,
			));
		}
	}

	// Lets show people where all users are.. addicted to the points, so probably in lottery
	public function add_page_viewonline($event)
	{
		if (strrpos($event['row']['session_page'], 'app.' . $this->php_ext . '/ultimatepoints') === 0)
		{
			$event['location'] = $this->user->lang('ACP_POINTS');
			$event['location_url'] = $this->helper->route('dmzx_ultimatepoints_controller');
		}

		if (strrpos($event['row']['session_page'], 'app.' . $this->php_ext . '/ultimatepointslist') === 0)
		{
			$event['location'] = $this->user->lang('POINTS_LIST_TOTAL');
			$event['location_url'] = $this->helper->route('dmzx_ultimatepoints_list_controller');
		}
	}

	// Check if people have to pay points for downloading attachments
	public function download_file_send_to_browser_before($event)
	{
		$topic_id = (int) $event['attachment']['topic_id'];
		$display_cat = $event['display_cat'];
		$points_values = $this->cache->get('points_values');

		$sql_array = array(
			'SELECT'	=> 'f.forum_cost',

			'FROM'		=> array(
				FORUMS_TABLE	=> 'f',
			),

			'LEFT_JOIN' => array(
				array(
					'FROM'	=> array(TOPICS_TABLE => 't'),
					'ON'	=> 't.forum_id = f.forum_id',
				)
			),

			'WHERE'	 => 't.topic_id = ' . $topic_id,
		);

		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$forum_cost = $this->db->sql_fetchfield('forum_cost');
		$this->db->sql_freeresult($result);

		if ($forum_cost > 0 && $display_cat != 1 && $this->auth->acl_getf('f_pay_attachment'))
		{
			if ($this->config['allow_attachments'] && $this->config['points_enable'] && ($this->user->data['user_points'] < $forum_cost))
			{
				$message = sprintf($this->user->lang['POINTS_ATTACHMENT_MINI_POSTS'], $this->config['points_name']) . '<br /><br /><a href="' . append_sid("{$this->root_path}index.{$this->php_ext}") . '">&laquo; ' . $this->user->lang['POINTS_RETURN_INDEX'] . '</a>';
				trigger_error($message);
			}
			if ($this->config['points_enable'])
			{
				$this->functions_points->substract_points($this->user->data['user_id'], $forum_cost);
			}
		}
	}

	// Submit form (add/update)
	public function acp_manage_forums_request_data($event)
	{
		$forum_data = $event['forum_data'];

		$forum_data = array_merge($forum_data, array(
			'forum_pertopic'	=> $this->request->variable('forum_pertopic', 0.00),
			'forum_perpost'		=> $this->request->variable('forum_perpost', 0.00),
			'forum_peredit'		=> $this->request->variable('forum_peredit', 0.00),
			'forum_cost'		=> $this->request->variable('forum_cost', 0.00),
			'forum_cost_topic'	=> $this->request->variable('forum_cost_topic', 0.00),
			'forum_cost_post'	=> $this->request->variable('forum_cost_post', 0.00),
		));

		$event['forum_data'] = $forum_data;
	}

	// Default settings for new forums
	public function acp_manage_forums_initialise_data($event)
	{
		$forum_data = $event['forum_data'];

		if ($event['action'] == 'add')
		{
			$forum_data['forum_pertopic'] = 0.00;
			$forum_data['forum_perpost'] =	0.00;
			$forum_data['forum_peredit'] =	0.00;
			$forum_data['forum_cost'] = 0.00;
			$forum_data['forum_cost_topic'] = 0.00;
			$forum_data['forum_cost_post']	= 0.00;
		}
		$event['forum_data'] = $forum_data;
	}

	// ACP forums template output
	public function acp_manage_forums_display_form($event)
	{
		$template_data = $event['template_data'];
		$forum_data = $event['forum_data'];

		$template_data = array_merge($template_data, array(
			'FORUM_PERTOPIC'			=> $forum_data['forum_pertopic'],
			'FORUM_PERPOST'				=> $forum_data['forum_perpost'],
			'FORUM_PEREDIT'				=> $forum_data['forum_peredit'],
			'FORUM_COST'				=> $forum_data['forum_cost'],
			'FORUM_COST_TOPIC'			=> $forum_data['forum_cost_topic'],
			'FORUM_COST_POST'			=> $forum_data['forum_cost_post'],
		));
		$event['template_data'] = $template_data;
	}

	/**
	* Allow Admin to enter a user points
	*
	* @param object $event The event object
	* @return null
	* @access public
	*/
	public function acp_users_overview_modify_data($event)
	{
		$event['data'] = array_merge($event['data'], array(
			'user_points'	=> $this->request->variable('user_points', 0.00),
		));

		$event['sql_ary'] = array_merge($event['sql_ary'], array(
			'user_points' 		=> $event['data']['user_points'],
		));
	}

	/**
	* Display Overview in ACP
	*
	* @param object $event The event object
	* @return null
	* @access public
	*/
	public function acp_users_display_overview($event)
	{
		$this->template->assign_vars(array(
			'USER_POINTS'		=> $event['user_row']['user_points'],
		));
	}

	// Update warning message send to the user,
	// now it includes the amount of points deducted.
	public function mcp_warn_user_before($event)
	{
		$warning = $event['warning'];
		$points_values = $this->cache->get('points_values');

		if ($this->config['points_enable'] && $points_values['points_per_warn'] != 0)
		{
			// Update notification message to include the deducted points
			$warning .= '<br />' . sprintf($this->user->lang['WARN_USER_POINTS'], $points_values['points_per_warn'], $this->config['points_name']);
			$event['warning'] = $warning;
		}
	}

	// Update notification message for the moderator,
	// it now also shows how many points were deducted.
	public function mcp_warn_user_after($event)
	{
		$points_values = $this->cache->get('points_values');

		if ($this->config['points_enable'])
		{
			// Substract user's points
			$this->functions_points->substract_points((int) $event['user_row']['user_id'], $points_values['points_per_warn']);

			// Notify the moderator about the additional point deduction
			$message = $event['message'];
			$message .= '<br />' . sprintf($this->user->lang['WARN_MOD_POINTS'], $points_values['points_per_warn'], $this->config['points_name'], $event['user_row']['username']);
			$event['message'] = $message;
		}
	}

	// Give our poor newly registered user some start up cash..
	public function user_add_modify_data($event)
	{
		$points_values = $this->cache->get('points_values');
		$reg_points_bonus = (isset($points_values['reg_points_bonus'])) ? true : false;

		if ($this->config['points_enable'] && $reg_points_bonus)
		{
			$event['sql_ary'] = array_merge($event['sql_ary'], array(
				'user_points'	=> $points_values['reg_points_bonus'],
			));
		}
		else
		{
			$event['sql_ary'] = array_merge($event['sql_ary'], array(
				'user_points'	=> 0,
			));
		}
	}

	// Here come's the real stuff, points incrementation!
	public function submit_post_end($event)
	{
		$points_config = $this->cache->get('points_config');
		$points_values = $this->cache->get('points_values');

		if ($this->config['points_enable'])
		{
			$data = $event['data'];
			$mode = $event['mode'];
			$poll = $event['poll'];
			$post_id = (int) $data['post_id'];
			$topic_id = (int) $data['topic_id'];
			$forum_id = (int) $data['forum_id'];
			$user_id = (int) $this->user->data['user_id'];

			// Send the user_id away to check for a bonus increment
			$this->functions_points->random_bonus_increment($user_id);

			/**
			* Grab our message and strip it clean.
			* This means removing all BBCode,
			* and removing text inside code and quote blocks
			*/
			$message = $this->functions_points->strip_text($data['message']);

			// Set default values
			$topic_word = $topic_char = $forum_topic = 0;
			$post_word = $post_char = $forum_post = 0;
			$difference = $total_points = $prev_points = 0;
			$has_attach = $per_attach = 0;
			$total_attachments = $points_attach = 0;
			$has_poll = $per_poll = $points_poll = $total_poll_options = 0;

			// We grab global points increment
			$topic_word = $points_values['points_per_topic_word']; // Points per word in a topic
			$topic_char = $points_values['points_per_topic_character']; // Points per character in a topic
			$post_word = $points_values['points_per_post_word']; // Points per word in a post (reply)
			$post_char = $points_values['points_per_post_character']; // Points per word in a post (reply)
			$has_attach = $points_values['points_per_attach']; // Points for having attachments in your post
			$per_attach = $points_values['points_per_attach_file']; // Points per attachment in your post
			$has_poll = $points_values['points_per_poll']; // Points for having a poll in your topic
			$per_poll = $points_values['points_per_poll_option']; // Points per poll option in your topic

			// We grab forum specific points increment
			$sql = 'SELECT forum_peredit, forum_perpost, forum_pertopic, forum_cost_topic, forum_cost_post
				FROM ' . FORUMS_TABLE . '
				WHERE forum_id = ' . (int) $forum_id;
			$result = $this->db->sql_query($sql);
			$forum = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);

			// First we check if we have to pay for new topics/post
			if ($mode == 'post' && $forum['forum_cost_topic'] > 0 && $this->auth->acl_get('f_pay_topic', (int) $forum_id))
			{
				$this->functions_points->substract_points((int) $user_id, $forum['forum_cost_topic']);
			}
			else if (($mode == 'reply' || $mode == 'quote') && $forum['forum_cost_post'] > 0 && $this->auth->acl_get('f_pay_post', (int) $forum_id))
			{
				$this->functions_points->substract_points((int) $user_id, $forum['forum_cost_post']);
			}

			// We grab some specific message data
			$sizeof_msg = sizeof(explode(' ', $message)); // Amount of words
			$chars_msg = utf8_strlen($message); // Amount of characters

			// Check if the post has attachment, if so calculate attachment points
			if (!empty($data['attachment_data']))
			{
				$total_attachments = sizeof($data['attachment_data']);
				$points_attach = ($total_attachments * $per_attach) + $has_attach;
			}

			// Check if the post has a poll, if so calculate poll points
			if (!empty($poll['poll_options']))
			{
				$total_poll_options = sizeof($poll['poll_options']);
				$points_poll = ($total_poll_options * $per_poll) + $has_poll;
			}

			// If it's a new topic
			if ($mode == 'post' && $forum['forum_pertopic'] > 0)
			{
				// We calculate the total points
				$words_points = $topic_word * $sizeof_msg;
				$chars_points = $topic_char * $chars_msg;
				$total_points = $words_points + $chars_points + $forum['forum_pertopic'] + $points_attach + $points_poll;

				// We add the total points
				$this->functions_points->add_points($user_id, $total_points); // Add to the user
				$this->functions_points->add_points_to_table($post_id, $total_points, 'topic', $total_attachments, $total_poll_options); // Add to the post table
			}
			// If it's a new post
			else if (($mode == 'reply' || $mode == 'quote') && $forum['forum_perpost'] > 0)
			{
				// We calculate the total points
				$words_points = $post_word * $sizeof_msg;
				$chars_points = $post_char * $chars_msg;
				$total_points = $words_points + $chars_points + $forum['forum_perpost'] + $points_attach;

				// We add the total points
				$this->functions_points->add_points($user_id, $total_points); // Add to the user
				$this->functions_points->add_points_to_table($post_id, $total_points, 'post', $total_attachments, 0); // Add to the post table
			}
			// If it's a topic edit
			else if (($mode == 'edit_topic' || $mode == 'edit_first_post') && $forum['forum_peredit'] > 0)
			{
				// We calculate the total points
				$words_points = $topic_word * $sizeof_msg;
				$chars_points = $topic_char * $chars_msg;
				$total_points = $words_points + $chars_points + $forum['forum_peredit'] + $points_attach + $points_poll;

				// We grab previously received points amount
				$sql = 'SELECT points_topic_received
					FROM ' . POSTS_TABLE . '
					WHERE post_id = ' . (int) $post_id;
				$result = $this->db->sql_query($sql);
				$prev_points = $this->db->sql_fetchfield('points_topic_received');
				$this->db->sql_freeresult($result);

				// We calculate the difference
				$difference = $total_points - $prev_points;

				// We add the difference, only if it's positive, cause we're generous :-)
				if ($difference > 0)
				{
					$this->functions_points->add_points($user_id, $difference); // Add to the user
					$this->functions_points->add_points_to_table($post_id, $total_points, 'topic', $total_attachments, $total_poll_options); // Update to the post table
				}
				else
				{
					return; // "AM I NOT MERCIFUL??" - Caesar Commodus (Gladiator [2000])
				}
			}
			// If it's a post edit
			else if (($mode == 'edit' || $mode == 'edit_last_post') && $forum['forum_peredit'] > 0)
			{
				// We calculate the total points
				$words_points = $post_word * $sizeof_msg;
				$chars_points = $post_char * $chars_msg;
				$total_points = $words_points + $chars_points + $forum['forum_peredit'] + $points_attach;

				// We grab previously received points amount
				$sql = 'SELECT points_post_received
					FROM ' . POSTS_TABLE . '
					WHERE post_id = ' . (int) $post_id;
				$result = $this->db->sql_query($sql);
				$prev_points = $this->db->sql_fetchfield('points_post_received');
				$this->db->sql_freeresult($result);

				// We calculate the difference
				$difference = $total_points - $prev_points;

				// We add the difference, only if it's positive, cause we're generous :-)
				if ($difference > 0)
				{
					$this->functions_points->add_points($user_id, $difference); // Add to the user
					$this->functions_points->add_points_to_table($post_id, $total_points, 'post', $total_attachments, 0); // Update to the post table
				}
				else
				{
					return; // "AM I NOT MERCIFUL??" - Caesar Commodus (Gladiator [2000])
				}
			}
			else
			{
				// We do nothing..
				return; // The only thing necessary for the triumph of evil, is for good men to do nothing. - Edmund Burke
			}
		}
		else
		{
			return;
		}
	}

	public function modify_posting_auth($event)
	{
		if ($this->config['points_enable'])
		{
			$mode = $event['mode'];

			// Grab the costs of making a topic or post in this forum
			$sql = 'SELECT forum_cost_topic, forum_cost_post
				FROM ' . FORUMS_TABLE . '
				WHERE forum_id = ' . (int) $event['forum_id'];
			$result = $this->db->sql_query($sql);
			$forum = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);

			// Grab the user's points
			$sql = 'SELECT user_points
				FROM ' . USERS_TABLE . '
				WHERE user_id = ' . (int) $this->user->data['user_id'];
			$result = $this->db->sql_query($sql);
			$user_points = $this->db->sql_fetchfield('user_points');
			$this->db->sql_freeresult($result);

			if ($mode == 'post' && $forum['forum_cost_topic'] > 0 && $user_points < $forum['forum_cost_topic'] && $this->auth->acl_get('f_pay_topic', (int) $event['forum_id']))
			{
				$message = sprintf($this->user->lang['POINTS_INSUFFICIENT_TOPIC'], $forum['forum_cost_topic'], $this->config['points_name']);
				$message .= '<br /><br />' . $this->user->lang('RETURN_FORUM', '<a href="' . append_sid("{$this->root_path}viewforum.{$this->php_ext}", 'f=' . (int) $event['forum_id']) . '">', '</a>');
				trigger_error($message);
			}
			else if (($mode == 'reply' || $mode == 'quote') && $forum['forum_cost_post'] > 0 && $user_points < $forum['forum_cost_post'] && $this->auth->acl_get('f_pay_post', (int) $event['forum_id']))
			{
				$message = sprintf($this->user->lang['POINTS_INSUFFICIENT_POST'], $forum['forum_cost_post'], $this->config['points_name']);
				$message .= '<br /><br />' . $this->user->lang('RETURN_FORUM', '<a href="' . append_sid("{$this->root_path}viewforum.{$this->php_ext}", 'f=' . (int) $event['forum_id']) . '">', '</a>');
				trigger_error($message);
			}
		}
	}

	// Show permissions
	public function permissions($event)
	{
		$permissions = $event['permissions'];
		$permissions += array(
			'u_use_points'		=> array(
				'lang'		=> 'ACL_U_USE_POINTS',
				'cat'		=> 'ultimatepoints'
			),
			'u_use_bank'	=> array(
				'lang'		=> 'ACL_U_USE_BANK',
				'cat'		=> 'ultimatepoints'
			),
			'u_use_logs'	=> array(
				'lang'		=> 'ACL_U_USE_LOGS',
				'cat'		=> 'ultimatepoints'
			),
			'u_use_robbery'	=> array(
				'lang'		=> 'ACL_U_USE_ROBBERY',
				'cat'		=> 'ultimatepoints'
			),
			'u_use_lottery'	=> array(
				'lang'		=> 'ACL_U_USE_LOTTERY',
				'cat'		=> 'ultimatepoints'
			),
			'u_use_transfer'	=> array(
				'lang'		=> 'ACL_U_USE_TRANSFER',
				'cat'		=> 'ultimatepoints'
			),
			'f_pay_attachment'	=> array(
				'lang'		=> 'ACL_F_PAY_ATTACHMENT',
				'cat'		=> 'ultimatepoints'
			),
			'f_pay_topic'	=> array(
				'lang'		=> 'ACL_F_PAY_TOPIC',
				'cat'		=> 'ultimatepoints'
			),
			'f_pay_post'	=> array(
				'lang'		=> 'ACL_F_PAY_POST',
				'cat'		=> 'ultimatepoints'
			),
			'm_chg_points'	=> array(
				'lang'		=> 'ACL_M_CHG_POINTS',
				'cat'		=> 'ultimatepoints'
			),
			'm_chg_bank'	=> array(
				'lang'		=> 'ACL_M_CHG_BANK',
				'cat'		=> 'ultimatepoints'
			),
			'a_points'	=> array(
				'lang'		=> 'ACL_A_POINTS',
				'cat'		=> 'ultimatepoints'
			),
		);
		$event['permissions'] = $permissions;
		$categories['ultimatepoints'] = 'ACL_CAT_POINTS';
		$event['categories'] = array_merge($event['categories'], $categories);
	}
}
