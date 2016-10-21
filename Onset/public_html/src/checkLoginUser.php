<?php

require_once(__DIR__.'/core.php');

session_start();

$room = Onset::varidate($_SESSION['onset_room']);
$id = Onset::varidate($_SESSION['onset_id']);
$lock = Onset::varidate($_POST['lock']);

if(!$room || !$id){
    echo "不正なアクセス";
    die();
}

$dir = RoomSavepath.$room."/connect/";
$arr = scandir($dir);

if($lock === 'unlock') {
    file_put_contents($dir.$id, time()."\n".$_SESSION['onset_nick']);
    die();
}

file_put_contents($dir.$id, time()."\n".$_SESSION['onset_nick']."\nlocked");

$ret = "";
$num = 0;

foreach($arr as $value) {
    if($value == "." || $value == "..") continue;

    $arr = explode("\n", file_get_contents($dir.$value));
    $time = $arr[0];
    $nick = $arr[1];
    $isLock = count($arr) > 2 ? $arr : '';

    if($time + 5 < time() && $isLock !== 'locked') {
        unlink($dir.$value);
        continue;
    }

    $ret .= $nick.'#'.$value."\n";

    $num++;
}

echo $num."人がログイン中\n".$ret;
