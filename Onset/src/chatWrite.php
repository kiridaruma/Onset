<?php
require_once(dirname(__FILE__).'/core.php');

session_start();

$name = isset($_GET['name']) && $_GET['name'] != NULL ? trim($_GET['name']) : FALSE;
$text = isset($_GET['text']) && $_GET['text'] != NULL ? trim($_GET['text']) : FALSE;
// $sys  = isset($_GET['sys'])  && $_POST['sys']  != NULL ? trim($_POST['sys'])  : FALSE;
// $room = isset($_SESSION['onset_room']) && $_SESSION['onset_room'] != NULL ? $_SESSION['onset_room'] : FALSE;

/*
if(!$text || !$name || !$room || !$sys){
	echo "不正なアクセス:invalid_access";
	die();
}
 */

if(!$name || !$text) {
	echo "err.".PHP_EOL;
	die();
}

require_once('config.php');

$dir = $dir.'tests';

// isLongChat($text, $name);

//ダイス処理
// $url = $config['bcdiceURL'];

// $encordedText = urlencode($text);
// $encordedSys  = urlencode($sys);

// SSL Configure
// $s = "";
// if($config["enableSSL"]) $s = 's';

// $ret = file_get_contents("http{$s}://{$url}?text={$encordedText}&sys={$encordedSys}");

// if(trim($ret) == '1' || trim($ret) == 'error'){
// 	$ret = "";
// }

// $diceRes = str_replace('onset: ', '', $ret);

$name    = htmlspecialchars($name, ENT_QUOTES);
$text    = nl2br(htmlspecialchars($text, ENT_QUOTES));

date_default_timezone_set('asia/Tokyo');
$ISO8601time    = date('c');
$RFC822time     = date('r');
$UNIXtime       = date('U');
// $diceRes = htmlspecialchars($diceRes, ENT_QUOTES);

// TODO https://github.com/AkagiCrafter/Onset/issues/5
// $line    = "<div class=\"chat\"><b>{$name}</b>({$_SESSION['onset_id']})<br>\n{$text}<br>\n<i>{$diceRes}</i></div>\n";

$line    = [
	'name' => $name,
	'text' => $text,
	'UNIXtime' => $UNIXtime,
	'ISO8601time' => $ISO8601time,
	'RFC822time'  => $RFC822time
];

// var_dump($line);

$line    = json_encode($line).file_get_contents("{$dir}/chatLogs.json");

echo file_get_contents($dir.'/chatLogs.json').PHP_EOL;
file_put_contents("{$dir}/chatLogs.json", $line, LOCK_EX);
echo $line;
// $_SESSION['onset_name'] = $name;
