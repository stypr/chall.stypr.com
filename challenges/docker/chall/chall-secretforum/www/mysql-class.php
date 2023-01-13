<?php

class MySQL {
	private $connexion, $nbQueries;

	function __construct($login, $pass, $base, $host) {
		if (!$this->connexion = @mysql_pconnect($host, $login, $pass))
			echo "connect error!";

		if (!@mysql_select_db($base, $this->connexion))
			echo "connect error!";
	}

	public function dbQuery($query) {
		$result = @mysql_query($query);
		return $result;
	}

	public function numRows($sql) {
		return @mysql_num_rows($sql);
	}

	public function fetch($sql) {
		return @mysql_fetch_assoc($sql);
	}

	public function nbQueries() {
		return $this->nbQueries;
	}

	function __destruct() {
		@mysql_close($this->connexion);
	}
}
?>