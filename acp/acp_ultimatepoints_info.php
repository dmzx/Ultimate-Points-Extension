<?php
/**
*
* @package phpBB Extension - Ultimate Points
* @copyright (c) 2016 dmzx & posey - https://www.dmzx-web.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace dmzx\ultimatepoints\acp;

class acp_ultimatepoints_info
{
	function module()
	{
		return array(
			'filename'		=> '\dmzx\ultimatepoints\acp\acp_ultimatepoints_module',
			'title'			=> 'ACP_POINTS',
			'modes'			=> array(
				'points'	=> array(
					'title' => 'ACP_POINTS_INDEX_TITLE',
					'auth' 	=> 'ext_dmzx/ultimatepoints && acl_a_board',
					'cat' 	=> array('ACP_POINTS'),
				),
				'forumpoints'	=> array(
					'title' => 'ACP_POINTS_FORUM_TITLE',
					'auth' 	=> 'ext_dmzx/ultimatepoints && acl_a_board',
					'cat' 	=> array('ACP_POINTS'),
				),
				'bank'		=> array(
					'title' => 'ACP_POINTS_BANK_TITLE',
					'auth' 	=> 'ext_dmzx/ultimatepoints && acl_a_board',
					'cat' 	=> array('ACP_POINTS'),
				),
				'lottery'	=> array(
					'title' => 'ACP_POINTS_LOTTERY_TITLE',
					'auth' 	=> 'ext_dmzx/ultimatepoints && acl_a_board',
					'cat' 	=> array('ACP_POINTS'),
				),
				'robbery'	=> array(
					'title' => 'ACP_POINTS_ROBBERY_TITLE',
					'auth' 	=> 'ext_dmzx/ultimatepoints && acl_a_board',
					'cat' 	=> array('ACP_POINTS'),
				),
			),
		);
	}
}
