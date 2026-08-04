# Ultimate Points Extension Changelog

## Changes in 1.3.1
- Migrated the entire extension from the legacy `$this->user->lang['KEY']` pattern to the standard `\phpbb\language\language` service (`$this->language->lang('KEY')`) — 20 files, no functional change.
- Refactored the UCP controller (`ucp/ucp_ultimatepoints_module.php`) to a thin module shell delegating to a proper dependency-injected `controller/ucp_controller.php` service, removing all `global` statements from the UCP code path except the one unavoidable `$phpbb_container` bridge.
- Fixed a duplicate property/constructor-parameter/assignment bug in `core/points_main.php` that could cause a fatal "Cannot redeclare property" error.

## Changes in 1.3.0
- Added the Bounty Board: post a task with an escrowed reward, another member claims and submits it, paid out instantly or after poster approval depending on an ACP toggle.
- Added Duel/Wager: 1v1 points wagers between members, resolved by automatic coin-flip or moderator decision, with escrow on accept and full abuse-prevention (max open challenges, expiry refunds, self-resolve protection).
- Added a stable public API service (`dmzx.ultimatepoints.api`) exposing `add_points()`, `substract_points()`, `set_points()`, `get_points()`, `format_points()`, `get_points_name()` for third-party extensions.
- Added dispatcher events for both new features (`bounty_posted_after`, `bounty_claimed_after`, `bounty_paid_out`, `bounty_cancelled_after`, `duel_challenged_after`, `duel_accepted_after`, `duel_resolved_after`).
- Fixed a pre-existing bug where the Bounty tab was invisible on 6 of 8 sibling pages due to a missing `U_USE_BOUNTY` template variable.

## Changes in 1.2.9
- phpBB 4.0.x compatibility: fixed Font Awesome 6 icon rendering (front-end and ACP), cross-version avatar rendering (`phpbb_get_user_avatar()` vs. `avatar.helper`), and modernized the ACP icon-picker's icon list.
- Security: fixed a SQL injection vector in `set_points_values()` (now uses `sql_build_array()` instead of raw string interpolation).
- Security: replaced direct `$_POST` superglobal access with `$this->request` in the ACP controller.
- Fixed two assignment-instead-of-comparison bugs (`=` vs `==`) in the lottery flow, one of which also referenced a non-existent database column.
- Fixed a bug where the bank balance (`USER_BALANCE`, `USER_WITHDRAW`, `MAIN_BANK_HAVE`) always displayed as "1.00" instead of the real holding, and hardened bank/main page code against PHP 8 "array offset on bool" warnings for users without a bank account yet.
- Fixed a template bug where log table rows never alternated background colour.
- Added dispatcher events (`dmzx.ultimatepoints.add_points_after`, `substract_points_after`, `set_points_after`, `set_bank_after`, `lottery_winner_selected`, `transfer_after`, `robbery_after`, `group_transfer_after`, `resync_after`) so third-party extensions can react to point mutations without querying the database directly.
- Added a stable public API service (`dmzx.ultimatepoints.api`) exposing `add_points()`, `substract_points()`, `set_points()`, `get_points()`, `format_points()`, `get_points_name()` for third-party extensions.
- `composer.json`: widened the `phpbb/phpbb` constraint to allow 4.x (previously hard-excluded `<4.0.0@dev`), raised the PHP floor from 7.1.3 to 7.4.
- Added `README.md` and explicit `methods` in `config/routing.yml`.

## Changes in 1.2.8
- Add email notifications.
- Code clean-up.

## Changes in 1.2.7
- Short syntax code.
- Code updates.
- Language file added for ACP.
- Code clean-up.
