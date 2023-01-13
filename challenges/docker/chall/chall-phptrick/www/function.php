<?php
	function adminpasswordcheck($str)
	{
		$Temp = false;
		if(preg_match("/x|b|e/i", $str))
		{
			$Temp = true;
		}
		else if($str === "10251974855569910250535299484910010250971009949525553985749575551")
		{
			$Temp = true;		
		}
		return $Temp;
	}
	function filter($str)
	{
		if(preg_match("/union|select|\(|\)|_|schema|or|and|by|group|pw|\/|\*|\\\\/i", $str))
		{
			exit("no hack");
		}
		return $str;
	}
?>
