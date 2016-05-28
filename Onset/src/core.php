<?php
require_once(dirname(__FILE__).'/config.php');

/*
 * getRoomListsJSON
 * roomLists.jsonを取得します。
 *
 * @param  boolean        $isDecode 返却するJSONをデコードするか
 * @return resource/array $JSON     返却するJSON $isDecodeがtrueならarray
 *
 */
function getRoomListsJSON($isDecode = true) {
  global $dir;

  $JSON = file_get_contents($dir.'roomLists.json');

  if($isDecode) $JSON = json_decode($JSON, true);

  return $JSON;
}

/*
 * getRoomInfoJSON
 * roomInfo.jsonを取得します。
 *
 * @param  string         $roomID   部屋ID
 * @param  boolean        $isDecode 返却するJSONをデコードするか
 * @return resource/array $JSON     返却するJSON $isDecodeがtrueならarray
 *
 */
function getRoomInfoJSON($roomID, $isDecode = true) {
  global $dir;
  $JSON = file_get_contents($dir.$roomID.'/roomInfo.json');

  if($isDecode) $JSON = json_decode($JSON, true);

  return $JSON;
}

/*
 * getChatLogsJSON
 * chatLogs.jsonを取得します。
 *
 * @param  string         $roomID   部屋ID
 * @param  boolean        $isDecode 返却するJSONをデコードするか
 * @return resource/array $JSON     返却するJSON $isDecodeがtrueならarray
 */
function getChatLogsJSON($roomID, $isDecode = true) {
  global $dir;
  $JSON = file_get_contents($dir.$roomID.'/chatLogs.json');

  if($isDecode) $JSON = json_decode($JSON, true);

  return $JSON;
}

/*
 * isIllegalAccess
 */
function isIllegalAccess($rand, $onset_rand) {
  if($rand != $onset_rand) {
    return false;
  }
  return true;
}

/*
 * isExistRoom
 */
function isExistRoom($roomLists, $room) {
  foreach($roomLists as $k) {
    if($k['roomName'] === $room || $k['roomID'] === $room) return true;
  }

  return false;
}

/*
 * isLongRoomName
 */
function isLongRoomName($name) {
  global $config;
  if(mb_strlen($name) >= $config['maxRoomName']) {
    echo mb_strlen($name)."文字";
    echo "部屋名が長過ぎます。";
    die();
  }
  return true;
}

/*
 * isLongChat
 */
function isLongChat($text, $name) {
  global $config;
  if(mb_strlen($text) >= $config["maxChatText"]) {
    echo "送信するテキストの文字数が多すぎます(ブラウザバックをお願いします)。";
    echo "最大数:".$config["maxChatText"];
    die();
  }

  if(mb_strlen($name) >= $config["maxChatNick"]) {
    echo "送信する名前の文字数が多すぎます(ブラウザバックをお願いします)。";
    echo "最大数:".$config["maxChatNick"];
    die();
  }
  return true;
}

/*
 * isNULLRoom
 */
function isNULLRoom($room) {
  if($room === NULL) {
    echo "Invalid Access: Room number is null.";
    die();
  }
  return true;
}

/*
 * isSetNamePass
 */
function isSetNameAndPass($name, $pass) {
  if(!$name) {
    echo "部屋名を設定、もしくは指定してください。";
    die();
  }

  if(!$pass) {
    echo "パスワードを設定、もしくは指定してください。";
    die();
  }
  return true;
}

/*
 * isCorrectPassword
 */
function isCorrectPassword($pass, $hash) {
  global $config;
  if(!password_verify($pass, $hash) && $config['pass'] != $pass) {
    return false;
  }
  return true;
}
