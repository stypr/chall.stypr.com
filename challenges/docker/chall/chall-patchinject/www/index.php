<?php
    include("config.php");

    $id = @$_REQUEST['id'];
    $pw = @$_REQUEST['pw'];

    /*
        adm1nkyj made the sqli blocking solution and I'm the one re-implementing it.
    */
    $hackword = "sylvanas|0x|0b|limit|like|regexp|limit|_|information|schema|char|sin|cos|asin|procedure|patchinject|trim|pad|make|mid";
    /*
        what the...
        jinmo123 hacked my system using every bits of sqli and the below list is the payload he used...
        I've blocked most part of his patterns, now he can't beat me for sure..
    */
    $hackword .= "substr|compress|where|code|replace|conv|insert|right|left|cast|ascii|x|hex|version|user|data|load_file|out|gcc|locate|count|reverse|b|y|hello";


    /*
        [2015-07-31 14:31:46] Rex_is_the_King solved PatchInject

        how did rex solve this man lol
        time to block more sqli!!
    */
    $hackword .= "glob|php|load|inject|month|day|now|user|collation";

    /*
        yeah, this is just to defend more sqli.
    */
    if(substr_count($pw, "'")>1 || substr_count($pw, "\"")>1 || substr_count($pw, "(")>10 ||
       preg_match("/'|\\\\/i", $id) || preg_match("/$hackword/i", $pw)){
        echo("Hacking not allowed!");
        goto bye;
    }

    $q = @mysql_fetch_assoc(mysql_query("SELECT * FROM patchinject WHERE id='{$id}' AND pw='{$pw}';"));
    if($q['id']){
        if(strtolower($q['id']) === "sylvanas"){ // sylvanas not in the table..
            echo(__FLAG__);
            goto bye;
        }else{
            echo("Login Success");
            goto bye;
        }
    }else{
        echo("Login Fail..");
        goto bye;
    }

bye:
    echo("<hr>");
    highlight_file(__FILE__);
?>
