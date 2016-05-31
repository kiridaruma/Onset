<?php
require_once(dirname(__FILE__).'/config.php');

/*
 * 全体的なTODOなこと
 * 1. $roomListsはglobalでこっちで処理はどうだろう。
 * 2. true/falseで返すように。
 *
 */

/*
 * getRoomListsJSON
 * roomLists.jsonを取得します。
 *
 * @param  boolean $isDecode    返却するJSONをデコードするか
 * @return mixed   返却するJSON $isDecodeがtrueならarray
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
 * @param  string  $roomID      部屋ID
 * @param  boolean $isDecode    返却するJSONをデコードするか
 * @return mixed   返却するJSON $isDecodeがtrueならarray
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
 * @param  string  $roomID      部屋ID
 * @param  boolean $isDecode    返却するJSONをデコードするか
 * @return mixed   返却するJSON $isDecodeがtrueならarray
 */
function getChatLogsJSON($roomID, $isDecode = true) {
  global $dir;
  $JSON = file_get_contents($dir.$roomID.'/chatLogs.json');

  if($isDecode) $JSON = json_decode($JSON, true);

  return $JSON;
}

/*
 * イリーガルなアクセスか確認する
 *
 * @param string $rand       ランダム値
 * @param string $onset_rand ランダム値
 *
 * @return boolean trueかfalse
 *
 * TODO: 要らなくね(使用箇所が一箇所だけなのでここに置く理由がない)?
 */
function isIllegalAccess($rand, $onset_rand) {
  if($rand != $onset_rand) {
    return false;
  }
  return true;
}

/*
 * 部屋が存在するか確認する
 *
 * @param array  $roomLists 部屋配列
 * @param string $room      部屋名
 *
 * @return boolean true/false...
 *
 * TODO: isNULLRoomとの統合
 */
function isExistRoom($roomLists, $room) {
  foreach($roomLists as $k) {
    if($k['roomName'] === $room || $k['roomID'] === $room) return true;
  }

  return false;
}

/*
 * 部屋名が長くないかチェックする
 *
 * @param string $name 部屋名
 *
 * @return mixed 部屋名が投げぇかtrueが返される。
 *
 * TODO: true/false...
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
 * チャット送信内容が過剰に長くないかチェックする
 *
 * @param string $text チャットデータ
 * @param string $name 名前
 *
 * @return mixed 下記のコード見てください...大丈夫ならtrue.
 *
 * TODO: true/falseで投げてくだせぇ!?
 * TODO: textとnameは分離すべきなのか。
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
 * 部屋が存在しないか確認する。
 *
 * @param string $room 部屋名
 *
 * @return mixed Invalid accessと返すかtrue...ｱｲｴｴｴｴｴ!?
 *
 * TODO: isExistRoomとの統合はどうでしょうか。
 *
 */
function isNULLRoom($room) {
  if($room === NULL) {
    echo "Invalid Access: Room number is null.";
    die();
  }
  return true;
}

/*
 * 部屋名とパスワードの値セットのチェック
 *
 * @param string $name 部屋名
 * @param string $pass パスワード
 *
 * @return boolean true/false...
 *
 * TODO: 名前とパスワードはメソッドを分離して処理でいいんじゃないかな。
 * TODO: true/falseで投げてくだせぇ!?
 *
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
 * パスワードのベリファイ。
 *
 * @param string $pass パスワード
 * @param string $hash パスワードのハッシュ
 *
 * @return boolean true/false...
 *
 */
function isCorrectPassword($pass, $hash) {
  global $config;
  if(!password_verify($pass, $hash) && $config['pass'] != $pass) {
    return false;
  }
  return true;
}
