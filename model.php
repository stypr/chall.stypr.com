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
		public $user_first_date;
		public $user_latest_date;
		public $user_ip;
		public $user_last_solved;
		public $user_comment;
		public $user_permission;
	}

	interface PlayerInterface {
		public function get_everyone(): array;
		public function get_by_username(string $username): Player;
		public function get_by_nickname(string $nickname): Player;
		public function set(Player $player): bool;
	}

	class PlayerInfo implements PlayerInterface {
		protected $db;
		public function __construct($db) { $this->db = $db; }

		private function parse_rank(string $username): int{
			$res = $this->db->query("SELECT x.* FROM (SELECT @rank:=@rank+1 AS rank, username FROM user p,(SELECT @rank:=0)r WHERE permission !=9 ".
									"ORDER BY score DESC, last_solved ASC, join_date ASC)x WHERE x.username='$username'", 1);
			if($res){
				return (int)$res['rank'];
			}else{
				return 0;
			}
		}

		private function parse_info(array $res): Player{
			// mysql res -> player
			$player = new Player;
			$player->user_no = (int)$res['id'];
			$player->user_id = (string)$res['username'];
			$player->user_rank = (int)(($res['rank']) ? $res['rank'] : $this->parse_rank($player->user_id));
			$player->user_pw = (string)$res['password'];
			$player->user_nickname = (string)$res['nickname'];
			$player->user_score = (int)$res['score'];
			$player->user_first_date = (string)$res['join_date'];
			$player->user_latest_date = (string)$res['login_date'];
			$player->user_ip = (string)$res['login_ip'];
			$player->user_last_solved = (string)$res['last_solved'];
			$player->user_comment = (string)$res['comment'];
			$player->user_permission = (int)$res['permission'];
			return $player;
		}

		public function get_everyone(): array {
			// get top 50 user info.
			$res = $this->db->query("SELECT p.*, @rank := @rank + 1 AS rank FROM user p,".
									" (SELECT @rank := 0) r WHERE permission != 9 ORDER BY score DESC, last_solved ASC, join_date ASC LIMIT 50", 2);
			for($i=0;$i<count($res);$i++){ $res[$i] = $this->parse_info($res[$i]); }
			return $res;
		}

		public function get_by_username(string $username): Player{
			// get_by_username
			$name = $this->db->filter($username);
			$res = $this->db->query("SELECT * FROM user WHERE username='$name'", 1);
			return $this->parse_info($res);
		}

		public function get_by_nickname(string $nickname): Player{
			// get_by_nickname
			$nick = $this->db->filter($nickname);
			$res = $this->db->query("SELECT * FROM user WHERE nickname='$nick'", 1);
			return $this->parse_info($res);
		}

		public function set(Player $player): bool{
			// insert if new, update if exist
			if($this->get_by_username($player->user_id)->user_id == $player->user_id){
				return true;
			}else{
				return false;
			}
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