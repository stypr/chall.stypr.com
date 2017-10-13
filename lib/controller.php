<?php

	/* lib/controller.php
	Will be on continuous updates, should be buggy */

	// PHPMailer: should be installed by composer! //
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;

	// Default feature for the controller //
	class Controller {
		protected $db;
		public function DefaultAction() { $this->output_json( false ); }

		public function is_auth() {
			// Check whether the user is authenticated
			$session = $_SESSION['username'] . $_SERVER['REMOTE_ADDR'];
			if ( secure_hash( $session ) == $_SESSION['session'] ) {
				return true;
			}
			return false;
		}

		public function auth_filter( $input ) {
			return $this->db->filter( $input, "auth" );
		}

		public function output( $data ) {
			// Return output in JSON //
			header( "Content-Type: application/json;charset=utf-8" );
			echo json_encode( $data );
			exit;
		}

		public function __construct(){
			global $query;
			$this->db = $query;
		}
	}

	// User Controller //
	class UserController extends Controller {
		// login, register, modify, find, recover
		private function recover_account(){
			$log = new LoggingInfo($this->db);
			$player = new PlayerInfo($this->db);
			$code = $this->db->filter($_POST['recovery_code']);
			$password = secure_hash($this->db->filter($_POST['password'], "auth"));

			$_check_log = $log->get_by_info($code);
			if($_check_log->log_id && $_check_log->log_no >= 0 &&
				$_check_log->log_info === $code){
				// remove all request logs
				$_check_del = $log->get_by_type("Recovery");
				for($i=0;$i<count($_check_del);$i++){
					$_check_del_user = $_check_del[$i]->log_id;
					if($_check_del_user === $_check_log->log_id){
						$log->del($_check_del[$i]);
					}
				}
				// change password
				$p = $player->get_by_username($_check_log->log_id);
				$p->user_pw = $password;
				$player->set($p);
				$this->output_json(true);
			}
			$this->output_json(false);
		}
		private function find_account(){
			$log = new LoggingInfo($this->db);
			$player = new PlayerInfo($this->db);
			$username = $this->db->filter($_POST['username'], 'auth');
			$_check = $player->get_by_username($username);
			//generate csprng random string
			$code = '';
			$_table = '0123456789ABCDEFGHIJKLMNOPQRSTUVWZYZabcdefghijklmnopqrstuvwxyz';
			$_table_len = strlen($_table) - 1;
			for ($i=0; $i<32; $i++){
				$code .= $_table[random_int(0,$_table_len)];
			}
			if($username != '' && $_check->user_id === $username){
				// recovery count 3
				$_check_log = $log->get_by_type("Recovery");
				$_list_log = [];
				for($i=0;$i<count($_check_log);$i++){
					$_check_log_user = $_check_log[$i]->log_id;
					$_list_log[$_check_log_user] += 1;
				}
				// recovery count check
				// this count will be reset on a successful confirmation.
				if($_list_log[$_check->user_id] > 3) $this->output_json('exceed');
				// log the code
				$_log = new Logging();
				$_log->log_id = $_check->user_id;
				$_log->log_type = 'Recovery';
				$_log->log_challenge = '';
				$_log->log_date = date("Y-m-d H:i:s");
				$_log->log_info = $code;
				$log->set($_log);
				$nickname = $_check->user_nickname;
				try {
					// uses PHPMailer
					$mail = new PHPMailer;
					$mail->SMTPDebug = 0;
					$mail->isSMTP();
					$mail->CharSet="UTF-8";
					$mail->Host = 'smtp.gmail.com';
					$mail->Port = 587;
					$mail->SMTPSecure = 'tls';
					$mail->SMTPAuth = true;
					$mail->Username = __GMAIL_USER__;
					$mail->Password = __GMAIL_PASS__;
					$mail->SMTPSecure = 'tls';
					$mail->Port = 587;
					$mail->setFrom('86exploit@gmail.com', 'Harold Kim');
					$mail->addAddress($_check->user_id);
					$mail->isHTML(true);
					$mail->Subject = 'Hello, ' . $nickname;
					$mail->Body = "Hi " . $nickname . ",<br><br>" .
						"It seems like you or someone pretending to be you has requested a password request.<br>" .
						"If you've not requested this message, Please ignore this mail." .
						"<hr>" .
						"Please <a href='" . __HOST__ . "#/user/find/" . $code . "'>click here</a> " .
						"to continue your password recovery request.";
					$mail->send();
					$this->output_json('done');
				} catch (Exception $e) {
					$this->output_json('fail');
				}

			}else{
				$this->output_json('nope');
			}
		}

		private function register_account(){
			$player = new PlayerInfo($this->db);
			$username = $this->db->filter($_POST['username'], 'auth');
			$nickname = $this->db->filter($_POST['nickname'], 'auth');
			$password = $this->db->filter($_POST['password'], 'auth');
			$ip = $this->db->filter($_SERVER['REMOTE_ADDR'], 'auth');

			if((strlen($username) >= 5 && strlen($username) <= 100) &&
				(strlen($password) >= 4 && strlen($password) <= 100) &&
				(strlen($nickname) >= 3 && strlen($nickname) <= 20)){
				$check_by_nick = $player->get_by_nickname($nickname);
				$check_by_mail = $player->get_by_username($username);
				if($check_by_nick->user_nickname) $this->output_json('duplicate_nick');
				if($check_by_mail->user_nickname) $this->output_json('duplicate_mail');
				if(!filter_var($username, FILTER_VALIDATE_EMAIL)){
					$this->output_json('email_format');
				}
				$encrypted_password = secure_hash($password);
				// generate new player
				$new_player = new Player();
				$new_player->user_id = $username;
				$new_player->user_pw = $encrypted_password;
				$new_player->user_nickname = $nickname;
				$new_player->user_score = 0;
				$new_player->user_join_date = date("Y-m-d H:i:s");
				$new_player->user_join_ip = $ip;
				$new_player->user_permission = 0;
				$player->set($new_player);
				$this->output_json('true');

			}else{
				$this->output_json('size');
			}
		}
		private function modify_account(){
			if(isset($_POST['password'])) $password = $this->db->filter($_POST['password'], 'auth');
			if(isset($_POST['comment'])) $comment = $this->db->filter($_POST['comment'], 'memo');
			$player = new PlayerInfo($this->db);
			$user = $player->get_by_username($_SESSION['username']);
			if($password) $user->user_pw = secure_hash($password);
			if($comment) $user->user_comment = $comment;
			$player->set($user);
			$this->output_json(true);
		}

		public function CheckAction(){
			// Check whether the user is logged in
			$this->output( $this->is_auth() );
		}

		public function LoginAction(){
			if ( $this->is_auth() ) $this->output( false );
			$user = new UserInfo;
			$nick = $this->auth_filter( $_POST['nickname'] );
			$pass = $this->auth_filter( $_POST['password'] );
			$addr = $this->auth_filter( $_SERVER['REMOTE_ADDR'] );
			// Check length
			if ( strlen( $nick ) >= 3 && strlen( $nick ) <= 100 &&
				strlen( $pass ) >= 4 && strlen( $pass ) <= 100 ){
				// I give very lenient options to users.
				// Emails are *also* accepted, yet nicknames are the first priority.
				$check_nick = $user->get( ['user_nickname' => $nick], 1 );
				$check_mail = $user->get( ['user_id' => $nick], 1 );
				// Get the result
				if ( $check_nick->user_nickname ) {
					$result_nick = $check_nick->user_nickname;
					$result_pass = $check_nick->user_pw;
				}
				elseif ( $check_mail->user_nickname ) {
					$result_nick = $check_mail->user_nickname;
					$result_pass = $check_nick->user_pw;
				}
				else {
					$this->output( false );
				}
				if ( !( $result_nick && $result_pass ) ) $this->output( false );
				// Verify result
				$encrypted_pass = secure_hash($pass);
				if ( $result_pass === $encrypted_pass && $pass != '' &&
					$result_user === $nick ) {
					// Log access and set authentication.
					$me = $user->get( ['user_nickname' => $result_nick], 1);
					$me->user_auth_date = date("Y-m-d H:i:s");
					$me->user_auth_ip = $addr;
					$user->set( $me );
					$_SESSION['username'] = $me->user_id;
					$_SESSION['nickname'] = $me->user_nickname;
					$_SESSION['session'] = secure_hash( $me->user_id . $addr );
			}
		}

		public function LogoutAction(){
			if( !$this->is_auth() ) $this->output( false );
			// Destroy session
			$_SESSION = [];
			session_destroy();
			$this->output( true );
		}
		public function RegisterAction(){
			if($this->is_auth() || !$_POST) $this->output_json(false);
			$this->register_account();
		}
		public function FindAction(){
			// find user -> mail request
			if($this->is_auth() || !$_POST) $this->output_json(false);
			$this->find_account();
		}
		public function RecoverAction(){
			// find user -> password change
			if($this->is_auth() || !$_POST) $this->output_json(false);
			$this->recover_account();
		}
		public function EditAction(){
			if(!$this->is_auth() || !$_POST) $this->output_json(false);
			$this->modify_account();
		}
	}

	/* Status Controller */
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
			// parse solved
			$me_solved = [];
			$me_log = $log->get( ['log_id' => $me->user_id] );
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
			// return a favorable output
			$result = ['nick' => $me->user_nickname,
				'username' => $email,
				'last_solved' => $me->user_last_solved,
				'comment' => $profile->user_comment,
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

	/* Challenge Controller */
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
		private function list_challenges(){
			if(!$this->is_auth()) $this->output_json(false);
			$chall = new ChallengeInfo($this->db);
			$_list = $chall->get_list();
			$_solved = $this->list_solved();
			for($i=0;$i<count($_list);$i++){
				$_list[$i]->challenge_solved = false;
				if(in_array($_list[$i]->challenge_name, $_solved)){
					$_list[$i]->challenge_solved = true;
				}
				$_list[$i]->challenge_flag = null;
			}
			$this->output_json($_list);
		}
		public function ListAction(){
			if(!$this->is_auth()) $this->output_json(false);
			$this->list_challenges();
		}
		public function AuthAction(){
			if(!$this->is_auth() || !$_POST) $this->output_json(false);
			$chall = new ChallengeInfo($this->db);
			$profile = new PlayerInfo($this->db);
			$log = new LoggingInfo($this->db);
			$flag = $this->db->filter($_POST['flag'], 'auth');
			$_chall = $chall->get_by_flag($flag);
			if($_chall->challenge_flag && $flag !== '' &&
				$_chall->challenge_is_open == "1"){
				// check if solved
				$_solved = $log->get_by_username($_SESSION['username']);
				for($i=0;$i<count($_solved);$i++){
					if($_solved[$i]->log_challenge === $_chall->challenge_name &&
					$_solved[$i]->log_type == "Correct"){
						$this->output_json("already-solved");
					}
				}
				// add score and solve count
				$me = $profile->get_by_username($_SESSION['username']);
				$me->user_score += $_chall->challenge_score;
				$me->user_last_solved = date("Y-m-d H:i:s");
				$profile->set($me);
				$_chall->challenge_solve_count += 1;
				$chall->set($_chall);
				// add success log
				$_log = new Logging();
				$_log->log_id = $_SESSION['username'];
				$_log->log_type = 'Correct';
				$_log->log_challenge = $_chall->challenge_name;
				$_log->log_date = date("Y-m-d H:i:s");
				$_log->log_info = '';
				$log->set($_log);
				// update by wechall
				if(__WECHALL__ !== "__WECHALL__") @update_wechall();
				// return msg
				$this->output_json("success");
			}else{
				$_log = new Logging();
				$_log->log_id = $_SESSION['username'];
				$_log->log_type = 'Wrong';
				$_log->log_challenge = $_chall->challenge_name;
				$_log->log_date = date("Y-m-d H:i:s");
				$_log->log_info = (string) $flag;
				$log->set($_log);
				$this->output_json("nope");
			}
		}
		public function RateAction(){}
	}

	// WeChall Controller //
	class WeChallController extends Controller {
		public function __construct() {
			Controller::__construct();
			// Check feature availability
			if ( __WECHALL__ == "__WECHALL__" || __WECHALL__ == "" ) {
				$this->output( "* WeChall feature disabled." );
			}
			// Check if the request is from WeChall
			$check = $this->auth_filter( $_GET['authkey'] );
			if ( $check !== __WECHALL__ ) {
				$this->output( "* Failed Authentication." );
			}
		}
		public function VerifyAction(){
			// Verify user info and return in WeChall Format
			// Valid Output: 1 | 0
			$user = new UserInfo;

			$nickname = $this->auth_filter( $_GET['username'] );
			$mailaddr = $this->auth_filter( $_GET['email'] );
			$check = ( $user->get( ['user_id' => $mailaddr], 1 )->user_nickname === $nickname &&
				$user->get( ['user_nickname' => $nickname], 1 )->user_id === $mailaddr );

			echo ($check) ? '1' : '0';
			exit;
		}
		public function RankAction(){
			// Retrieve user information in WeChall format
			// Valid Output: username:rank:score:maxscore:challssolved:challcount:usercount
			$user = new UserInfo;
			$chall = new ChallengeInfo;
			$log = new LoggingInfo;

			$nickname = $this->auth_filter( $_GET['username'] );
			$me = $user->get( ['user_nickname' => $nickname], 1 );
			if ( !$me->user_nickname ) $this->output( false );
			$me_solved_count = $log->count( ['log_type' => 'Correct', 'log_id' => $me->user_id] );

			$chall_total_score = $chall->sum( 'challenge_score', ['challenge_is_open' => 1] );
			$chall_total_count = $chall->count( ['challenge_is_open' => 1] );
			$user_total_count = $user->count();

			// Check if user actually exist..
			$out = [];
			if ( $user->get( ['user_id' => user_id], 1 )->user_nickname === $nick ) {
				$out[] = $nickname;
				$out[] = $me->user_rank;
				$out[] = $me->user_score;
				$out[] = $chall_total_score;
				$out[] = $me_solved_count;
				$out[] = $chall_total_count;
				$out[] = $user_total_count;
			}
			echo implode(':', $out);
			exit;
		}
	}

	// Default Controller //
	class DefaultController extends Controller {
		public function DefaultAction() {
			$template = new Template();
			// Checks for the CTF Mode
			if ( __CTF__ === true ) {
				if ( is_after( __CTF_START__ ) && !is_after( __CTF_END__ ) ){
					$template->include("index");
				} else {
					$template->include("ready");
				}
				exit;
			}
			$template->include("index");
		}
	}

?>