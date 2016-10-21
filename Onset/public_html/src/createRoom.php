<?php

require_once(__DIR__.'/core.php');

session_start();

$room = Onset::varidate($_POST['room']);
$pass = Onset::varidate($_POST['pass']);

try {

    if($_POST['rand'] != $_SESSION['onset_rand']) throw new Exception('不正なアクセス。');

    if($room === false || $pass === false) throw new Exception('部屋名かパスワードが空です。');

    if(mb_strlen($room) >= MaxRoomName) throw new Exception('部屋名が長すぎます。');

    $room     = htmlspecialchars($room, ENT_QUOTES);
    $roomlist = Onset::getRoomlist();

    if(isset($roomlist->{$room})) throw new Exception('同名の部屋がすでに存在しています。');

    if(count((array)$roomlist) >= RoomLimit) throw new Exception('部屋数制限いっぱいです。');

    $uuid = uniqid('', true);

    $_dir = RoomSavepath.$uuid;

    if(!mkdir($_dir)) throw new Exception('部屋ディレクトリ作成に失敗しました。');

    if(!mkdir($_dir.'/connect')) throw new Exception('接続ディレクトリ作成に失敗しました。');

    if(!touch($_dir.'/pass.hash'))   throw new Exception('パスワードハッシュの生成に失敗しました。');
    if(!file_put_contents($_dir.'/log.json', '[]')) throw new Exception('ログインハッシュの生成に失敗しました。');

    if(!chmod($_dir,                0777)) throw new Exception('パーミッションの変更に失敗しました。');
    if(!chmod($_dir.'/connect/',    0777)) throw new Exception('パーミッションの変更に失敗しました。');
    if(!chmod($_dir.'/pass.hash',   0666)) throw new Exception('パーミッションの変更に失敗しました。');
    if(!chmod($_dir.'/log.json', 0666)) throw new Exception('パーミッションの変更に失敗しました。');

    $hash = password_hash($pass, PASSWORD_DEFAULT);
    unset($pass);

    if(!file_put_contents($_dir.'/pass.hash', $hash)) throw new Exception('パスワードハッシュのデータ挿入に失敗しました。');

    $roomlist->{$room} = new stdClass();
    $roomlist->{$room}->path = $uuid;

    if(!Onset::saveRoomlist($roomlist)) throw new Exception('部屋一覧の処理に失敗しました。');

} catch(Exception $e) {
    echo Onset::jsonMessage($e->getMessage(), -1);
    die();
}

echo Onset::jsonMessage('ok');
