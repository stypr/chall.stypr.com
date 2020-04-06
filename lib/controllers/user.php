<?php

/**
 * lib/controllers/user.php
 *
 * User Controller
 */

class UserController extends Controller
{
    public function RecoverAction()
    {
        $log = new LoggingInfo;
        $user = new UserInfo;
        $recovery_code = $this->auth_filter($_POST['recovery_code']);
        $password = $this->auth_filter($_POST['password']);
        $encrypted_password = secure_hash($password);

        $log_verify = $log->get(['log_info' => $recovery_code], 1);

        if ($log_verify->log_id && $log_verify->log_no >= 0
            && $log_verify->log_type == "Recovery"
            && $log_verify->log_info === $recovery_code
        ) {
            // Delete request logs
            /* get multiple obj -> delete by stmt */
            $log->del(['log_id' => $log_verify->log_id, 'log_type' => 'Recovery']);
            // Change Password
            $me = $user->get(['user_id' => $log_verify->log_id], 1);
            $me->user_pw = $encrypted_password;
            $user->set($me);
            $this->output(true);
        }
        $this->output(false);
    }

    public function FindAction()
    {
        $log = new LoggingInfo;
        $user = new UserInfo;

        $username = $this->auth_filter($_POST['username']);
        $me = $user->get(['user_id' => $username], 1);

        if ($username != '' && $me->user_id == $username) {
            // Maximum recovery request is 3 times. Spamming = Ban
            $log_count = $log->get(['log_type' => 'Recovery', 'log_id' => $username]);
            if (count($log_count) >= 3) {
                $this->output("exceed");
            }
            // Log the request with the code
            $recovery_code = generate_random_string(40);
            $new_log = new Logging;
            $new_log->log_id = $username;
            $new_log->log_type = "Recovery";
            $new_log->log_challenge = '';
            $new_log->log_date = date("Y-m-d H:i:s");
            $new_log->log_info = $recovery_code;
            $log->set($new_log);

            $nickname = $me->user_nickname;
            try {
                $mail_title = "Hello, " . $nickname;
                $mail_body = "Hi $nickname, <br><br>";
                $mail_body .= "It seems like you or someone pretending to be you ";
                $mail_body .= "has requested a password recovery request.<br>";
                $mail_body .= "If you've not requested this message, ";
                $mail_body .= "Please ignore this mail.<br><hr>Please ";
                $mail_body .= "<a href='" . __HOST__ . "#/user/find/" . $recovery_code . "'>";
                $mail_body .= "click here</a> to complete your password recovery.";
                // Send a mail using Mailer class (lib/mail.php)
                $mailer = new Mailer;
                $mailer->SendMail($me->user_id, $mail_title, $mail_body);
                $this->output("done");
            } catch (Exception $e) {
                $this->output("fail");
            }
        } else {
            $this->output("nope");
        }
    }

    public function RegisterAction()
    {
        $user = new UserInfo;

        $mail = $this->auth_filter($_POST['username']);
        $pass = $this->auth_filter($_POST['password']);
        $nick = $this->auth_filter($_POST['nickname']);
        $addr = $this->auth_filter($_SERVER['REMOTE_ADDR']);

        if (strlen($mail) >= 5 && strlen($mail) <= 100
            && strlen($pass) >= 5 && strlen($pass) <= 100
            && strlen($nick) >= 3 && strlen($nick) <= 20
        ) {

            $check_nick = $user->get(['user_nickname' => $nick], 1);
            $check_mail = $user->get(['user_id' => $mail], 1);

            if ($check_nick->user_nickname) {
                $this->output('duplicate_nick');
            }
            if ($check_mail->user_nickname) {
                $this->output('duplicate_mail');
            }
            if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
                $this->output('email_format');
            }
            $encrypted_password = secure_hash($pass);

            // generate new player
            $me = new User;
            $me->user_id = $mail;
            $me->user_pw = $encrypted_password;
            $me->user_nickname = $nick;
            $me->user_score = 0;
            $me->user_join_date = date("Y-m-d H:i:s");
            $me->user_join_ip = $addr;
            $me->user_permission = 0;
            $user->set($me);
            $this->output('true');
        } else {
            $this->output('size');
        }
    }

    public function EditAction()
    {
        if (!$this->is_auth()) {
            $this->output(false);
        }
        $user = new UserInfo;
        $me = $user->get(['user_id' => $_SESSION['username']], 1);
        if (isset($_POST['password'])) {
            $new_password = $this->auth_filter($_POST['password']);
            if ($new_password) {
                if (strlen($new_password) > 100) {
                    $new_password = substr($new_password, 0, 100);
                }
                $me->user_pw = secure_hash($new_password);
            }
        }

        if (isset($_POST['comment'])) {
            $new_comment = $this->db->filter($_POST['comment'], "memo");
            if ($new_comment) {
                if (strlen($new_comment) == 100) {
                    $new_comment = substr($new_comment, 0, 100);
                }
                $me->user_comment = $new_comment;
            }
        }
        $user->set($me);
        $this->output(true);
    }

    public function CheckAction()
    {
        // Check whether the user is logged in
        $this->output($this->is_auth());
    }

    public function LoginAction()
    {
        if ($this->is_auth()) {
            $this->output(false);
        }
        $user = new UserInfo;
        $nick = $this->auth_filter($_POST['nickname']);
        $pass = $this->auth_filter($_POST['password']);
        $addr = $this->auth_filter($_SERVER['REMOTE_ADDR']);
        // Check length
        if (strlen($nick) >= 3 && strlen($nick) <= 100
            && strlen($pass) >= 4 && strlen($pass) <= 100
        ) {
            // I give very lenient options to users.
            // Emails are *also* accepted, yet nickname is the first priority.
            $check_nick = $user->get(['user_nickname' => $nick], 1);
            $check_mail = $user->get(['user_id' => $nick], 1);
            // Get the result
            if ($check_nick->user_nickname) {
                $result_nick = $check_nick->user_nickname;
                $result_pass = $check_nick->user_pw;
            }
            elseif ($check_mail->user_nickname) {
                $result_nick = $check_mail->user_nickname;
                $result_pass = $check_nick->user_pw;
            } else {
                $this->output(false);
            }
            if (!($result_nick && $result_pass)) {
                $this->output(false);
            }
            // Verify result
            $encrypted_pass = secure_hash($pass);
            if ($result_pass === $encrypted_pass
                && $pass != ''
                && $result_nick === $nick
            ) {
                // Log access and set authentication.
                $me = $user->get(['user_nickname' => $result_nick], 1);
                $me->user_auth_date = date("Y-m-d H:i:s");
                $me->user_auth_ip = $addr;
                $user->set($me);
                $_SESSION['username'] = $me->user_id;
                $_SESSION['nickname'] = $me->user_nickname;
                $_SESSION['session'] = secure_hash($me->user_id . $addr);
                $this->output(true);
            }
            $this->output(false);
        }
    }

    public function LogoutAction()
    {
        if (!$this->is_auth()) {
            $this->output(false);
        }
        // Destroy session
        $_SESSION = [];
        session_destroy();
        $this->output(true);
    }
}
