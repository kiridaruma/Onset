<?php
require_once 'core.php';

session_start();

$time   = isset($_POST['time'])            && $_POST['time']            !== '' ? $_POST['time']          : false;
$roomId = isset($_SESSION['onset_roomid']) && $_SESSION['onset_roomid'] !== '' ? $_SESSION['onset_roomid'] : false;

if ($time === false || $roomId === false) {
    echo Onset::jsonStatus("不正なアクセス", -1);
    die();
}

$roomDir = Config::roomSavepath.$roomId;

if ($time < filemtime($roomDir."/xxlogxx.txt") * 1000) {
    $fp = fopen($roomDir."/xxlogxx.txt", 'r');

    do {
        $line = fgets($fp);
        if($line !== false) echo $line;
    } while($line !== false);

    fclose($fp);
} else {
    echo "none";
}

$tmp = $roomDir."/connect/".$_SESSION['onset_playerid'];
file_put_contents($tmp, time()."\n".$_SESSION['onset_playername'], LOCK_EX);

clearstatcache();
