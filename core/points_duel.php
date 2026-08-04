<?php
/**
 *
 * @package phpBB Extension - Ultimate Points
 * @copyright (c) 2026 dmzx https://www.dmzx-web.net
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace dmzx\ultimatepoints\core;

use phpbb\auth\auth;
use phpbb\config\config;
use phpbb\controller\helper;
use phpbb\db\driver\driver_interface;
use phpbb\event\dispatcher_interface;
use phpbb\notification\manager;
use phpbb\request\request;
use phpbb\language\language;
use phpbb\template\template;
use phpbb\user;
use Symfony\Component\DependencyInjection\Container;

class points_duel
{
	const STATUS_PENDING = 0;
	const STATUS_ACTIVE = 1;
	const STATUS_COMPLETED = 2;
	const STATUS_DECLINED = 3;
	const STATUS_EXPIRED = 4;
	const STATUS_CANCELLED = 5;

	const RESOLUTION_COINFLIP = 0;
	const RESOLUTION_ADMIN = 1;

	/** @var functions_points */
	protected $functions_points;

	/** @var auth */
	protected $auth;

	/** @var template */
	protected $template;

	/** @var user */
	protected $user;

	/** @var language */
	protected $language;

	/** @var driver_interface */
	protected $db;

	/** @var request */
	protected $request;

	/** @var config */
	protected $config;

	/** @var helper */
	protected $helper;

	/** @var manager */
	protected $notification_manager;

	/** @var Container */
	protected $phpbb_container;

	/** @var dispatcher_interface */
	protected $dispatcher;

	/** @var string */
	protected $php_ext;

	/** @var string phpBB root path */
	protected $root_path;

	/**
	 * The database tables
	 *
	 * @var string
	 */
	protected $points_duel_table;

	protected $points_config_table;

	protected $points_values_table;

	/**
	 * Constructor
	 *
	 * @param functions_points $functions_points
	 * @param auth $auth
	 * @param template $template
	 * @param user $user
	 * @param driver_interface $db
	 * @param request $request
	 * @param config $config
	 * @param helper $helper
	 * @param manager $notification_manager
	 * @param Container $phpbb_container
	 * @param dispatcher_interface $dispatcher
	 * @param string $php_ext
	 * @param string $root_path
	 * @param string $points_duel_table
	 * @param string $points_config_table
	 * @param string $points_values_table
	 *
	 */
	public function __construct(
		functions_points $functions_points,
		auth $auth,
		template $template,
		user $user,
		language $language,
		driver_interface $db,
		request $request,
		config $config,
		helper $helper,
		manager $notification_manager,
		Container $phpbb_container,
		dispatcher_interface $dispatcher,
		$php_ext,
		$root_path,
		$points_duel_table,
		$points_config_table,
		$points_values_table
	)
	{
		$this->functions_points = $functions_points;
		$this->auth = $auth;
		$this->template = $template;
		$this->user = $user;
		$this->language = $language;
		$this->db = $db;
		$this->request = $request;
		$this->config = $config;
		$this->helper = $helper;
		$this->notification_manager = $notification_manager;
		$this->phpbb_container = $phpbb_container;
		$this->dispatcher = $dispatcher;
		$this->php_ext = $php_ext;
		$this->root_path = $root_path;
		$this->points_duel_table = $points_duel_table;
		$this->points_config_table = $points_config_table;
		$this->points_values_table = $points_values_table;
	}

	var $u_action;

	/**
	 * Entry point for the duel/wager board (mode=duel)
	 *
	 * @param array $checked_user
	 */
	function main($checked_user)
	{
		// Get all values and configs
		$points_values = $this->functions_points->points_all_values();
		$points_config = $this->functions_points->points_all_configs();

		// Check, if the duel board is enabled
		if (!$points_config['duel_enable'])
		{
			$message = $this->language->lang('DUEL_DISABLED') . '<br /><br /><a href="' . $this->helper->route('dmzx_ultimatepoints_controller') . '">&laquo; ' . $this->language->lang('BACK_TO_PREV') . '</a>';
			trigger_error($message);
		}

		// Check, if user is allowed to use duels
		if (!$this->auth->acl_get('u_use_duel'))
		{
			$message = $this->language->lang('NOT_AUTHORISED') . '<br /><br /><a href="' . $this->helper->route('dmzx_ultimatepoints_controller') . '">&laquo; ' . $this->language->lang('BACK_TO_PREV') . '</a>';
			trigger_error($message);
		}

		// Add part to bar
		$this->template->assign_block_vars('navlinks', [
			'U_VIEW_FORUM' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'duel']),
			'FORUM_NAME' => $this->language->lang('DUEL_TITLE_MAIN'),
		]);

		// Release pending challenges that were never accepted in time
		$this->release_expired_challenges((int) $points_values['duel_accept_time_limit']);

		add_form_key('duel_action');

		if ($this->request->is_set_post('submit') && $this->request->variable('action', '') == 'challenge')
		{
			$this->handle_challenge($points_values, $points_config);
		}
		else if ($this->request->is_set_post('duel_accept'))
		{
			$this->handle_accept($points_config);
		}
		else if ($this->request->is_set_post('duel_decline'))
		{
			$this->handle_decline();
		}
		else if ($this->request->is_set_post('duel_cancel'))
		{
			$this->handle_cancel();
		}
		else if ($this->request->is_set_post('duel_resolve'))
		{
			$this->handle_resolve();
		}

		$this->assign_duel_list($checked_user);

		$this->template->assign_vars([
			'DUEL_MIN_WAGER' => $this->functions_points->number_format_points($points_values['duel_min_wager']),
			'DUEL_MAX_WAGER' => $this->functions_points->number_format_points($points_values['duel_max_wager']),
			'S_DUEL_DEFAULT_ADMIN' => (bool) $points_config['duel_default_admin_resolve'],
			'POINTS_NAME' => $this->config['points_name'],
			'LOTTERY_NAME' => $points_values['lottery_name'],
			'BANK_NAME' => $points_values['bank_name'],
			'U_ACTION' => $this->u_action,
			'U_FIND_USERNAME' => append_sid("{$this->root_path}memberlist.{$this->php_ext}", "mode=searchuser&amp;form=post&amp;field=opponent"),
			'U_DUEL' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'duel']),
			'U_BOUNTY' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'bounty']),
			'U_TRANSFER_USER' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'transfer_user']),
			'U_LOGS' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'logs']),
			'U_LOTTERY' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'lottery']),
			'U_BANK' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'bank']),
			'U_ROBBERY' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'robbery']),
			'U_INFO' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'info']),
			'U_USE_TRANSFER' => $this->auth->acl_get('u_use_transfer'),
			'U_USE_LOGS' => $this->auth->acl_get('u_use_logs'),
			'U_USE_LOTTERY' => $this->auth->acl_get('u_use_lottery'),
			'U_USE_BANK' => $this->auth->acl_get('u_use_bank'),
			'U_USE_ROBBERY' => $this->auth->acl_get('u_use_robbery'),
			'U_USE_BOUNTY' => $this->auth->acl_get('u_use_bounty'),
			'U_USE_DUEL' => $this->auth->acl_get('u_use_duel'),
		]);

		// Generate the page
		page_header($this->language->lang('DUEL_TITLE_MAIN'));

		$this->template->set_filenames([
			'body' => 'points/points_duel.html',
		]);

		page_footer();
	}

	/**
	 * Challenge another member. Escrows the wager from the challenger immediately.
	 *
	 * @param array $points_values
	 * @param array $points_config
	 */
	protected function handle_challenge($points_values, $points_config)
	{
		if (!check_form_key('duel_action'))
		{
			trigger_error('FORM_INVALID');
		}

		$opponent_username = $this->request->variable('opponent', '', true);
		$wager = round($this->request->variable('wager', 0.00), 2);
		$resolution_type = ($this->request->variable('resolution_type', 0) == self::RESOLUTION_ADMIN) ? self::RESOLUTION_ADMIN : self::RESOLUTION_COINFLIP;

		if ($opponent_username === '')
		{
			$message = $this->language->lang('DUEL_MISSING_FIELDS') . '<br /><br /><a href="' . $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'duel']) . '">&laquo; ' . $this->language->lang('BACK_TO_PREV') . '</a>';
			trigger_error($message);
		}

		if ($wager < (float) $points_values['duel_min_wager'] || $wager > (float) $points_values['duel_max_wager'])
		{
			$message = sprintf($this->language->lang('DUEL_WAGER_OUT_OF_RANGE'), $this->functions_points->number_format_points($points_values['duel_min_wager']), $this->functions_points->number_format_points($points_values['duel_max_wager']), $this->config['points_name']) . '<br /><br /><a href="' . $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'duel']) . '">&laquo; ' . $this->language->lang('BACK_TO_PREV') . '</a>';
			trigger_error($message);
		}

		if ($wager > (float) $this->user->data['user_points'])
		{
			$message = sprintf($this->language->lang('DUEL_INSUFFICIENT_FUNDS'), $this->config['points_name']) . '<br /><br /><a href="' . $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'duel']) . '">&laquo; ' . $this->language->lang('BACK_TO_PREV') . '</a>';
			trigger_error($message);
		}

		// Look up the opponent
		$sql_array = [
			'SELECT' => 'user_id',
			'FROM' => [
				USERS_TABLE => 'u',
			],
			'WHERE' => 'username_clean = "' . $this->db->sql_escape(utf8_clean_string($opponent_username)) . '"',
		];
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$opponent_id = (int) $this->db->sql_fetchfield('user_id');
		$this->db->sql_freeresult($result);

		if (!$opponent_id)
		{
			$message = $this->language->lang('DUEL_OPPONENT_NOT_FOUND') . '<br /><br /><a href="' . $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'duel']) . '">&laquo; ' . $this->language->lang('BACK_TO_PREV') . '</a>';
			trigger_error($message);
		}

		$challenger_id = (int) $this->user->data['user_id'];

		if ($opponent_id === $challenger_id)
		{
			trigger_error($this->language->lang('DUEL_CANNOT_CHALLENGE_SELF'));
		}

		// Cap how many open challenges a single user can have outstanding at once
		$max_open = (int) $points_config['duel_max_open_per_user'];
		if ($max_open > 0)
		{
			$sql = 'SELECT COUNT(*) AS total
				FROM ' . $this->points_duel_table . '
				WHERE challenger_id = ' . $challenger_id . '
					AND status = ' . self::STATUS_PENDING;
			$result = $this->db->sql_query($sql);
			$open_count = (int) $this->db->sql_fetchfield('total');
			$this->db->sql_freeresult($result);

			if ($open_count >= $max_open)
			{
				$message = sprintf($this->language->lang('DUEL_TOO_MANY_OPEN'), $max_open) . '<br /><br /><a href="' . $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'duel']) . '">&laquo; ' . $this->language->lang('BACK_TO_PREV') . '</a>';
				trigger_error($message);
			}
		}

		// Escrow the challenger's wager right away
		$this->functions_points->substract_points($challenger_id, $wager);

		$sql = 'INSERT INTO ' . $this->points_duel_table . ' ' . $this->db->sql_build_array('INSERT', [
			'challenger_id' => $challenger_id,
			'opponent_id' => $opponent_id,
			'wager' => $wager,
			'resolution_type' => $resolution_type,
			'status' => self::STATUS_PENDING,
			'winner_id' => 0,
			'created_time' => time(),
			'accepted_time' => 0,
			'resolved_time' => 0,
		]);
		$this->db->sql_query($sql);
		$duel_id = (int) $this->db->sql_nextid();

		/**
		 * Event that is triggered after a duel challenge has been posted
		 *
		 * @event dmzx.ultimatepoints.duel_challenged_after
		 * @var int		duel_id			The ID of the new duel
		 * @var int		challenger_id	The user who issued the challenge
		 * @var int		opponent_id		The user who was challenged
		 * @var float	wager			The escrowed wager amount (each side stakes this much)
		 * @since 1.3.0
		 */
		$vars = [
			'duel_id',
			'challenger_id',
			'opponent_id',
			'wager',
		];
		extract($this->dispatcher->trigger_event('dmzx.ultimatepoints.duel_challenged_after', compact($vars)));

		$this->config->increment('points_notification_id', 1);
		$this->notification_manager->add_notifications('dmzx.ultimatepoints.notification.type.points', [
			'points_notify_id' => (int) $this->config['points_notification_id'],
			'points_notify_msg' => sprintf($this->language->lang('NOTIFICATION_DUEL_CHALLENGED'), $this->functions_points->number_format_points($wager), $this->config['points_name']),
			'sender' => $challenger_id,
			'receiver' => $opponent_id,
			'mode' => 'duel',
		]);

		if ($this->phpbb_container->has('dmzx.mchat.settings') && $this->config['duel_mchat_enable'])
		{
			$this->functions_points->mchat_message($challenger_id, $this->functions_points->number_format_points($wager), $this->language->lang('DUEL_MCHAT_CHALLENGED'), $this->config['points_name']);
		}

		$message = $this->language->lang('DUEL_CHALLENGE_SUCCESS') . '<br /><br /><a href="' . $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'duel']) . '">&laquo; ' . $this->language->lang('BACK_TO_PREV') . '</a>';
		trigger_error($message);
	}

	/**
	 * Opponent accepts a pending challenge. Escrows the opponent's wager and,
	 * for coin-flip duels, resolves immediately.
	 *
	 * @param array $points_config
	 */
	protected function handle_accept($points_config)
	{
		if (!check_form_key('duel_action'))
		{
			trigger_error('FORM_INVALID');
		}

		$duel_id = $this->request->variable('duel_id', 0);
		$duel = $this->get_duel($duel_id);

		$opponent_id = (int) $this->user->data['user_id'];

		if (!$duel || (int) $duel['status'] !== self::STATUS_PENDING || (int) $duel['opponent_id'] !== $opponent_id)
		{
			trigger_error($this->language->lang('DUEL_NOT_AVAILABLE'));
		}

		$wager = (float) $duel['wager'];

		if ($wager > (float) $this->user->data['user_points'])
		{
			$message = sprintf($this->language->lang('DUEL_INSUFFICIENT_FUNDS'), $this->config['points_name']) . '<br /><br /><a href="' . $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'duel']) . '">&laquo; ' . $this->language->lang('BACK_TO_PREV') . '</a>';
			trigger_error($message);
		}

		// Claim the row atomically before touching any balances
		$sql = 'UPDATE ' . $this->points_duel_table . '
			SET ' . $this->db->sql_build_array('UPDATE', [
				'status' => self::STATUS_ACTIVE,
				'accepted_time' => time(),
			]) . '
			WHERE duel_id = ' . (int) $duel_id . '
				AND status = ' . self::STATUS_PENDING;
		$this->db->sql_query($sql);

		if (!$this->db->sql_affectedrows())
		{
			trigger_error($this->language->lang('DUEL_NOT_AVAILABLE'));
		}

		// Escrow the opponent's wager now that the challenge is claimed
		$this->functions_points->substract_points($opponent_id, $wager);

		$challenger_id = (int) $duel['challenger_id'];

		/**
		 * Event that is triggered after a duel challenge has been accepted
		 *
		 * @event dmzx.ultimatepoints.duel_accepted_after
		 * @var int		duel_id			The ID of the accepted duel
		 * @var int		challenger_id	The user who issued the challenge
		 * @var int		opponent_id		The user who accepted
		 * @var float	wager			The wager amount escrowed on each side
		 * @since 1.3.0
		 */
		$vars = [
			'duel_id',
			'challenger_id',
			'opponent_id',
			'wager',
		];
		extract($this->dispatcher->trigger_event('dmzx.ultimatepoints.duel_accepted_after', compact($vars)));

		$this->config->increment('points_notification_id', 1);
		$this->notification_manager->add_notifications('dmzx.ultimatepoints.notification.type.points', [
			'points_notify_id' => (int) $this->config['points_notification_id'],
			'points_notify_msg' => sprintf($this->language->lang('NOTIFICATION_DUEL_ACCEPTED'), $this->functions_points->number_format_points($wager), $this->config['points_name']),
			'sender' => $opponent_id,
			'receiver' => $challenger_id,
			'mode' => 'duel',
		]);

		if ((int) $duel['resolution_type'] === self::RESOLUTION_COINFLIP)
		{
			// Resolve immediately - the outcome is decided server-side only, never from request data
			$duel['status'] = self::STATUS_ACTIVE;
			$winner_id = (mt_rand(0, 1) === 0) ? $challenger_id : $opponent_id;
			$this->resolve_duel($duel, $winner_id);

			$message = $this->language->lang('DUEL_ACCEPT_SUCCESS') . '<br /><br /><a href="' . $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'duel']) . '">&laquo; ' . $this->language->lang('BACK_TO_PREV') . '</a>';
			trigger_error($message);
		}

		$message = $this->language->lang('DUEL_ACCEPT_SUCCESS') . '<br /><br /><a href="' . $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'duel']) . '">&laquo; ' . $this->language->lang('BACK_TO_PREV') . '</a>';
		trigger_error($message);
	}

	/**
	 * Opponent declines a pending challenge, refunding the challenger
	 */
	protected function handle_decline()
	{
		if (!check_form_key('duel_action'))
		{
			trigger_error('FORM_INVALID');
		}

		$duel_id = $this->request->variable('duel_id', 0);
		$duel = $this->get_duel($duel_id);

		if (!$duel || (int) $duel['status'] !== self::STATUS_PENDING || (int) $duel['opponent_id'] !== (int) $this->user->data['user_id'])
		{
			trigger_error($this->language->lang('DUEL_NOT_YOUR_CHALLENGE'));
		}

		$this->refund_and_close($duel, self::STATUS_DECLINED);

		$this->config->increment('points_notification_id', 1);
		$this->notification_manager->add_notifications('dmzx.ultimatepoints.notification.type.points', [
			'points_notify_id' => (int) $this->config['points_notification_id'],
			'points_notify_msg' => $this->language->lang('NOTIFICATION_DUEL_DECLINED'),
			'sender' => (int) $duel['opponent_id'],
			'receiver' => (int) $duel['challenger_id'],
			'mode' => 'duel',
		]);

		$message = $this->language->lang('DUEL_DECLINE_SUCCESS') . '<br /><br /><a href="' . $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'duel']) . '">&laquo; ' . $this->language->lang('BACK_TO_PREV') . '</a>';
		trigger_error($message);
	}

	/**
	 * Challenger cancels their own challenge while it is still pending, refunding themselves
	 */
	protected function handle_cancel()
	{
		if (!check_form_key('duel_action'))
		{
			trigger_error('FORM_INVALID');
		}

		$duel_id = $this->request->variable('duel_id', 0);
		$duel = $this->get_duel($duel_id);

		if (!$duel || (int) $duel['status'] !== self::STATUS_PENDING || (int) $duel['challenger_id'] !== (int) $this->user->data['user_id'])
		{
			trigger_error($this->language->lang('DUEL_NOT_YOUR_CHALLENGE'));
		}

		$this->refund_and_close($duel, self::STATUS_CANCELLED);

		$message = $this->language->lang('DUEL_CANCEL_SUCCESS') . '<br /><br /><a href="' . $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'duel']) . '">&laquo; ' . $this->language->lang('BACK_TO_PREV') . '</a>';
		trigger_error($message);
	}

	/**
	 * Moderator resolves an admin-resolved duel that is active. A moderator who
	 * is one of the two participants is never allowed to resolve their own duel.
	 */
	protected function handle_resolve()
	{
		if (!check_form_key('duel_action'))
		{
			trigger_error('FORM_INVALID');
		}

		if (!$this->auth->acl_get('m_resolve_duel'))
		{
			trigger_error($this->language->lang('DUEL_NOT_AUTHORISED_RESOLVE'));
		}

		$duel_id = $this->request->variable('duel_id', 0);
		$winner_id = $this->request->variable('winner_id', 0);
		$duel = $this->get_duel($duel_id);

		if (!$duel || (int) $duel['status'] !== self::STATUS_ACTIVE || (int) $duel['resolution_type'] !== self::RESOLUTION_ADMIN)
		{
			trigger_error($this->language->lang('DUEL_NOT_AVAILABLE'));
		}

		$moderator_id = (int) $this->user->data['user_id'];
		if ($moderator_id === (int) $duel['challenger_id'] || $moderator_id === (int) $duel['opponent_id'])
		{
			trigger_error($this->language->lang('DUEL_CANNOT_RESOLVE_OWN'));
		}

		// The winner must be one of the two actual participants - never trust the posted id blindly
		if ($winner_id !== (int) $duel['challenger_id'] && $winner_id !== (int) $duel['opponent_id'])
		{
			trigger_error($this->language->lang('DUEL_NOT_AVAILABLE'));
		}

		$this->resolve_duel($duel, $winner_id);

		$message = $this->language->lang('DUEL_RESOLVE_SUCCESS') . '<br /><br /><a href="' . $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'duel']) . '">&laquo; ' . $this->language->lang('BACK_TO_PREV') . '</a>';
		trigger_error($message);
	}

	/**
	 * Pays the full pot (both wagers) out to the winner and marks the duel completed.
	 * Used by both the coin-flip auto-resolve path and the admin-resolve path.
	 *
	 * @param array $duel
	 * @param int $winner_id
	 */
	protected function resolve_duel($duel, $winner_id)
	{
		$duel_id = (int) $duel['duel_id'];
		$challenger_id = (int) $duel['challenger_id'];
		$opponent_id = (int) $duel['opponent_id'];
		$wager = (float) $duel['wager'];
		$winner_id = (int) $winner_id;
		$loser_id = ($winner_id === $challenger_id) ? $opponent_id : $challenger_id;
		$pot = $wager * 2;

		$sql = 'UPDATE ' . $this->points_duel_table . '
			SET ' . $this->db->sql_build_array('UPDATE', [
				'status' => self::STATUS_COMPLETED,
				'winner_id' => $winner_id,
				'resolved_time' => time(),
			]) . '
			WHERE duel_id = ' . $duel_id . '
				AND status = ' . self::STATUS_ACTIVE;
		$this->db->sql_query($sql);

		if (!$this->db->sql_affectedrows())
		{
			return;
		}

		$this->functions_points->add_points($winner_id, $pot);

		/**
		 * Event that is triggered after a duel has been resolved and the pot paid out
		 *
		 * @event dmzx.ultimatepoints.duel_resolved_after
		 * @var int		duel_id		The ID of the resolved duel
		 * @var int		winner_id	The user who won the pot
		 * @var int		loser_id	The user who lost their wager
		 * @var float	wager		The original wager staked by each side
		 * @var float	pot			The total amount paid out to the winner
		 * @since 1.3.0
		 */
		$vars = [
			'duel_id',
			'winner_id',
			'loser_id',
			'wager',
			'pot',
		];
		extract($this->dispatcher->trigger_event('dmzx.ultimatepoints.duel_resolved_after', compact($vars)));

		$this->config->increment('points_notification_id', 1);
		$this->notification_manager->add_notifications('dmzx.ultimatepoints.notification.type.points', [
			'points_notify_id' => (int) $this->config['points_notification_id'],
			'points_notify_msg' => sprintf($this->language->lang('NOTIFICATION_DUEL_WON'), $this->functions_points->number_format_points($pot), $this->config['points_name']),
			'sender' => $loser_id,
			'receiver' => $winner_id,
			'mode' => 'duel',
		]);

		$this->config->increment('points_notification_id', 1);
		$this->notification_manager->add_notifications('dmzx.ultimatepoints.notification.type.points', [
			'points_notify_id' => (int) $this->config['points_notification_id'],
			'points_notify_msg' => sprintf($this->language->lang('NOTIFICATION_DUEL_LOST'), $this->functions_points->number_format_points($wager), $this->config['points_name']),
			'sender' => $winner_id,
			'receiver' => $loser_id,
			'mode' => 'duel',
		]);

		if ($this->phpbb_container->has('dmzx.mchat.settings') && $this->config['duel_mchat_enable'])
		{
			$this->functions_points->mchat_message($winner_id, $this->functions_points->number_format_points($pot), $this->language->lang('DUEL_MCHAT_WON'), $this->config['points_name']);
		}
	}

	/**
	 * Refunds the challenger's escrowed wager and marks the duel closed with the
	 * given terminal status. Only ever refunds the challenger: at this point the
	 * opponent has never paid in, so there is nothing to refund on their side.
	 *
	 * @param array $duel
	 * @param int $status
	 */
	protected function refund_and_close($duel, $status)
	{
		$sql = 'UPDATE ' . $this->points_duel_table . '
			SET ' . $this->db->sql_build_array('UPDATE', [
				'status' => $status,
			]) . '
			WHERE duel_id = ' . (int) $duel['duel_id'] . '
				AND status = ' . self::STATUS_PENDING;
		$this->db->sql_query($sql);

		if ($this->db->sql_affectedrows())
		{
			$this->functions_points->add_points((int) $duel['challenger_id'], (float) $duel['wager']);
		}
	}

	/**
	 * Releases pending challenges that were never accepted within the configured
	 * time limit, refunding the challenger. Checked inline on page load, the same
	 * way points_bounty checks for expired claims.
	 *
	 * @param int $accept_time_limit Hours, 0 = no limit
	 */
	protected function release_expired_challenges($accept_time_limit)
	{
		if (!$accept_time_limit)
		{
			return;
		}

		$expire_before = time() - ($accept_time_limit * 3600);

		$sql = 'SELECT *
			FROM ' . $this->points_duel_table . '
			WHERE status = ' . self::STATUS_PENDING . '
				AND created_time < ' . (int) $expire_before;
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->refund_and_close($row, self::STATUS_EXPIRED);
		}
		$this->db->sql_freeresult($result);
	}

	/**
	 * Fetches a single duel row
	 *
	 * @param int $duel_id
	 * @return array|null
	 */
	protected function get_duel($duel_id)
	{
		$sql = 'SELECT *
			FROM ' . $this->points_duel_table . '
			WHERE duel_id = ' . (int) $duel_id;
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		return $row ?: null;
	}

	/**
	 * Assigns the pending/active duels the viewer is party to, and a short
	 * completed history, to the template
	 *
	 * @param array $checked_user
	 */
	protected function assign_duel_list($checked_user)
	{
		$user_id = (int) $this->user->data['user_id'];

		$sql_array = [
			'SELECT' => 'd.*, uc.username AS challenger_username, uc.user_colour AS challenger_colour, uo.username AS opponent_username, uo.user_colour AS opponent_colour',
			'FROM' => [
				$this->points_duel_table => 'd',
			],
			'LEFT_JOIN' => [
				[
					'FROM' => [USERS_TABLE => 'uc'],
					'ON' => 'd.challenger_id = uc.user_id',
				],
				[
					'FROM' => [USERS_TABLE => 'uo'],
					'ON' => 'd.opponent_id = uo.user_id',
				],
			],
			'WHERE' => '(d.challenger_id = ' . $user_id . ' OR d.opponent_id = ' . $user_id . ')
				AND d.status IN (' . self::STATUS_PENDING . ', ' . self::STATUS_ACTIVE . ')',
			'ORDER_BY' => 'd.status ASC, d.created_time DESC',
		];
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query_limit($sql, 30);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->assign_duel_row($row, $user_id);
		}
		$this->db->sql_freeresult($result);

		$sql_array = [
			'SELECT' => 'd.*, uc.username AS challenger_username, uc.user_colour AS challenger_colour, uo.username AS opponent_username, uo.user_colour AS opponent_colour',
			'FROM' => [
				$this->points_duel_table => 'd',
			],
			'LEFT_JOIN' => [
				[
					'FROM' => [USERS_TABLE => 'uc'],
					'ON' => 'd.challenger_id = uc.user_id',
				],
				[
					'FROM' => [USERS_TABLE => 'uo'],
					'ON' => 'd.opponent_id = uo.user_id',
				],
			],
			'WHERE' => '(d.challenger_id = ' . $user_id . ' OR d.opponent_id = ' . $user_id . ')
				AND d.status IN (' . self::STATUS_COMPLETED . ', ' . self::STATUS_DECLINED . ', ' . self::STATUS_EXPIRED . ', ' . self::STATUS_CANCELLED . ')',
			'ORDER_BY' => 'd.created_time DESC',
		];
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query_limit($sql, 10);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->assign_duel_row($row, $user_id, true);
		}
		$this->db->sql_freeresult($result);

		// Moderators with resolve rights get a queue of admin-resolved duels
		// awaiting a decision - these are never the moderator's own duels,
		// so they wouldn't otherwise show up in the lists above.
		if ($this->auth->acl_get('m_resolve_duel'))
		{
			$sql_array = [
				'SELECT' => 'd.*, uc.username AS challenger_username, uc.user_colour AS challenger_colour, uo.username AS opponent_username, uo.user_colour AS opponent_colour',
				'FROM' => [
					$this->points_duel_table => 'd',
				],
				'LEFT_JOIN' => [
					[
						'FROM' => [USERS_TABLE => 'uc'],
						'ON' => 'd.challenger_id = uc.user_id',
					],
					[
						'FROM' => [USERS_TABLE => 'uo'],
						'ON' => 'd.opponent_id = uo.user_id',
					],
				],
				'WHERE' => 'd.status = ' . self::STATUS_ACTIVE . '
					AND d.resolution_type = ' . self::RESOLUTION_ADMIN . '
					AND d.challenger_id <> ' . $user_id . '
					AND d.opponent_id <> ' . $user_id,
				'ORDER_BY' => 'd.accepted_time ASC',
			];
			$sql = $this->db->sql_build_query('SELECT', $sql_array);
			$result = $this->db->sql_query_limit($sql, 20);

			while ($row = $this->db->sql_fetchrow($result))
			{
				$this->template->assign_block_vars('duel_resolve_queue', [
					'DUEL_ID' => (int) $row['duel_id'],
					'DUEL_CHALLENGER' => get_username_string('full', $row['challenger_id'], $row['challenger_username'], $row['challenger_colour']),
					'DUEL_OPPONENT' => get_username_string('full', $row['opponent_id'], $row['opponent_username'], $row['opponent_colour']),
					'DUEL_WAGER' => $this->functions_points->number_format_points($row['wager']),
					'DUEL_POT' => $this->functions_points->number_format_points($row['wager'] * 2),
					'DUEL_CREATED' => $this->user->format_date($row['accepted_time']),
					'CHALLENGER_ID' => (int) $row['challenger_id'],
					'OPPONENT_ID' => (int) $row['opponent_id'],
				]);
			}
			$this->db->sql_freeresult($result);
		}
	}

	/**
	 * Assigns a single duel row to the appropriate template block
	 *
	 * @param array $row
	 * @param int $user_id The currently viewing user
	 * @param bool $completed
	 */
	protected function assign_duel_row($row, $user_id, $completed = false)
	{
		$status = (int) $row['status'];
		$is_challenger = $user_id === (int) $row['challenger_id'];
		$is_opponent = $user_id === (int) $row['opponent_id'];
		$is_admin_resolution = (int) $row['resolution_type'] === self::RESOLUTION_ADMIN;

		$outcome_label = '';
		if ($completed)
		{
			if ($status === self::STATUS_COMPLETED)
			{
				$won = ((int) $row['winner_id'] === $user_id);
				$outcome_label = $won
					? sprintf($this->language->lang('DUEL_RESULT_WON'), $this->functions_points->number_format_points($row['wager']))
					: sprintf($this->language->lang('DUEL_RESULT_LOST'), $this->functions_points->number_format_points($row['wager']));
			}
			else
			{
				$outcome_label = $this->language->lang('DUEL_RESULT_REFUNDED');
			}
		}

		$template_data = [
			'DUEL_ID' => (int) $row['duel_id'],
			'DUEL_CHALLENGER' => get_username_string('full', $row['challenger_id'], $row['challenger_username'], $row['challenger_colour']),
			'DUEL_OPPONENT' => get_username_string('full', $row['opponent_id'], $row['opponent_username'], $row['opponent_colour']),
			'DUEL_WAGER' => $this->functions_points->number_format_points($row['wager']),
			'DUEL_POT' => $this->functions_points->number_format_points($row['wager'] * 2),
			'DUEL_CREATED' => $this->user->format_date($row['created_time']),
			'DUEL_OUTCOME' => $outcome_label,
			'S_DUEL_PENDING' => $status === self::STATUS_PENDING,
			'S_DUEL_ACTIVE' => $status === self::STATUS_ACTIVE,
			'S_DUEL_COMPLETED' => $status === self::STATUS_COMPLETED,
			'S_DUEL_ADMIN_RESOLUTION' => $is_admin_resolution,
			'S_DUEL_IS_CHALLENGER' => $is_challenger,
			'S_DUEL_IS_OPPONENT' => $is_opponent,
			'S_DUEL_CAN_ACCEPT' => $status === self::STATUS_PENDING && $is_opponent,
			'S_DUEL_CAN_DECLINE' => $status === self::STATUS_PENDING && $is_opponent,
			'S_DUEL_CAN_CANCEL' => $status === self::STATUS_PENDING && $is_challenger,
			'S_DUEL_CAN_RESOLVE' => $status === self::STATUS_ACTIVE && $is_admin_resolution && !$is_challenger && !$is_opponent && $this->auth->acl_get('m_resolve_duel'),
			'S_DUEL_AWAITING_MOD' => $status === self::STATUS_ACTIVE && $is_admin_resolution && ($is_challenger || $is_opponent),
		];

		$this->template->assign_block_vars($completed ? 'duel_completed' : 'duel', $template_data);
	}
}
