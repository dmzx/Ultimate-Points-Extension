<?php
/**
*
* @package phpBB Extension - Ultimate Points
* @copyright (c) 2016 dmzx & posey - http://www.dmzx-web.net
* Nederlandse vertaling @ Solidjeuh <https://www.froddelpower.be>
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
// ’ » “ ” …

$lang = array_merge($lang, array(
	'ACL_CAT_POINTS'		=> 'Ultimate Points',
	'ACL_U_USE_POINTS'		=> 'Kan Ultimate Points gebruiken',
	'ACL_U_USE_ROBBERY'		=> 'Kan de Overval Module gebruiken',
	'ACL_U_USE_BANK'		=> 'kan de Bank Module gebruiken',
	'ACL_U_USE_LOGS'		=> 'Kan de Log Module gebruiken',
	'ACL_U_USE_LOTTERY'		=> 'Kan de Lotto Module gebruiken',
	'ACL_U_USE_TRANSFER'	=> 'Kan de Overschrijving Module gebruiken',
	'ACL_F_PAY_ATTACHMENT'	=> 'Moet betalen om bijlagen te downloaden',
	'ACL_F_PAY_TOPIC'		=> 'Moet betalen om een nieuwe onderwerp te maken',
	'ACL_F_PAY_POST'		=> 'Moet betalen om een nieuw bericht te maken',
	'ACL_M_CHG_POINTS'		=> 'Kan gebruikers punten wijzigen',
	'ACL_M_CHG_BANK'		=> 'Kan gebruikers Bank punten wijzigen',
	'ACL_A_POINTS'			=> 'Kan Ultimate Points beheren',
));
