<?php
require_once('config.php');
require_once('core.php');

// ログイン名。
$loginName = isset($_POST['loginName']) || $_POST['loginName'] != 0 ? htmlspecialchars($_POST['loginName'], ENT_QUOTES) : FALSE;

// 部屋名。
$roomName  = isset($_POST['roomName']) || $_POST['roomName']   != 0 ? $_POST['roomName'] : FALSE;

// 部屋パスワード。
$roomPass  = isset($_POST['roomPass']) || $_POST['roomPass']   != 0 ? $_POST['roomPass'] : FALSE;

// いずれか1つでも未セットならアーメン。
if(!$loginName || !$roomPass || !$roomName){
  echo "名前とパスワードを入力してください(ブラウザバックをお願いします)";
  die();
}

// 部屋存在するの? ...したらいいですね!
if(isExistRoom($roomLists, $roomName) === false){
  echo "存在しない部屋です(ブラウザバックをお願いします)";
  die();
}

// 部屋ID取得。
// TODO: この処理はcore.phpに投げてもいいんじゃないかな。
// TODO: ^については、Classブランチにおいて解決しています。

foreach($roomLists as $k) {
  if($k['roomName'] === $roomName) {
    $roomID   = $k['roomID'];
  }
}

// roomInfo.json
$roomInfoJSON = getRoomInfoJSON($roomID);

// 部屋パスワードハッシュの取得。
$roomPassHash = $roomInfoJSON['roomPassword'];

// Is this correct?
// Or incorrect?
if(isCorrectPassword($roomPass, $roomPassHash) === false) {
  echo 'パスワードが違います。';
  die();
}

// TODO: どうだっていいんですけどここの処理どうなるんだろう。
$userID = ip2long($_SERVER['REMOTE_ADDR']) + mt_rand();

// セッションスタールト!
// (スタールト: ロケットの名前です)
session_start();

// セッションに値放り込む。
$_SESSION['onset_name'] = $loginName;
$_SESSION['onset_room'] = $roomID;
$_SESSION['onset_id']   = dechex($userID);

// Onset.phpに処理を投げつけます。
header("Location: ../Onset.php");
