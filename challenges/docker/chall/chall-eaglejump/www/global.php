<?php
	function randstr($leng)
	{
		$TABLE = "QWERTYUIOPASDFGHJKLZXCVBNM1234567890qwertyuiopasdfghjklzxcvbnm";
		$ret ="";
		for($i =0; $i<$leng; $i++)
			$ret .= $TABLE[rand()%strlen($TABLE)];
		return $ret;
	}

?>
