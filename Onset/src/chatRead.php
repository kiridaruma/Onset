<?php

require_once('config.php');

session_start();

$room = isset($_SESSION['onset_room']) 	&& $_SESSION['onset_room'] 	!= NULL ? $_SESSION['onset_room']	: false;
$time = isset($_POST['time']);


// 値が未セットなら終わり
if(!$room || !$time) {
  echo "Invalid Access: Time OR Room variables is null.";
  die();
}

$dir = $dir.$room;
$json = file_get_contents($dir."/chatLogs.json", 'r');
echo $json;

$tmp = $dir."/connect/".$_SESSION['onset_id'];
file_put_contents($tmp, time()."\n".$_SESSION['onset_name']);

clearstatcache();
