<?php
/**
 *
 * @package phpBB Extension - Ultimate Points
 * @copyright (c) 2021 dmzx & posey - https://www.dmzx-web.net
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
	'ACP_POINTS_BANK_EXPLAIN' => 'Here you can alter the settings for the Bank module.',
	'ACP_POINTS_DEACTIVATED' => 'Ultimate Points is currently disabled!',
	'ACP_POINTS_FORUM_EXPLAIN' => 'Here you can set the default forum points for all forums at once. So ideal for your first settings.<br />Please keep in mind, that these settings are for <strong>ALL</strong> forums. So if you manually changed any of your forum points settings with individual values, you need to redone this after using this option!',
	'ACP_POINTS_INDEX_EXPLAIN' => 'Here you can alter the General settings of Ultimate Points.',
	'ACP_POINTS_LOTTERY_EXPLAIN' => 'Here you can alter the settings of the Lottery module.',
	'ACP_POINTS_ROBBERY_EXPLAIN' => 'Here you can alter the settings of the Robbery module.',
	'ACP_POINTS_VALUES_HINT' => '<strong>Hint: </strong>Always enter values without the thousands separator<br />and decimals with a point, i.e. 1000.50',
	'ACP_POINTS_MAINICON' => 'Select main icon',
	'ACP_POINTS_MAINICON_EXPLAIN' => 'Click on name to select new Font Awesome icon.<br />See <samp><strong><a href="https://fontawesome.com/v4.7.0/icons/" title="Font Awesome">Font Awesome</a></strong></samp> for more info.',
	'ACP_POINTS_UPLIST' => 'Select icon for userlist',
	'ACP_POINTS_UPLIST_EXPLAIN' => 'Click on name to select new Font Awesome icon.<br />See <samp><strong><a href="https://fontawesome.com/v4.7.0/icons/" title="Font Awesome">Font Awesome</a></strong></samp> for more info.',
	'ACP_POINTS_LOTTERYICON' => 'Select icon for lottery',
	'ACP_POINTS_LOTTERYICON_EXPLAIN' => 'Click on name to select new Font Awesome icon.<br />See <samp><strong><a href="https://fontawesome.com/v4.7.0/icons/" title="Font Awesome">Font Awesome</a></strong></samp> for more info.',
	'ACP_POINTS_BANKICON' => 'Select icon for bank',
	'ACP_POINTS_BANKICON_EXPLAIN' => 'Click on name to select new Font Awesome icon.<br />See <samp><strong><a href="https://fontawesome.com/v4.7.0/icons/" title="Font Awesome">Font Awesome</a></strong></samp> for more info.',

	'BANK_COST' => 'The cost for maintaining a bank account',
	'BANK_COST_EXPLAIN' => 'Here you set the price, that users have to pay every period for their bank account. (set 0 to disable this feature)',
	'BANK_ENABLE' => 'Enable bank module',
	'BANK_ENABLE_EXPLAIN' => 'This will allow users to use the bank module.',
	'BANK_FEES' => 'Withdraw fees',
	'BANK_FEES_ERROR' => 'The withdraw fees cannot be higher than 100% !!',
	'BANK_FEES_EXPLAIN' => 'The amount in percent (%) that users will have to pay, when they withdraw from the bank.',
	'BANK_INTEREST' => 'Interest rate',
	'BANK_INTERESTCUT' => 'Disable interest at',
	'BANK_INTERESTCUTP' => '(set 0 to disable this feature)',
	'BANK_INTERESTCUT_EXPLAIN' => 'This is the maximum amount for which a user will get the interest rate. If they own more, the set value is the maximum! Set 0 to deactivate this feature.',
	'BANK_INTEREST_ERROR' => 'The interest rate cannot be higher than 100% !!',
	'BANK_INTEREST_EXPLAIN' => 'The amount in % of interest.',
	'BANK_MINDEPOSIT' => 'Min. deposit',
	'BANK_MINDEPOSIT_EXPLAIN' => 'The minimum amount that users can deposit in the bank.',
	'BANK_MINWITHDRAW' => 'Min. withdraw',
	'BANK_MINWITHDRAW_EXPLAIN' => 'The minimum amount that users can withdraw from the bank.',
	'BANK_NAME' => 'Name of your bank',
	'BANK_NAME_EXPLAIN' => 'Enter a name for your bank, i.e. Our Forum Bank.',
	'BANK_OPTIONS' => 'Bank Settings',
	'BANK_PAY' => 'Interest payment time',
	'BANK_PAY_EXPLAIN' => 'The time period between bank payments.',
	'BANK_TIME' => 'days',
	'BANK_VIEW' => 'Enable points bank',
	'BANK_VIEW_EXPLAIN' => 'This will enable the bank module.',

	'FORUM_OPTIONS' => 'Forum Points',
	'FORUM_PEREDIT' => 'Points Per Edit',
	'FORUM_PEREDIT_EXPLAIN' => 'Enter here, how much points users will receive for <strong>editing</strong> a post. Please be aware, that they will also receive additional points, which you defined in the advanced points settings.<br />Set to 0 to disable receiving points for this forum.',
	'FORUM_PERPOST' => 'Points Per Post',
	'FORUM_PERPOST_EXPLAIN' => 'Enter here, how much points users will receive for placing <strong>posts (replies)</strong>. Please be aware, that they will also receive additional points, which you defined in the advanced points settings.<br />Set to 0 to disable receiving points for this forum. This way also the advanced points settings are disabled for this forum!',
	'FORUM_PERTOPIC' => 'Points Per Topic',
	'FORUM_PERTOPIC_EXPLAIN' => 'Enter here, how much points users will receive for placing a <strong>new topic</strong>. Please be aware, that they will also receive additional points, which you defined in the advanced points settings.<br />Set to 0 to disable receiving points for this forum. This way also the advanced points settings are disabled for this forum!',
	'FORUM_COST' => 'Points Per Attachment Download',
	'FORUM_COST_EXPLAIN' => 'Enter here, how much points users will have to pay for <strong>downloading an attachment</strong>.<br />Set to 0 to disable this feature.',
	'FORUM_COST_TOPIC' => 'Points to pay for new topic',
	'FORUM_COST_TOPIC_EXPLAIN' => 'Enter here, how much points a user has to pay to start a new topic in this forum.',
	'FORUM_COST_POST' => 'Points to pay for new post',
	'FORUM_COST_POST_EXPLAIN' => 'Enter here, how much points a user has to pay to make a new post in this forum.',
	'FORUM_POINT_SETTINGS' => 'Ultimate Points Settings',
	'FORUM_POINT_SETTINGS_EXPLAIN' => 'Here you can setup, how much points users will gain for placing new topics, new posts (replies) and editing their posts. These settings are on a per forum basis. This way you can make it very detailed, where users will get points and where not.',
	'FORUM_POINT_SETTINGS_UPDATED' => 'Global forum points updated',
	'FORUM_POINT_UPDATE' => 'Update global forum points',
	'FORUM_POINT_UPDATE_CONFIRM' => '<br />Are you sure you want to update all forum points with the given values?<br />This step will overwrite all current settings and cannot made reversible!',

	'LOTTERY_BASE_AMOUNT' => 'Base jackpot',
	'LOTTERY_BASE_AMOUNT_EXPLAIN' => 'The Jackpot will begin initially with this amount. If raised during a draw period, additional sums will be added to the next draw. The Jackpot will not decrease if lowered.',
	'LOTTERY_CHANCE' => 'Chance to win the Jackpot',
	'LOTTERY_CHANCE_ERROR' => 'The chance to win cannot be higher than 100% !!',
	'LOTTERY_CHANCE_EXPLAIN' => 'Here you can set the percentage to win the Jackpot. (the higher the value, the bigger the chance to win)',
	'LOTTERY_DISPLAY_STATS' => 'Display next draw time on index page',
	'LOTTERY_DISPLAY_STATS_EXPLAIN' => 'This will display the next lottery draw time on the index page.',
	'LOTTERY_DRAW_PERIOD' => 'Draw period',
	'LOTTERY_DRAW_PERIOD_EXPLAIN' => 'Amount of time in hours between each draw. Changing this will affect the current draw day/time. Set to 0 to disable drawings, the current tickets/jackpot will remain.',
	'LOTTERY_DRAW_PERIOD_SHORT' => 'The draw period has to be higher than 0!',
	'LOTTERY_ENABLE' => 'Enable Lottery Module',
	'LOTTERY_ENABLE_EXPLAIN' => 'This will allow users to use the Lottery Module.',
	'LOTTERY_MAX_TICKETS' => 'Max. number of tickets',
	'LOTTERY_MAX_TICKETS_EXPLAIN' => 'Set the maximum number of tickets a user can buy.',
	'LOTTERY_MCHAT_OPTIONS' => 'Lottery mChat Settings',
	'LOTTERY_MCHAT_ENABLE' => 'Enable posting in mChat for lottery',
	'LOTTERY_MCHAT_ENABLE_EXPLAIN' => 'Post tickets bought and jackpot winner in mChat.',
	'LOTTERY_MULTI_TICKETS' => 'Allow multiple tickets',
	'LOTTERY_MULTI_TICKETS_EXPLAIN' => 'Set this to "Yes" to allow users to buy more than one ticket.',
	'LOTTERY_NAME' => 'Name of your Lottery',
	'LOTTERY_NAME_EXPLAIN' => 'Enter a name for your Lottery, i.e. Our Forum Lottery.',
	'LOTTERY_OPTIONS' => 'Lottery Settings',
	'LOTTERY_PM_ID' => 'Sender ID',
	'LOTTERY_PM_ID_EXPLAIN' => 'Enter here the user ID, which will be used as sender of the PM to the lucky winner. (0 = use the winner’s ID)',
	'LOTTERY_TICKET_COST' => 'Ticket costs',
	'LOTTERY_VIEW' => 'Enable Points Lottery',
	'LOTTERY_VIEW_EXPLAIN' => 'This will enable the Lottery Module.',

	'NO_RECIPIENT' => 'No recipient defined.',

	'POINTS_ADV_OPTIONS' => 'Advanced Points Settings',
	'POINTS_ADV_OPTIONS_EXPLAIN' => 'If Forum Points are set to 0 (disabled), all settings here are not calculated.',
	'POINTS_ATTACHMENT' => 'General points for adding attachments in a post',
	'POINTS_ATTACHMENT_PER_FILE' => 'Additional points for each file attachment',
	'POINTS_BONUS_CHANCE' => 'Point Bonus Chance',
	'POINTS_BONUS_CHANCE_EXPLAIN' => 'The chance a user receives bonus points for making a new topic, post or edit.<br />Chance is between 0 and 100%, you can use decimals.<br />Set to <strong>0</strong> to disable this feature.',
	'POINTS_BONUS_VALUE' => 'Point Bonus Value',
	'POINTS_BONUS_VALUE_EXPLAIN' => 'Give boundaries between which we will choose a random bonus amount.<br />If you want a fixed amount, set the minimum and the maximum the same.',
	'POINTS_COMMENTS' => 'Allow Comments',
	'POINTS_COMMENTS_EXPLAIN' => 'Allow users to leave comments with their points transfer/donation.',
	'POINTS_CONFIG_SUCCESS' => 'The Ultimate Points settings have been updated successfully',
	'POINTS_DISABLEMSG' => 'Disabled message',
	'POINTS_DISABLEMSG_EXPLAIN' => 'Message to display, when the Ultimate Points System is disabled.',
	'POINTS_ENABLE' => 'Enable Points',
	'POINTS_ENABLE_EXPLAIN' => 'Allow users to use Ultimate Points.',
	'POINTS_GROUP_TRANSFER' => 'Group Transfer',
	'POINTS_GROUP_TRANSFER_ADD' => 'Add',
	'POINTS_GROUP_TRANSFER_EXPLAIN' => 'Here you can add, subtract or set values for a certain group. You also may send a personal message to each member of the group. Handy, if you like to send i.e. Christmas Greetings with a small present (you can use smilies and bbCodes). If you don’ want to send a personal message with your transfer, just leave the fields subject and comment empty.',
	'POINTS_GROUP_TRANSFER_FUNCTION' => 'Function',
	'POINTS_GROUP_TRANSFER_PM_COMMENT' => 'Comment for your personal message',
	'POINTS_GROUP_TRANSFER_PM_ERROR' => 'You need to enter the subject <strong>AND</strong> the comment in order to send a personal message with your group transfer!',
	'POINTS_GROUP_TRANSFER_PM_SUCCESS' => 'The Group Transfer was processed successfully and<br />the members of the group have received your personal message.',
	'POINTS_GROUP_TRANSFER_PM_TITLE' => 'Subject for the personal message',
	'POINTS_GROUP_TRANSFER_SEL_ERROR' => 'You cannot make a group transfer to the groups Bots and Guests!',
	'POINTS_GROUP_TRANSFER_SET' => 'Set',
	'POINTS_GROUP_TRANSFER_SUBSTRACT' => 'Subtract',
	'POINTS_GROUP_TRANSFER_SUCCESS' => 'The Group Transfer was processed successfully.',
	'POINTS_GROUP_TRANSFER_USER' => 'User group',
	'POINTS_GROUP_TRANSFER_VALUE' => 'Value',

	'POINTS_ICON_PLACEHOLDER' => 'Click to select',
	'POINTS_IMAGES_MEMBERLIST' => 'Display an icon instead of points name in profile',
	'POINTS_IMAGES_MEMBERLIST_EXPLAIN' => 'Display an Font Awesome icon instead of the points name in users profiles.',
	'POINTS_IMAGES_TOPIC' => 'Display an icon instead of points name',
	'POINTS_IMAGES_TOPIC_EXPLAIN' => 'Display an Font Awesome icon in topics instead of the points name.',
	'POINTS_LOGS' => 'Enable points logs',
	'POINTS_LOGS_EXPLAIN' => 'Allow users to view transfer logs.',
	'POINTS_MINIMUM' => '&nbsp;Minimum', // &nbsp; is for alignment of input boxes for Points Bonus Value
	'POINTS_MAXIMUM' => 'Maximum',
	'POINTS_NAME' => 'Points name',
	'POINTS_NAME_EXPLAIN' => 'The name you want to display instead of the word <em>Points</em> on your board.',
	'POINTS_NAME_UPLIST' => 'Name userlist',
	'POINTS_NAME_UPLIST_EXPLAIN' => 'The name you want to display instead of the word <em>UP List</em> on your board.',
	'POINTS_POLL' => 'Points per new poll',
	'POINTS_POLL_PER_OPTION' => 'Points per option in a poll',
	'POINTS_POST_PER_CHARACTER' => 'Points per character in new posts',
	'POINTS_POST_PER_WORD' => 'Points per word in new posts',
	'POINTS_SHOW_PER_PAGE' => 'Number of entries per page',
	'POINTS_SHOW_PER_PAGE_ERROR' => 'The number per page to show needs to be at least 5 entries.',
	'POINTS_SHOW_PER_PAGE_EXPLAIN' => 'Enter here the number of entries, which should be shown per page in the logs and the lottery history. (min. 5)',
	'POINTS_SMILIES' => 'Smilies',
	'POINTS_STATS' => 'Display points statistics on index',
	'POINTS_STATS_EXPLAIN' => 'Display points statistics on the main board index page.',
	'POINTS_TOPIC_PER_CHARACTER' => 'Points per character on new topics',
	'POINTS_TOPIC_PER_WORD' => 'Points per word on new topics',
	'POINTS_TRANSFER' => 'Allow Transfers',
	'POINTS_TRANSFER_EXPLAIN' => 'Allow users to transfer/donate points to each other.',
	'POINTS_TRANSFER_FEE' => 'Transfer Fee',
	'POINTS_TRANSFER_FEE_EXPLAIN' => 'Percentage that is being helt back per transfer.',
	'POINTS_TRANSFER_FEE_ERROR' => 'Transfer Fee can not be a 100% or more.',
	'POINTS_TRANSFER_PM' => 'Notify user by PM of a transfer',
	'POINTS_TRANSFER_PM_EXPLAIN' => 'Allow users to receive a notice by PM, when somebody send points to them.',
	'POINTS_WARN' => 'Amount of points to be subtracted per user warning (set 0 to disable this feature)',

	'REG_POINTS_BONUS' => 'Registration Points Bonus',
	'RESYNC_ATTENTION' => 'The following actions cannot be undone!!',
	'RESYNC_DESC' => 'Reset User Points and Logs',
	'RESYNC_LOTTERY_HISTORY' => 'Reset the Lottery history',
	'RESYNC_LOTTERY_HISTORY_CONFIRM' => 'Are you sure, you want to reset the Lottery history?<br />Note: This action cannot be undone!',
	'RESYNC_LOTTERY_HISTORY_EXPLAIN' => 'This will reset the complete Lottery history.',
	'RESYNC_POINTS' => 'Reset users points',
	'RESYNC_POINTSLOGS' => 'Reset users logs',
	'RESYNC_POINTSLOGS_CONFIRM' => 'Are you sure, you want to reset the users logs?<br />Note: This action cannot be undone!',
	'RESYNC_POINTSLOGS_EXPLAIN' => 'Delete all users logs.',
	'RESYNC_POINTS_CONFIRM' => 'Are you sure, you want to reset all users points?<br />Note: This cannot be undone!',
	'RESYNC_POINTS_EXPLAIN' => 'Reset all users’ points accounts to zero.',
	'ROBBERY_CHANCE' => 'Chance to make a successful robbery',
	'ROBBERY_CHANCE_ERROR' => 'The chance for a successful robbery cannot be higher than 100% !!',
	'ROBBERY_CHANCE_EXPLAIN' => 'Here you can set the percentage to make a successful robbery. (the higher the value, the bigger the chance to be successful)',
	'ROBBERY_CHANCE_MINIMUM' => 'The chance for a successful robbery must be higher than 0% !!',
	'ROBBERY_ENABLE' => 'Enable Robbery Module',
	'ROBBERY_ENABLE_EXPLAIN' => 'This will allow users to use the robbery module.',
	'ROBBERY_LOOSE' => 'Penalty on failed robbery',
	'ROBBERY_LOOSE_ERROR' => 'Penalty on failed robbery cannot be higher than 100% !!',
	'ROBBERY_LOOSE_EXPLAIN' => 'If a user robbery fails, the user who tried to rob someone else will lose x% of the desired robbery value.',
	'ROBBERY_LOOSE_MINIMUM' => 'Penalty on failed robbery should not be 0%. You really should give the thieve a penalty !!',
	'ROBBERY_MAX_ROB' => 'Percentage of maximum robbery',
	'ROBBERY_MAX_ROB_ERROR' => 'You cannot set a value higher than 100% !!',
	'ROBBERY_MAX_ROB_EXPLAIN' => 'This value is the percentage of the users cash amount, which can be robbed at once.',
	'ROBBERY_MAX_ROB_MINIMUM' => 'The value for the maximum Robbery should be higher than 0%. Otherwise this option does not make sense!',
	'ROBBERY_MCHAT_OPTIONS' => 'Robbery mChat Settings',
	'ROBBERY_MCHAT_ENABLE' => 'Enable posting in mChat for robbery',
	'ROBBERY_MCHAT_ENABLE_EXPLAIN' => 'Post robbery fail and good robbery.',
	'ROBBERY_NOTIFY' => 'Send a Notification to the robbed user',
	'ROBBERY_NOTIFY_EXPLAIN' => 'This will activate the option to send a Notification to the attacked users.',
	'ROBBERY_OPTIONS' => 'Robbery Settings',

	'TOP_POINTS' => 'Number of top rich members to display',
	'TOP_POINTS_EXPLAIN' => 'Here you can set the value for the most rich users to show. Works in different views.',
	'TRANSFER_MCHAT_ENABLE' => 'Enable posting in mChat for transfer',
	'TRANSFER_MCHAT_ENABLE_EXPLAIN' => 'Post transfers from members.',
	'TRANSFER_MCHAT_OPTIONS' => 'Transfer mChat Settings',

	'UPLIST_ENABLE' => 'Enable Ultimate Points List',
	'UPLIST_ENABLE_EXPLAIN' => 'Allow users to use Ultimate Points List.',
	'USER_POINTS' => 'User Points',
	'USER_POINTS_EXPLAIN' => 'Amount of points the user owns.',
]);
