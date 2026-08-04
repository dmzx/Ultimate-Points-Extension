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

class points_bounty
{
	const STATUS_OPEN = 0;
	const STATUS_CLAIMED = 1;
	const STATUS_PENDING = 2;
	const STATUS_COMPLETED = 3;

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
	protected $points_bounty_table;

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
	 * @param string $points_bounty_table
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
		$points_bounty_table,
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
		$this->points_bounty_table = $points_bounty_table;
		$this->points_config_table = $points_config_table;
		$this->points_values_table = $points_values_table;
	}

	var $u_action;

	/**
	 * Entry point for the bounty board (mode=bounty)
	 *
	 * @param array $checked_user
	 */
	function main($checked_user)
	{
		// Get all values and configs
		$points_values = $this->functions_points->points_all_values();
		$points_config = $this->functions_points->points_all_configs();

		// Check, if the bounty board is enabled
		if (!$points_config['bounty_enable'])
		{
			$message = $this->language->lang('BOUNTY_DISABLED') . '<br /><br /><a href="' . $this->helper->route('dmzx_ultimatepoints_controller') . '">&laquo; ' . $this->language->lang('BACK_TO_PREV') . '</a>';
			trigger_error($message);
		}

		// Check, if user is allowed to use the bounty board
		if (!$this->auth->acl_get('u_use_bounty'))
		{
			$message = $this->language->lang('NOT_AUTHORISED') . '<br /><br /><a href="' . $this->helper->route('dmzx_ultimatepoints_controller') . '">&laquo; ' . $this->language->lang('BACK_TO_PREV') . '</a>';
			trigger_error($message);
		}

		// Add part to bar
		$this->template->assign_block_vars('navlinks', [
			'U_VIEW_FORUM' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'bounty']),
			'FORUM_NAME' => $this->language->lang('BOUNTY_TITLE_MAIN'),
		]);

		// Release claims that have been sitting unsubmitted past the claim time limit
		$this->release_expired_claims((int) $points_values['bounty_claim_time_limit']);

		add_form_key('bounty_action');

		if ($this->request->is_set_post('submit') && $this->request->variable('action', '') == 'post')
		{
			$this->handle_post($points_values);
		}
		else if ($this->request->is_set_post('bounty_claim'))
		{
			$this->handle_claim();
		}
		else if ($this->request->is_set_post('bounty_submit_work'))
		{
			$this->handle_submit_work($points_config);
		}
		else if ($this->request->is_set_post('bounty_approve'))
		{
			$this->handle_approve($points_config);
		}
		else if ($this->request->is_set_post('bounty_reject'))
		{
			$this->handle_reject();
		}
		else if ($this->request->is_set_post('bounty_cancel'))
		{
			$this->handle_cancel();
		}

		$this->assign_bounty_list($checked_user);

		$this->template->assign_vars([
			'BOUNTY_MIN_REWARD' => $this->functions_points->number_format_points($points_values['bounty_min_reward']),
			'BOUNTY_MAX_REWARD' => $this->functions_points->number_format_points($points_values['bounty_max_reward']),
			'S_BOUNTY_REQUIRE_APPROVAL' => (bool) $points_config['bounty_require_approval'],
			'POINTS_NAME' => $this->config['points_name'],
			'LOTTERY_NAME' => $points_values['lottery_name'],
			'BANK_NAME' => $points_values['bank_name'],
			'U_ACTION' => $this->u_action,
			'U_BOUNTY' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'bounty']),
			'U_TRANSFER_USER' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'transfer_user']),
			'U_LOGS' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'logs']),
			'U_LOTTERY' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'lottery']),
			'U_BANK' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'bank']),
			'U_ROBBERY' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'robbery']),
			'U_INFO' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'info']),
			'U_DUEL' => $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'duel']),
			'U_USE_TRANSFER' => $this->auth->acl_get('u_use_transfer'),
			'U_USE_LOGS' => $this->auth->acl_get('u_use_logs'),
			'U_USE_LOTTERY' => $this->auth->acl_get('u_use_lottery'),
			'U_USE_BANK' => $this->auth->acl_get('u_use_bank'),
			'U_USE_ROBBERY' => $this->auth->acl_get('u_use_robbery'),
			'U_USE_BOUNTY' => $this->auth->acl_get('u_use_bounty'),
			'U_USE_DUEL' => $this->auth->acl_get('u_use_duel'),
		]);

		// Generate the page
		page_header($this->language->lang('BOUNTY_TITLE_MAIN'));

		$this->template->set_filenames([
			'body' => 'points/points_bounty.html',
		]);

		page_footer();
	}

	/**
	 * Post a new bounty. Escrows the reward from the poster immediately.
	 *
	 * @param array $points_values
	 */
	protected function handle_post($points_values)
	{
		if (!check_form_key('bounty_action'))
		{
			trigger_error('FORM_INVALID');
		}

		$title = $this->request->variable('title', '', true);
		$description = $this->request->variable('description', '', true);
		$reward = round($this->request->variable('reward', 0.00), 2);

		if ($title === '' || $description === '')
		{
			$message = $this->language->lang('BOUNTY_MISSING_FIELDS') . '<br /><br /><a href="' . $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'bounty']) . '">&laquo; ' . $this->language->lang('BACK_TO_PREV') . '</a>';
			trigger_error($message);
		}

		if ($reward < (float) $points_values['bounty_min_reward'] || $reward > (float) $points_values['bounty_max_reward'])
		{
			$message = sprintf($this->language->lang('BOUNTY_REWARD_OUT_OF_RANGE'), $this->functions_points->number_format_points($points_values['bounty_min_reward']), $this->functions_points->number_format_points($points_values['bounty_max_reward']), $this->config['points_name']) . '<br /><br /><a href="' . $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'bounty']) . '">&laquo; ' . $this->language->lang('BACK_TO_PREV') . '</a>';
			trigger_error($message);
		}

		if ($reward > (float) $this->user->data['user_points'])
		{
			$message = sprintf($this->language->lang('BOUNTY_INSUFFICIENT_FUNDS'), $this->config['points_name']) . '<br /><br /><a href="' . $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'bounty']) . '">&laquo; ' . $this->language->lang('BACK_TO_PREV') . '</a>';
			trigger_error($message);
		}

		$poster_id = (int) $this->user->data['user_id'];

		// Escrow the reward from the poster right away
		$this->functions_points->substract_points($poster_id, $reward);

		$sql = 'INSERT INTO ' . $this->points_bounty_table . ' ' . $this->db->sql_build_array('INSERT', [
			'poster_id' => $poster_id,
			'claimer_id' => 0,
			'title' => $title,
			'description' => $description,
			'reward' => $reward,
			'status' => self::STATUS_OPEN,
			'created_time' => time(),
			'claimed_time' => 0,
			'submitted_time' => 0,
			'paid_time' => 0,
		]);
		$this->db->sql_query($sql);
		$bounty_id = (int) $this->db->sql_nextid();

		/**
		 * Event that is triggered after a new bounty has been posted
		 *
		 * @event dmzx.ultimatepoints.bounty_posted_after
		 * @var int		bounty_id	The ID of the new bounty
		 * @var int		poster_id	The user who posted the bounty
		 * @var float	reward		The escrowed reward amount
		 * @since 1.2.9
		 */
		$vars = [
			'bounty_id',
			'poster_id',
			'reward',
		];
		extract($this->dispatcher->trigger_event('dmzx.ultimatepoints.bounty_posted_after', compact($vars)));

		if ($this->phpbb_container->has('dmzx.mchat.settings') && $this->config['bounty_mchat_enable'])
		{
			$this->functions_points->mchat_message($poster_id, $this->functions_points->number_format_points($reward), $this->language->lang('BOUNTY_MCHAT_POSTED'), $this->config['points_name']);
		}

		$message = $this->language->lang('BOUNTY_POSTED_SUCCESS') . '<br /><br /><a href="' . $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'bounty']) . '">&laquo; ' . $this->language->lang('BACK_TO_PREV') . '</a>';
		trigger_error($message);
	}

	/**
	 * Claim an open bounty
	 */
	protected function handle_claim()
	{
		if (!check_form_key('bounty_action'))
		{
			trigger_error('FORM_INVALID');
		}

		$bounty_id = $this->request->variable('bounty_id', 0);
		$bounty = $this->get_bounty($bounty_id);

		if (!$bounty || (int) $bounty['status'] !== self::STATUS_OPEN)
		{
			trigger_error($this->language->lang('BOUNTY_NOT_AVAILABLE'));
		}

		if ((int) $bounty['poster_id'] === (int) $this->user->data['user_id'])
		{
			trigger_error($this->language->lang('BOUNTY_CANNOT_CLAIM_OWN'));
		}

		$claimer_id = (int) $this->user->data['user_id'];

		$sql = 'UPDATE ' . $this->points_bounty_table . '
			SET ' . $this->db->sql_build_array('UPDATE', [
				'claimer_id' => $claimer_id,
				'status' => self::STATUS_CLAIMED,
				'claimed_time' => time(),
			]) . '
			WHERE bounty_id = ' . (int) $bounty_id . '
				AND status = ' . self::STATUS_OPEN;
		$this->db->sql_query($sql);

		if (!$this->db->sql_affectedrows())
		{
			trigger_error($this->language->lang('BOUNTY_NOT_AVAILABLE'));
		}

		$poster_id = (int) $bounty['poster_id'];
		$reward = (float) $bounty['reward'];

		/**
		 * Event that is triggered after a bounty has been claimed
		 *
		 * @event dmzx.ultimatepoints.bounty_claimed_after
		 * @var int		bounty_id	The ID of the claimed bounty
		 * @var int		poster_id	The user who posted the bounty
		 * @var int		claimer_id	The user who claimed the bounty
		 * @var float	reward		The escrowed reward amount
		 * @since 1.2.9
		 */
		$vars = [
			'bounty_id',
			'poster_id',
			'claimer_id',
			'reward',
		];
		extract($this->dispatcher->trigger_event('dmzx.ultimatepoints.bounty_claimed_after', compact($vars)));

		// Notify the poster
		$this->config->increment('points_notification_id', 1);
		$this->notification_manager->add_notifications('dmzx.ultimatepoints.notification.type.points', [
			'points_notify_id' => (int) $this->config['points_notification_id'],
			'points_notify_msg' => sprintf($this->language->lang('NOTIFICATION_BOUNTY_CLAIMED'), $bounty['title']),
			'sender' => $claimer_id,
			'receiver' => $poster_id,
			'mode' => 'bounty',
		]);

		if ($this->phpbb_container->has('dmzx.mchat.settings') && $this->config['bounty_mchat_enable'])
		{
			$this->functions_points->mchat_message($poster_id, $this->functions_points->number_format_points($reward), $this->language->lang('BOUNTY_MCHAT_CLAIMED'), $this->config['points_name']);
		}

		$message = $this->language->lang('BOUNTY_CLAIM_SUCCESS') . '<br /><br /><a href="' . $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'bounty']) . '">&laquo; ' . $this->language->lang('BACK_TO_PREV') . '</a>';
		trigger_error($message);
	}

	/**
	 * Claimer submits their work. Pays out immediately if approval isn't required.
	 *
	 * @param array $points_config
	 */
	protected function handle_submit_work($points_config)
	{
		if (!check_form_key('bounty_action'))
		{
			trigger_error('FORM_INVALID');
		}

		$bounty_id = $this->request->variable('bounty_id', 0);
		$bounty = $this->get_bounty($bounty_id);

		if (!$bounty || (int) $bounty['status'] !== self::STATUS_CLAIMED || (int) $bounty['claimer_id'] !== (int) $this->user->data['user_id'])
		{
			trigger_error($this->language->lang('BOUNTY_NOT_YOUR_CLAIM'));
		}

		if ($points_config['bounty_require_approval'])
		{
			$sql = 'UPDATE ' . $this->points_bounty_table . '
				SET ' . $this->db->sql_build_array('UPDATE', [
					'status' => self::STATUS_PENDING,
					'submitted_time' => time(),
				]) . '
				WHERE bounty_id = ' . (int) $bounty_id . '
					AND status = ' . self::STATUS_CLAIMED;
			$this->db->sql_query($sql);

			$this->config->increment('points_notification_id', 1);
			$this->notification_manager->add_notifications('dmzx.ultimatepoints.notification.type.points', [
				'points_notify_id' => (int) $this->config['points_notification_id'],
				'points_notify_msg' => sprintf($this->language->lang('NOTIFICATION_BOUNTY_SUBMITTED'), $bounty['title']),
				'sender' => (int) $this->user->data['user_id'],
				'receiver' => (int) $bounty['poster_id'],
				'mode' => 'bounty',
			]);

			$message = $this->language->lang('BOUNTY_SUBMIT_SUCCESS') . '<br /><br /><a href="' . $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'bounty']) . '">&laquo; ' . $this->language->lang('BACK_TO_PREV') . '</a>';
			trigger_error($message);
		}

		// No approval required - pay out straight away
		$this->pay_out_bounty($bounty);

		$message = $this->language->lang('BOUNTY_PAID_SUCCESS') . '<br /><br /><a href="' . $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'bounty']) . '">&laquo; ' . $this->language->lang('BACK_TO_PREV') . '</a>';
		trigger_error($message);
	}

	/**
	 * Poster approves a pending submission, releasing the escrowed reward to the claimer
	 *
	 * @param array $points_config
	 */
	protected function handle_approve($points_config)
	{
		if (!check_form_key('bounty_action'))
		{
			trigger_error('FORM_INVALID');
		}

		$bounty_id = $this->request->variable('bounty_id', 0);
		$bounty = $this->get_bounty($bounty_id);

		if (!$bounty || (int) $bounty['status'] !== self::STATUS_PENDING || (int) $bounty['poster_id'] !== (int) $this->user->data['user_id'])
		{
			trigger_error($this->language->lang('BOUNTY_NOT_YOUR_BOUNTY'));
		}

		$this->pay_out_bounty($bounty);

		$message = $this->language->lang('BOUNTY_APPROVE_SUCCESS') . '<br /><br /><a href="' . $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'bounty']) . '">&laquo; ' . $this->language->lang('BACK_TO_PREV') . '</a>';
		trigger_error($message);
	}

	/**
	 * Poster rejects a pending submission, sending the bounty back to the claimed state
	 */
	protected function handle_reject()
	{
		if (!check_form_key('bounty_action'))
		{
			trigger_error('FORM_INVALID');
		}

		$bounty_id = $this->request->variable('bounty_id', 0);
		$bounty = $this->get_bounty($bounty_id);

		if (!$bounty || (int) $bounty['status'] !== self::STATUS_PENDING || (int) $bounty['poster_id'] !== (int) $this->user->data['user_id'])
		{
			trigger_error($this->language->lang('BOUNTY_NOT_YOUR_BOUNTY'));
		}

		$sql = 'UPDATE ' . $this->points_bounty_table . '
			SET ' . $this->db->sql_build_array('UPDATE', [
				'status' => self::STATUS_CLAIMED,
				'submitted_time' => 0,
			]) . '
			WHERE bounty_id = ' . (int) $bounty_id . '
				AND status = ' . self::STATUS_PENDING;
		$this->db->sql_query($sql);

		$this->config->increment('points_notification_id', 1);
		$this->notification_manager->add_notifications('dmzx.ultimatepoints.notification.type.points', [
			'points_notify_id' => (int) $this->config['points_notification_id'],
			'points_notify_msg' => sprintf($this->language->lang('NOTIFICATION_BOUNTY_REJECTED'), $bounty['title']),
			'sender' => (int) $this->user->data['user_id'],
			'receiver' => (int) $bounty['claimer_id'],
			'mode' => 'bounty',
		]);

		$message = $this->language->lang('BOUNTY_REJECT_SUCCESS') . '<br /><br /><a href="' . $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'bounty']) . '">&laquo; ' . $this->language->lang('BACK_TO_PREV') . '</a>';
		trigger_error($message);
	}

	/**
	 * Poster cancels their own bounty while it is still open, refunding the escrow
	 */
	protected function handle_cancel()
	{
		if (!check_form_key('bounty_action'))
		{
			trigger_error('FORM_INVALID');
		}

		$bounty_id = $this->request->variable('bounty_id', 0);
		$bounty = $this->get_bounty($bounty_id);

		if (!$bounty || (int) $bounty['status'] !== self::STATUS_OPEN || (int) $bounty['poster_id'] !== (int) $this->user->data['user_id'])
		{
			trigger_error($this->language->lang('BOUNTY_NOT_YOUR_BOUNTY'));
		}

		$sql = 'DELETE FROM ' . $this->points_bounty_table . '
			WHERE bounty_id = ' . (int) $bounty_id . '
				AND status = ' . self::STATUS_OPEN;
		$this->db->sql_query($sql);

		if ($this->db->sql_affectedrows())
		{
			$poster_id = (int) $bounty['poster_id'];
			$reward = (float) $bounty['reward'];

			// Refund the escrowed reward
			$this->functions_points->add_points($poster_id, $reward);

			/**
			 * Event that is triggered after a bounty has been cancelled and refunded
			 *
			 * @event dmzx.ultimatepoints.bounty_cancelled_after
			 * @var int		bounty_id	The ID of the cancelled bounty
			 * @var int		poster_id	The user who posted (and cancelled) the bounty
			 * @var float	reward		The reward amount that was refunded
			 * @since 1.2.9
			 */
			$vars = [
				'bounty_id',
				'poster_id',
				'reward',
			];
			extract($this->dispatcher->trigger_event('dmzx.ultimatepoints.bounty_cancelled_after', compact($vars)));
		}

		$message = $this->language->lang('BOUNTY_CANCEL_SUCCESS') . '<br /><br /><a href="' . $this->helper->route('dmzx_ultimatepoints_controller', ['mode' => 'bounty']) . '">&laquo; ' . $this->language->lang('BACK_TO_PREV') . '</a>';
		trigger_error($message);
	}

	/**
	 * Pays out an approved bounty: releases the escrowed reward to the claimer
	 * and marks the bounty as completed. Used by both the direct-submit path
	 * (approval not required) and the poster-approval path.
	 *
	 * @param array $bounty
	 */
	protected function pay_out_bounty($bounty)
	{
		$bounty_id = (int) $bounty['bounty_id'];
		$poster_id = (int) $bounty['poster_id'];
		$claimer_id = (int) $bounty['claimer_id'];
		$reward = (float) $bounty['reward'];

		$sql = 'UPDATE ' . $this->points_bounty_table . '
			SET ' . $this->db->sql_build_array('UPDATE', [
				'status' => self::STATUS_COMPLETED,
				'paid_time' => time(),
			]) . '
			WHERE bounty_id = ' . $bounty_id . '
				AND status IN (' . self::STATUS_CLAIMED . ', ' . self::STATUS_PENDING . ')';
		$this->db->sql_query($sql);

		if (!$this->db->sql_affectedrows())
		{
			return;
		}

		// Release the escrowed reward to the claimer
		$this->functions_points->add_points($claimer_id, $reward);

		/**
		 * Event that is triggered after a bounty has been paid out to its claimer
		 *
		 * @event dmzx.ultimatepoints.bounty_paid_out
		 * @var int		bounty_id	The ID of the paid-out bounty
		 * @var int		poster_id	The user who posted the bounty
		 * @var int		claimer_id	The user who completed the bounty and received the reward
		 * @var float	reward		The reward amount that was paid out
		 * @since 1.2.9
		 */
		$vars = [
			'bounty_id',
			'poster_id',
			'claimer_id',
			'reward',
		];
		extract($this->dispatcher->trigger_event('dmzx.ultimatepoints.bounty_paid_out', compact($vars)));

		$this->config->increment('points_notification_id', 1);
		$this->notification_manager->add_notifications('dmzx.ultimatepoints.notification.type.points', [
			'points_notify_id' => (int) $this->config['points_notification_id'],
			'points_notify_msg' => sprintf($this->language->lang('NOTIFICATION_BOUNTY_PAID'), $this->functions_points->number_format_points($reward), $this->config['points_name']),
			'sender' => $poster_id,
			'receiver' => $claimer_id,
			'mode' => 'bounty',
		]);

		if ($this->phpbb_container->has('dmzx.mchat.settings') && $this->config['bounty_mchat_enable'])
		{
			$this->functions_points->mchat_message($claimer_id, $this->functions_points->number_format_points($reward), $this->language->lang('BOUNTY_MCHAT_PAID'), $this->config['points_name']);
		}
	}

	/**
	 * Releases claims that have been sitting unsubmitted past the configured
	 * claim time limit, sending the bounty back to the open pool. Checked
	 * inline on page load, the same way points_bank checks whether it's
	 * time to pay bank interest.
	 *
	 * @param int $claim_time_limit Hours, 0 = no limit
	 */
	protected function release_expired_claims($claim_time_limit)
	{
		if (!$claim_time_limit)
		{
			return;
		}

		$expire_before = time() - ($claim_time_limit * 3600);

		$sql = 'UPDATE ' . $this->points_bounty_table . '
			SET ' . $this->db->sql_build_array('UPDATE', [
				'claimer_id' => 0,
				'status' => self::STATUS_OPEN,
				'claimed_time' => 0,
			]) . '
			WHERE status = ' . self::STATUS_CLAIMED . '
				AND claimed_time < ' . (int) $expire_before;
		$this->db->sql_query($sql);
	}

	/**
	 * Fetches a single bounty row
	 *
	 * @param int $bounty_id
	 * @return array|null
	 */
	protected function get_bounty($bounty_id)
	{
		$sql = 'SELECT *
			FROM ' . $this->points_bounty_table . '
			WHERE bounty_id = ' . (int) $bounty_id;
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		return $row ?: null;
	}

	/**
	 * Assigns the open/claimed/pending bounties and a short completed history
	 * to the template
	 *
	 * @param array $checked_user
	 */
	protected function assign_bounty_list($checked_user)
	{
		$user_id = (int) $this->user->data['user_id'];

		$sql_array = [
			'SELECT' => 'b.*, up.username AS poster_username, up.user_colour AS poster_colour, uc.username AS claimer_username, uc.user_colour AS claimer_colour',
			'FROM' => [
				$this->points_bounty_table => 'b',
			],
			'LEFT_JOIN' => [
				[
					'FROM' => [USERS_TABLE => 'up'],
					'ON' => 'b.poster_id = up.user_id',
				],
				[
					'FROM' => [USERS_TABLE => 'uc'],
					'ON' => 'b.claimer_id = uc.user_id',
				],
			],
			'WHERE' => 'b.status IN (' . self::STATUS_OPEN . ', ' . self::STATUS_CLAIMED . ', ' . self::STATUS_PENDING . ')',
			'ORDER_BY' => 'b.status ASC, b.created_time DESC',
		];
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query_limit($sql, 30);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->assign_bounty_row($row, $user_id);
		}
		$this->db->sql_freeresult($result);

		$sql_array = [
			'SELECT' => 'b.*, up.username AS poster_username, up.user_colour AS poster_colour, uc.username AS claimer_username, uc.user_colour AS claimer_colour',
			'FROM' => [
				$this->points_bounty_table => 'b',
			],
			'LEFT_JOIN' => [
				[
					'FROM' => [USERS_TABLE => 'up'],
					'ON' => 'b.poster_id = up.user_id',
				],
				[
					'FROM' => [USERS_TABLE => 'uc'],
					'ON' => 'b.claimer_id = uc.user_id',
				],
			],
			'WHERE' => 'b.status = ' . self::STATUS_COMPLETED,
			'ORDER_BY' => 'b.paid_time DESC',
		];
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query_limit($sql, 10);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->assign_bounty_row($row, $user_id, true);
		}
		$this->db->sql_freeresult($result);
	}

	/**
	 * Assigns a single bounty row to the appropriate template block
	 *
	 * @param array $row
	 * @param int $user_id The currently viewing user
	 * @param bool $completed
	 */
	protected function assign_bounty_row($row, $user_id, $completed = false)
	{
		$status = (int) $row['status'];
		$is_poster = $user_id === (int) $row['poster_id'];
		$is_claimer = $user_id === (int) $row['claimer_id'];

		$template_data = [
			'BOUNTY_ID' => (int) $row['bounty_id'],
			'BOUNTY_TITLE' => $row['title'],
			'BOUNTY_DESCRIPTION' => $row['description'],
			'BOUNTY_REWARD' => $this->functions_points->number_format_points($row['reward']),
			'BOUNTY_POSTER' => get_username_string('full', $row['poster_id'], $row['poster_username'], $row['poster_colour']),
			'BOUNTY_CLAIMER' => $row['claimer_id'] ? get_username_string('full', $row['claimer_id'], $row['claimer_username'], $row['claimer_colour']) : '',
			'BOUNTY_CREATED' => $this->user->format_date($row['created_time']),
			'S_BOUNTY_OPEN' => $status === self::STATUS_OPEN,
			'S_BOUNTY_CLAIMED' => $status === self::STATUS_CLAIMED,
			'S_BOUNTY_PENDING' => $status === self::STATUS_PENDING,
			'S_BOUNTY_COMPLETED' => $status === self::STATUS_COMPLETED,
			'S_BOUNTY_IS_POSTER' => $is_poster,
			'S_BOUNTY_IS_CLAIMER' => $is_claimer,
			'S_BOUNTY_CAN_CLAIM' => $status === self::STATUS_OPEN && !$is_poster,
			'S_BOUNTY_CAN_CANCEL' => $status === self::STATUS_OPEN && $is_poster,
			'S_BOUNTY_CAN_SUBMIT' => $status === self::STATUS_CLAIMED && $is_claimer,
			'S_BOUNTY_CAN_REVIEW' => $status === self::STATUS_PENDING && $is_poster,
		];

		$this->template->assign_block_vars($completed ? 'bounty_completed' : 'bounty', $template_data);
	}
}
