<?php
/**
 *
 * @package phpBB Extension - Ultimate Points
 * @copyright (c) 2026 dmzx - https://www.dmzx-web.net
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace dmzx\ultimatepoints\core;

use phpbb\db\driver\driver_interface;

/**
 * Public integration surface for third-party extensions.
 *
 * functions_points is the internal implementation and may change shape
 * between releases (methods added/removed, internal-only helpers like
 * run_lottery(), mchat_message(), points_config plumbing). The methods
 * on this class are a committed, stable contract instead: their names
 * and signatures will not change without a major version bump.
 *
 * Third-party extensions that want to add/substract/read a user's
 * points should inject this service rather than functions_points
 * directly, e.g.:
 *
 *   dmzx.ultimatepoints.api: '@?dmzx.ultimatepoints.api'
 *
 * and check for null (or $phpbb_container->has('dmzx.ultimatepoints.api'))
 * before using it, since the extension may not be installed.
 *
 * Combine with the dmzx.ultimatepoints.* dispatcher events
 * (add_points_after, substract_points_after, set_points_after,
 * set_bank_after, transfer_after, robbery_after, lottery_winner_selected,
 * group_transfer_after, resync_after, bounty_posted_after,
 * bounty_claimed_after, bounty_paid_out, bounty_cancelled_after)
 * to react to point mutations instead of querying the database directly.
 *
 * @since 1.2.9
 */
class api
{
	/** @var functions_points */
	protected $functions_points;

	/** @var driver_interface */
	protected $db;

	/**
	 * Constructor
	 *
	 * @param functions_points $functions_points
	 * @param driver_interface $db
	 */
	public function __construct(
		functions_points $functions_points,
		driver_interface $db
	)
	{
		$this->functions_points = $functions_points;
		$this->db = $db;
	}

	/**
	 * Add points to a user. Dispatches dmzx.ultimatepoints.add_points_after.
	 *
	 * @param int $user_id
	 * @param float $amount
	 * @return void
	 */
	public function add_points($user_id, $amount)
	{
		$this->functions_points->add_points($user_id, $amount);
	}

	/**
	 * Substract points from a user. Dispatches dmzx.ultimatepoints.substract_points_after.
	 *
	 * @param int $user_id
	 * @param float $amount
	 * @return void
	 */
	public function substract_points($user_id, $amount)
	{
		$this->functions_points->substract_points($user_id, $amount);
	}

	/**
	 * Set a user's points to a fixed amount. Dispatches dmzx.ultimatepoints.set_points_after.
	 *
	 * @param int $user_id
	 * @param float $amount
	 * @return void
	 */
	public function set_points($user_id, $amount)
	{
		$this->functions_points->set_points($user_id, $amount);
	}

	/**
	 * Get a user's current points balance.
	 *
	 * @param int $user_id
	 * @return float
	 */
	public function get_points($user_id)
	{
		$sql = 'SELECT user_points
			FROM ' . USERS_TABLE . '
			WHERE user_id = ' . (int) $user_id;
		$result = $this->db->sql_query($sql);
		$points = (float) $this->db->sql_fetchfield('user_points');
		$this->db->sql_freeresult($result);

		return $points;
	}

	/**
	 * Get the top N users by points balance, richest first. Same query
	 * shape as the "richest users" widget on the Points index page.
	 *
	 * @param int $limit
	 * @return array Rows: user_id, username, user_colour, user_points
	 */
	public function get_top_balances($limit = 10)
	{
		$sql_array = [
			'SELECT' => 'user_id, username, user_colour, user_points',
			'FROM' => [
				USERS_TABLE => 'u',
			],
			'WHERE' => 'user_points > 0',
			'ORDER_BY' => 'user_points DESC, username_clean ASC',
		];
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query_limit($sql, $limit);

		$rows = [];
		while ($row = $this->db->sql_fetchrow($result))
		{
			$rows[] = $row;
		}
		$this->db->sql_freeresult($result);

		return $rows;
	}

	/**
	 * Format a points amount for display (e.g. "1,234.56 Points").
	 *
	 * @param float $amount
	 * @return string
	 */
	public function format_points($amount)
	{
		return $this->functions_points->number_format_points($amount);
	}

	/**
	 * Get the display name of the points currency (e.g. "Points", "Coins").
	 *
	 * @return string
	 */
	public function get_points_name()
	{
		return $this->functions_points->get_name();
	}
}
