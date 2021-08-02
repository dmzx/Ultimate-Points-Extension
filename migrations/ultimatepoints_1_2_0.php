<?php
/**
 *
 * @package phpBB Extension - Ultimate Points
 * @copyright (c) 2016 dmzx & posey - https://www.dmzx-web.net
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace dmzx\ultimatepoints\migrations;

use phpbb\db\migration\migration;

class ultimatepoints_1_2_0 extends migration
{
	static public function depends_on()
	{
		return [
			'\dmzx\ultimatepoints\migrations\ultimatepoints_1_1_9',
		];
	}

	public function update_data()
	{
		return [
			['config.update', ['ultimate_points_version', '1.2.0']],
			['config.add', ['points_icon_mainicon', 'fa-university']],
			['config.add', ['points_icon_uplist', 'fa-users']],
			['config.add', ['points_name_uplist', 'UP List']],
			['config.add', ['points_name', 'Points']],
			['config.add', ['points_enable', 1]],
			['config.add', ['points_icon_bankicon', 'fa-money']],
			['config.add', ['points_icon_lotteryicon', 'fa-ticket']],
		];
	}
}
