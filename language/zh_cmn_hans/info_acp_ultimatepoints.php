<?php

/**
 *
 * @package phpBB Extension - Ultimate Points
 * @copyright (c) 2016 dmzx & posey - https://www.dmzx-web.net
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

/**
 * DO NOT CHANGE
 */
if (!defined('IN_PHPBB')) {
	exit;
}

if (empty($lang) || !is_array($lang)) {
	$lang = [];
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
//
// Some characters you may want to copy&paste:
// ’ » “ ” …
//

// Merge the following language entries into the lang array
$lang = array_merge($lang, [
	'ACP_POINTS' => '终极积分',
	'ACP_POINTS_BANK_TITLE' => '银行设置',
	'ACP_POINTS_FORUM_TITLE' => '论坛积分设置',
	'ACP_POINTS_INDEX_TITLE' => '积分设置',
	'ACP_POINTS_LOTTERY_TITLE' => '彩票设置',
	'ACP_POINTS_ROBBERY_TITLE' => '抢劫设置',
	'ACP_USER_POINTS_TITLE' => '终极积分设置',
	// 日志操作
	'LOG_GROUP_TRANSFER_ADD' => '向一个用户组转移了积分',
	'LOG_GROUP_TRANSFER_SET' => '为一个用户组的积分设置了新值',
	'LOG_MOD_BANK' => '<strong>编辑了银行积分</strong><br />» %1$s',
	'LOG_MOD_POINTS' => '<strong>编辑了积分</strong><br />» %1$s',
	'LOG_MOD_POINTS_BANK' => '<strong>编辑了银行设置</strong>',
	'LOG_MOD_POINTS_BANK_PAYS' => '<strong>银行利息支付</strong><br />» %1$s',
	'LOG_MOD_POINTS_FORUM' => '<strong>编辑了全局论坛积分设置</strong>',
	'LOG_MOD_POINTS_FORUM_SWITCH' => '<strong>编辑了论坛积分开关</strong>',
	'LOG_MOD_POINTS_FORUM_VALUES' => '<strong>编辑了论坛积分值</strong>',
	'LOG_MOD_POINTS_LOTTERY' => '<strong>编辑了彩票设置</strong>',
	'LOG_MOD_POINTS_RANDOM' => '<strong>以下用户赢得了随机积分</strong><br />» %1$s',
	'LOG_MOD_POINTS_ROBBERY' => '<strong>编辑了抢劫设置</strong>',
	'LOG_MOD_POINTS_SETTINGS' => '<strong>编辑了积分设置</strong>',
	'LOG_RESYNC_LOTTERY_HISTORY' => '<strong>彩票历史记录已成功重置</strong>',
	'LOG_RESYNC_POINTSCOUNTS' => '<strong>所有用户的积分已成功重置</strong>',
	'LOG_RESYNC_POINTSLOGSCOUNTS' => '<strong>所有用户的日志已成功重置</strong>',
]);
