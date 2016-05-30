<?php
require_once(dirname(__FILE__).'/core.php');

session_start();

$nick = isset($_POST['nick']) && $_POST['nick'] != NULL ? trim($_POST['nick']) : FALSE;
$text = isset($_POST['text']) && $_POST['text'] != NULL ? trim($_POST['text']) : FALSE;
$sys  = isset($_POST['sys'])  && $_POST['sys']  != NULL ? trim($_POST['sys'])  : FALSE;
$room = isset($_SESSION['onset_room']) && $_SESSION['onset_room'] != NULL ? $_SESSION['onset_room'] : FALSE;

if(!$text || !$nick || !$room || !$sys){
	echo "不正なアクセス:invalid_access";
	die();
}

require_once('config.php');

$dir = $dir.$room;

isLongChat($text, $nick);

//var_dump($_POST);
//var_dump($nick);
//var_dump($text);

//ダイス処理
$url = $config['bcdiceURL'];

$encordedText = urlencode($text);
$encordedSys  = urlencode($sys);

$s = "";
if($config["enableSSL"]){$s = 's';}
$ret = file_get_contents("http{$s}://{$url}?text={$encordedText}&sys={$encordedSys}");
if(trim($ret) == '1' || trim($ret) == 'error'){
	$ret = "";
}
$diceRes = str_replace('onset: ', '', $ret);

//var_dump($nick);
//var_dump($text);
//var_dump($diceRes);

$nick = htmlspecialchars($nick, ENT_QUOTES);
$text = htmlspecialchars($text, ENT_QUOTES);
$diceRes = htmlspecialchars($diceRes, ENT_QUOTES);

$text = nl2br($text);

$line = "<div class=\"chat\"><b>{$nick}</b>({$_SESSION['onset_id']})<br>\n{$text}<br>\n<i>{$diceRes}</i></div>\n";

//var_dump($line);

$line = $line.file_get_contents("{$dir}/xxlogxx.txt");
file_put_contents("{$dir}/xxlogxx.txt", $line, LOCK_EX);
$_SESSION['onset_nick'] = $nick;
