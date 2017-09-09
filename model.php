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
		public $user_join_date;
		public $user_auth_date;
		public $user_join_ip;
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
		public function set(Player $player);
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
			// I failed logics and efficiency. this should be a TODO
			$res = $this->db->query("SELECT x.* FROM (".
									"SELECT @rank:=@rank+1 AS rank, user_id FROM user p,(SELECT @rank:=0)r".
									"WHERE user_permission !=9 ORDER BY user_score DESC, user_last_solved ASC".
									", user_join_date ASC)x WHERE x.user_id='$username'", 1);
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
			$player->user_rank = (int)((@$res['rank']) ?
										($res['rank']) :
										($this->parse_rank($player->user_id)));
			$player->user_pw = (string)$res['user_pw'];
			$player->user_nickname = (string)$res['user_nickname'];
			$player->user_score = (int)$res['user_score'];
			$player->user_join_date = (string)$res['user_join_date'];
			$player->user_auth_date = (string)$res['user_auth_date'];
			$player->user_join_ip = (string)$res['user_join_ip'];
			$player->user_auth_ip = (string)$res['user_auth_ip'];
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

		public function set(Player $player){
			$user_check = $this->get_by_username($player->user_id);
			$player = $this->input_filter($player);
			if($user_check->user_id === $player->user_id){
				// update by diff
				$diff_curr = get_object_vars($player);
				$diff_prev = get_object_vars($user_check);
				$diff = array_diff($diff_curr, $diff_prev);
				$query = "UPDATE user SET ";
				foreach($diff as $key => $val){
					$query .= $key . "='" . $val . "', ";
				}
				$query = substr($query, 0, -2); // remove last two trailing characters
				$query .= "WHERE username='$player->user_id'";
				$this->db->query($query);
			}else{
				// insert by query
				$key=""; $val ="";
				$p = get_object_vars($player);
				$p['user_no'] = null;
				unset($p['user_rank']); // user_rank is not the column!
				foreach($p as $k => $v){
					$key .= "$k,";
					$val .= ($val)? "'$v'," : "NULL,"; // check null.
				}
				$key = substr($key, 0, -1);
				$val = substr($val, 0, -1);
				$query="INSERT INTO user ($key) VALUES($val)";
				echo $query;
				$this->db->query($query);
			}
		}
	}

	/* Challenge Information */
	class Challenge {
		public $challenge_id;
		public $challenge_name;
		public $challenge_desc;
		public $challenge_score;
		public $challenge_flag;
		public $challenge_rate;
		public $challenge_solve_count;
		public $challenge_is_open;
		public $challenge_by;
	}

	interface ChallengeInterface {
		public function get_list(bool $all=false): array;
		public function get_solver(Challenge $chall): array;
		public function get_by_name(string $name): Challenge;
		public function get_by_flag(string $flag): Challenge;
		public function set(Challenge $chall): bool;
	}

	class ChallengeInfo implements ChallengeInterface {
		protected $db;
		public function __construct($db) { $this->db = $db; }

		private function parse_challenge(array $res): Challenge{
 			$challenge = new Challenge;
			$challenge->challenge_id = (int)$res['challenge_id'];
			$challenge->challenge_name = (string)$res['challenge_name'];
			$challenge->challenge_desc = (string)$res['challenge_desc'];
			$challenge->challenge_score = (int)$res['challenge_score'];
			$challenge->challenge_flag = (string)$res['challenge_flag'];
			$challenge->challenge_rate = (float)$res['challenge_rate'];
			$challenge->challenge_solve_count = (int)$res['challenge_solve_count'];
			$challenge->challenge_is_open = (int)$res['challenge_is_open'];
			$challenge->challenge_by = (string)$res['challenge_by'];
			return $challenge;
		}
		public function get_by_name(string $name): Challenge {
			$name = $this->db->filter($name);
			$res = $this->db->query("SELECT * FROM chal WHERE challenge_name='$name'", 1);
			return ($res) ? $res : new Challenge;
		}
		public function get_by_flag(string $flag): Challenge {
			// get by flag
			$flag = $this->db->filter($flag);
			// this routine adds/removes prefix and suffixes depending on the input.
			$FLAG_PREFIX = "flag"; // this one --> flag{...}
			$start = 0; $end = 0;
			if(!substr_count($flag, "flag{")) $flag = "flag{" . $flag;
			if(!substr_count($flag, "}")) $flag = $flag . "}";
			if(substr_count(strtolower($flag), "flag{") >= 2){
				$flag = substr($flag, strripos($flag, "flag{"));
				$end = stripos($flag, "}");
				$flag = substr($flag, 0, $end + 1);
			}
			$res = $this->db->query("SELECT * FROM chal WHERE challenge_flag='$flag'".
									"AND challenge_is_open=1", 1);
			return ($res) ? parse_challenge($res) : new Challenge;
		}
		public function get_list(bool $all=false): array {
			// Loads list of challenges.
			// list only available challenges if $all is false
			$where = ($all) ? ('challenge_is_open') : (1);
			$query = "SELECT * FROM chal WHERE challenge_is_open=";
			$res = $this->db->query($query.$where,2);
			for($i=0;$i<count($res);$i++){ $res[$i] = $this->parse_challenge($res[$i]); }
			return ($res) ? $res : Array();
		}
		public function get_solver(Challenge $chall): array{
			
		}
		public function set(Challenge $chall): bool{
		}
	}

	class Logging {
		public $log_no;
		public $log_id;
		public $log_challenge;
		public $log_auth;
	}

?>