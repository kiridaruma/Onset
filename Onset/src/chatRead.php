<?php

require_once('core.php');
require_once('config.php');

session_start();

$roomID = isset($_SESSION['onset_room']) 	&& $_SESSION['onset_room'] 	!= NULL ? $_SESSION['onset_room']	: false;
$time = isset($_POST['time']);

// 値が未セットなら終わり
if(!$roomID || !$time) {
  echo "Invalid Access: Time OR Room variables is null.";
  die();
}

// chatLogs.json
$chatLogsJSON = getChatLogsJSON($roomID, false);

// 吐き出すだけ。
echo $chatLogsJSON;

// connectの処理
$tmp = $dir.$roomID."/connect/".$roomID;
file_put_contents($tmp, time()."\n".$roomID);

clearstatcache();
