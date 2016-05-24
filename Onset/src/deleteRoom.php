<?php

require_once('config.php');
require_once('core.php');

session_start();

if(isIllegalAccess($_POST['rand'], $_SESSION['onset_rand']) === false) {
  echo 'Exception: invalid_access.';
  die();
}

$roomName = isset($_POST['roomName']) && $_POST['roomName'] != "" ? $_POST['roomName'] : FALSE;
$roomPass = isset($_POST['roomPass']) && $_POST['roomPass'] != "" ? $_POST['roomPass'] : FALSE;
$roomMode = $_POST['mode'];

isSetNameAndPass($roomName, $roomPass);
isLongRoomName($roomName);

// $roomID   : 部屋のUUID
// $roomName : 部屋の名前
foreach($roomLists as $k) {
  if($k['roomName'] === $roomName) {
    $roomID   = $k['roomID'];
  }
}

if(isExistRoom($roomLists, $roomID) === false) {
  echo "部屋が存在しません(ブラウザバックをおねがいします)";
  die();
}

$roomInfoJSON = getRoomInfoJSON($roomID);
$roomPassHash = $roomInfoJSON['roomPassword'];

// PW一致の確認。
if(isCorrectPassword($roomPass, $roomPassHash) === false) {
  echo 'パスワードが違います。';
  die();
}

try{
  foreach(scandir("{$dir}{$roomID}/connect/") as $value) {
    if($value != "." || $value != "..") {
      unlink("{$dir}{$roomID}/connect/{$value}") ? "" : function() { throw new Exception('Failed to unlink "/connect".'); };
    }
  }
  rmdir("{$dir}{$roomID}/connect/") ? "" : function(){throw new Exception('Failed to delete "/connect".');};

  foreach(scandir($dir.$roomID) as $value) {
    if($value != "." || $value != "..") {
      unlink("{$dir}{$roomID}/{$value}") ? "" : function(){throw new Exception('Failed to unlink "." or "..".');};
    }
  }
  rmdir($dir.$roomID) ? "" : function(){throw new Exception();};

  unset($roomLists[$roomID]);
  $roomLists = json_encode($roomLists);

  file_put_contents($dir."/roomLists.json", $roomLists) ? "" : function(){throw new Exception('Failed to put contents to "roomlist".');};

  header("Location: ../index.php");

} catch(Exception $e) {
  echo "Exception: ".$e;
}

