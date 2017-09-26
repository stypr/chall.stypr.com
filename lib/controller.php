<?php

	/* Controller classes
	Nothing to comment. commenting irritates me! */

	/* Default features for controllers; Abstract class */
	class Controller {
		protected $db;

		public function DefaultAction(){
			// default action for all controllers.

		}

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
		// login, register, modify, forgot
		private function login_account(){}
		private function register_account(){}
		private function modify_account(){}
		private function forgot_account(){}
		
		public function CheckAction(){
			$this->output_json($this->is_auth());
		}
		public function LoginAction(){
			if($this->is_auth()) $this->output_json(false);
			if($_POST){
				$this->login_account();
			}else{
				die("template for login");
			}
		}
		public function RegisterAction(){
			if($this->is_auth()) $this->output_json(false);
			if($_POST){
				$this->register_account();
			}else{
				die("template for register");
			}
		}
		public function ForgotAction(){
			if($this->is_auth()) $this->output_json(false);
			if($_POST){
				$this->forgot_account();
			}else{
				die("template for forgot");
			}
		}
		public function ModifyAction(){
			if(!$this->is_auth()) $this->output_json(false);
			if($_POST){
				$this->modify_account();
			}else{
				die("template for modify");
			}
		}
		public function GetAction(){
			global $query;
			if(!$this->is_auth()) $this->output_json(false);
			$player = new PlayerInfo($query);
			$this->output_json($player->get_by_username($_SESSION['username']));
		}
	}

	/* Status Controller */
	class StatusController extends Controller {
		public function ScoreboardAction(){
			global $query;
			$player = new PlayerInfo($query);
			$log = new LoggingInfo($query);

			// get breakthrough count for players
			$break = $log->get_first_list();
			$_break = [];
			for($i=0;$i<count($break);$i++){
				$_break_user = ($break[$i]->log_id);
				$_break[$_break_user] = $_break[$_break_user]+1;
			}

			$ranker = $player->get_ranker();
			// TODO: parse only important data~
			$result = [];
			for($i=0;$i<count($ranker);$i++){
				$user = $ranker[$i]->user_id;
				$_break_count = $_break[$user] ? $_break[$user] : '';

				$result[] = ['nickname' => $ranker[$i]->user_nickname,
					'score' => $ranker[$i]->user_score,
					'break_count' => $_break_count,
					'comment' => $ranker[$i]->user_comment,
					'last_solved' => $ranker[$i]->user_last_solved];
			}
			$this->output_json($result);
		}
		public function ChallengeAction(){
			global $query;
			$player = new PlayerInfo($query);
			$log = new LoggingInfo($query);
			$chall = new ChallengeInfo($query);
			$chall_list = $chall->get_list();
			//var_dump($chall_list);
			$result = [];
			for($i=0;$i<count($chall_list);$i++){
				// get breakthrough and last-solved by log
				$_name = $chall_list[$i]->challenge_name;
				$_log = $log->get_by_challenge($_name);

				$_break = null;
				$_break_log = $_log[0]->log_id;
				$_last = null;
				$_last = end($_log)->log_date;

				if($_break_log) $_break = $player->get_by_username($_break_log)->user_nickname;
				
				$result[] = ['id' => $chall_list[$i]->challenge_id,
					'name' => $chall_list[$i]->challenge_name,
					'solver' => $chall_list[$i]->challenge_solve_count,
					'break' => $_break,
					'author' => $chall_list[$i]->challenge_by,
					'last-solved' => $_last,
					'rate' => $chall_list[$i]->challenge_rate];
			}
			$this->output_json($result);
		}
		public function AuthAction(){}
		public function FameAction(){}
	}

	/* Challenge Controller */
	class ChallengeController extends Controller {
		public function ListAction(){}
		public function AuthAction(){}
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