<?php
/**
*
* @package phpBB Extension - Ultimate Points
* @copyright (c) 2016 dmzx & posey - https://www.dmzx-web.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace dmzx\ultimatepoints\core;

class points_bank_edit
{
	/** @var \dmzx\ultimatepoints\core\functions_points */
	protected $functions_points;

	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \phpbb\cache\service */
	protected $cache;

	/** @var \phpbb\log\log */
	protected $log;

	/** @var string */
	protected $php_ext;

	/** @var string phpBB root path */
	protected $root_path;

	/**
	* The database tables
	*
	* @var string
	*/
	protected $points_bank_table;

	/**
	* Constructor
	*
	* @var \dmzx\ultimatepoints\core\functions_points	$functions_points
	* @param \phpbb\auth\auth							$auth
	* @param \phpbb\template\template		 			$template
	* @param \phpbb\user								$user
	* @param \phpbb\db\driver\driver_interface			$db
	* @param \phpbb\request\request		 				$request
	* @param \phpbb\config\config						$config
	* @param \phpbb\controller\helper		 			$helper
	* @param \phpbb\cache\service		 				$cache
	* @param \phpbb\log\log					 			$log
	* @param string										$php_ext
	* @param string										$root_path
	* @param string 									$points_bank_table
	*
	*/
	public function __construct(
		\dmzx\ultimatepoints\core\functions_points $functions_points,
		\phpbb\auth\auth $auth,
		\phpbb\template\template $template,
		\phpbb\user $user,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\request\request $request,
		\phpbb\config\config $config,
		\phpbb\controller\helper $helper,
		\phpbb\cache\service $cache,
		\phpbb\log\log $log,
		$php_ext,
		$root_path,
		$points_bank_table
	)
	{
		$this->functions_points		= $functions_points;
		$this->auth					= $auth;
		$this->template 			= $template;
		$this->user 				= $user;
		$this->db 					= $db;
		$this->request 				= $request;
		$this->config 				= $config;
		$this->helper 				= $helper;
		$this->cache 				= $cache;
		$this->log 					= $log;
		$this->php_ext 				= $php_ext;
		$this->root_path 			= $root_path;
		$this->points_bank_table 	= $points_bank_table;
	}

	var $u_action;

	function main()
	{
		// Only registered users can go beyond this point
		if (!$this->user->data['is_registered'])
		{
			if ($this->user->data['is_bot'])
			{
				redirect(append_sid("{$this->root_path}index.{$this->php_ext}"));
			}
			login_box('', $this->user->lang['LOGIN_INFO']);
		}

		$adm_points	= $this->request->variable('adm_points', false);
		$u_id 		= $this->request->variable('user_id', 0);
		$post_id	= $this->request->variable('post_id', 0);

		if (empty($u_id))
		{
			$message = $this->user->lang['EDIT_NO_ID_SPECIFIED'] . '<br /><br /><a href="' . $this->helper->route('dmzx_ultimatepoints_controller', array('mode' => 'bank_edit')) . '">&laquo; ' . $this->user->lang['BACK_TO_PREV'] . '</a>';
			trigger_error($message);
		}

		$user_id = $u_id;
		add_form_key('bank_edit');

		if ($adm_points != false && ($this->auth->acl_get('a_') || $this->auth->acl_get('m_chg_bank')))
		{
			$this->template->assign_block_vars('administer_bank', array());

			if ($this->request->is_set_post('submit'))
			{
				if (!check_form_key('bank_edit'))
				{
					trigger_error('FORM_INVALID');
				}

				$new_points = round($this->request->variable('points', 0.00),2);

				$this->functions_points->set_bank($u_id, $new_points);

				$sql_array = array(
					'SELECT'	=> 'user_id, username, user_points, user_colour',
					'FROM'		=> array(
						USERS_TABLE => 'u',
					),
					'WHERE'		=> 'user_id = ' . (int) $u_id,
				);
				$sql = $this->db->sql_build_query('SELECT', $sql_array);
				$result = $this->db->sql_query($sql);
				$points_user = $this->db->sql_fetchrow($result);

				// Add logs
				$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_MOD_BANK', false, array($points_user['username']));
				$message = ($post_id) ? sprintf($this->user->lang['EDIT_P_RETURN_POST'], '<a href="'. append_sid("{$this->root_path}viewtopic.{$this->php_ext}", "p=" . $post_id) . '">', '</a>') : sprintf($this->user->lang['EDIT_P_RETURN_INDEX'], '<a href="' . append_sid("{$this->root_path}index.{$this->php_ext}") . '">', '</a>');
				trigger_error((sprintf($this->user->lang['EDIT_POINTS_SET'], $this->config['points_name'])) . $message);
			}
			else
			{
				$sql_array = array(
					'SELECT'	=> 'u.user_id, u.username, u.user_points, u.user_colour, b.holding',

					'FROM'		=> array(
						USERS_TABLE	=> 'u',
					),

					'LEFT_JOIN'	=> array(
						array(
							'FROM'	=> array($this->points_bank_table => 'b'),
							'ON'	=> 'u.user_id = b.user_id'
						),
					),

					'WHERE'		=> 'u.user_id = ' . (int) $u_id,
				);
				$sql = $this->db->sql_build_query('SELECT', $sql_array);
				$result = $this->db->sql_query($sql);
				$row = $this->db->sql_fetchrow($result);

				if (empty($u_id))
				{
					$message = $this->user->lang['EDIT_USER_NOT_EXIST'] . '<br /><br /><a href="' . $this->helper->route('dmzx_ultimatepoints_controller', array('mode' => 'bank_edit')) . '">&laquo; ' . $this->user->lang['BACK_TO_PREV'] . '</a>';
					trigger_error($message);
				}

				$hidden_fields = build_hidden_fields(array(
					'user_id'	=> $u_id,
					'post_id'	=> $post_id,
				));

				$points_values = $this->cache->get('points_values');

				$this->template->assign_vars(array(
					'USER_NAME'			=> get_username_string('full', $u_id, $row['username'], $row['user_colour']),
					'BANK_POINTS'		=> sprintf($this->functions_points->number_format_points($row['holding'])),
					'BANK_BALANCE'		=> sprintf($this->user->lang['BANK_BALANCE'], $points_values['bank_name']),
					'POINTS_NAME'		=> $this->config['points_name'],
					'CURRENT_VALUE'		=> $row['holding'],
					'L_POINTS_MODIFY'	=> sprintf($this->user->lang['EDIT_BANK_MODIFY'], $this->config['points_name']),
					'L_P_BANK_TITLE'	=> sprintf($this->user->lang['EDIT_P_BANK_TITLE'], $this->config['points_name']),
					'L_USERNAME'		=> $this->user->lang['USERNAME'],
					'L_SET_AMOUNT'		=> $this->user->lang['EDIT_SET_AMOUNT'],
					'U_USER_LINK'		=> append_sid("{$this->root_path}memberlist.{$this->php_ext}", "mode=viewprofile&amp;u=" . $u_id),
					'S_ACTION'			=> $this->helper->route('dmzx_ultimatepoints_controller', array('mode' => 'bank_edit', 'adm_points' => '1')),
					'S_HIDDEN_FIELDS'	=> $hidden_fields,
				));
			}
		}
		// Generate the page
		page_header($this->user->lang['EDIT_POINTS_ADMIN']);

		// Generate the page template
		$this->template->set_filenames(array(
			'body'	=> 'points/points_bank_edit.html'
		));

		page_footer();
	}
}
