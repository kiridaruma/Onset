<?php
require_once('core.php');
header('content-type: application/json; charset=utf-8');

session_start();

try {

    if (!Onset::isValidAccess($_POST['rand'])) throw new Exception('不正なアクセス。');

    $room = isset($_POST['room']) && $_POST['room'] !== "" ? htmlspecialchars($_POST['room'], ENT_QUOTES) : false;
    $pass = isset($_POST['pass']) && $_POST['pass'] !== "" ? $_POST['pass'] : false;

    if ($room === false || $pass === false) throw new Exception('ルーム名かパスワードがセットされていません');

    $roomlist = Onset::getRoomlist();

    if (!isset($roomlist[$room])) throw new Exception('部屋が存在しません');

    $roompath = $roomlist[$room]['path'];
    $dir      = config::roomSavepath;
    $_dir     = $dir.$roompath;
    $hash     = file_get_contents($_dir.'/pass.hash');

    if (!password_verify($pass, $hash) && $pass != config::pass) throw new Exception('パスワードを間違えています');

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

    unset($roomlist[$room]);

    if (!Onset::setRoomlist($roomlist)) throw new Exception('部屋リストからの削除に失敗');
} catch (Exception $e) {
    echo Onset::jsonStatus($e->getMessage(), -1);
    die();
}

echo Onset::jsonStatus('ok');
