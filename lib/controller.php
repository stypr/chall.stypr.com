<?php

	/* Controller classes
	Nothing to comment so much. commenting irritates me! */

	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;
	/* Default features for controllers; Abstract class */
	class Controller {
		protected $db;
		public function DefaultAction(){ $this->output_json(false); }

		public function is_auth(){
			// check if authenticated
			$session = $_SESSION['username'] . $_SERVER['REMOTE_ADDR'];
			if(secure_hash($session) == $_SESSION['session']){
				return true;
			}
			return false;
		}

		public function output_json($data){
			// return result as json
			header("Content-Type: application/json;charset=utf-8");
			echo json_encode($data);
			exit;
		}
		
		public function __construct(){
			global $query;
			$this->db = $query;
		}
	}

	/* User Controller */
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
		private function login_account(){
			$player = new PlayerInfo($this->db);
			$nickname = $this->db->filter($_POST['nickname'], 'auth');
			$password = $this->db->filter($_POST['password'], 'auth');
			$ip = $this->db->filter($_SERVER['REMOTE_ADDR'], 'auth');
			if((strlen($nickname) >= 3 && strlen($nickname) <= 100) &&
				(strlen($password) >= 4 && strlen($password) <= 100)){
				// I give very lenient options to users.
				// emails are accepted, yet the nickname is the first priority.
				$check_by_nick = $player->get_by_nickname($nickname);
				$check_by_mail = $player->get_by_username($nickname);
				if(!$check_by_nick->user_nickname && !$check_by_mail->user_nickname) return false;
				if($check_by_nick->user_nickname){
					$_nick = $check_by_nick->user_nickname;
					$_pass = $check_by_nick->user_pw;
				}else{
					$_nick = $check_by_mail->user_nickname;
					$_pass = $check_by_mail->user_pw;
				}
				if(!$_nick || !$_pass) return false;
				$encrypted_password = secure_hash($password);
				if($_pass === $encrypted_password && $_pass != ''){
					// on success, log access and set authentication
					$_user = $player->get_by_nickname($_nick);
					$_user->user_auth_date = date("Y-m-d H:i:s");
					$_user->user_auth_ip = $ip;
					$player->set($_user);
					// set auth..
					$_SESSION['username'] = $_user->user_id;
					$_SESSION['nickname'] = $_user->user_nickname;
					$_SESSION['session'] = secure_hash($_SESSION['username'] . $_SERVER['REMOTE_ADDR']);
					return true;
				}else{
					return false;
				}
			}else{
				return false;
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
		private function forgot_account(){}
		
		public function CheckAction(){
			$this->output_json($this->is_auth());
		}
		public function LoginAction(){
			if($this->is_auth()) $this->output_json(false);
			if($_POST) $this->output_json($this->login_account());
			$this->output_json(false);
		}
		public function LogoutAction(){
			if(!$this->is_auth()) $this->output_json(false);
			$_SESSION = [];
			session_destroy();
			$this->output_json(true);
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
		/*
		public function GetAction(){
			if(!$this->is_auth()) $this->output_json(false);
			$player = new PlayerInfo($this->db);
			$this->output_json($player->get_by_username($_SESSION['username']));
		}
		*/
	}

	/* Status Controller */
	class StatusController extends Controller {
		public function ScoreboardAction(){
			$player = new PlayerInfo($this->db);
			$log = new LoggingInfo($this->db);

			// get breakthrough count for players
			$break = $log->get_break_list();
			$ranker = $player->get_ranker();
			// calculate breakthrough points of users on each challenges.
			$_break = [];
			for($i=0;$i<count($break);$i++){
				$_break_user = ($break[$i]->log_id);
				$_break_point = ($break[$i]->rank);
				$_break[$_break_user] += 4-$_break_point;
			}
			// retrieve top rankers
			$result = ['total' => $player->get_count()];
			for($i=0;$i<count($ranker);$i++){
				$user = $ranker[$i]->user_id;
				$_break_count = $_break[$user] ? $_break[$user] : '';

				$result['ranker'][] = ['nickname' => $ranker[$i]->user_nickname,
					'score' => $ranker[$i]->user_score,
					'break_count' => $_break_count,
					'comment' => $ranker[$i]->user_comment,
					'last_solved' => $ranker[$i]->user_last_solved
				];
			}
			$this->output_json($result);
		}
		public function ChallengeAction(){
			$player = new PlayerInfo($this->db);
			// retrieve nickname from users
			$player_nick = $player->get_nickname();
			$log = new LoggingInfo($this->db);
			$chall = new ChallengeInfo($this->db);
			$chall_list = $chall->get_list();
			$_break = $log->get_break_list();
			$break = [];
			for($i=0;$i<count($_break);$i++){
				$_break_chall = $_break[$i]->log_challenge;
				$_break_user = $player_nick[$_break[$i]->log_id];
				$_break_date = $_break[$i]->log_date;
				$_break_rank = $_break[$i]->rank;
				$break[$_break_chall][] = ['user' => $_break_user,
					'date' => $_break_date,
					'rank' => $_break_rank
				];
			}
			$result = [];
			for($i=0;$i<count($chall_list);$i++){
				// get breakthrough and last-solved by log
				$_name = $chall_list[$i]->challenge_name;
				$_log = $log->get_by_challenge($_name);
				$_break = null;
				$_break_log = $_log[0]->log_id;
				$_last = null;
				$_last = end($_log)->log_date;
				
				$result[] = ['id' => $chall_list[$i]->challenge_id,
					'name' => $chall_list[$i]->challenge_name,
					'author' => $chall_list[$i]->challenge_by,
					'score' => $chall_list[$i]->challenge_score,
					'solver' => $chall_list[$i]->challenge_solve_count,
					'break' => $break[$chall_list[$i]->challenge_name],
					'author' => $chall_list[$i]->challenge_by,
					'last-solved' => $_last,
					'rate' => $chall_list[$i]->challenge_rate
				];
			}
			$this->output_json($result);
		}
		public function AuthAction(){
			$player = new PlayerInfo($this->db);
			$player_nick = $player->get_nickname();
			$log = new LoggingInfo($this->db);
			$log_list = $log->get_by_type('Correct');
			$result = [];
			for($i=(count($log_list)-1);$i>0;$i--){
				$result[] = ['no' => $log_list[$i]->log_no,
					'nick' => $player_nick[$log_list[$i]->log_id],
					'chall' => $log_list[$i]->log_challenge,
					'date' => $log_list[$i]->log_date
				];
			}
			$this->output_json($result);
		}
		public function FameAction(){}
		public function ProfileAction(){
			$player = new PlayerInfo($this->db);
			$log = new LoggingInfo($this->db);
			$chall = new ChallengeInfo($this->db);
			$_GET['nickname'] = (!$_GET['nickname']) ? ($_SESSION['nickname']) : ($_GET['nickname']);
			$nickname = $this->db->filter($_GET['nickname'], "auth");
			// check if viewed by admin

			$admin_mode = false;
			if($_SESSION['username']){
				$_check = $player->get_by_username($_SESSION['username']);
				if($_check->user_permission == 9 || $_SESSION['username'] == $_check->user_id){
					$admin_mode = true;
				}
			}

			// retreive by nickname
			$profile = $player->get_by_nickname($nickname);
			if(!$profile->user_nickname) $this->output_json(false);

			// get breakpoints -> add that to solved challenges
			$break = $log->get_break_list();
			$_break = [];
			for($i=0;$i<count($break);$i++){
				$_break_user = ($break[$i]->log_id);
				$_break_chall = ($break[$i]->log_challenge);
				$_break_rank = ($break[$i]->rank);
				$_break_point = 4-$_break_rank;
				//$_break_user
				if($_break_user === $profile->user_id){
					$_break[$_break_chall] = [
						'break_point' => $_break_point,
						'break_rank' => $_break_rank,
					];
				}
			}
			$normal = $log->get_by_username($profile->user_id);
			$_normal = [];
			for($i=0;$i<count($normal);$i++){
				if($normal[$i]->log_type == "Correct"){
					$_chall = $chall->get_by_name($normal[$i]->log_challenge);
					$_normal[] = ['chall_name' => $normal[$i]->log_challenge,
						'chall_solve_date' => $normal[$i]->log_date,
						'chall_score' => $_chall->challenge_score,
						'chall_break' => $_break[$normal[$i]->log_challenge],
					];
				}
			}

			// only list the domain name, unless viewed by admin.
			if(!$admin_mode){
				$email = '@'. explode('@', $profile->user_id)[1];
			}else{
				$email = $profile->user_id;
			}
			$result = ['nick' => $profile->user_nickname,
				'username' => $email,
				'last_solved' => $profile->user_last_solved,
				'comment' => $profile->user_comment,
				'join_date' => explode(' ', $profile->user_join_date)[0],
				'rank' => $profile->user_rank, 
				'score' => $profile->user_score,
				'badge' => null, // tbd
				// the service uses gravatar, you can customize it if you wish to.
				'profile_picture' => 'https://www.gravatar.com/avatar/'.
					md5($profile->user_id) . '?v=3&s=200&'.
					'd=//github.com/identicons/'.rand(1,500).'.png',
				'solved' => $_normal,
			];

			$this->output_json($result);
		}
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

	/* WeChall Controller */
	class WeChallController extends Controller {
		public function ListUserAction(){}
		public function RankAction(){}
		public function PushAction(){}
	}

	/* Default Controller */
	class DefaultController extends Controller {
		public function DefaultAction(){
			// load static page
			$template = new Template();
			$template->include("index");
		}
	}

?>