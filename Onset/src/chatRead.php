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

$chatLogsJSON = getChatLogsJSON($roomID, false);
echo $chatLogsJSON;

$tmp = $dir.$roomID."/connect/".$roomID;
file_put_contents($tmp, time()."\n".$roomID);

clearstatcache();
