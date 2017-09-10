<?php

	/* Controller classes
	Nothing to comment. commenting irritates me! */


	/* Default features for controller */
	class Controller {
		protected $db;
		public function is_auth(){
			// check if authenticated
			$session = $_SESSION['username'] . $_SERVER['REMOTE_ADDR'];
			if(secure_hash($session) == $_SESSION['session']){
				return true;
			}
			return false;
		}

		public function __construct($db){
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

		public function LoginAction(){
			if($this->is_auth()) die("wtf");
			if($_POST){
				$this->login_account();
			}else{
				die("template for login");
			}
		}
		public function RegisterAction(){
			if($this->is_auth()) die("wtf");
			if($_POST){
				$this->register_account();
			}else{
				die("template for register");
			}
		}
		public function ForgotAction(){
			if($this->is_auth()) die("wtf");
			if($_POST){
				$this->forgot_account();
			}else{
				die("template for forgot");
			}
		}
		public function ModifyAction(){
			if(!$this->is_auth()) die("wtf");
			if($_POST){
				$this->modify_account();
			}else{
				die("template for modify");
			}
		}
	}
?>