<?php

	/* lib/controller.php
	Will be on a continuous updates, should be buggy.
	This is a loader class. You should chefck out the controllers subdirectory. */

	// PHPMailer: should be installed by composer! //
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;

	// Base Class //
	class Controller {
		protected $db;

		public function auth_filter($str): string {
			// filter for the authentication
			return $this->db->filter($str, 'auth');
		}

		public function is_auth(){
			// check if authenticated
			$session = $_SESSION['username'] . $this->auth_filter( $_SERVER['REMOTE_ADDR'] );
			if( secure_hash($session) == $_SESSION['session'] ){
				return true;
			}
			return false;
		}

		public function output($data){
			// return result as json
			header("Content-Type: application/json;charset=utf-8");
			echo json_encode($data);
			exit;
		}

		public function DefaultAction(){
			$this->output( false );
		}

		public function __construct(){
			global $query;
			$this->db = $query;
		}
	}

	// Load Controller
	$controller_dir = "lib/controllers/";
	$controller_list = array_diff( scandir( $controller_dir ), ['.', '..'] );
	foreach ( $controller_list as $controller ) {
		require_once( $controller_dir . DIRECTORY_SEPARATOR . $controller );
	}

?>