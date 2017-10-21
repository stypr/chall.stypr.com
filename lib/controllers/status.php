<?php

	/* lib/controllers/status.php */

	class StatusController extends Controller {

		public function ScoreboardAction(){
			// Get top rankers

			$user = new UserInfo;
			$log = new LoggingInfo;
			// Get Top 50 and User Total
			$top_user_order = ['user_score DESC', 'user_auth_date ASC'];
			$top_user = $user->get( [], 50, [], $top_user_order );
			$result = ['total' => $user->count()];
			// make user breakpoint into dict
			$all_break = $log->get_break();
			foreach ( $all_break as $key => $val ) {
				$all_break_dict[$val['log_id']] += $val['break_point'];
			}
			// Append top player information to result
			foreach  ( $top_user as $key => $val ) {
				$user_id = $val->user_id;
				$break_point = $all_break_dict[$user_id] ?: 0;
				$result['ranker'][] = ['nickname' => $val->user_nickname,
					'score' => $val->user_score,
					'break_count' => $break_point,
					'comment' => $val->user_comment,
					'last_solved' => $val->user_last_solved,
				];
			}
			$this->output( $result );
		}

		public function ChallengeAction(){
			// Retrieve Challenge status

			$user = new UserInfo;
			$chall = new ChallengeInfo;
			$log = new LoggingInfo;
			$result = [];
			$chall_all = $chall->get( ['challenge_is_open' => 1] );
			$user_nick_list = $user->get_nick_dict();
			// Get top breakthrough of all challenges
			$chall_break = $log->get_break();
			$chall_break_log = [];
			foreach ( $chall_break as $key => $val ) {
				$chall_break_log[$val['log_challenge']][] = ['rank' => $val['rank'],
					'user' => $user_nick_list[$val['log_id']],
					'date' => $val['log_date'],
				];
			}
			// well, print it out
			foreach ( $chall_all as $key => $val ) {
				$log_last = ['log_challenge' => $val->challenge_name, 'log_type' => 'Correct'];
				$log_last = $log->get( $log_last, 1, ['log_date'], ['log_date DESC'] );

				$result[] = ['id' => $val->challenge_id,
					'name' => $val->challenge_name,
					'author' => $val->challenge_by,
					'solver' => $val->challenge_solve_count,
					'score' => $val->challenge_score,
					'rate' => $val->challenge_rate,
					'break' => $chall_break_log[$val->challenge_name],
					'last-solved' => $log_last->log_date,
				];
			}
			$this->output($result);
		}

		public function AuthAction(){
			// Retrieve Auth Log
			$user = new UserInfo;
			$log = new LoggingInfo;
			$result = [];
			$user_nick_list = $user->get_nick_dict();
			$log_all = $log->get( ['log_type' => 'Correct'], null, [], ['log_date DESC'] );
			foreach ( $log_all as $key => $val ) {
				$result[] = ['no' => $val->log_no,
					'nick' => $user_nick_list[$val->log_id],
					'chall' => $val->log_challenge,
					'date' => $val->log_date,
				];
			}
			$this->output($result);
		}

		public function ProfileAction(){
			// Get profile info of user
			// returns current user's profile on null parameter

			$user = new UserInfo;
			$log = new LoggingInfo;
			$chall = new ChallengeInfo;
			$nickname = $this->auth_filter( $_GET['nickname'] ?: $_SESSION['nickname'] );
			$me = $user->get( ['user_nickname' => $nickname ], 1 );
			if ( !$me->user_nickname ) $this->output(false);
			// Godmode is only available for the account owner and admin.
			$godmode = false;
			if( $_SESION['nickname'] ) {
				if ( $nickname === $_SESSION['nickname'] || $me->user_permission == 9 ) {
					$godmode = true;
				}
			}
			// Only godmode user can view the mail address.
			$email = ( $godmode ) ? ( '@' . explode( $me->user_id )[1] ) : ( $me->user_id );
			// get user's breakthrough info; make it into dict format
			$me_break = $log->get_break( $me->user_id );
			$me_break_dict = [];
			foreach($me_break as $key => $val){
				$me_break_dict[$val['log_challenge']] = [
					'break_point' => $val['break_point'],
					'break_rank' => $val['rank'],
				];
			}
			// Parse solved challenges
			$me_solved = [];
			$me_log = $log->get( ['log_id' => $me->user_id] );
			if ( is_array( $me_log ) ) {
				for ( $i=0; $i<count($me_log); $i++) {
					if ( $me_log[$i]->log_type == "Correct" ) {
						$solved_chall_name = $me_log[$i]->log_challenge;
						$solved_chall = $chall->get( ['challenge_name' => $solved_chall_name], 1 );
						$me_solved[] = ['chall_name' => $solved_chall_name,
							'chall_solve_date' => $me_log[$i]->log_date,
							'chall_score' => $solved_chall->challenge_score,
							'chall_break' => $me_break_dict[$solved_chall_name],
						];
					}
				}
			}
			// return a favorable output
			$result = ['nick' => $me->user_nickname,
				'username' => $email,
				'last_solved' => $me->user_last_solved,
				'comment' => $me->user_comment,
				'join_date' => explode( ' ', $me->user_join_date )[0],
				'rank' => $me->user_rank,
				'score' => $me->user_score,
				'badge' => null, // TBD
                // gravatar by default, you can customized this.
                'profile_picture' => '//www.gravatar.com/avatar/'.
                    md5( $me->user_id ) . '?v=3&s=100&'.
                    'd=//github.com/identicons/' . rand( 1, 500 ) . '.png',
                'solved' => $me_solved,
            ];
            $this->output( $result );
		}

		public function FameAction(){} // TBD

	}

?>