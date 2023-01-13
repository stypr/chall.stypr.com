<?php

header("Cache-control: no-cache");
if(!defined("__INIT_LOAD__")) {
    header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found", true, 404);
    exit;
}

$content = explode("\n", file_get_contents("~current_status"));
$traffic = $content[0];
$vm = json_decode($content[1], true);

function get_stat(){
    global $traffic;
    @exec("free -m | awk 'NR==2{printf \"%s/%s\", $3,$2 }'", $mem);
    $mem = $mem[0];
    @exec("grep 'cpu ' /proc/stat | awk '{usage=($2+$4)*100/($2+$4+$5)} END {print usage}'", $cpu);
    $cpu = $cpu[0];
    @exec("df -h | awk '\$NF==\"/\"{printf \"%d/%d\", $3,$2 }' 2>&1", $disk);
    $disk = $disk[0];
    return [$mem, $cpu, $disk, $traffic];
}

ksort($vm['chall.stypr.com']);

?>
