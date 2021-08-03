<?php
/**
 *
 * @package phpBB Extension - Ultimate Points
 * @copyright (c) 2021 dmzx https://www.dmzx-web.net
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace dmzx\ultimatepoints\migrations;

use phpbb\db\migration\migration;

class ultimatepoints_1_2_7 extends migration
{
	static public function depends_on()
	{
		return [
			'\dmzx\ultimatepoints\migrations\ultimatepoints_1_2_6',
		];
	}

	public function update_data()
	{
		return [
			['config.update', ['ultimate_points_version', '1.2.7']],
		];
	}
}
