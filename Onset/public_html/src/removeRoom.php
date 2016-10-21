<?php

require_once(__DIR__.'/core.php');

session_start();

try {

    if ($_POST['rand'] != $_SESSION['onset_rand']) throw new Exception('不正なアクセス。');

    $room = Onset::varidate($_POST['room']);
    $pass = Onset::varidate($_POST['pass']);

    if ($room === false || $pass === false) throw new Exception('ルーム名かパスワードがセットされていません');

    $roomlist = Onset::getRoomlist();

    if (!isset($roomlist->{$room})) throw new Exception('部屋が存在しません');

    $roompath = $roomlist->{$room}->path;
    Onset::removeRoomData(RoomSavepath.$roompath."/");

    unset($roomlist->{$room});
    if (!Onset::saveRoomlist($roomlist)) throw new Exception('部屋インデックスデータの保存に失敗しました');
} catch (Exception $e) {
    echo Onset::jsonMessage($e->getMessage(), -1);
    die();
}

echo Onset::jsonMessage('ok');
