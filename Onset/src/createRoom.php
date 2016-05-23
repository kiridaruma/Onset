<?php

require_once('config.php');
require_once('core.php');

session_start();

if(isIllegalAccess($_POST['rand'], $_SESSION['onset_rand']) === false) {
  echo 'Illegal Access: invalid_access.';
  die();
}

$roomName = isset($_POST['roomName']) && $_POST['roomName'] != "" ? $_POST['roomName'] : FALSE;
$roomPass = isset($_POST['roomPass']) && $_POST['roomPass'] != "" ? $_POST['roomPass'] : FALSE;
$roomMode = $_POST['mode'];


// 部屋名とPWセットの確認
isSetNameAndPass($roomName, $roomPass);

// 部屋の長さチェック
isLongRoomName($roomName);

$roomName = htmlspecialchars($roomName, ENT_QUOTES);

if(isExistRoom($roomLists, $roomName) === true) {
  echo "同名の部屋がすでに存在しています(ブラウザバックをおねがいします)";
  die();
}

if(count($roomLists) >= $config["roomLimit"]){
  echo "これ以上部屋を立てられません、制限いっぱいです。";
  die();
}


try{
  // $uuidは
  // 部屋のディレクトリ名
  // 部屋のID
  // に使われます。
  $uuid = uniqid("", true);

  mkdir($dir.$uuid);

  // roomInfo.json
  // 部屋データの管理
  touch($dir.$uuid.'/roomInfo.json');

  // chatLogs.json
  // チャットデータの管理
  touch($dir.$uuid.'/chatLogs.json');
  mkdir($dir.$uuid.'/connect');

  // 'chmod b111000000\n'
  // - Ar tonelico
  chmod($dir.$uuid, 									0777);
  chmod($dir.$uuid.'/chatLogs.json',	0666);
  chmod($dir.$uuid.'/roomInfo.json', 	0666);
  chmod($dir.$uuid.'/connect/',		 		0777);

  //
  // UUID: {
  //   'roomID'   : 'UUID',
  //   'roomName' : 'NAME'
  // }
  //
  $newRoomListHash = [
    $uuid => [
      'roomID' => $uuid,
      'roomName' => $roomName
    ]
  ];

  // マージ。
  $mergedRoomLists = json_encode(array_merge($roomLists, $newRoomListHash));

  file_put_contents($dir.'/roomLists.json', $mergedRoomLists);

  //
  // roomInfo.json
  // roomList.jsonの処理と間違えないようにっ!
  //
  // {
  //   'roomName'     : '$roomName',
  //   'roomPassword' : '$roomPass'
  // }
  //
  $newRoomInfoHash =	[
    "roomName" => $roomName,
    "roomPassword" => password_hash($roomPass, PASSWORD_DEFAULT)
  ];

  $roomInfoJSON = json_encode($newRoomInfoHash);

  file_put_contents($dir.$uuid.'/roomInfo.json', $roomInfoJSON);


  unset($roomPass);			//念の為、平文のパスワードを削除
  unset($_POST['roomPass']);

  header("Location: ../index.php");

} catch(Exception $e) {
  echo "Exception: ".$e;
}

