<?php
/**
*
* @package phpBB Extension - Ultimate Points
* @copyright (c) 2016 dmzx & posey - https://www.dmzx-web.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace dmzx\ultimatepoints\controller;

class userlist
{
	/** @var \dmzx\ultimatepoints\core\functions_points */
	protected $functions_points;

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

	/** @var \phpbb\pagination */
	protected $pagination;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/**
	* The database tables
	*
	* @var string
	*/
	protected $points_config_table;

	protected $points_values_table;

	/**
	* Constructor
	*
	* @var \dmzx\ultimatepoints\core\functions_points	$functions_points
	* @param \phpbb\template\template		 			$template
	* @param \phpbb\user								$user
	* @param \phpbb\db\driver\driver_interface			$db
	* @param \phpbb\request\request		 				$request
	* @param \phpbb\config\config						$config
	* @param \phpbb\pagination							$pagination
	* @param \phpbb\controller\helper		 			$helper
	* @param string 									$points_config_table
	* @param string 									$points_values_table
	*
	*/
	public function __construct(
		\dmzx\ultimatepoints\core\functions_points $functions_points,
		\phpbb\template\template $template,
		\phpbb\user $user,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\request\request $request,
		\phpbb\config\config $config,
		\phpbb\pagination $pagination,
		\phpbb\controller\helper $helper,
		$points_config_table,
		$points_values_table
	)
	{
		$this->functions_points 	= $functions_points;
		$this->template 			= $template;
		$this->user 				= $user;
		$this->db 					= $db;
		$this->request 				= $request;
		$this->config 				= $config;
		$this->pagination 			= $pagination;
		$this->helper 				= $helper;
		$this->points_config_table 	= $points_config_table;
		$this->points_values_table 	= $points_values_table;
	}

	public function handle_ultimatepoints_list()
	{
		// Get all configs
		$points_config = $this->functions_points->points_all_configs();

		// UPlist disabled
		if (!$points_config['uplist_enable'])
		{
			$message = $this->user->lang['POINTS_LIST_DISABLE'] . '<br /><br /><a href="' . $this->helper->route('dmzx_ultimatepoints_controller') . '">&laquo; ' . $this->user->lang['BACK_TO_PREV'] . '</a>';
			trigger_error($message);
		}

		$this->functions_points->assign_authors();

		// Get all values
		$points_values = $this->functions_points->points_all_values();

		// Generate donors list
		$start = $this->request->variable('start', 0);
		$limit = $points_values['number_show_per_page'];

		$sql = 'SELECT *
			FROM ' . USERS_TABLE . '
			WHERE user_points > 0
			ORDER BY user_points DESC';
		$result = $this->db->sql_query_limit($sql, $limit, $start);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->template->assign_block_vars('ultimatelist', array(
				'USERNAME'	=> get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
				'AVATAR'	=> phpbb_get_user_avatar($row),
				'POINT'		=> sprintf($this->functions_points->number_format_points($row['user_points'])),
			));
		}
		$this->db->sql_freeresult($result);

		// Generate pagination
		$sql = 'SELECT COUNT(user_points) AS ultimatepoints_total
			FROM ' . USERS_TABLE . '
			WHERE user_points > 0';
		$result = $this->db->sql_query($sql);
		$ultimatepoints_total = (int) $this->db->sql_fetchfield('ultimatepoints_total');

		//Start pagination
		$pagination_url = $this->helper->route('dmzx_ultimatepoints_list_controller');
		$this->pagination->generate_template_pagination($pagination_url, 'pagination', 'start', $ultimatepoints_total, $limit, $start);

		$this->template->assign_vars(array(
			'TOTAL_ULTIMATEPOINTS_LIST'		=> ($ultimatepoints_total == 1) ? $this->user->lang['POINTS_LIST_USER'] : sprintf($this->user->lang['POINTS_LIST_USERS'], $ultimatepoints_total),
			'POINTS_LIST_TOTAL'				=> $this->config['points_name_uplist'],
			'POINTS_LINK'					=> $this->config['points_name'],
			'ULTIMATEPOINTS_FOOTER_VIEW'	=> true,
		));

		// Output the page
		page_header($this->config['points_name_uplist']);

		$this->template->set_filenames(array(
			'body' => 'points/points_list.html'
		));

		page_footer();
	}
}
