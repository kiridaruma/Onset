<?php
require_once('config.php');
require_once('core.php');

$loginName = isset($_POST['loginName']) || $_POST['loginName'] != 0 ? htmlspecialchars($_POST['loginName'], ENT_QUOTES) : FALSE;
$roomName  = isset($_POST['roomName']) || $_POST['roomName']   != 0 ? $_POST['roomName'] : FALSE;
$roomPass  = isset($_POST['roomPass']) || $_POST['roomPass']   != 0 ? $_POST['roomPass'] : FALSE;

if(!$loginName || !$roomPass || !$roomName){
  echo "名前とパスワードを入力してください(ブラウザバックをお願いします)";
  die();
}

if(isExistRoom($roomLists, $roomName) === false){
  echo "存在しない部屋です(ブラウザバックをお願いします)";
  die();
}

foreach($roomLists as $k) {
  if($k['roomName'] === $roomName) {
    $roomID   = $k['roomID'];
  }
}

$roomPath = $dir.$roomID.'/roomInfo.json';

$roomInfoJSON = json_decode(file_get_contents($roomPath), true);
$roomPassHash = $roomInfoJSON['roomPassword'];

isCorrectPassword($roomPass, $roomPassHash);

$userID = ip2long($_SERVER['REMOTE_ADDR']) + mt_rand();

session_start();

$_SESSION['onset_name'] = $loginName;
$_SESSION['onset_room'] = $roomID;
$_SESSION['onset_id']   = dechex($userID);

header("Location: ../Onset.php");
