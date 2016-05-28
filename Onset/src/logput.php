<?php
require_once('config.php');
require_once('core.php');

session_start();

$roomID  = isset($_SESSION['onset_room'])   && $_SESSION['onset_room'] != NULL ? $_SESSION['onset_room']  : FALSE;

isNULLRoom($roomID);

$chatLogsJSON = getChatLogsJSON($roomID);

header("Content-type: text/plain");
foreach($chatLogsJSON as $k) {
  if($k['diceRes'] === '') {
    echo $k['ISO8601time'].' : '.$k['name'].' : '.$k['text'].PHP_EOL;
    continue;
  }

  echo $k['ISO8601time'].' : '.$k['name'].' : '.$k['text'].' '.$k['diceRes'];
}
