<?php

/* lib/mail.php
Made for lenient sendmail. Requires PHPMailer. */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer {

	private $type = '';

	public function SendMail( $mail_to, $mail_title, $mail_body ) {

		if( !$this->type ) return false;

		// Change it as needed.
		$sender = 'Harold Kim';

		$mail = new PHPMailer;
		$mail->SMTPDebug = 0;
		$mail->isSMTP();
		$mail->CharSet = "UTF-8";
		$mail->SMTPAuth = true;

		switch ( $this->type ) {
			case "gmail":
				$mail->Host = "smtp.gmail.com";
				$mail->Port = 587;
				$mail->SMTPSecure = 'tls';
				$mail->Username = __MAIL_USER__;
				$mail->Password = __MAIL_PASS__;
				break;
			case "default":
				$mail->Host = __MAIL_HOST__;
				$mail->Port = (int)__MAIL_PORT__;
				$mail->SMTPAuth = true;
				// add it on your demand.
				// $mail->SMTPSecure = 'tls';
				break;
		}

		$mail->Username = __MAIL_USER__;
		$mail->Password = __MAIL_PASS__;
		$mail->setFrom( __MAIL_USER__, $sender );
		$mail->addAddress( $mail_to );
		$mail->isHTML( true );
		$mail->Subject = $mail_title;
		$mail->Body = $mail_body;

		try {
			$mail->send();
			return true;
		} catch ( Exception $e ) {
			return false;
		}
	}

	public function __construct() {
		// Get type from global variable //
		$this->type = __MAIL_TYPE__;
		if ( $this->type == "" || $this->type == "__MAIL_TYPE__" ) {
			$this->type = '';
		}
	}

}
