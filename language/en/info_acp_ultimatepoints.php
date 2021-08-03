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
// Some characters you may want to copy&paste:
// ’ » “ ” …
//

// Merge the following language entries into the lang array
$lang = array_merge($lang, [
	'ACP_POINTS' => 'Ultimate Points',
	'ACP_POINTS_BANK_TITLE' => 'Bank Settings',
	'ACP_POINTS_FORUM_TITLE' => 'Forum Points Settings',
	'ACP_POINTS_INDEX_TITLE' => 'Point Settings',
	'ACP_POINTS_LOTTERY_TITLE' => 'Lottery Settings',
	'ACP_POINTS_ROBBERY_TITLE' => 'Robbery Settings',
	'ACP_USER_POINTS_TITLE' => 'Ultimate Points Settings',
	// Log actions
	'LOG_GROUP_TRANSFER_ADD' => 'Transferred Points to a group',
	'LOG_GROUP_TRANSFER_SET' => 'Set Points to a new value for a group',
	'LOG_MOD_BANK' => '<strong>Edited bank points</strong><br />» %1$s',
	'LOG_MOD_POINTS' => '<strong>Edited points</strong><br />» %1$s',
	'LOG_MOD_POINTS_BANK' => '<strong>Edited bank settings</strong>',
	'LOG_MOD_POINTS_BANK_PAYS' => '<strong>Bank interest payments</strong><br />» %1$s',
	'LOG_MOD_POINTS_FORUM' => '<strong>Edited Global Forum Points settings</strong>',
	'LOG_MOD_POINTS_FORUM_SWITCH' => '<strong>Edited Forum Point Switches</strong>',
	'LOG_MOD_POINTS_FORUM_VALUES' => '<strong>Edited Forum Point Values</strong>',
	'LOG_MOD_POINTS_LOTTERY' => '<strong>Edited Lottery settings</strong>',
	'LOG_MOD_POINTS_RANDOM' => '<strong>Random points won by</strong><br />» %1$s',
	'LOG_MOD_POINTS_ROBBERY' => '<strong>Edited Robbery settings</strong>',
	'LOG_MOD_POINTS_SETTINGS' => '<strong>Edited Points settings</strong>',
	'LOG_RESYNC_LOTTERY_HISTORY' => '<strong>The lottery history was reset successfully</strong>',
	'LOG_RESYNC_POINTSCOUNTS' => '<strong>All users points were reset successfully</strong>',
	'LOG_RESYNC_POINTSLOGSCOUNTS' => '<strong>All user logs were reset successfully</strong>',
]);
