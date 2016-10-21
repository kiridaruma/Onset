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
    $dir      = RoomSavepath;
    $_dir     = $dir.$roompath;
    $hash     = file_get_contents($_dir.'/pass.hash');

    if (!password_verify($pass, $hash) && $pass != Pass) throw new Exception('パスワードを間違えています');

    foreach (scandir($_dir.'/connect/') as $k) {
        if ($k == '.' || $k == '..') continue;
        if (!unlink($_dir.'/connect/'.$k)) throw new Exception('接続ディレクトリの削除に失敗。');
    }

    if (!rmdir($_dir.'/connect/')) throw new Exception('接続ディレクトリの削除に失敗。');

    foreach (scandir($_dir) as $k) {
        if ($k == '.' || $k == '..') continue;
        if (!unlink($_dir.'/'.$k)) throw new Exception('部屋ディレクトリの削除に失敗。');
    }

    if (!rmdir($_dir)) throw new Exception('部屋ディレクトリの削除に失敗。');

    unset($roomlist->$room);

    if (!Onset::saveRoomlist($roomlist)) throw new Exception('部屋リストからの削除に失敗');
} catch (Exception $e) {
    echo Onset::jsonMessage($e->getMessage(), -1);
    die();
}

echo Onset::jsonMessage('ok');
