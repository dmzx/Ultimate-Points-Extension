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
use phpbb\log\log;
use phpbb\request\request;
use phpbb\template\template;
use phpbb\user;

class points_points_edit
{
	/** @var functions_points */
	protected $functions_points;

	/** @var auth */
	protected $auth;

	/** @var template */
	protected $template;

	/** @var user */
	protected $user;

	/** @var driver_interface */
	protected $db;

	/** @var config */
	protected $config;

	/** @var helper */
	protected $helper;

	/** @var log */
	protected $log;

	/** @var request */
	protected $request;

	/** @var string php_ext */
	protected $php_ext;

	/** @var string phpBB root path */
	protected $root_path;

	/**
	 * Constructor
	 *
	 * @param auth $auth
	 * @param template $template
	 * @param user $user
	 * @param driver_interface $db
	 * @param config $config
	 * @param helper $helper
	 * @param log $log
	 * @param request $request
	 * @param string $php_ext
	 * @param string $root_path
	 *
	 * @var functions_points $functions_points
	 */
	public function __construct(
		functions_points $functions_points,
		auth $auth,
		template $template,
		user $user,
		driver_interface $db,
		config $config,
		helper $helper,
		log $log,
		request $request,
		$php_ext,
		$root_path
	)
	{
		$this->functions_points = $functions_points;
		$this->auth = $auth;
		$this->template = $template;
		$this->user = $user;
		$this->db = $db;
		$this->config = $config;
		$this->helper = $helper;
		$this->log = $log;
		$this->request = $request;
		$this->php_ext = $php_ext;
		$this->root_path = $root_path;
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

		$adm_points = $this->request->variable('adm_points', false);
		$u_id = $this->request->variable('user_id', 0);
		$post_id = $this->request->variable('post_id', 0);
		$method = $this->request->variable('method', '');

		add_form_key('points_edit');

		if (empty($u_id))
		{
			$message = $this->user->lang['EDIT_NO_ID_SPECIFIED'] . '<br /><br /><a href="' . $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'points_edit']) . '">&laquo; ' . $this->user->lang['BACK_TO_PREV'] . '</a>';
			trigger_error($message);
		}

		if ($adm_points != false && ($this->auth->acl_get('a_') || $this->auth->acl_get('m_chg_points')))
		{
			$this->template->assign_block_vars('administer_points', []);

			if ($this->request->is_set_post('submit'))
			{
				if (!check_form_key('points_edit'))
				{
					trigger_error('FORM_INVALID');
				}

				$new_points = round($this->request->variable('points', 0.00), 2);

				// Do we set new points amount
				if ($method == 'set')
				{
					$this->functions_points->set_points($u_id, $new_points);
				} // Or do we add points
				else if ($method == 'add')
				{
					$this->functions_points->add_points($u_id, $new_points);
				} // Or do we substract points
				else if ($method == 'substract')
				{
					$this->functions_points->substract_points($u_id, $new_points);
				}

				$sql_array = [
					'SELECT' => 'user_id, username, user_points, user_colour',
					'FROM' => [
						USERS_TABLE => 'u',
					],
					'WHERE' => 'user_id = ' . (int) $u_id,
				];
				$sql = $this->db->sql_build_query('SELECT', $sql_array);
				$result = $this->db->sql_query($sql);
				$points_user = $this->db->sql_fetchrow($result);

				// Add logs
				$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_MOD_POINTS', false, [$points_user['username']]);
				$message = ($post_id) ? sprintf($this->user->lang['EDIT_P_RETURN_POST'], '<a href="' . append_sid("{$this->root_path}viewtopic.{$this->php_ext}", "p=" . $post_id) . '">', '</a>') : sprintf($this->user->lang['EDIT_P_RETURN_INDEX'], '<a href="' . append_sid("{$this->root_path}index.{$this->php_ext}") . '">', '</a>');
				trigger_error((sprintf($this->user->lang['EDIT_POINTS_SET'], $this->config['points_name'])) . $message);
			} else
			{
				$sql_array = [
					'SELECT' => 'user_id, username, user_points, user_colour',
					'FROM' => [
						USERS_TABLE => 'u',
					],
					'WHERE' => 'user_id = ' . (int) $u_id,
				];
				$sql = $this->db->sql_build_query('SELECT', $sql_array);
				$result = $this->db->sql_query($sql);
				$row = $this->db->sql_fetchrow($result);

				if (empty($u_id))
				{
					$message = $this->user->lang['EDIT_USER_NOT_EXIST'] . '<br /><br /><a href="' . $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'points_edit']) . '">&laquo; ' . $this->user->lang['BACK_TO_PREV'] . '</a>';
					trigger_error($message);
				}

				$hidden_fields = build_hidden_fields([
					'user_id' => $u_id,
					'post_id' => $post_id,
				]);

				$this->template->assign_vars([
					'USER_NAME' => get_username_string('full', $u_id, $row['username'], $row['user_colour']),
					'POINTS_OF_USER' => sprintf($this->functions_points->number_format_points($row['user_points'])),
					'POINTS_NAME' => $this->config['points_name'],
					'CURRENT_VALUE' => $row['user_points'],
					'L_POINTS_MODIFY' => sprintf($this->user->lang['EDIT_POINTS_MODIFY'], $this->config['points_name']),
					'L_P_POINTS_TITLE' => sprintf($this->user->lang['EDIT_P_POINTS_TITLE'], $this->config['points_name']),
					'L_USERNAME' => $this->user->lang['USERNAME'],
					'S_ACTION' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'points_edit', 'adm_points' => '1']),
					'S_HIDDEN_FIELDS' => $hidden_fields,
					'U_USER_LINK' => append_sid("{$this->root_path}memberlist.{$this->php_ext}", "mode=viewprofile&amp;u=" . $u_id),
				]);
			}
		}

		// Generate the page
		page_header($this->user->lang['EDIT_POINTS_ADMIN']);

		// Generate the page template
		$this->template->set_filenames([
			'body' => 'points/points_points_edit.html'
		]);

		page_footer();
	}
}
