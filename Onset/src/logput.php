<?php
session_start();
if($_SESSION['onset_room'] === NULL){
      echo "不正なアクセス:invalid access";
      die();
}

require_once('config.php');

$dir = $config['roomSavepath'];

$logdir = "{$dir}{$_SESSION['onset_room']}/xxlogxx.txt";
$text = file_get_contents($logdir);

$text = strip_tags($text);
$text = htmlspecialchars_decode($text);

header("Content-type: text/plain");
echo $text;
