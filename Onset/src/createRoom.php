<?php

require_once('config.php');
require_once('core.php');

session_start();

if(isIllegalAccess($_POST['rand'], $_SESSION['onset_rand']) === false) {
  echo 'Illegal Access: invalid_access.';
  die();
}

$name = isset($_POST['name']) && $_POST['name'] != "" ? $_POST['name'] : FALSE;
$pass = isset($_POST['pass']) && $_POST['pass'] != "" ? $_POST['pass'] : FALSE;
$mode = $_POST['mode'];


// 部屋名とPWセットの確認
isSetNameAndPass($name, $pass);

// 部屋の長さチェック
isLongRoomName($name);

$name = htmlspecialchars($name, ENT_QUOTES);

if(isExistRoom($roomlist, $name) === true) {
  echo "同名の部屋がすでに存在しています(ブラウザバックをおねがいします)";
  die();
}

if(count($roomlist) >= $config["roomLimit"]){
  echo "これ以上部屋を立てられません、制限いっぱいです。";
  die();
}


try{
  // $uuidは
  // 部屋のディレクトリ名
  // 部屋のID
  // に使われます。
  $uuid = uniqid("", true);

  // roomInfo.json
  // roomList.jsonの処理と間違えないようにっ!
  $hash =	[
    "roomName" => $_POST['name'],
    "roomPassword" => password_hash($pass, PASSWORD_DEFAULT)
  ];

  $roomJSON = json_encode($hash);

  unset($pass);			//念の為、平文のパスワードを削除

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

  file_put_contents($dir.$uuid.'/roomInfo.json', $roomJSON);

  //
  // UUID: {
  //   'roomID'   : 'UUID',
  //   'roomName' : 'NAME'
  // }
  //
  $newRoom = [
    $uuid => [
      'roomID' => $uuid,
      'roomName' => $_POST['name']
    ]
  ];

  // マージ。
  $roomlist = array_merge($roomlist, $newRoom);
  $json = json_encode($roomlist);

  file_put_contents($dir.'/roomLists.json', $json);
  header("Location: ../index.php");

} catch(Exception $e) {
  echo "Exception: ".$e;
}

