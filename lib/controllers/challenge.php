<?php

	/* lib/controllers/challenge.php */

	class ChallengeController extends Controller {

		private function list_solved(): array{
			if(!$_SESSION['username']) return [];
			$log = new LoggingInfo($this->db);
			$log = $log->get_by_username($_SESSION['username']);
			$_solved = [];
			for($i=0;$i<count($log);$i++){
				if($log[$i]->log_type == "Correct") $_solved[$i] = $log[$i]->log_challenge;
			}
			return $_solved;
		}

		public function ListAction(){
			if( !$this->is_auth() ) $this->output( false );
			// Add filter to hide the flag
			$chall = new ChallengeInfo;
			$chall_filter = ['*', 'NULL AS challenge_flag'];
			$chall_all = $chall->get( ['challenge_is_open' => 1], null, $chall_filter );
			$this->output($chall_all);
		}

		public function AuthAction(){
			if( !$this->is_auth() ) $this->output( false ) ;

			$chall = new ChallengeInfo;
			$user = new UserInfo;
			$log = new LoggingInfo;

			$flag = $this->auth_filter( $_POST['flag'] );
			if ( !$flag ) $this->output( false );

			// You can customize this :)
			$flag_prefix = "flag{";
			$flag_suffix = "}";
			// This adds/removes prefix and suffix depending on its existance
			$s = 0; $e = 0;
			if ( !substr_count( $flag, $flag_prefix ) ) $flag = $flag_prefix . $flag;
			if ( !substr_count( $flag, $flag_suffix ) ) $flag = $flag . $flag_suffix;
			// Parse the real flag if there are multiple prefixes and suffixes
			if ( substr_count( strtolower($flag), $flag_prefix ) >= 2 ) {
				$flag = substr( $flag, strripos( $flag, $flag_prefix ) );
				$flag = substr( $flag, 0, stripos( $flag, $flag_suffix ) + 1 );
			}

			$check_flag = $chall->get( ['challenge_flag' => $flag, 'challenge_is_open' => 1], 1 );
			$me = $user->get( ['user_id' => $_SESSION['username']], 1 );
			if ( $check_flag->challenge_flag === $flag && $flag != '' &&
				$check_flag->challenge_is_open == "1") {

				// Check if user already solved this challenge
				$chall_solved_query = ['log_id' => $me->user_id,
					'log_challenge' => $check_flag->challenge_name,
					'log_type' => 'Correct' ];
				$chall_solved = $log->get( $chall_solved_query, 1 );
				if ( $chall_solved->log_challenge == $check_flag->challenge_name &&
					$chall_solved->log_type == "Correct" ) {
					$this->output( "already-solved" );
				}

				// Add score to user, SolverCount++, Add Log
				$me->user_score += $check_flag->challenge_score;
				$me->user_last_solved = date( "Y-m-d H:i:s" );
				$user->set( $me );

				$check_flag->challenge_solve_count += 1;
				$chall->set( $check_flag );

				$log_new = new Logging;
				$log_new->log_id = $me->user_id;
				$log_new->log_type = 'Correct';
				$log_new->log_challenge = $check_flag->challenge_name;
				$log_new->log_date = date( "Y-m-d H:i:s" );
				$log_new->log_info = '';
				$log->set( $log_new );

				// Update to WeChall if the feature is enabled.
				if( __WECHALL__ !== "__WECHALL__" ) @update_wechall();

				$this->output( "success" );
			}else{
				$log_new = new Logging();
				$log_new->log_id = $_SESSION['username'];

				$log_new->log_type = 'Wrong';
				$log_new->log_info = $flag;
				$log_new->log_date = date( "Y-m-d H:i:s" );
				$log->set( $log_new );
				$this->output( "nope" );
			}
		}

		public function RateAction(){}

	}

?>