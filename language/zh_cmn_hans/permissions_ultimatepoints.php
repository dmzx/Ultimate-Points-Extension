<?php

/**
 *
 * @package phpBB Extension - Ultimate Points
 * @copyright (c) 2016 dmzx & posey - https://www.dmzx-web.net
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
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
// Some characters for use
// ’ » “ ” …

$lang = array_merge($lang, [
	'ACL_CAT_POINTS' => '终极积分',
	'ACL_U_USE_POINTS' => '可以使用终极积分系统',
	'ACL_U_USE_ROBBERY' => '可以使用抢劫模块',
	'ACL_U_USE_BANK' => '可以使用银行模块',
	'ACL_U_USE_LOGS' => '可以使用日志模块',
	'ACL_U_USE_LOTTERY' => '可以使用彩票模块',
	'ACL_U_USE_TRANSFER' => '可以使用转账模块',
	'ACL_F_PAY_ATTACHMENT' => '下载附件需要付费',
	'ACL_F_PAY_TOPIC' => '创建新主题需要付费',
	'ACL_F_PAY_POST' => '发布新帖子需要付费',
	'ACL_M_CHG_POINTS' => '可以更改用户的积分',
	'ACL_M_CHG_BANK' => '可以更改用户的银行积分',
	'ACL_A_POINTS' => '可以管理终极积分系统',
]);
