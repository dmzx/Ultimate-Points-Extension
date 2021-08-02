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
		return [
			'filename' => '\dmzx\ultimatepoints\acp\acp_ultimatepoints_module',
			'title' => 'ACP_POINTS',
			'modes' => [
				'points' => [
					'title' => 'ACP_POINTS_INDEX_TITLE',
					'auth' => 'ext_dmzx/ultimatepoints && acl_a_board',
					'cat' => ['ACP_POINTS'],
				],
				'forumpoints' => [
					'title' => 'ACP_POINTS_FORUM_TITLE',
					'auth' => 'ext_dmzx/ultimatepoints && acl_a_board',
					'cat' => ['ACP_POINTS'],
				],
				'bank' => [
					'title' => 'ACP_POINTS_BANK_TITLE',
					'auth' => 'ext_dmzx/ultimatepoints && acl_a_board',
					'cat' => ['ACP_POINTS'],
				],
				'lottery' => [
					'title' => 'ACP_POINTS_LOTTERY_TITLE',
					'auth' => 'ext_dmzx/ultimatepoints && acl_a_board',
					'cat' => ['ACP_POINTS'],
				],
				'robbery' => [
					'title' => 'ACP_POINTS_ROBBERY_TITLE',
					'auth' => 'ext_dmzx/ultimatepoints && acl_a_board',
					'cat' => ['ACP_POINTS'],
				],
			],
		];
	}
}
