<?php

	/* lib/controllers/user.php */

	class UserController extends Controller {
		// login, register, modify, find, recover
		public function RecoverAction(){
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
		private function FindAction(){
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

		public function RegisterAction(){
			$user = new UserInfo;

			$user = $this->auth_filter( $_POST['username'] );
			$pass = $this->auth_filter( $_POST['nickname'] );
			$nick = $this->auth_filter( $_POST['password'] );
			$addr = $this->auth_filter( $_SERVER['REMOTE_ADDR'] );

			if ( strlen( $user ) >= 5 && strlen( $user ) <= 100 &&
				strlen( $pass ) >= 5 && strlen( $pass ) <= 100 &&
				strlen( $nick ) >= 3 && strlen( $nick ) <= 20 ) {

				$check_nick = $user->get( ['user_nickname' => $nick], 1 );
				$check_mail = $user_>get( ['user_id' => $user], 1 );

				if($check_nick->user_nickname) $this->output( 'duplicate_nick' );
				if($check_mail->user_nickname) $this->output( 'duplicate_mail' );
				if( !filter_var( $user, FILTER_VALIDATE_EMAIL) ) {
					$this->output('email_format');
				}
				$encrypted_password = secure_hash($password);
				// generate new player
				$me = new User;
				$me->user_id = $user;
				$me->user_pw = $encrypted_password;
				$me->user_nickname = $nick;
				$me->user_score = 0;
				$me->user_join_date = date( "Y-m-d H:i:s" );
				$me->user_join_ip = $addr;
				$me->user_permission = 0;
				$user->set( $me );
				$this->output_json( 'true' );
			}else{
				$this->output_json( 'size' );
			}
		}

		public function EditAction(){
			if( !$this->is_auth() ) $this->output( false );
			$user = new UserInfo;
			$me = $user->get( ['user_id' => $_SESSION['username']], 1 );
			if( isset( $_POST['password'] ) ) {
				$new_password = $this->auth_filter( $_POST['password'] );
				if ( $new_password ) $me->user_pw = secure_hash( $new_password );
			}
			if( isset( $_POST['comment'] ) ) {
				$new_comment = $this->db->filter( $_POST['comment'], "memo" );
				if ( $new_comment ) $me->user_comment = $new_comment;
			}
			$user->set($me);
			$this->output(true);
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
					$result_nick === $nick ) {
					// Log access and set authentication.
					$me = $user->get( ['user_nickname' => $result_nick], 1);
					$me->user_auth_date = date("Y-m-d H:i:s");
					$me->user_auth_ip = $addr;
					$user->set( $me );
					$_SESSION['username'] = $me->user_id;
					$_SESSION['nickname'] = $me->user_nickname;
					$_SESSION['session'] = secure_hash( $me->user_id . $addr );
					$this->output( true );
				}
				$this->output( false );
			}
		}

		public function LogoutAction(){
			if( !$this->is_auth() ) $this->output( false );
			// Destroy session
			$_SESSION = [];
			session_destroy();
			$this->output( true );
		}
	}

?>