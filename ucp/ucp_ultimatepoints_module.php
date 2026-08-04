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
	public $tpl_name;
	public $page_title;

	function main($id, $mode)
	{
		global $phpbb_container;

		$this->tpl_name = 'points/ucp_ultimatepoints';
		$this->page_title = $phpbb_container->get('language')->lang('UCP_ULTIMATEPOINTS_TITLE');

		$phpbb_container->get('dmzx.ultimatepoints.ucp.controller')->main($mode);
	}
}
