<?php

/* lib/model.php
Done a bit of refactoring, hopefully it works */

// Classes per DB structure //
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
}
class Challenge {
	public $challenge_no = '';
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

// Default ModelHandler //
class ModelHandler {
	protected $db;
	protected $ModelName;
	protected $TableName;
	protected $CheckColumn;

	// Model helper functions
	private function input_filter( $obj ) {
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
		$obj = new $this->ModelName;

		// process according to the type of the array
		if( array_keys( $res ) !== range( 0, count( $res ) - 1 ) ){
			// single array
			$obj = new $this->ModelName;
			foreach ( $res as $key => $val ) {
				$obj->$key = $val;
			}
		}else{
			// arrays of objects
			$obj = [];
			for ( $i=0; $i<count($res); $i++ ){
				$obj[$i] = new $this->ModelName;
				foreach ( $res[$i] as $key => $val ) {
					$obj[$i]->$key = $val;
				}
			}
		}
		return $obj;
	}
	private function parse_where(array $where, string $delim = 'AND'): string {
		// Parse MySQL where conditions
		$condition = [];
		foreach ( $where as $key => $val ) {
			$k = $this->db->filter( $key );
			$v = $this->db->filter( $val );
			$condition []= " $k='$v' ";
		}

		return implode( $delim, $condition );
	}

	public function __construct() {
		global $query;
		$this->db = $query;
	}

	// Query -> obj functions
	public function get(Array $where = [], $limit = null,
		Array $get_only = [], Array $order = []) {

		$condition = ( $where ) ? "WHERE " . $this->parse_where( $where ) : "";
		$columns = ( $get_only ) ? implode( ",", $get_only ) : "*";
		$order_by = ( $order ) ? "ORDER BY " . implode( ",", $order ) : "";
		if( $limit ){
			if ( is_integer( $limit ) ) {
				$limit_str = "LIMIT $limit";
			} else {
				$limit_str = "LIMIT $limit[1] OFFSET $limit[0]";
			}
		} else {
			$limit_str = "";
		}

		$statement = "SELECT $columns ";
		$statement .= "FROM " . $this->TableName;
		$statement .= " $condition $order_by $limit_str";
		$type = ( $limit == 1 ) ? 1 : 2;
		$result = $this->db->query( $statement, $type );
		return ( $result ) ? $this->parse_array( $result ) : new $this->ModelName;
	}
	public function set($obj){
		// Insert if new, Update if non-exist
		$check_column = $this->CheckColumn;
		$check = $this->get( [$check_column => $obj->$check_column], 1 );
		if( $check->$check_column === $obj->$check_column &&
			$obj->$check_column != NULL ) {
			// Update by Diff
			$diff_curr = get_object_vars( $obj );
			$diff_prev = get_object_vars( $check );
			$diff = array_diff( $diff_curr, $diff_prev );
			$statement = "UPDATE " . $this->TableName . " SET ";
			foreach ( $diff as $key => $val ) {
				$key = (string)$key;
				$val = (string)$val;
				$statement .= "$key='$val', ";
			}
			$statement = substr( $statement, 0, -2 );
			$statement .= " WHERE $check_column='" . $obj->$check_column . "'";
			$ret = $this->db->query( $statement );
		} else {
			// Insert new (removes the first variable; the index)
			$stmt_key = "";
			$stmt_val = "";
			$new_obj = get_object_vars( $obj );
			$new_obj_idx = array_keys( $new_obj )[0];
			$new_obj[$new_obj_idx] = null;
			// Remove user-customized keys
			$allowed_keys = array_keys( get_object_vars( new $this->ModelName ) );
			foreach ( $new_obj as $key => $val ) {
				if ( in_array( $key, $allowed_keys, true ) ){
					$stmt_key .= "$key,";
					if ( isset($val) && $val !== null && $val !== '' ) {
						$stmt_val .= "'$val',";
					}else{
						$stmt_val .= "NULL,";
					}
				}
			}
			$stmt_key = substr( $stmt_key, 0, -1 );
			$stmt_val = substr( $stmt_val, 0, -1 );
			$statement = "INSERT INTO " . $this->TableName;
			$statement .= "($stmt_key) VALUES ($stmt_val)";
			$ret = $this->db->query( $statement );
		}
		return $ret;
	}

	public function del($obj): int {
		if ( is_object( $obj ) ) {
			$check_column = $this->CheckColumn;
			$check = $this->get( [$check_column => $obj->$check_column], 1 );
			if ( $check->$check_column === $obj->$check_column &&
				$obj->$check_column != NULL ) {
				$del_id = $obj->$check_column;
				$statement = "DELETE FROM " . $this->TableName . " WHERE ";
				$statement .= "$check_column='$del_id'";
				$this->db->query( $statement );
				return true;
			}
		} elseif ( is_array( $obj ) ) {
			$where = $this->parse_where( $obj );
			$statement = "DELETE FROM " . $this->TableName . " WHERE $where";
			$this->db->query( $statement );
			return true;
		}
		return false;
	}

	// Query -> int functions
	public function count(Array $where = []): int {
		$condition = ( $where ) ? "WHERE " . $this->parse_where( $where ) : "";
		$statement = "SELECT COUNT(*) AS cnt FROM " . $this->TableName . " $condition";
		$result = $this->db->query( $statement, 1 );
		return ( $result ) ? (int)$result['cnt'] : -1;
	}
	public function sum($col = '', Array $where = []): int {
		$condition = ( $where ) ? "WHERE " . $this->parse_where( $where ) : "";
		$statement = "SELECT SUM($col) AS sum FROM " . $this->TableName . " $condition";
		$result = $this->db->query( $statement, 1 );
		return ( $result ) ? (int)$result['sum'] : -1;
	}
}

class ChallengeInfo extends ModelHandler {
	public function __construct() {
		$this->ModelName = "Challenge";
		$this->TableName = "chal";
		$this->CheckColumn = "challenge_id";
		ModelHandler::__construct();
	}

}

class LoggingInfo extends ModelHandler {
	public function __construct() {
		$this->ModelName = "Logging";
		$this->TableName = "log";
		$this->CheckColumn = "log_no";
		ModelHandler::__construct();
	}

	public function get_break(string $username = ''): array {
		$where_user = ( $username ) ? "log_id='$username' AND" : "";
		$statement = "SELECT *, 4 - rank AS break_point FROM " .
			"(SELECT log_challenge, log_id, log_date, ROW_NUMBER() OVER" .
			"(PARTITION BY log_challenge ORDER BY log_date ASC) AS rank" .
			" FROM log WHERE log_type='Correct')x WHERE " .
			" $where_user rank <= 3 ORDER BY log_challenge, rank";
		$break_status = $this->db->query( $statement, 2 );
		return ( $break_status ) ?: [];
	}
}

class UserInfo extends ModelHandler {
	public function __construct() {
		$this->ModelName = "User";
		$this->TableName = "user";
		$this->CheckColumn = "user_id";
		ModelHandler::__construct();
	}

	private function get_rank(string $nickname): int {
		// MySQL 5.7+ supports ROW_NUMBER(). yeah!
		$statement = "SELECT x.rank FROM " .
			"(SELECT user_nickname, ROW_NUMBER() OVER " .
			"(ORDER BY user_score DESC, user_last_solved ASC, user_join_date ASC)" .
			"AS rank FROM user WHERE user_permission != 9)x" .
			" WHERE x.user_nickname='$nickname'";
		$rank = $this->db->query( $statement, 1 );
		if ( $rank && is_array( $rank ) ) {
			return (int)$rank['rank'];
		}
		return 0;
	}

	public function get_nick_dict(): array {
		// Get a dict of email => nick
		$result = $this->get([], null, ['user_id', 'user_nickname']);
		$result_dict = [];
		foreach ( $result as $key => $val ) {
			$result_dict[$val->user_id] = $val->user_nickname;
		}
		return $result_dict;
	}

	public function get(Array $where = [], $limit = null,
		Array $get_only = [], Array $order = []) {
		// User needs rank too.. :p
		$result = ModelHandler::get( $where, $limit, $get_only, $order );
		if( $result->user_nickname && !$order ) {
			$result->user_rank = $this->get_rank( $result->user_nickname );
		}
		return $result;
	}

}

?>