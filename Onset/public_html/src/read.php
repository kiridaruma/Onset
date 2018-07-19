<?php
require_once __DIR__ . '/core.php';

session_start();

$time = Onset::varidate($_POST['time']);
$room = Onset::varidate($_SESSION['onset_room']);

if ($time === false || $room === false) {
    echo Onset::jsonMessage("不正なアクセス", -1);
    die();
}

$dir = RoomSavepath . $room;

if (!file_exists($dir)) {
    echo Onset::jsonMessage("部屋が存在しません", -1);
    die();
}

$chatLog = json_decode(file_get_contents($dir . "/log.json"));
$cuttedLog = Onset::searchLog($chatLog, $time);

echo Onset::jsonMessage("ok", 1, $cuttedLog);

$tmp = $dir . "/connect/" . $_SESSION['onset_id'];
file_put_contents($tmp, time() . "\n" . $_SESSION['onset_nick'], LOCK_EX);

clearstatcache();
