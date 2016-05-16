<?php

require_once('config.php');
require_once('core.php');

$name = isset($_POST['name']) || $_POST['name'] != 0 ? htmlspecialchars($_POST['name'], ENT_QUOTES) : FALSE;
$pass = isset($_POST['pass']) || $_POST['pass'] != 0 ? $_POST['pass'] : FALSE;
$room = isset($_POST['room']) || $_POST['room'] != 0 ? $_POST['room'] : FALSE;

if(!$name || !$pass || !$room){
	echo "名前とパスワードを入力してください(ブラウザバックをお願いします)";
	die();
}

if(isExistRoom($roomlist, $room) === false){
	echo "存在しない部屋です(ブラウザバックをお願いします)";
	die();
}

$roompath = $roomlist[$room]['path'];

$hash = file_get_contents("{$dir}{$roompath}/pass.hash");

isCorrectPassword($pass, $hash);

$ip = ip2long($_SERVER['REMOTE_ADDR']);
$id = $ip + mt_rand();

session_start();

$_SESSION['onset_name'] = $name;
$_SESSION['onset_room'] = $roompath;
$_SESSION['onset_id']   = dechex($id);

header("Location: ../Onset.php");
