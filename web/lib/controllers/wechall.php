<?php

/**
 * lib/controllers/wechall.php
 *
 * Wechall Controller
 */

class WeChallController extends Controller
{

    public function __construct()
    {
        Controller::__construct();
        // Check feature availability
        if (__WECHALL__ == "__WECHALL__" || __WECHALL__ == "") {
            $this->output("* WeChall feature disabled.");
        }
        // Check if the request is from WeChall
        $check = $this->auth_filter($_GET['authkey']);
        if ($check !== __WECHALL__) {
            $this->output("* Failed Authentication.");
        }
    }
    public function VerifyAction()
    {
        // Verify user info and return in WeChall Format
        // Valid Output: 1 | 0
        $user = new UserInfo;

        $nickname = $this->auth_filter($_GET['username']);
        $mailaddr = $this->auth_filter($_GET['email']);
        $check = (
            $user->get(['user_id' => $mailaddr], 1)->user_nickname === $nickname &&
            $user->get(['user_nickname' => $nickname], 1)->user_id === $mailaddr
        );

        echo ($check) ? '1' : '0';
        exit;
    }
    public function RankAction()
    {
        // Retrieve user information in WeChall format
        // Valid Output: username:rank:score:maxscore:challssolved:challcount:usercount
        $user = new UserInfo;
        $chall = new ChallengeInfo;
        $log = new LoggingInfo;

        $nickname = $this->auth_filter($_GET['username']);
        $me = $user->get(['user_nickname' => $nickname], 1);
        if (!$me->user_nickname) {
            $this->output(false);
        }
        $me_solved_count = $log->count(['log_type' => 'Correct', 'log_id' => $me->user_id]);

        $chall_total_score = $chall->sum('challenge_score', ['challenge_is_open' => 1]);
        $chall_total_count = $chall->count(['challenge_is_open' => 1]);
        $user_total_count = $user->count();

        // Check if user actually exists..
        $out = [];
        if ($user->get(['user_id' => user_id], 1)->user_nickname === $nick) {
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
