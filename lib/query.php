<?php

	/* Query module
	Used just for chall.stypr.com project, only works with mysqli */

    class Query{
        private $conn;

        public function check(){
            return ($this->conn) ? True : False;
        }

        public function connect($host, $username, $password, $db=""){
            // @return //
            $this->conn = mysqli_connect($host, $username, $password, $db);
            if(!$this->conn) return mysqli_connect_errno();
        }

        public function query($query, $result=0){
            /* $result
				0 returns bool
				1 returns associative array of a single row
				2 returns arrays of associative arrays of rows
			*/
            if(!$this->conn) return false;
            $_query = mysqli_query($this->conn, $query);
            if(!$_query) return false;
            switch($result){
                case 2:
                    $_result = Array();
                    while($_result_temp = mysqli_fetch_assoc($_query)){
                        $_result[] = $_result_temp;
                    }
                    return $_result;
                case 1:
                    return mysqli_fetch_assoc($_query);
                default:
                    return true;
            }
        }

        public function filter($str, $type='sql'){
            switch($type){
                case "url":
                    return preg_replace("/[^a-zA-Z0-9-_&\/]/", "", $str);
                case "sql":
                    if($this->conn){
                        $_filter = preg_replace("/[^a-zA-Z0-9-_:+!@#$.%^+&*(){}:\/\.\ <>가-힣]/", "", $str);
                        return mysqli_real_escape_string($this->conn, $_filter);
                    }
                case "memo":
                    if($this->conn){
                        $_filter = htmlspecialchars(preg_replace("/[^a-zA-Z0-9-_:+!@#$.%^&*(){}:\/.\ <>가-힣]/", "", $str));
                        return mysqli_real_escape_string($this->conn, $_filter);
					}
				case "auth":
					return preg_replace("/[^a-zA-Z0-9-_!@$\.%^&*(){}가-힣]/", "", $str);
            }
        }

        public function __construct(){
            if(!function_exists("mysqli_connect")){
                die('php_mysqli extension is not installed. :(');
            }
        }

        public function __destruct(){
            if($this->conn){
                mysqli_close($this->conn);
            }
        }
    }
?>
