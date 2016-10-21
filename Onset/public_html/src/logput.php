<?php

require_once(__DIR__.'/core.php');

session_start();

if(!isset($_SESSION['onset_room'])){
    echo "不正なアクセスです";
    die();
}
$dir = RoomSavepath;
$logdir = $dir.$_SESSION['onset_room']."/log.json";
$chatLog = json_decode(file_get_contents($logdir));

header("Content-type: text/plain");
foreach($chatLog as $key => $val){
    echo $val->nick . " " . $val->id . ":" . date("Y/n/d H:i:s", $val->time) . "\n";
    echo $val->text . "\n";
    echo $val->dice !== "" ? $val->dice . "\n" : "";
    echo "\n==========\n";
}
