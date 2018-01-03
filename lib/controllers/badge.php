<?php

	/* lib/controllers/badge.php */

	class BadgeController extends Controller {

		public function GetAction() {
			// Get badge data
			$nickname = $this->auth_filter( $_GET['nickname'] );
			if ( !$nickname ) {
				if ( $_SESSION['nickname'] ) {
					$nickname = $_SESSION['nickname'];
				} else {
					$this->output( false );
				}
			}
			$badge = [];
			$user = new UserInfo;
			$chall = new ChallengeInfo;
			$log = new LoggingInfo;
			$me = $user->get( ['user_nickname' => $nickname], 1 );

			// If user exists, return the badges
			if ( $me->user_nickname && $nickname === $me->user_nickname ) {
				// Admin (perm == 9)
				if ( $me->user_permission == 9 ) {
					$badge[] = ['name' => 'Admin', 'type' => 'red'];
				}
				// Contrib (if any by creator)
				$contrib = $chall->get( ['challenge_by' => $me->user_nickname],  1);
				if ( $contrib->challenge_by && $me->user_permission != 9 ) {
					$badge[] = ['name' => 'Contrib', 'type' => 'yellow'];
				}
				// Pwner (perm == 3)
				if ( $me->user_permission == 3 ) {
					$badge[] = ['name' => 'Pwner', 'type' => 'purple'];
				}
				// AllClear (count(solved) === count(all_open))
				$stmt = ['log_id' => $me->user_id, 'log_type' => 'Correct'];
				$chall_solved = $log->count( $stmt );
				$stmt = ['challenge_is_open' => 1];
				$chall_available = $chall->count( $stmt );
				if ( $chall_solved == $chall_available ) {
					$badge[] = ['name' => 'AllClear', 'type' => 'green'];
				}
				// Top 3 (Brand new crown..)
				if ( $me->user_permission != 9 ) {
					if ( $me->user_rank <= 3) {
						$badge[] = ['name' => '&#128081;', 'type' => 'black'];
					// Top 10 (rank <= 10)
					} elseif ( $me->user_rank <= 10 ) {
						$badge[] = ['name' => 'Top10', 'type' => 'blue'];
					}
				}
				// Flame (solved_break_pt >= 3)
				$me_break = $log->get_break( $me->user_id );
				$me_break_pt = 0;
				foreach ( $me_break as $key => $val ) {
					$me_break_pt += $val['break_point'];
				}
				if ( $me_break_pt >= 3 ) {
					$badge[] = ['name' => '&#128293;', 'type' => 'red'];
				}

				$this->output( $badge );
			}
			$this->output( false );
		}

	}
?>