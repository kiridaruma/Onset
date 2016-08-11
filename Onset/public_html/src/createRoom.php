<?php
require_once 'core.php';

session_start();

$roomName = isset($_POST['roomName']) && $_POST['roomName'] !== "" ? $_POST['roomName'] : false;
$roomPw   = isset($_POST['roomPw'])   && $_POST['roomPw']   !== "" ? $_POST['roomPw']   : false;

try {
    if(!Onset::isValidAccess($_POST['rand'])) throw new Exception('不正なアクセス。');

    if($roomName === false || $roomPw === false) throw new Exception('部屋名かパスワードが空です。');

    if(mb_strlen($roomName) >= Config::maxRoomName) throw new Exception('部屋名が長すぎます。');

    $roomName = htmlspecialchars($roomName, ENT_QUOTES);
    $roomList = Onset::getRoomlist();

    if(isset($roomList[$roomName])) throw new Exception('同名の部屋がすでに存在しています。');

    if(count($roomList) >= Config::roomLimit) throw new Exception('部屋数制限いっぱいです。');

    $roomId = uniqid('', true);

    $roomDir = Config::roomSavepath.$roomId;

    if(!mkdir($roomDir)) throw new Exception('部屋ディレクトリ作成に失敗しました。');

    if(!mkdir($roomDir.'/connect')) throw new Exception('接続ディレクトリ作成に失敗しました。');

    if(!touch($roomDir.'/pass.hash'))   throw new Exception('パスワードハッシュの生成に失敗しました。');
    if(!touch($roomDir.'/xxlogxx.txt')) throw new Exception('ログインハッシュの生成に失敗しました。');

    if(!chmod($roomDir,                0777)) throw new Exception('パーミッションの変更に失敗しました。');
    if(!chmod($roomDir.'/connect/',    0777)) throw new Exception('パーミッションの変更に失敗しました。');
    if(!chmod($roomDir.'/pass.hash',   0666)) throw new Exception('パーミッションの変更に失敗しました。');
    if(!chmod($roomDir.'/xxlogxx.txt', 0666)) throw new Exception('パーミッションの変更に失敗しました。');

    $hash = password_hash($roomPw, PASSWORD_DEFAULT);
    unset($roomPw);

    if(!file_put_contents($roomDir.'/pass.hash', $hash)) throw new Exception('パスワードハッシュのデータ挿入に失敗しました。');

    $roomList[$roomName]["path"] = $roomId;

    if(!Onset::setRoomlist($roomList)) throw new Exception('部屋一覧の処理に失敗しました。');

} catch(Exception $e) {
    echo Onset::jsonStatus($e->getMessage(), -1);
    die();
}

echo Onset::jsonStatus('ok');
