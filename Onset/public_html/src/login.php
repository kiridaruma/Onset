<?php
require_once 'core.php';

$playerName = isset($_POST['playerName']) || $_POST['playerName'] !== '' ? htmlspecialchars($_POST['playerName'], ENT_QUOTES) : FALSE;
$roomName   = isset($_POST['roomName'])   || $_POST['roomName']   !== '' ? htmlspecialchars($_POST['roomName'], ENT_QUOTES)   : FALSE;
$roomPw     = isset($_POST['roomPw'])     || $_POST['roomPw']     !== '' ? $_POST['roomPw']                                   : FALSE;

try {
    if($playerName === false || $roomName === false || $roomPw === false) throw new Exception('空欄があります');
    if($config['maxNick'] <= mb_strlen($playerName)) throw new Exception('名前が長すぎます ('. mb_strlen($playerName) .')');

    $roomList = Onset::getRoomlist();

    if(!isset($roomList[$roomName])) throw new Exception('存在しない部屋です');

    $dir      = $config['roomSavepath'];
    $roomId   = $roomList[$roomName]['path'];
    $roomDir  = $dir.$roomId;

    $passHash = file_get_contents($roomDir.'/pass.hash');

    if(!password_verify($roomPw, $passHash) && $config['pass'] != $roomPw) throw new Exception('パスワードが間違っています');

} catch (Exception $e) {
    echo Onset::jsonStatus($e->getMessage(), -1);
    die();
}

session_start();

$_SESSION['onset_playerid']   = dechex(ip2long($_SERVER['REMOTE_ADDR']) + mt_rand());
$_SESSION['onset_playername'] = $playerName;
$_SESSION['onset_roomid']     = $roomId;

echo Onset::jsonStatus('認証OK');
