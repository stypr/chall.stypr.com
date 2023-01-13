<?php
	function newLfiFilter($str)
	{
		if(preg_match("/home|var|etc|db|index|bin|sess|file|tp|php:|zlib|data|glob|phar|ssh|rar|ogg|expect|\.\./i",$str))
			return false;
		return true;
	}

	function newSQLFilter($str)
	{
		if(preg_match("/union|asc|cha|sub|mid|lef|rig|sle|pro|into|by|if|case|ex|ben|li|ran|upd|by|jo|@/i",$str))
			return false;
		return true;
	}
?>
