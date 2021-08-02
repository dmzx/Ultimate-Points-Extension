<?php
/**
*
* @package phpBB Extension - Ultimate Points
* @copyright (c) 2016 dmzx & posey - https://www.dmzx-web.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace dmzx\ultimatepoints\migrations;

class ultimatepoints_1_2_2 extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return [
			'\dmzx\ultimatepoints\migrations\ultimatepoints_1_2_1',
        ];
	}

	public function update_data()
	{
		return [
			['config.update', ['ultimate_points_version', '1.2.2']],
			['config.add', ['lottery_mchat_enable', 0]],
			['config.add', ['robbery_mchat_enable', 0]],
			['config.add', ['transfer_mchat_enable', 0]],
        ];
	}
}
