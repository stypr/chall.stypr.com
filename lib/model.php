<?php
	/* Model classes
	it's all in a mess, you can give me feedback about it.
	*/

	/* Player Information */
	class Player {
		public $user_no = "";
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
		protected $user_controllable = ['user_id', 'user_pw',
										'user_nickname', 'user_last_solved', 'user_comment'];
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
			foreach($res as $key => $val){
				$player->$key = $val;
			}
			foreach($this->user_controllable as $control){
				$player->$control = (string) $player->$control;
			}
			// customized input
			$player->user_rank = (int)((@$res['rank']) ?
										($res['rank']) :
										($this->parse_rank($player->user_id)));
			return $player;
		}
		public function get_ranker(): array {
			// get top 50 user info.
			$res = $this->db->query("SELECT p.*, @user_rank := @user_rank + 1 AS rank FROM user p,".
									" (SELECT @user_rank := 0) r WHERE user_permission != 9".
									" ORDER BY user_score DESC, user_last_solved ASC, ".
									" user_join_date ASC LIMIT 50", 2);
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
			if($user_check->user_id === $player->user_id &&
			$player->user_id != NULL){
				if(!$player->user_no){
                    // delete if index is null
                    $name = $this->db->filter($player->user_id);
                    $query = "DELETE FROM user WHERE user_id='$name'";
                    $r = $this->db->query($query);
				}else{
					// update by diff
					$diff_curr = get_object_vars($player);
					$diff_prev = get_object_vars($user_check);
					$diff = array_diff($diff_curr, $diff_prev);
					$query = "UPDATE user SET ";
					foreach($diff as $key => $val){
						$query .= $key . "='" . $val . "', ";
					}
					// remove last two trailling characters
					$query = substr($query, 0, -2);
					$query .= "WHERE username='$player->user_id'";
					$r = $this->db->query($query);
				}
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
				$query = "INSERT INTO user ($key) VALUES($val)";
				$r = $this->db->query($query);
			}
			return $r;
		}
	}

	/* Challenge Information */
	class Challenge {
		public $challenge_no = "";
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
			foreach($res as $key => $val){
				$challenge->$key = $val;
			}
			return $challenge;
		}
		public function get_by_name(string $name): Challenge {
		$name = $this->db->filter($name);
			$res = $this->db->query("SELECT * FROM chal WHERE challenge_name='$name'", 1);
			return ($res) ? $this->parse_challenge($res) : new Challenge;
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
			return ($res) ? $this->parse_challenge($res) : new Challenge;
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
			// is a TODO again; this is inefficient
			$log = new LoggingInfo($this->db);
			var_dump($log->get_by_challenge($chall->challenge_name));
			return Array();
		}
		public function set(Challenge $chall): bool{
			// insert if new, update if non-exist
			$chall_check = $this->get_by_name($chall->challenge_name);
			if($chall_check->$challenge_id === $chall->challenge_id &&
			$chall_challenge_id != NULL){
				// update by diff
				if(!$diff_curr->challenge_no){
					// delete if index is null
					$name = $this->db->filter($chall->challenge_name);
					$query = "DELETE FROM chal WHERE challenge_name='$name'";
					$r = $this->db->query($query);
				}else{
					// update if nothing's wrong..
					$diff_curr = get_object_var($chall);
					$diff_prev = get_object_vars($chall_check);
					$diff = array_diff($diff_curr, $diff_prev);
					$query = "UPDATE chal SET ";
					foreach($diff as $key => $val){
						$query .= $key . "='" . (string)$val. "', ";
					}
					$query = substr($query, 0, -2);
					$query .= "WHERE challenge_id='$chall->chall_id'";
					$r = $this->db->query($query);
				}
			}else{
				// insert by query
				$key=""; $val="";
				$p = get_object_vars($chall);
				$p['chall_no'] = null;
				foreach($p as $k => $v){
					$key .= "$k,";
					$val .= ($val)? "'$v'," : "NULL,";
				}
				$key = substr($key, 0, -1);
				$val = substr($val, 0, -1);
				$query = "INSERT INTO user ($key) VALUES ($val)";
				$r = $this->db->query($query);
			}
			return $r;
		}
	}

	/* Logging Information */
	class Logging {
		public $log_no = "";
		public $log_id;
		public $log_type;
		public $log_challenge;
		public $log_date;
		public $log_info;
	}

	interface LoggingInterface {
		public function get_by_username(string $name): array;
		public function get_by_type(string $type): array;
		public function get_by_challenge(string $chall): array;
		public function get_by_no(string $no): Logging;
		public function set(Logging $log): bool;
	}

	class LoggingInfo implements LoggingInterface {
		protected $db;
		public function __construct($db){ $this->db = $db; }
		private function parse_log(array $res): Logging {
			$log = new Logging;
			foreach($res as $key => $val){
				$log->$key = $val;
			}
			return $log;
		}
		public function get_by_username(string $name): array {
			$name = $this->db->filter($name);
			$res = $this->db->query("SELECT * FROM log WHERE log_id='$name'", 2);
			for($i=0;$i<count($res);$i++){ $res[$i] = $this->parse_log($res[$i]); }
			return ($res) ? $res : Array();
		}
		public function get_by_type(string $type): array {
			$type = $this->db->filter($type);
			$res = $this->db->query("SELECT * FROM log WHERE log_type='$type'", 2);
			var_dump($res);
			for($i=0;$i<count($res);$i++){ $res[$i] = $this->parse_log($res[$i]); }
			return ($res) ? $res : Array();
		}
		public function get_by_challenge(string $chall): array {
			$chall = $this->db->filter($chall);
            $res = $this->db->query("SELECT * FROM log WHERE log_challenge='$chall'", 2);
            for($i=0;$i<count($res);$i++){ $res[$i] = $this->parse_log($res[$i]); }
            return ($res) ? $res : Array();
		}
		public function get_by_no(string $no): Logging {
			$no = $this->db->filter($no);
			$res = $this->db->query("SELECT * FROM log WHERE log_no='$no'", 1);
			return ($res) ? $this->parse_log($res) : new Logging;
		}
		public function set(Logging $log): bool {
			// insert if new, update if non-exist
			$log_check = $this->get_by_no($log->log_no);
			if($log_check->log_no === $log->log_no && $log->log_no != NULL){
				// update by diff
				$diff_curr = get_object_vars($log);
				$diff_prev = get_object_vars($log_check);
				$diff = array_diff($diff_curr, $diff_prev);
				$query = "UPDATE log SET ";
				foreach($diff as $key => $val){
					$query .= $key . "='" . (string)$val. "', ";
				}
				$query = substr($query, 0, -2);
				$query .= "WHERE log_no='$log->log_no'";
				$r = $this->db->query($query);
			}else{
				// insert by query
				$key=""; $val="";
				$p = get_object_vars($log);
				$p['log_no'] = null;
				$p['log_date'] = ($p['log_date']) ? $p['log_date'] : date("Y-m-d H:i:s");
				foreach($p as $k => $v){
					$key .= "$k,";
					$val .= ($val)? "'$v'," : "NULL,";
				}
				$key = substr($key, 0, -1);
				$val = substr($val, 0, -1);
				$query = "INSERT INTO log ($key) VALUES ($val)";
				$r = $this->db->query($query);
            }
			return $r;
		}
	}

?>