<?php
/**
 *
 * @package phpBB Extension - Ultimate Points
 * @copyright (c) 2016 dmzx & posey - https://www.dmzx-web.net
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace dmzx\ultimatepoints\acp;

class acp_ultimatepoints_module
{
	public $u_action;

	function main($id, $mode)
	{
		global $phpbb_container, $user;

		// Get an instance of the admin controller
		$admin_controller = $phpbb_container->get('dmzx.ultimatepoints.admin.controller');

		// Add the ACP lang file
		$user->add_lang_ext('dmzx/ultimatepoints', 'acp_ultimatepoints');

		// Make the $u_action url available in the admin controller
		$admin_controller->set_page_url($this->u_action);

		switch ($mode)
		{
			case 'points':
				// Load a template from adm/style for our ACP page
				$this->tpl_name = 'acp_points_main';
				// Set the page title for our ACP page
				$this->page_title = $user->lang['ACP_POINTS_INDEX_TITLE'];
				// Load the display points in the admin controller
				$admin_controller->display_points();
				break;

			case 'lottery':
				// Load a template from adm/style for our ACP page
				$this->tpl_name = 'acp_points_lottery';
				// Set the page title for our ACP page
				$this->page_title = $user->lang['ACP_POINTS_LOTTERY_TITLE'];
				// Load the display lottery in the admin controller
				$admin_controller->display_lottery();
				break;

			case 'bank':
				// Load a template from adm/style for our ACP page
				$this->tpl_name = 'acp_points_bank';
				// Set the page title for our ACP page
				$this->page_title = $user->lang['ACP_POINTS_BANK_TITLE'];
				// Load the display bank in the admin controller
				$admin_controller->display_bank();
				break;

			case 'robbery':
				// Load a template from adm/style for our ACP page
				$this->tpl_name = 'acp_points_robbery';
				// Set the page title for our ACP page
				$this->page_title = $user->lang['ACP_POINTS_ROBBERY_TITLE'];
				// Load the display robbery in the admin controller
				$admin_controller->display_robbery();
				break;

			case 'userguide':
				// Load a template from adm/style for our ACP page
				$this->tpl_name = 'acp_points_userguide';
				// Set the page title for our ACP page
				$this->page_title = $user->lang['ACP_POINTS_USERGUIDE_TITLE'];
				// Load the display userguide in the admin controller
				$admin_controller->display_userguide();
				break;

			case 'forumpoints':
				// Load a template from adm/style for our ACP page
				$this->tpl_name = 'acp_points_forum';
				// Set the page title for our ACP page
				$this->page_title = $user->lang['ACP_POINTS_FORUM_TITLE'];
				// Load the display forumpoints in the admin controller
				$admin_controller->display_forumpoints();
				break;
		}
	}
}
