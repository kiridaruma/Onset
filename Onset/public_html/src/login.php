<?php
require_once __DIR__ . '/core.php';

$nick = Onset::varidate($_POST['nick']);
$pass = Onset::varidate($_POST['pass']);
$room = Onset::varidate($_POST['room']);

try {
    if ($nick === false || $pass === false || $room === false) {
        throw new Exception('空欄があります');
    }

    if (MaxNick <= mb_strlen($nick)) {
        throw new Exception('名前が長すぎます (' . mb_strlen($nick) . ')');
    }

    $roomlist = Onset::getRoomlist();
    $room = htmlspecialchars($room, ENT_QUOTES);
    if (!isset($roomlist->{$room})) {
        throw new Exception('存在しない部屋です');
    }

    $roompath = $roomlist->{$room}->path;
    $dir = RoomSavepath;
    $_dir = $dir . $roompath;
    $hash = file_get_contents($_dir . '/pass.hash');

    if (!password_verify($pass, $hash) && Pass != $pass) {
        throw new Exception('パスワードが間違っています');
    }

} catch (Exception $e) {
    echo Onset::jsonMessage($e->getMessage(), -1);
    die();
}

$id = ip2long($_SERVER['REMOTE_ADDR']) + mt_rand();

session_start();

$_SESSION['onset_nick'] = $nick;
$_SESSION['onset_room'] = $roompath;
$_SESSION['onset_id'] = dechex($id);

echo Onset::jsonMessage('認証OK');
