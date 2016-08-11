<?php
require_once 'core.php';

session_start();

$roomId   = isset($_SESSION['onset_roomid'])   && $_SESSION['onset_roomid']   != NULL ? $_SESSION['onset_roomid']   : FALSE;
$playerId = isset($_SESSION['onset_playerid']) && $_SESSION['onset_playerid'] != NULL ? $_SESSION['onset_playerid'] : FALSE;

if(!$roomId || !$playerId){
    echo "不正なアクセス";
    die();
}

$roomDir = $config['roomSavepath'].$roomId."/connect/";
$arr = scandir($roomDir);

if($_POST['lock'] === 'unlock') {
    file_put_contents($roomDir.$playerId, time()."\n".$_SESSION['onset_playername']);
    die();
}

file_put_contents($roomDir.$roomId, time()."\n".$_SESSION['onset_playername']."\nlocked");

foreach($arr as $value) {
    if($value == "." || $value == "..") continue;

    list($time, $nick, $isLock) = explode("\n", file_get_contents($roomDir.$value));

    if($time + 5 < time() && $isLock !== 'locked') {
        unlink($roomDir.$value);
        continue;
    }

    $ret .= $nick.'#'.$value."\n";

    $num++;
}

echo $num."人がログイン中\n".$ret;
