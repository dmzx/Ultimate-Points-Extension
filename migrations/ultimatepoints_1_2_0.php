<?php
/**
*
* @package phpBB Extension - Ultimate Points
* @copyright (c) 2016 dmzx & posey - https://www.dmzx-web.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace dmzx\ultimatepoints\migrations;

class ultimatepoints_1_2_0 extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array(
			'\dmzx\ultimatepoints\migrations\ultimatepoints_1_1_9',
		);
	}

	public function update_data()
	{
		return array(
			array('config.update', array('ultimate_points_version', '1.2.0')),
			array('config.add', array('points_icon_mainicon', 'fa-university')),
			array('config.add', array('points_icon_uplist', 'fa-users')),
			array('config.add', array('points_name_uplist', 'UP List')),
			array('config.add', array('points_name', 'Points')),
			array('config.add', array('points_enable', 1)),
			array('config.add', array('points_icon_bankicon', 'fa-money')),
			array('config.add', array('points_icon_lotteryicon', 'fa-ticket')),
		);
	}
}
