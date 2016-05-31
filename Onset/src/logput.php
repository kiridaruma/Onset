<?php
require_once('config.php');
require_once('core.php');

session_start();

// 部屋ID.
$roomID  = isset($_SESSION['onset_room'])   && $_SESSION['onset_room'] != NULL ? $_SESSION['onset_room']  : FALSE;

// 部屋があればいいんですけど。
// TODO: true/falseで投げてこっちでメッセージ処理せなあかん。
isNULLRoom($roomID);

// chatLogs.json
$chatLogsJSON = getChatLogsJSON($roomID);

// ヘッダはテキストでござんす。
header("Content-type: text/plain");

// チャットログをオブジェクト毎に処理。
foreach($chatLogsJSON as $k) {
  if($k['diceRes'] === '') {
    echo $k['ISO8601time'].' : '.$k['name'].' : '.$k['text'].PHP_EOL;
    continue;
  }

  echo $k['ISO8601time'].' : '.$k['name'].' : '.$k['text'].' '.$k['diceRes'];
}
