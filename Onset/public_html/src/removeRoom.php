<?php
require_once 'core.php';

session_start();

try {
    if (!Onset::isValidAccess($_POST['rand'])) throw new Exception('不正なアクセス。');

    $roomName = isset($_POST['roomName']) && $_POST['roomName'] !== "" ? htmlspecialchars($_POST['roomName'], ENT_QUOTES) : false;
    $roomPw   = isset($_POST['roomPw'])   && $_POST['roomPw']   !== "" ? $_POST['roomPw']                                 : false;

    if ($roomName === false || $roomPw === false) throw new Exception('ルーム名かパスワードがセットされていません');

    $roomList = Onset::getroomList();

    if (!isset($roomList[$roomName])) throw new Exception('部屋が存在しません');

    $dir      = $config['roomSavepath'];
    $roomId   = $roomList[$roomName]['path'];
    $roomDir  = $dir.$roomId;
    $passHash = file_get_contents($roomDir.'/pass.hash');

    if (!password_verify($roomPw, $passHash) && $roomPw != $config['pass']) throw new Exception('パスワードを間違えています');

    foreach (scandir($roomDir.'/connect/') as $k) {
        if ($k == '.' || $k == '..') continue;
        if (!unlink($roomDir.'/connect/'.$k)) throw new Exception('接続ディレクトリの削除に失敗。');
    }

    if (!rmdir($roomDir.'/connect/')) throw new Exception('接続ディレクトリの削除に失敗。');

    foreach (scandir($roomDir) as $k) {
        if ($k == '.' || $k == '..') continue;
        if (!unlink($roomDir.'/'.$k)) throw new Exception('部屋ディレクトリの削除に失敗。');
    }

    if (!rmdir($roomDir)) throw new Exception('部屋ディレクトリの削除に失敗。');

    unset($roomList[$roomName]);

    if (!Onset::setRoomList($roomList)) throw new Exception('部屋リストからの削除に失敗');
} catch (Exception $e) {
    echo Onset::jsonStatus($e->getMessage(), -1);
    die();
}

echo Onset::jsonStatus('ok');
