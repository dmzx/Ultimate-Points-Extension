<?php
/**
 *
 * @package phpBB Extension - 终极积分
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
	'ACP_POINTS_BANK_EXPLAIN' => '在这里，您可以修改银行模块的设置。',
    'ACP_POINTS_DEACTIVATED' => '终极积分 当前已禁用！',
    'ACP_POINTS_FORUM_EXPLAIN' => '在这里，您可以一次性为所有版块设置默认的论坛积分。非常适合首次设置。<br />请注意，这些设置适用于<strong>所有</strong>版块。如果您手动更改了任何版块的积分设置，使用此选项后需要重新设置！',
    'ACP_POINTS_INDEX_EXPLAIN' => '在这里，您可以修改 终极积分 的常规设置。',
    'ACP_POINTS_LOTTERY_EXPLAIN' => '在这里，您可以修改彩票模块的设置。',
    'ACP_POINTS_ROBBERY_EXPLAIN' => '在这里，您可以修改抢劫模块的设置。',
    'ACP_POINTS_VALUES_HINT' => '<strong>提示：</strong>始终输入不带千位分隔符的值<br />并使用小数点，例如 1000.50',
    'ACP_POINTS_MAINICON' => '选择主图标',
    'ACP_POINTS_MAINICON_EXPLAIN' => '点击名称以选择新的 Font Awesome 图标。<br />查看 <samp><strong><a href="https://fontawesome.com/v4.7.0/icons/" title="Font Awesome">Font Awesome</a></strong></samp> 获取更多信息。',
    'ACP_POINTS_UPLIST' => '选择用户列表图标',
    'ACP_POINTS_UPLIST_EXPLAIN' => '点击名称以选择新的 Font Awesome 图标。<br />查看 <samp><strong><a href="https://fontawesome.com/v4.7.0/icons/" title="Font Awesome">Font Awesome</a></strong></samp> 获取更多信息。',
    'ACP_POINTS_LOTTERYICON' => '选择彩票图标',
    'ACP_POINTS_LOTTERYICON_EXPLAIN' => '点击名称以选择新的 Font Awesome 图标。<br />查看 <samp><strong><a href="https://fontawesome.com/v4.7.0/icons/" title="Font Awesome">Font Awesome</a></strong></samp> 获取更多信息。',
    'ACP_POINTS_BANKICON' => '选择银行图标',
    'ACP_POINTS_BANKICON_EXPLAIN' => '点击名称以选择新的 Font Awesome 图标。<br />查看 <samp><strong><a href="https://fontawesome.com/v4.7.0/icons/" title="Font Awesome">Font Awesome</a></strong></samp> 获取更多信息。',

    'BANK_COST' => '维护银行账户的费用',
    'BANK_COST_EXPLAIN' => '在这里设置用户每个周期需要支付的银行账户费用。（设置为 0 以禁用此功能）',
    'BANK_ENABLE' => '启用银行模块',
    'BANK_ENABLE_EXPLAIN' => '这将允许用户使用银行模块。',
    'BANK_FEES' => '取款费用',
    'BANK_FEES_ERROR' => '取款费用不能高于 100%！',
    'BANK_FEES_EXPLAIN' => '用户从银行取款时需要支付的百分比（%）。',
    'BANK_INTEREST' => '利率',
    'BANK_INTERESTCUT' => '在以下金额时禁用利息',
    'BANK_INTERESTCUTP' => '（设置为 0 以禁用此功能）',
    'BANK_INTERESTCUT_EXPLAIN' => '这是用户获得利息的最大金额。如果用户拥有更多，则设置的值是上限！设置为 0 以禁用此功能。',
    'BANK_INTEREST_ERROR' => '利率不能高于 100%！',
    'BANK_INTEREST_EXPLAIN' => '利息的百分比（%）。',
    'BANK_MINDEPOSIT' => '最小存款',
    'BANK_MINDEPOSIT_EXPLAIN' => '用户可以在银行中存款的最小金额。',
    'BANK_MINWITHDRAW' => '最小取款',
    'BANK_MINWITHDRAW_EXPLAIN' => '用户可以从银行取款的最小金额。',
    'BANK_NAME' => '银行名称',
    'BANK_NAME_EXPLAIN' => '为您的银行输入一个名称，例如“我们的论坛银行”。',
    'BANK_OPTIONS' => '银行设置',
    'BANK_PAY' => '利息支付时间',
    'BANK_PAY_EXPLAIN' => '银行支付之间的时间周期。',
    'BANK_TIME' => '天',
    'BANK_VIEW' => '启用积分银行',
    'BANK_VIEW_EXPLAIN' => '这将启用银行模块。',

    'FORUM_OPTIONS' => '论坛积分',
    'FORUM_PEREDIT' => '每次编辑的积分',
    'FORUM_PEREDIT_EXPLAIN' => '在此输入用户<strong>编辑</strong>帖子时将获得的积分。请注意，他们还将获得您在高级积分设置中定义的额外积分。<br />设置为 0 以禁用此版块的积分奖励。',
    'FORUM_PERPOST' => '每次发帖的积分',
    'FORUM_PERPOST_EXPLAIN' => '在此输入用户<strong>发帖（回复）</strong>时将获得的积分。请注意，他们还将获得您在高级积分设置中定义的额外积分。<br />设置为 0 以禁用此版块的积分奖励。这样也会禁用此版块的高级积分设置！',
    'FORUM_PERTOPIC' => '每次发主题的积分',
    'FORUM_PERTOPIC_EXPLAIN' => '在此输入用户<strong>发布新主题</strong>时将获得的积分。请注意，他们还将获得您在高级积分设置中定义的额外积分。<br />设置为 0 以禁用此版块的积分奖励。这样也会禁用此版块的高级积分设置！',
    'FORUM_COST' => '每次下载附件的积分',
    'FORUM_COST_EXPLAIN' => '在此输入用户<strong>下载附件</strong>时需要支付的积分。<br />设置为 0 以禁用此功能。',
    'FORUM_COST_TOPIC' => '发布新主题需要支付的积分',
    'FORUM_COST_TOPIC_EXPLAIN' => '在此输入用户在此版块发布新主题时需要支付的积分。',
    'FORUM_COST_POST' => '发布新帖子需要支付的积分',
    'FORUM_COST_POST_EXPLAIN' => '在此输入用户在此版块发布新帖子时需要支付的积分。',
    'FORUM_POINT_SETTINGS' => '终极积分 设置',
    'FORUM_POINT_SETTINGS_EXPLAIN' => '在这里，您可以设置用户发布新主题、新帖子（回复）和编辑帖子时将获得的积分。这些设置是基于每个版块的。这样您可以非常详细地控制用户在哪些地方获得积分。',
    'FORUM_POINT_SETTINGS_UPDATED' => '全局论坛积分已更新',
    'FORUM_POINT_UPDATE' => '更新全局论坛积分',
    'FORUM_POINT_UPDATE_CONFIRM' => '<br />您确定要用给定的值更新所有论坛积分吗？<br />此步骤将覆盖所有当前设置且不可逆！',

    'LOTTERY_BASE_AMOUNT' => '基础奖池',
    'LOTTERY_BASE_AMOUNT_EXPLAIN' => '奖池将从此金额开始。如果在抽奖期间增加，额外的金额将添加到下一次抽奖中。奖池不会因降低而减少。',
    'LOTTERY_CHANCE' => '赢得奖池的机会',
    'LOTTERY_CHANCE_ERROR' => '赢得奖池的机会不能高于 100%！',
    'LOTTERY_CHANCE_EXPLAIN' => '在这里，您可以设置赢得奖池的百分比。（值越高，赢得的机会越大）',
    'LOTTERY_DISPLAY_STATS' => '在首页显示下次抽奖时间',
    'LOTTERY_DISPLAY_STATS_EXPLAIN' => '这将在首页显示下次彩票抽奖时间。',
    'LOTTERY_DRAW_PERIOD' => '抽奖周期',
    'LOTTERY_DRAW_PERIOD_EXPLAIN' => '每次抽奖之间的时间（小时）。更改此设置将影响当前抽奖日期/时间。设置为 0 以禁用抽奖，当前票数/奖池将保留。',
    'LOTTERY_DRAW_PERIOD_SHORT' => '抽奖周期必须大于 0！',
    'LOTTERY_ENABLE' => '启用彩票模块',
    'LOTTERY_ENABLE_EXPLAIN' => '这将允许用户使用彩票模块。',
    'LOTTERY_MAX_TICKETS' => '最大票数',
    'LOTTERY_MAX_TICKETS_EXPLAIN' => '设置用户可以购买的最大票数。',
    'LOTTERY_MCHAT_OPTIONS' => '彩票 mChat 设置',
    'LOTTERY_MCHAT_ENABLE' => '启用彩票的 mChat 发布',
    'LOTTERY_MCHAT_ENABLE_EXPLAIN' => '在 mChat 中发布购买的票数和奖池赢家。',
    'LOTTERY_MULTI_TICKETS' => '允许多张票',
    'LOTTERY_MULTI_TICKETS_EXPLAIN' => '设置为“是”以允许用户购买多张票。',
    'LOTTERY_NAME' => '彩票名称',
    'LOTTERY_NAME_EXPLAIN' => '为您的彩票输入一个名称，例如“我们的论坛彩票”。',
    'LOTTERY_OPTIONS' => '彩票设置',
    'LOTTERY_PM_ID' => '发件人 ID',
    'LOTTERY_PM_ID_EXPLAIN' => '在此输入将用作幸运赢家私信发件人的用户 ID。（0 = 使用赢家的 ID）',
    'LOTTERY_TICKET_COST' => '票的成本',
    'LOTTERY_VIEW' => '启用积分彩票',
    'LOTTERY_VIEW_EXPLAIN' => '这将启用彩票模块。',

    'NO_RECIPIENT' => '未定义收件人。',

    'POINTS_ADV_OPTIONS' => '高级积分设置',
    'POINTS_ADV_OPTIONS_EXPLAIN' => '如果论坛积分设置为 0（禁用），则此处所有设置均不计算。',
    'POINTS_ATTACHMENT' => '在帖子中添加附件的一般积分',
    'POINTS_ATTACHMENT_PER_FILE' => '每个文件附件的额外积分',
    'POINTS_BONUS_CHANCE' => '积分奖励机会',
    'POINTS_BONUS_CHANCE_EXPLAIN' => '用户发布新主题、帖子或编辑时获得奖励积分的机会。<br />机会介于 0 到 100% 之间，您可以使用小数。<br />设置为 <strong>0</strong> 以禁用此功能。',
    'POINTS_BONUS_VALUE' => '积分奖励值',
    'POINTS_BONUS_VALUE_EXPLAIN' => '给出我们将从中选择随机奖励金额的边界。<br />如果您想要固定金额，请将最小值和最大值设置为相同。',
    'POINTS_COMMENTS' => '允许评论',
    'POINTS_COMMENTS_EXPLAIN' => '允许用户在积分转账/捐赠时留下评论。',
    'POINTS_CONFIG_SUCCESS' => '终极积分 设置已成功更新',
    'POINTS_DISABLEMSG' => '禁用消息',
    'POINTS_DISABLEMSG_EXPLAIN' => '当 终极积分 系统禁用时显示的消息。',
    'POINTS_ENABLE' => '启用积分',
    'POINTS_ENABLE_EXPLAIN' => '允许用户使用 终极积分。',
    'POINTS_GROUP_TRANSFER' => '群组转账',
    'POINTS_GROUP_TRANSFER_ADD' => '添加',
    'POINTS_GROUP_TRANSFER_EXPLAIN' => '在这里，您可以为某个群组添加、减去或设置值。您还可以向群组的每个成员发送私信。方便您发送例如圣诞祝福和小礼物（您可以使用表情和 bbCodes）。如果您不想在转账时发送私信，只需将主题和评论字段留空。',
    'POINTS_GROUP_TRANSFER_FUNCTION' => '功能',
    'POINTS_GROUP_TRANSFER_PM_COMMENT' => '私信的评论',
    'POINTS_GROUP_TRANSFER_PM_ERROR' => '您需要输入主题<strong>和</strong>评论才能发送带有群组转账的私信！',
    'POINTS_GROUP_TRANSFER_PM_SUCCESS' => '群组转账已成功处理，<br />群组成员已收到您的私信。',
    'POINTS_GROUP_TRANSFER_PM_TITLE' => '私信的主题',
    'POINTS_GROUP_TRANSFER_SEL_ERROR' => '您不能向“机器人”和“访客”群组进行群组转账！',
    'POINTS_GROUP_TRANSFER_SET' => '设置',
    'POINTS_GROUP_TRANSFER_SUBSTRACT' => '减去',
    'POINTS_GROUP_TRANSFER_SUCCESS' => '群组转账已成功处理。',
    'POINTS_GROUP_TRANSFER_USER' => '用户群组',
    'POINTS_GROUP_TRANSFER_VALUE' => '值',

    'POINTS_ICON_PLACEHOLDER' => '点击选择',
    'POINTS_IMAGES_MEMBERLIST' => '在个人资料中显示图标而不是积分名称',
    'POINTS_IMAGES_MEMBERLIST_EXPLAIN' => '在用户个人资料中显示 Font Awesome 图标而不是积分名称。',
    'POINTS_IMAGES_TOPIC' => '显示图标而不是积分名称',
    'POINTS_IMAGES_TOPIC_EXPLAIN' => '在主题中显示 Font Awesome 图标而不是积分名称。',
    'POINTS_LOGS' => '启用积分日志',
    'POINTS_LOGS_EXPLAIN' => '允许用户查看转账日志。',
    'POINTS_MINIMUM' => '&nbsp;最小值', // &nbsp; 用于对齐积分奖励值的输入框
    'POINTS_MAXIMUM' => '最大值',
    'POINTS_NAME' => '积分名称',
    'POINTS_NAME_EXPLAIN' => '您希望在论坛上显示的积分名称，而不是“Points”。',
    'POINTS_NAME_UPLIST' => '用户列表名称',
    'POINTS_NAME_UPLIST_EXPLAIN' => '您希望在论坛上显示的用户列表名称，而不是“UP List”。',
    'POINTS_POLL' => '每次新投票的积分',
    'POINTS_POLL_PER_OPTION' => '每次投票选项的积分',
    'POINTS_POST_PER_CHARACTER' => '新帖子中每个字符的积分',
    'POINTS_POST_PER_WORD' => '新帖子中每个单词的积分',
    'POINTS_SHOW_PER_PAGE' => '每页显示的条目数',
    'POINTS_SHOW_PER_PAGE_ERROR' => '每页显示的条目数至少需要 5 条。',
    'POINTS_SHOW_PER_PAGE_EXPLAIN' => '在此输入日志和彩票历史中每页应显示的条目数。（最少 5 条）',
    'POINTS_SMILIES' => '表情',
    'POINTS_STATS' => '在首页显示积分统计',
    'POINTS_STATS_EXPLAIN' => '在论坛首页显示积分统计。',
    'POINTS_TOPIC_PER_CHARACTER' => '新主题中每个字符的积分',
    'POINTS_TOPIC_PER_WORD' => '新主题中每个单词的积分',
    'POINTS_TRANSFER' => '允许转账',
    'POINTS_TRANSFER_EXPLAIN' => '允许用户相互转账/捐赠积分。',
    'POINTS_TRANSFER_FEE' => '转账费用',
    'POINTS_TRANSFER_FEE_EXPLAIN' => '每次转账时扣除的百分比。',
    'POINTS_TRANSFER_FEE_ERROR' => '转账费用不能为 100% 或更高。',
    'POINTS_TRANSFER_PM' => '通过私信通知用户转账',
    'POINTS_TRANSFER_PM_EXPLAIN' => '允许用户在有人向他们发送积分时收到私信通知。',
    'POINTS_WARN' => '每次用户警告时扣除的积分（设置为 0 以禁用此功能）',

    'REG_POINTS_BONUS' => '注册积分奖励',
    'RESYNC_ATTENTION' => '以下操作无法撤消！！',
    'RESYNC_DESC' => '重置用户积分和日志',
    'RESYNC_LOTTERY_HISTORY' => '重置彩票历史',
    'RESYNC_LOTTERY_HISTORY_CONFIRM' => '您确定要重置彩票历史吗？<br />注意：此操作无法撤消！',
    'RESYNC_LOTTERY_HISTORY_EXPLAIN' => '这将重置完整的彩票历史。',
    'RESYNC_POINTS' => '重置用户积分',
    'RESYNC_POINTSLOGS' => '重置用户日志',
    'RESYNC_POINTSLOGS_CONFIRM' => '您确定要重置用户日志吗？<br />注意：此操作无法撤消！',
    'RESYNC_POINTSLOGS_EXPLAIN' => '删除所有用户日志。',
    'RESYNC_POINTS_CONFIRM' => '您确定要重置所有用户积分吗？<br />注意：此操作无法撤消！',
    'RESYNC_POINTS_EXPLAIN' => '将所有用户的积分账户重置为零。',
    'ROBBERY_CHANCE' => '成功抢劫的机会',
    'ROBBERY_CHANCE_ERROR' => '成功抢劫的机会不能高于 100%！',
    'ROBBERY_CHANCE_EXPLAIN' => '在这里，您可以设置成功抢劫的百分比。（值越高，成功的机会越大）',
    'ROBBERY_CHANCE_MINIMUM' => '成功抢劫的机会必须高于 0%！',
    'ROBBERY_ENABLE' => '启用抢劫模块',
	'ROBBERY_ENABLE_EXPLAIN' => '这将允许用户使用抢劫模块。',
    'ROBBERY_LOOSE' => '抢劫失败的惩罚',
    'ROBBERY_LOOSE_ERROR' => '抢劫失败的惩罚不能高于100%！',
    'ROBBERY_LOOSE_EXPLAIN' => '如果用户抢劫失败，尝试抢劫他人的用户将损失抢劫目标金额的x%。',
    'ROBBERY_LOOSE_MINIMUM' => '抢劫失败的惩罚不应为0%。你真的应该给抢劫者一些惩罚！',
    'ROBBERY_MAX_ROB' => '最大抢劫百分比',
    'ROBBERY_MAX_ROB_ERROR' => '你不能设置高于100%的值！',
    'ROBBERY_MAX_ROB_EXPLAIN' => '此值是用户现金金额的百分比，表示一次可以抢劫的最大金额。',
    'ROBBERY_MAX_ROB_MINIMUM' => '最大抢劫值应高于0%。否则此选项没有意义！',
    'ROBBERY_MCHAT_OPTIONS' => '抢劫mChat设置',
    'ROBBERY_MCHAT_ENABLE' => '启用mChat中的抢劫消息',
    'ROBBERY_MCHAT_ENABLE_EXPLAIN' => '发布抢劫失败和成功的消息。',
    'ROBBERY_NOTIFY' => '向被抢劫用户发送通知',
    'ROBBERY_NOTIFY_EXPLAIN' => '这将激活向被攻击用户发送通知的选项。',
    'ROBBERY_OPTIONS' => '抢劫设置',

    'TOP_POINTS' => '显示的顶级富豪成员数量',
    'TOP_POINTS_EXPLAIN' => '在这里你可以设置显示的顶级富豪用户数量。适用于不同的视图。',
    'TRANSFER_MCHAT_ENABLE' => '启用mChat中的转账消息',
    'TRANSFER_MCHAT_ENABLE_EXPLAIN' => '发布用户之间的转账消息。',
    'TRANSFER_MCHAT_OPTIONS' => '转账mChat设置',

    'UPLIST_ENABLE' => '启用终极积分列表',
    'UPLIST_ENABLE_EXPLAIN' => '允许用户使用终极积分列表。',
    'USER_POINTS' => '用户积分',
    'USER_POINTS_EXPLAIN' => '用户拥有的积分数。',
]);
