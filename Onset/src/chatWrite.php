<?php
require_once(dirname(__FILE__).'/core.php');

session_start();

// PL名
$name = isset($_POST['name']) && $_POST['name'] != NULL ? trim($_POST['name']) : FALSE;

// 送信チャットデータ
$text = isset($_POST['text']) && $_POST['text'] != NULL ? trim($_POST['text']) : FALSE;

// 使用TRPGシステム
$sys  = isset($_POST['sys'])  && $_POST['sys']  != NULL ? trim($_POST['sys'])  : FALSE;

// 部屋ID
$room = isset($_SESSION['onset_room']) && $_SESSION['onset_room'] != NULL ? $_SESSION['onset_room'] : FALSE;

// どれか1つでも未セットならエラー
if(!$text || !$name || !$room || !$sys){
	echo "不正なアクセス:invalid_access";
	die();
}

require_once('config.php');

// "<dir>/<roomID>"
$dir = $dir.$room;

// 行数処理
isLongChat($text, $name);

//ダイス処理
$url = $config['bcdiceURL'];

$encordedText = urlencode($text);
$encordedSys  = urlencode($sys);

// SSL Configure
$s = "";
if($config["enableSSL"]) $s = 's';

$ret = file_get_contents("http{$s}://{$url}?text={$encordedText}&sys={$encordedSys}");

if(trim($ret) == '1' || trim($ret) == 'error') $ret = "";

$diceRes = str_replace('onset: ', '', $ret);

$name    = htmlspecialchars($name, ENT_QUOTES);
$text    = nl2br(htmlspecialchars($text, ENT_QUOTES));

// $line で使う時間の値のタイムゾーン設定
date_default_timezone_set('asia/Tokyo');
$ISO8601time = date('c'); // ISO 8601
$RFC2822time = date('r'); // RFC 2822
$UNIXtime    = date('U'); // UNIXtime
$diceRes     = htmlspecialchars($diceRes, ENT_QUOTES); // BCDice結果

$line            = array(
	"userID"      => $_SESSION['onset_id'],
	"name"        => $name,
	"text"        => $text,
	"diceRes"     => $diceRes,
	"UNIXtime"    => $UNIXtime,
	"ISO8601time" => $ISO8601time,
	"RFC2822time" => $RFC2822time
);

$json   = json_decode(file_get_contents($dir.'/chatLogs.json'), true);
$json[] = $line;
$json   = json_encode($json);

file_put_contents($dir."/chatLogs.json", $json, LOCK_EX);

$_SESSION['onset_name'] = $name;
