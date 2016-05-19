<?php
require_once(dirname(__FILE__).'/core.php');

session_start();

$name = isset($_POST['name']) && $_POST['name'] != NULL ? trim($_POST['name']) : FALSE;
$text = isset($_POST['text']) && $_POST['text'] != NULL ? trim($_POST['text']) : FALSE;
$sys  = isset($_POST['sys'])  && $_POST['sys']  != NULL ? trim($_POST['sys'])  : FALSE;
$room = isset($_SESSION['onset_room']) && $_SESSION['onset_room'] != NULL ? $_SESSION['onset_room'] : FALSE;

if(!$text || !$name || !$room || !$sys){
	echo "不正なアクセス:invalid_access";
	die();
}

require_once('config.php');

$dir = $dir.$room;

$dir = $dir.'test';

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

date_default_timezone_set('asia/Tokyo');
$ISO8601time    = date('c');
$RFC822time     = date('r');
$UNIXtime       = date('U');
$diceRes = htmlspecialchars($diceRes, ENT_QUOTES);

$file = file_get_contents($dir.'/chatLogs.json');


if(strlen($file) == 0) {
	$json = [
		"chatLogs" => []
	];

	$json = json_encode($json).PHP_EOL;
	file_put_contents("{$dir}/chatLogs.json", $json, LOCK_EX);
}

$line    = array(
	"name" => $name,
	"text" => $text,
	"diceRes" => $diceRes,
	"UNIXtime" => $UNIXtime,
	"ISO8601time" => $ISO8601time,
	"RFC822time"  => $RFC822time
);

$file = json_decode($file);

$json = $file->chatLogs + $line;
$json = json_encode($json);

file_put_contents("{$dir}/chatLogs.json", $json, LOCK_EX);

$_SESSION['onset_name'] = $name;
