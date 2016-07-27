<?php
require_once('config.php');
require_once('core.php');

session_start();

$room = isset($_POST['room']) && $_POST['room'] !== "" ? $_POST['room'] : false;
$pass = isset($_POST['pass']) && $_POST['pass'] !== "" ? $_POST['pass'] : false;

try {

    if(!Onset::isValidAccess($_POST['rand'])) throw new Exception('不正なアクセス。');

    if($room === false || $pass === false) throw new Exception('部屋名かパスワードが空です。');

    if(mb_strlen($room) >= $config['maxRoomName']) throw new Exception('部屋名が長すぎます。');

    $room     = htmlspecialchars($room, ENT_QUOTES);
    $roomlist = Onset::getRoomlist();

    if(isset($roomlist[$room])) throw new Exception('同名の部屋がすでに存在しています。');

    if(count($roomlist) >= $config['roomLimit']) throw new Exception('部屋数制限いっぱいです。');

    $uuid = uniqid('', true);

    $_dir = $config['roomSavepath'].$uuid;

    if(!mkdir($_dir)) throw new Exception('部屋ディレクトリ作成に失敗しました。');

    if(!mkdir($_dir.'/connect')) throw new Exception('接続ディレクトリ作成に失敗しました。');

    if(!touch($_dir.'/pass.hash'))   throw new Exception('パスワードハッシュの生成に失敗しました。');
    if(!touch($_dir.'/xxlogxx.txt')) throw new Exception('ログインハッシュの生成に失敗しました。');

    if(!chmod($_dir,                0777)) throw new Exception('パーミッションの変更に失敗しました。');
    if(!chmod($_dir.'/connect/',    0777)) throw new Exception('パーミッションの変更に失敗しました。');
    if(!chmod($_dir.'/pass.hash',   0666)) throw new Exception('パーミッションの変更に失敗しました。');
    if(!chmod($_dir.'/xxlogxx.txt', 0666)) throw new Exception('パーミッションの変更に失敗しました。');

    $hash = password_hash($pass, PASSWORD_DEFAULT);
    unset($pass);

    if(!file_put_contents($_dir.'/pass.hash', $hash)) throw new Exception('パスワードハッシュのデータ挿入に失敗しました。');

    $roomlist[$room]["path"] = $uuid;

    if(!Onset::setRoomlist($roomlist)) throw new Exception('部屋一覧の処理に失敗しました。');

} catch(Exception $e) {
    echo Onset::jsonStatus($e->getMessage(), -1);
    die();
}

echo Onset::jsonStatus('ok');
