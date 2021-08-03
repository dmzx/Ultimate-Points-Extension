<?php
/**
 *
 * @package phpBB Extension - Ultimate Points
 * @copyright (c) 2016 dmzx & posey - https://www.dmzx-web.net
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace dmzx\ultimatepoints\core;

use parse_message;
use phpbb\cache\service;
use phpbb\config\config;
use phpbb\controller\helper;
use phpbb\db\driver\driver_interface;
use phpbb\extension\manager;
use phpbb\log\log;
use phpbb\request\request;
use phpbb\template\template;
use phpbb\user;
use Symfony\Component\DependencyInjection\Container;

class functions_points
{
	/** @var template */
	protected $template;

	/** @var user */
	protected $user;

	/** @var driver_interface */
	protected $db;

	/** @var helper */
	protected $helper;

	/** @var \phpbb\notification\manager */
	protected $notification_manager;

	/** @var log */
	protected $log;

	/** @var service */
	protected $cache;

	/** @var request */
	protected $request;

	/** @var config */
	protected $config;

	/** @var manager */
	protected $extension_manager;

	/** @var Container */
	protected $phpbb_container;

	/** @var string */
	protected $php_ext;

	/** @var string phpBB root path */
	protected $root_path;

	/**
	 * The database tables
	 *
	 * @var string
	 */
	protected $points_bank_table;

	protected $points_config_table;

	protected $points_lottery_history_table;

	protected $points_lottery_tickets_table;

	protected $points_values_table;

	/**
	 * Constructor
	 *
	 * @param template $template
	 * @param user $user
	 * @param driver_interface $db
	 * @param helper $helper
	 * @param \phpbb\notification\manager $notification_manager
	 * @param log $log
	 * @param service $cache
	 * @param request $request
	 * @param config $config
	 * @param manager $extension_manager
	 * @param Container $phpbb_container
	 * @param string $php_ext
	 * @param string $root_path
	 * @param string $points_bank_table
	 * @param string $points_config_table
	 * @param string $points_lottery_history_table
	 * @param string $points_lottery_tickets_table
	 * @param string $points_values_table
	 *
	 */
	public function __construct(
		template $template,
		user $user,
		driver_interface $db,
		helper $helper,
		\phpbb\notification\manager $notification_manager,
		log $log,
		service $cache,
		request $request,
		config $config,
		manager $extension_manager,
		Container $phpbb_container,
		$php_ext,
		$root_path,
		$points_bank_table,
		$points_config_table,
		$points_lottery_history_table,
		$points_lottery_tickets_table,
		$points_values_table
	)
	{
		$this->template = $template;
		$this->user = $user;
		$this->db = $db;
		$this->helper = $helper;
		$this->notification_manager = $notification_manager;
		$this->log = $log;
		$this->cache = $cache;
		$this->request = $request;
		$this->config = $config;
		$this->extension_manager = $extension_manager;
		$this->phpbb_container = $phpbb_container;
		$this->php_ext = $php_ext;
		$this->root_path = $root_path;
		$this->points_bank_table = $points_bank_table;
		$this->points_config_table = $points_config_table;
		$this->points_lottery_history_table = $points_lottery_history_table;
		$this->points_lottery_tickets_table = $points_lottery_tickets_table;
		$this->points_values_table = $points_values_table;
	}

	/**
	 * Strip text
	 */
	function strip_text($text)
	{
		//remove quotes
		$new_text = '';
		$text = explode('[quote', $text);
		$new_text .= $text[0]; //1st frame is always valid text

		for ($i = 1, $size = sizeof($text); $i < $size; $i++)
		{
			if (stristr($text[$i], '[/quote') === false) //checkout if it's a double/triple and so on quote
			{
				continue;
			}

			$item = explode('[/quote', $text[$i]);
			$last_frame = sizeof($item) - 1; //only last frame is valid text
			$new_text .= trim(substr($item[$last_frame], 10)); //remove bbcode uid
		}

		//remove code
		$text = $new_text;
		$new_text = '';
		$text = explode('[code', $text);
		$new_text .= $text[0]; //1st frame is always valid text

		for ($i = 1, $size = sizeof($text); $i < $size; $i++)
		{
			if (stristr($text[$i], '[/code') === false) //checkout if it's a double/triple and so on code
			{
				continue;
			}

			$item = explode('[/code', $text[$i]);
			$last_frame = sizeof($item) - 1; //only last frame is valid text
			$new_text .= trim(substr($item[$last_frame], 10)); //remove bbcode uid
		}

		//remove urls
		$text = $new_text;
		$new_text = '';
		$text = explode('[url', $text);
		$new_text .= $text[0]; //1st frame is always valid text

		for ($i = 1, $size = sizeof($text); $i < $size; $i++)
		{
			if (stristr($text[$i], '[/url') === false) //checkout if it's a double/triple and so on url
			{
				continue;
			}
			$item = explode('[/url', $text[$i]);
			$last_frame = sizeof($item) - 1; //only last frame is valid text
			$new_text .= trim(substr($item[$last_frame], 10)); //remove bbcode uid
		}

		//now remove the rest of bbcode
		$text = $new_text;
		$new_text = '';
		$text = explode('[', $text);
		$new_text .= $text[0]; //1st frame is always valid text

		for ($i = 1, $size = sizeof($text); $i < $size; $i++)
		{
			$item = explode(']', $text[$i]);
			if (sizeof($item) > 1) // if any part of text remains :-D
			{
				$new_text .= $item[1];
			}
		}
		$new_text = strip_tags($new_text); //remove smilies and images

		//BEGIN to remove extra spaces
		$new_text = explode(' ', $new_text);

		for ($i = 0, $size = sizeof($new_text); $i < $size; $i++)
		{
			if (trim($new_text[$i]) == '' || trim($new_text[$i]) == '&nbsp;')
			{
				unset($new_text[$i]);
			} else
			{
				$new_text[$i] = trim($new_text[$i]);
			}
		}
		//END to remove extra spaces
		return trim(join(' ', $new_text));
	}

	/**
	 * Add points to user
	 */
	function add_points($user_id, $amount)
	{
		// Select users current points
		$sql_array = [
			'SELECT' => 'user_points',
			'FROM' => [
				USERS_TABLE => 'u',
			],
			'WHERE' => 'user_id = ' . (int) $user_id,
		];
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$user_points = $this->db->sql_fetchfield('user_points');
		$this->db->sql_freeresult($result);

		// Add the points
		$data = [
			'user_points' => $user_points + $amount
		];

		$sql = 'UPDATE ' . USERS_TABLE . '
			SET ' . $this->db->sql_build_array('UPDATE', $data) . '
			WHERE user_id = ' . (int) $user_id;
		$this->db->sql_query($sql);

		return;
	}

	/**
	 * Substract points from user
	 */
	function substract_points($user_id, $amount)
	{
		// Select users current points
		$sql_array = [
			'SELECT' => 'user_points',
			'FROM' => [
				USERS_TABLE => 'u',
			],
			'WHERE' => 'user_id = ' . (int) $user_id,
		];
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$user_points = $this->db->sql_fetchfield('user_points');
		$this->db->sql_freeresult($result);

		// Update the points
		$data = [
			'user_points' => $user_points - $amount
		];

		$sql = 'UPDATE ' . USERS_TABLE . '
			SET ' . $this->db->sql_build_array('UPDATE', $data) . '
			WHERE user_id = ' . (int) $user_id;
		$this->db->sql_query($sql);

		return;
	}

	/**
	 * Set the amount of points to user
	 */
	function set_points($user_id, $amount)
	{
		// Set users new points
		$data = [
			'user_points' => $amount
		];

		$sql = 'UPDATE ' . USERS_TABLE . '
			SET ' . $this->db->sql_build_array('UPDATE', $data) . '
			WHERE user_id = ' . (int) $user_id;
		$this->db->sql_query($sql);

		return;
	}

	/**
	 * Set the amount of bank points to user
	 */
	function set_bank($user_id, $amount)
	{
		// Set users new holding
		$data = [
			'holding' => $amount
		];

		$sql = 'UPDATE ' . $this->points_bank_table . '
			SET ' . $this->db->sql_build_array('UPDATE', $data) . '
			WHERE user_id = ' . (int) $user_id;
		$this->db->sql_query($sql);

		return;
	}

	/**
	 * Preformat numbers
	 */
	function number_format_points($num)
	{
		$decimals = 2;

		return number_format($num, $decimals, $this->user->lang['POINTS_SEPARATOR_DECIMAL'], $this->user->lang['POINTS_SEPARATOR_THOUSANDS']);
	}

	/**
	 * Run Bank
	 */
	function run_bank()
	{
		// Get all values
		$points_values = $this->points_all_values();

		// time to pay users
		$time = time();
		$this->set_points_values('bank_last_restocked', $time);

		// Pay the users
		$sql = 'UPDATE ' . $this->points_bank_table . '
			SET holding = holding + round((holding / 100) * ' . $points_values['bank_interest'] . ')
			WHERE holding < ' . (int) $points_values['bank_interestcut'] . '
				OR ' . (int) $points_values['bank_interestcut'] . ' = 0';
		$this->db->sql_query($sql);

		// Maintain the bank costs
		if ($points_values['bank_cost'] <> '0')
		{
			$sql = 'UPDATE ' . $this->points_bank_table . '
				SET holding = holding - ' . (int) $points_values['bank_cost'] . '
				WHERE holding >= ' . (int) $points_values['bank_cost'] . '';
			$this->db->sql_query($sql);
		}
	}

	/**
	 * Run Lottery
	 */
	function run_lottery()
	{
		$current_time = time();

		// Get all values
		$points_values = $this->points_all_values();

		// Count number of tickets
		$sql_array = [
			'SELECT' => 'COUNT(ticket_id) AS num_tickets',
			'FROM' => [
				$this->points_lottery_tickets_table => 'l',
			],
		];
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$total_tickets = (int) $this->db->sql_fetchfield('num_tickets');
		$this->db->sql_freeresult($result);

		// Select a random user from tickets table
		$sql_layer = $this->db->get_sql_layer();

		switch ($sql_layer)
		{
			case 'postgres':
				$order_by = 'RANDOM()';
				break;

			case 'mssql':
			case 'mssql_odbc':
				$order_by = 'NEWID()';
				break;

			default:
				$order_by = 'RAND()';
				break;
		}

		$sql_array = [
			'SELECT' => '*',
			'FROM' => [
				$this->points_lottery_tickets_table => 'l',
			],
			'ORDER_BY' => $order_by,
		];
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query_limit($sql, 1);
		$random_user_by_tickets = $this->db->sql_fetchfield('user_id');
		$this->db->sql_freeresult($result);

		if ($total_tickets > 0)
		{
			// Generate a random number
			$rand_base = $points_values['lottery_chance'];
			$rand_value = rand(0, 100);

			// Decide, if the user really wins
			if ($rand_value <= $rand_base)
			{
				$winning_number = $random_user_by_tickets;

				// Select a winner from ticket table
				$sql_array = [
					'SELECT' => '*',
					'FROM' => [
						USERS_TABLE => 'u',
					],
					'WHERE' => 'user_id = ' . (int) $winning_number,
				];
				$sql = $this->db->sql_build_query('SELECT', $sql_array);
				$result = $this->db->sql_query($sql);
				$winner = $this->db->sql_fetchrow($result);
				$this->db->sql_freeresult($result);

				// Check if lottery is enabled and prepare winner informations
				$sql = 'SELECT *
					FROM ' . $this->points_config_table;
				$result = $this->db->sql_query($sql);
				$lottery_enabled = $this->db->sql_fetchfield('lottery_enable');
				$this->db->sql_freeresult($result);

				if ($lottery_enabled = 1)
				{
					$winner_notification = $this->number_format_points($points_values['lottery_jackpot']) . ' ' . ($this->config['points_name']) . ' ';
					$winner_deposit = $this->user->lang['LOTTERY_PM_CASH_ENABLED'];
					$amount_won = $points_values['lottery_jackpot'];
				}
				else
				{
					$winner_notification = '';
					$winner_deposit = '';
					$amount_won = '';
				}

				// Update previous winner information
				$this->set_points_values('lottery_prev_winner', ("'" . $winner['username'] . "'"));
				$this->set_points_values('lottery_prev_winner_id', $winner['user_id']);

				// Check, if user wants to be informed by PM
				if ($winner['user_allow_pm'] == 1)
				{
					$sql_array = [
						'SELECT' => '*',
						'FROM' => [
							USERS_TABLE => 'u',
						],
						'WHERE' => 'user_id = ' . (int) $points_values['lottery_pm_from'],
					];
					$sql = $this->db->sql_build_query('SELECT', $sql_array);
					$result = $this->db->sql_query($sql);
					$pm_sender = $this->db->sql_fetchrow($result);
					$this->db->sql_freeresult($result);

					// Notify the lucky winner by PM
					$pm_subject = $this->user->lang['LOTTERY_PM_SUBJECT'];
					$pm_text = sprintf($this->user->lang['LOTTERY_PM_BODY'], $winner_notification, $winner_deposit);

					include_once($this->root_path . 'includes/message_parser.' . $this->php_ext);

					$message_parser = new parse_message();
					$message_parser->message = $pm_text;
					$message_parser->parse(true, true, true, false, false, true, true);

					$pm_data = [
						'address_list' => ['u' => [$winner['user_id'] => 'to']],
						'from_user_id' => ($points_values['lottery_pm_from'] == 0) ? $winner['user_id'] : $pm_sender['user_id'],
						'from_username' => ($points_values['lottery_pm_from'] == 0) ? $this->user->lang['LOTTERY_PM_COMMISION'] : $pm_sender['username'],
						'icon_id' => 0,
						'from_user_ip' => '',

						'enable_bbcode' => true,
						'enable_smilies' => true,
						'enable_urls' => true,
						'enable_sig' => true,

						'message' => $message_parser->message,
						'bbcode_bitfield' => $message_parser->bbcode_bitfield,
						'bbcode_uid' => $message_parser->bbcode_uid,
					];

					submit_pm('post', $pm_subject, $pm_data, false);
				}

				// Add new winner to lottery history
				$sql = 'INSERT INTO ' . $this->points_lottery_history_table . ' ' . $this->db->sql_build_array('INSERT', [
						'user_id' => (int) $winner['user_id'],
						'user_name' => $winner['username'],
						'time' => $current_time,
						'amount' => $points_values['lottery_jackpot'],
					]);
				$this->db->sql_query($sql);

				// Update mChat with lottery winner
				if ($this->phpbb_container->has('dmzx.mchat.settings') && $this->config['lottery_mchat_enable'])
				{
					$message = $this->user->lang['LOTTERY_MCHAT_WINNER'];
					$name = $points_values['lottery_name'];

					$this->mchat_message($winner['user_id'], $points_values['lottery_jackpot'], $message, $name);
				}

				// Update winners total
				$this->set_points_values('lottery_winners_total', $points_values['lottery_winners_total'] + 1);

				// Add jackpot to winner
				$this->add_points((int) $winner['user_id'], $points_values['lottery_jackpot']);

				// Reset jackpot
				$this->set_points_values('lottery_jackpot', $points_values['lottery_base_amount']);
			}
			else
			{
				$this->set_points_values('lottery_jackpot', $points_values['lottery_jackpot'] + $points_values['lottery_base_amount']);

				$no_winner = 0;

				$sql = 'INSERT INTO ' . $this->points_lottery_history_table . ' ' . $this->db->sql_build_array('INSERT', [
						'user_id' => 0,
						'user_name' => $no_winner,
						'time' => $current_time,
						'amount' => 0,
					]);
				$this->db->sql_query($sql);

				// Update previous winner information
				$this->set_points_values('lottery_prev_winner', "'" . $no_winner . "'");
				$this->set_points_values('lottery_prev_winner_id', 0);
			}
		}

		// Reset lottery

		// Delete all tickets
		$sql = 'DELETE FROM ' . $this->points_lottery_tickets_table;
		$this->db->sql_query($sql);

		// Reset last draw time
		$check_time = $points_values['lottery_last_draw_time'] + $points_values['lottery_draw_period'];
		$current_time = time();
		if ($current_time > $check_time)
		{
			while ($check_time < $current_time)
			{
				$check_time = $check_time + $points_values['lottery_draw_period'];
				$check_time++;
			}

			if ($check_time > $current_time)
			{
				$check_time = $check_time - $points_values['lottery_draw_period'];
				$this->set_points_values('lottery_last_draw_time', $check_time);
			}
		} else
		{
			$this->set_points_values('lottery_last_draw_time', ($points_values['lottery_last_draw_time'] + $points_values['lottery_draw_period']));
		}
	}

	/**
	 * Set points config value. Creates missing config entry.
	 */
	function set_points_config($config_name, $config_value, $is_dynamic = false)
	{
		$sql = 'UPDATE ' . $this->points_config_table . "
			SET config_value = '" . $this->db->sql_escape($config_value) . "'
			WHERE config_name = '" . $this->db->sql_escape($config_name) . "'";
		$this->db->sql_query($sql);

		if (!$this->db->sql_affectedrows() && !isset($points_config[$config_name]))
		{
			$sql = 'INSERT INTO ' . $this->points_config_table . ' ' . $this->db->sql_build_array('INSERT', [
					'config_name' => $config_name,
					'config_value' => $config_value,
					'is_dynamic' => ($is_dynamic) ? 1 : 0]);
			$this->db->sql_query($sql);
		}

		$points_config[$config_name] = $config_value;

		if (!$is_dynamic)
		{
			$this->cache->destroy('config');
		}
	}

	/**
	 * Set points values
	 */
	function set_points_values($field, $value)
	{
		$sql = "UPDATE " . $this->points_values_table . "
			SET $field = $value";
		$this->db->sql_query($sql);

		return;
	}

	function points_values($config_name)
	{
		/**
		 * Read out config values
		 */
		$sql = 'SELECT ' . $config_name . '
			FROM ' . $this->points_values_table;
		$result = $this->db->sql_query($sql);
		$config_value = $this->db->sql_fetchfield($config_name);
		$this->db->sql_freeresult($result);

		return $config_value;
	}

	function points_all_values()
	{
		/**
		 * Read out config values
		 */
		// Get all values
		$sql = 'SELECT *
			FROM ' . $this->points_values_table;
		$result = $this->db->sql_query($sql);
		$points_values = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		return $points_values;
	}

	function points_all_configs()
	{
		// Get all point config names and config values
		$sql = 'SELECT config_name, config_value
			FROM ' . $this->points_config_table;
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$points_config[$row['config_name']] = $row['config_value'];
		}
		$this->db->sql_freeresult($result);

		return $points_config;
	}

	function add_points_to_table($post_id, $points, $mode, $attachments, $poll)
	{
		$sql_ary = [
			'points_' . $mode . '_received' => $points,
			'points_attachment_received' => $attachments,
			'points_poll_received' => $poll,
			'points_received' => $points
		];

		$sql = 'UPDATE ' . POSTS_TABLE . '
			SET ' . $this->db->sql_build_array('UPDATE', $sql_ary) . '
			WHERE post_id = ' . (int) $post_id;
		$this->db->sql_query($sql);
	}

	function random_bonus_increment($user_id)
	{
		// Get all values
		$points_values = $this->points_all_values();

		$bonus_chance = '';
		$bonus = false; // Basic value, sorry..
		$bonus_value = 0.00; // Basic value
		// Following numbers are 'times 100' to get rid of commas, as mt_rand doesn't get comma numbers.
		$bonus_chance = $points_values['points_bonus_chance'] * 100; // The chance percentage for a user to get the bonus
		$random_number = mt_rand(0, 10000); // The random number we compare to the chance percentage

		if ($random_number <= $bonus_chance)
		{
			$bonus = true;

			// Check if we want a fixed bonus value or not
			if ($points_values['points_bonus_min'] == $points_values['points_bonus_max'])
			{
				$bonus_value = $points_values['points_bonus_min'];
			} else
			{
				// Create the bonus value, between the set minimum and maximum
				// Following numbers are 'times 100' to get rid of commas, as mt_rand doesn't get comma numbers.
				$bonus_random = mt_rand($points_values['points_bonus_min'] * 100, $points_values['points_bonus_max'] * 100) / 100;
				$bonus_value = round($bonus_random, 0, PHP_ROUND_HALF_UP);
			}
		}

		if ($bonus && $bonus_value)
		{
			$this->add_points((int) $user_id, $bonus_value);

			// Send out notification

			// Increase our notification sent counter
			$this->config->increment('points_notification_id', 1);

			// Store the notification data we will use in an array
			$data = [
				'points_notify_id' => (int) $this->config['points_notification_id'],
				'points_notify_msg' => sprintf($this->user->lang['NOTIFICATION_RANDOM_BONUS'], $bonus_value, $this->config['points_name']),
				'sender' => (int) $this->user->data['user_id'],
				'receiver' => (int) $user_id,
				'mode' => 'logs', // The mode where the notification sends the user to
			];
			$this->notification_manager->add_notifications('dmzx.ultimatepoints.notification.type.points', $data);

			$sql_array = [
				'SELECT' => 'username',
				'FROM' => [
					USERS_TABLE => 'u',
				],
				'WHERE' => 'user_id = ' . (int) $user_id,
			];
			$sql = $this->db->sql_build_query('SELECT', $sql_array);
			$result = $this->db->sql_query($sql);
			$points_user = $this->db->sql_fetchrow($result);

			// Add logs
			$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_MOD_POINTS_RANDOM', false, [$points_user['username']]);
		}
	}

	function assign_authors()
	{
		$md_manager = $this->extension_manager->create_extension_metadata_manager('dmzx/ultimatepoints', $this->template);
		$meta = $md_manager->get_metadata();

		$author_names = [];
		$author_homepages = [];

		foreach (array_slice($meta['authors'], 0, 2) as $author)
		{
			$author_names[] = $author['name'];
			$author_homepages[] = sprintf('<a href="%1$s" title="%2$s">%2$s</a>', $author['homepage'], $author['name']);
		}
		$this->template->assign_vars([
			'ULTIMATEPOINTS_DISPLAY_NAME' => $meta['extra']['display-name'],
			'ULTIMATEPOINTS_AUTHOR_NAMES' => implode(' &amp; ', $author_names),
			'ULTIMATEPOINTS_AUTHOR_HOMEPAGES' => implode(' &amp; ', $author_homepages),
			'ULTIMATEPOINTS_VERSION' => $this->config['ultimate_points_version'],
		]);
		return;
	}

	function mchat_message($user_id, $amount, $message, $name)
	{
		global $table_prefix, $phpbb_container;

		if ($phpbb_container->has('dmzx.mchat.settings'))
		{
			$enable_bbcode = $enable_urls = $enable_smilies = true;

			$board_url = generate_board_url() . '/';

			$usercolour = get_username_string('colour', $this->user->data['user_id'], $this->user->data['username'], $this->user->data['user_colour']);
			$user_name_mchat = $usercolour ? '[url= ' . $board_url . 'memberlist.' . $this->php_ext . '?mode=viewprofile&u=' . $this->user->data['user_id'] . '][b][color=' . $usercolour . ']' . $this->user->data['username'] . '[/color][/b][/url]' : '[b]' . $this->user->data['username'] . '[/b]';

			$sql_arys = [
				'user_id' => (int) $user_id,
				'user_ip' => $this->user->ip,
				'message' => sprintf($message, $user_name_mchat, $amount, $name),
				'bbcode_bitfield' => '',
				'bbcode_uid' => '',
				'message_time' => time()
			];

			$options = 0;
			generate_text_for_storage($sql_arys['message'], $sql_arys['bbcode_uid'], $sql_arys['bbcode_bitfield'], $options, $enable_bbcode, $enable_urls, $enable_smilies);

			$this->db->sql_query('INSERT INTO ' . $table_prefix . 'mchat' . ' ' . $this->db->sql_build_array('INSERT', $sql_arys));
		}
	}

	function get_name()
	{
		if (empty($this->name))
		{
			$name = $this->config['points_name'];
			$name = !empty($name) ? $name : 'Points';
			$this->name = $name;
		}
		return $this->name;
	}
}
