<?php

require_once('config.php');
require_once('core.php');

$nick = isset($_POST['nick']) || $_POST['nick'] != 0 ? htmlspecialchars($_POST['nick'], ENT_QUOTES) : FALSE;
$pass = isset($_POST['pass']) || $_POST['pass'] != 0 ? $_POST['pass'] : FALSE;
$room = isset($_POST['room']) || $_POST['room'] != 0 ? $_POST['room'] : FALSE;

if(!$nick || !$pass || !$room){
    echo Onset::errorJson("空欄があります");
    die();
}

$roomlist = Onset::getRoomlist();

if(isset($roomlist[$room]) === false){
    echo Onset::errorJson("存在しない部屋です");
    die();
}

$roompath = $roomlist[$room]['path'];
$dir = $config['roomSavepath'];

$hash = file_get_contents("{$dir}{$roompath}/pass.hash");

if(!password_verify($pass, $hash) && $config['pass'] != $pass){
    echo Onset::errorJson("パスワードが間違っています");
    die();
}

$ip = ip2long($_SERVER['REMOTE_ADDR']);
$id = $ip + mt_rand();

session_start();

$_SESSION['onset_nick'] = $nick;
$_SESSION['onset_room'] = $roompath;
$_SESSION['onset_id']   = dechex($id);

echo Onset::okJson('認証OK');
