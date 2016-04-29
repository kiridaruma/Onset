<?php

require_once('config.php');

$name = isset($_POST['name']) || $_POST['name'] != 0 ? htmlspecialchars($_POST['name'], ENT_QUOTES) : FALSE;
$pass = isset($_POST['pass']) || $_POST['pass'] != 0 ? $_POST['pass'] : FALSE;
$room = isset($_POST['room']) || $_POST['room'] != 0 ? $_POST['room'] : FALSE;

if(!$name || !$pass || !$room){
      echo "名前とパスワードを入力してください(ブラウザバックをお願いします)";
      die();
}

$dir = $config['roomSavepath'];
$roomlist = unserialize(file_get_contents($dir."roomlist"));

if(!file_exists("../room/{$room}")){
      echo "存在しない部屋です(ブラウザバックをお願いします)";
      die();
}

$hash = file_get_contents("../room/{$room}/pass.hash");

if(!password_verify($pass, $hash) && $config['pass'] != $pass){
      echo "パスワードが間違っています(ブラウザバックをお願いします)";
      die();
}

session_start();
$_SESSION['onset_name'] = $name;
$_SESSION['onset_room'] = $room;
$_SESSION['onset_id'] = dechex(mt_rand());

header("Location: ../Onset.php");
