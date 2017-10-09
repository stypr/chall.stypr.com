<?php

/* lib/model.php
Done a bit of refactoring, hopefully it works */

// Classes made per DB structure //
class User {
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
class Logging {
	public $log_no;
	public $log_id;
	public $log_type;
	public $log_challenge;
	public $log_date;
	public $log_info;
}

// Basic ModelHandler //
class ModelHandler {
	protected $db;
	protected $ModelName;
	protected $TableName;

	// Model helper functions
	private function input_filter( $obj ){
		// mysqli_escape_string() for the model.
		$v = array_keys( get_class_vars( $ModelName ) );
		foreach ( $v as $e ) $obj->$e = $this->db->filter( $obj->$e );
		return $obj;
	}
	private function verify_class($obj): bool {
		// Verify by Key Counts
		$check_obj = array_keys( get_class_vars( get_class( $obj ) ) );
		$check_orig = array_keys( get_class_vars( new $ModelName ) );
		return count( $check_obj ) === count( $check_orig );
	}
	private function parse_array(array $res) {
		// Parse MySQL result and Convert to class
		$obj = new $ModelName;
		foreach($res as $key => $val) {
			$obj->$key = $val;
		}
		return $obj;
	}
	private function parse_where(array $where, string $delim = "AND"): string {
		// Parse MySQL where conditions
		$condition = [];
		foreach ( $where as $key => $val ) {
			$k = $this->db->filter( $key );
			$v = $this->db->filter( $val );
			$condition[] = "$k = '$v' ";
		}
		return implode( "$delim", $condition );
	}

	public function __construct() {
		global $query;
		$this->db = $query;
	}

	public function get(Array $where = [], Array $order = [],
		Array $get_only = [], $limit = null): array {
		// Example
		// $this->get(['user_id'=>'stypr', ['user_rank' => 'asc'],
		// ['user_nickname'], 50);

		$condition = ( $where ) ? $this->parse_where( $where ) : "";
		$columns = ( $get_only ) ? implode( ",", $get_only ) : "*";
		$order_by = ( $order ) ? $this->parse_order( $order ) : "";
		if( $limit ){
			if ( is_integer($limit) ) {
				$limit = "LIMIT $limit";
			} else {
				$limit = "LIMIT $limit[1] OFFSET $limit[0]";
			}
		} else {
			$limit = "";
		}

		$statement = "SELECT $columns FROM $this->TableName $condition $order_by $limit";
		$result = $this->db->query($statement, 2);
		return ($result) ? $result : [];
	}
	public function set($obj){
		// Insert if new, Update if non-exist
/*
			$chall_check = $this->get_by_name($chall->challenge_name);
			if($chall_check->challenge_id === $chall->challenge_id && $chall_challenge_id != NULL){
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
					$val .= (isset($v) && $v !== null && $v !== '') ? "'$v'," : "NULL,"; // check null.
				}
				$key = substr($key, 0, -1);
				$val = substr($val, 0, -1);
				$query = "INSERT INTO user ($key) VALUES ($val)";
				$r = $this->db->query($query);
			}
			return $r;
*/
	}
	public function count(Array $where = []): int {
	}
	public function sum(Array $where = []): int {
	}
}

class ChallengeInfo extends ModelHandler {
	public function __construct() {
		$this->$ModelName = "Challenge";
		$this->TableName = "chal";
	}
}

class LoggingInfo extends ModelHandler {
	public function __construct() {
		$this->ModelName = "Logging";
		$this->TableName = "log";
	}
}

class UserInfo extends ModelHandler {
	public function __construct() {
		$this->ModelName = "User";
		$this->TableName = "user";
		ModelHandler::__construct();
	}
}

?>