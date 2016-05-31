<?php

require_once('config.php');
require_once('core.php');

$nick = isset($_POST['nick']) || $_POST['nick'] != 0 ? htmlspecialchars($_POST['nick'], ENT_QUOTES) : FALSE;
$pass = isset($_POST['pass']) || $_POST['pass'] != 0 ? $_POST['pass'] : FALSE;
$room = isset($_POST['room']) || $_POST['room'] != 0 ? $_POST['room'] : FALSE;

if(!$nick || !$pass || !$room){
	echo "名前とパスワードを入力してください(ブラウザバックをお願いします)";
	die();
}

$roomlist = getRoomlist();

if(isExistRoom($roomlist, $room) === false){
	echo "存在しない部屋です(ブラウザバックをお願いします)";
	die();
}

$roompath = $roomlist[$room]['path'];
$dir = $config['roomSavepath'];

$hash = file_get_contents("{$dir}{$roompath}/pass.hash");

if(!password_verify($pass, $hash) && $config['pass'] != $pass){
	echo "パスワードが間違っています(ブラウザバックをお願いします)";
	die();
}

$ip = ip2long($_SERVER['REMOTE_ADDR']);
$id = $ip + mt_rand();

session_start();

$_SESSION['onset_nick'] = $nick;
$_SESSION['onset_room'] = $roompath;
$_SESSION['onset_id']   = dechex($id);

header("Location: ../Onset.php");
