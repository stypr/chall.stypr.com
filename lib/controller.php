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
			if(!$this->is_auth()) $this->output_json(false);
			$player = new Player();
			$this->output_json($player->get_by_username($_SESSION['username']));
		}
	}

	/* Status Controller */
	class StatusController extends Controller {
		public function ScoreboardAction(){}
		public function ChallengeAction(){}
		public function HackerAction(){}
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