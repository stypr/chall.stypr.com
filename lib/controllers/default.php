<?php

	/* lib/controllers/default.php
	This controller should serve client-side files */

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