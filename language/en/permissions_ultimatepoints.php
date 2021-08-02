<?php
/**
 *
 * @package phpBB Extension - Ultimate Points
 * @copyright (c) 2016 dmzx & posey - https://www.dmzx-web.net
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
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
	'ACL_CAT_POINTS' => 'Ultimate Points',
	'ACL_U_USE_POINTS' => 'Can use Ultimate Points',
	'ACL_U_USE_ROBBERY' => 'Can use Robbery Module',
	'ACL_U_USE_BANK' => 'Can use Bank Module',
	'ACL_U_USE_LOGS' => 'Can use Log Module',
	'ACL_U_USE_LOTTERY' => 'Can use Lottery Module',
	'ACL_U_USE_TRANSFER' => 'Can use Transfer Module',
	'ACL_F_PAY_ATTACHMENT' => 'Has to pay for downloading attachments',
	'ACL_F_PAY_TOPIC' => 'Has to pay for making a new topic',
	'ACL_F_PAY_POST' => 'Has to pay for making a new post',
	'ACL_M_CHG_POINTS' => 'Can change users points',
	'ACL_M_CHG_BANK' => 'Can change users Bank points',
	'ACL_A_POINTS' => 'Can administrate Ultimate Points',
]);
