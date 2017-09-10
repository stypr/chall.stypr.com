<?php
	/* Query module
	PHP MySQL module made for my projects, for a quick development. */
    class Query{
        private $conn, $mysqli;

        function check(){
            return ($this->conn) ? True : False;
        }

        function connect($host, $username, $password, $db=""){
            // @return //
            if($this->mysqli){
                $this->conn = mysqli_connect($host, $username, $password, $db);
                if(!$this->conn) return mysqli_connect_errno();
            }else{
                $this->conn = mysql_connect($host, $username, $password);
                mysql_select_db($db, $this->conn);
                if(!$this->conn) return mysql_error();
            }
        }

        function query($query, $result=0){
            // $result: 0 -> no return, 1 -> return_assoc, 2 -> return_array //
            if(!$this->conn) return false;
            if($this->mysqli){
                $_query = mysqli_query($this->conn, $query);
                if(!$_query) return false;
                switch($result){
                    case 2:
                        $_result = Array();
                        while($_result_temp = mysqli_fetch_array($_query)){
                            $_result[] = $_result_temp;
                        }
                        return $_result;
                    case 1:
                        return mysqli_fetch_assoc($_query);
                    default:
                        return true;
                }
            }else{
                $_query = mysql_query($query, $this->conn);
                if(!$_query) return false;
                switch($result){
                    case 2:
                        $_result = Array();
                        while($_result_temp = mysql_fetch_array($_query, MYSQL_ASSOC)){
                            $_result[] = $_result_temp;
                        }
                        return $_result;
                    case 1:
                        return mysql_fetch_assoc($_query);
                    default:
                        return true;
                }
            }
        }

        function filter($str, $type='sql'){
            switch($type){
                case "url":
                    return preg_replace("/[^a-zA-Z0-9-_&\/]/", "", $str);
                case "sql":
                    if($this->conn){
                        $_filter = preg_replace("/[^a-zA-Z0-9-_:+!@#$.%^+&*(){}:\.\ <>가-힣]/", "", $str);
                        if($this->mysqli){
                            return mysqli_real_escape_string($this->conn, $_filter);
                        }else{
                            return mysql_real_escape_string($_filter, $this->conn);
                        }
                    }
                case "memo":
                    if($this->conn){
                        $_filter = htmlspecialchars(preg_replace("/[^a-zA-Z0-9-_:+!@#$.%^&*(){}:\/.\ <>가-힣]/", "", $str));
                        if($this->mysqli){
                            return mysqli_real_escape_string($this->conn, $_filter);
                        }else{
                            return mysql_real_escape_string($_filter, $this->conn);
                        }
                    }
                case "auth":
                    return preg_replace("/[^a-zA-Z0-9-_!@#$.%^&*(){}가-힣]/", "", $str);
            }
        }

        function __construct(){
            if(function_exists("mysqli_connect")){
                $this->mysqli = true;
            }else{
                $this->mysqli = false;
            }
        }

        function __destruct(){
            if($this->conn){
                if($this->mysqli){
                    mysqli_close($this->conn);
                }else{
                    mysql_close($this->conn);
                }
            }
        }
    }
?>
