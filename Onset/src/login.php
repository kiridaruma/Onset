<?php
require_once('config.php');
require_once('core.php');

$nick = isset($_POST['nick']) || $_POST['nick'] !== '' ? htmlspecialchars($_POST['nick'], ENT_QUOTES) : FALSE;
$pass = isset($_POST['pass']) || $_POST['pass'] !== '' ? $_POST['pass'] : FALSE;
$room = isset($_POST['room']) || $_POST['room'] !== '' ? htmlspecialchars($_POST['room'], ENT_QUOTES) : FALSE;

try {
    if($nick === false || $pass === false || $room === false) throw new Exception('空欄があります');
    if($config['maxNick'] <= mb_strlen($nick)) throw new Exception('名前が長すぎます ('. mb_strlen($nick) .')');

    $roomlist = Onset::getRoomlist();

    if(!isset($roomlist[$room])) throw new Exception('存在しない部屋です');

    $roompath = $roomlist[$room]['path'];
    $dir      = $config['roomSavepath'];
    $_dir     = $dir.$roompath;
    $hash     = file_get_contents($_dir.'/pass.hash');

    if(!password_verify($pass, $hash) && $config['pass'] != $pass) throw new Exception('パスワードが間違っています');


} catch (Exception $e) {
    echo Onset::jsonStatus($e->getMessage(), -1);
    die();
}

$id = ip2long($_SERVER['REMOTE_ADDR']) + mt_rand();

session_start();

$_SESSION['onset_nick'] = $nick;
$_SESSION['onset_room'] = $roompath;
$_SESSION['onset_id']   = dechex($id);

echo Onset::jsonStatus('認証OK');
