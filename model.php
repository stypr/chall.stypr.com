<?php

	require("query.php");

	// Not Implemented!
	class Badges {
		public $badge_id;
		public $badge_list;
	}


	/* Player Information */
	class Player {
		public $user_no;
		public $user_id;
		public $user_pw;
		public $user_nickname;
		public $user_score;
		public $user_sign_date;
		public $user_auth_date;
		public $user_sign_ip;
		public $user_auth_ip;
		public $user_last_solved;
		public $user_comment;
		public $user_permission;
		public $user_rank;
	}

	interface PlayerInterface {
		public function get_ranker(): array;
		public function get_by_username(string $username): Player;
		public function get_by_nickname(string $nickname): Player;
		public function set(Player $player): Player;
	}

	class PlayerInfo implements PlayerInterface {
		protected $db;
		public function __construct($db) { $this->db = $db; }

		private function input_filter(Player $player): Player{
			$v = array_keys(get_class_vars("Player"));
			foreach($v as $e) $player->$e = $this->db->filter($player->$e);
			return $player;
		}

		private function verify_user_class(Player $player): bool{
			// verify user class by class counts.
			$check = array_keys(get_class_vars(new Player));
			$player = array_keys(get_class_vars(get_class($player)));
			return count($check) === count($player);
		}

		private function parse_rank(string $username): int{
			$res = $this->db->query("SELECT x.* FROM (SELECT @rank:=@rank+1 AS rank, user_id FROM user p,(SELECT @rank:=0)r WHERE user_permission !=9 ".
									"ORDER BY user_score DESC, user_last_solved ASC, user_join_date ASC)x WHERE x.user_id='$username'", 1);
			if($res && is_array($res)){
				return (int)$res['rank'];
			}else{
				return 0;
			}
		}

		private function parse_info(array $res): Player{
			// mysql res -> player
			$player = new Player;
			$player->user_no = (int)$res['user_no'];
			$player->user_id = (string)$res['user_id'];
			$player->user_rank = (int)(($res['rank']) ? $res['rank'] : $this->parse_rank($player->user_id));
			$player->user_pw = (string)$res['user_pw'];
			$player->user_nickname = (string)$res['user_nickname'];
			$player->user_score = (int)$res['user_score'];
			$player->user_join_date = (string)$res['user_join_date'];
			$player->user_auth_date = (string)$res['user_auth_date'];
			$player->user_ip = (string)$res['user_auth_ip'];
			$player->user_last_solved = (string)$res['user_last_solved'];
			$player->user_comment = (string)$res['user_comment'];
			$player->user_permission = (int)$res['user_permission'];
			return $player;
		}

		public function get_ranker(): array {
			// get top 50 user info.
			$res = $this->db->query("SELECT p.*, @user_rank := @user_rank + 1 AS rank FROM user p,".
									" (SELECT @user_rank := 0) r WHERE user_permission != 9".
									" ORDER BY user_score DESC, user_last_solved ASC, user_join_date ASC LIMIT 50", 2);
			for($i=0;$i<count($res);$i++){ $res[$i] = $this->parse_info($res[$i]); }
			return $res;
		}

		public function get_by_username(string $username): Player{
			// get_by_username
			$name = $this->db->filter($username);
			$res = $this->db->query("SELECT * FROM user WHERE user_id='$name'", 1);
			return ($res) ? ($this->parse_info($res)) : (new Player);
		}

		public function get_by_nickname(string $nickname): Player{
			// get_by_nickname
			$nick = $this->db->filter($nickname);
			$res = $this->db->query("SELECT * FROM user WHERE user_nickname='$nick'", 1);
			return ($res) ? ($this->parse_info($res)) : (new Player);
		}

		public function set(Player $player): Player{
			$user_check = $this->get_by_username($player->user_id);
			$player = $this->input_filter($player);
			if($user_check->user_id === $player->user_id){
				// update by diff
				$diff_curr = get_object_vars($player);
				$diff_prev = get_object_vars($user_check);
				$diff = array_diff($diff_curr, $diff_prev);
				$q = 'UPDATE user SET ';
				foreach($diff as $key => $val){
					$q .= $key . '=\'' . $val . '\' ';
				}
				$q .= "WHERE username='$player->user_id'";
				$this->db->query($q);
			}else{
				// insert by query
			}
			return $player;
		}
	}

	class Challenge {
		public $challenge_id;
		public $challenge_desc;
		public $challenge_score;
		public $challenge_flag;
		public $challenge_rate;
		public $challenge_solve_count;
		public $challenge_is_open;
		public $challenge_by;
	}

	class Logging {
		public $log_no;
		public $log_id;
		public $log_challenge;
		public $log_auth;
	}


	

?>