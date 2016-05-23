<?php
require_once(dirname(__FILE__).'/core.php');

session_start();

// PL名
$loginName = isset($_POST['name']) && $_POST['name'] != NULL ? trim($_POST['name']) : FALSE;

// 送信チャットデータ
$sendText = isset($_POST['text']) && $_POST['text'] != NULL ? trim($_POST['text']) : FALSE;

// 使用TRPGシステム
$usingSystem  = isset($_POST['sys'])  && $_POST['sys']  != NULL ? trim($_POST['sys'])  : FALSE;

// 部屋ID
$roomID = isset($_SESSION['onset_room']) && $_SESSION['onset_room'] != NULL ? $_SESSION['onset_room'] : FALSE;

// どれか1つでも未セットならエラー
if(!$loginName || !$sendText || !$usingSystem || !$roomID){
  echo "不正なアクセス:invalid_access";
  die();
}

require_once('config.php');

// "<dir>/<roomID>"
$dir = $dir.$roomID;

// 行数処理
isLongChat($sendText, $loginName);

//ダイス処理
$url = $config['bcdiceURL'];

$encordedText = urlencode($sendText);
$encordedSys  = urlencode($usingSystem);

// SSL Configure
$s = "";
if($config["enableSSL"]) $s = 's';

$resultDice = file_get_contents("http{$s}://{$url}?text={$encordedText}&sys={$encordedSys}");

if(trim($resultDice) == '1' || trim($resultDice) == 'error') $resultDice = "";

$diceRes   = str_replace('onset: ', '', $resultDice);

$loginName = htmlspecialchars($loginName, ENT_QUOTES);
$sendText  = nl2br(htmlspecialchars($sendText, ENT_QUOTES));

// $line で使う時間の値のタイムゾーン設定
date_default_timezone_set('asia/Tokyo');
$ISO8601time = date('c'); // ISO 8601
$RFC2822time = date('r'); // RFC 2822
$UNIXtime    = date('U'); // UNIXtime
$diceRes     = htmlspecialchars($diceRes, ENT_QUOTES); // BCDice結果

$line            = array(
  "userID"      => $_SESSION['onset_id'],
  "name"        => $loginName,
  "text"        => $sendText,
  "diceRes"     => $diceRes,
  "UNIXtime"    => $UNIXtime,
  "ISO8601time" => $ISO8601time,
  "RFC2822time" => $RFC2822time
);

$json   = json_decode(file_get_contents($dir.$roomID.'/chatLogs.json'), true);
$json[] = $line;
$json   = json_encode($json);

file_put_contents($dir."/chatLogs.json", $json, LOCK_EX);

$_SESSION['onset_name'] = $loginName;
