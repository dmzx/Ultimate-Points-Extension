<?php
/**
*
* @version $Id: permissions_ultimatepoints.php 156 2018-05-18 20:34:31Z Scanialady $
* @package phpBB Extension - Ultimate Points (DEUTSCH)
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
	$lang = array();
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
// ‚ ‘ ’ « » „ “ ” …

$lang = array_merge($lang, array(
	'ACL_CAT_POINTS'		=> 'Ultimate Points',
	'ACL_U_USE_POINTS'		=> 'Kann Ultimate Points benutzen',
	'ACL_U_USE_ROBBERY'		=> 'Kann Diebstahlmodul benutzen',
	'ACL_U_USE_BANK'		=> 'Kann Bankmodul benutzen',
	'ACL_U_USE_LOGS'		=> 'Kann Protokollmodul benutzen',
	'ACL_U_USE_LOTTERY'		=> 'Kann Lotteriemodul benutzen',
	'ACL_U_USE_TRANSFER'	=> 'Kann Überweisungsmodul benutzen',
	'ACL_F_PAY_ATTACHMENT'	=> 'Muss zahlen für Dateianhang-Download',
	'ACL_F_PAY_TOPIC'		=> 'Muss zahlen für Erstellung neuer Themen',
	'ACL_F_PAY_POST'		=> 'Muss zahlen für Erstellung neuer Beiträge',
	'ACL_M_CHG_POINTS'		=> 'Kann Benutzerpunkte ändern',
	'ACL_M_CHG_BANK'		=> 'Kann Bankguthaben der Benutzer ändern',
	'ACL_A_POINTS'			=> 'Kann Ultimate Points administrieren',
));
