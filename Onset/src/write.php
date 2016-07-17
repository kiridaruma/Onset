<?php
require_once(dirname(__FILE__).'/core.php');

session_start();

$nick = isset($_POST['nick']) && $_POST['nick'] != NULL ? trim($_POST['nick']) : FALSE;
$text = isset($_POST['text']) && $_POST['text'] != NULL ? trim($_POST['text']) : FALSE;
$sys  = isset($_POST['sys'])  && $_POST['sys']  != NULL ? trim($_POST['sys'])  : FALSE;
$room = isset($_SESSION['onset_room']) && $_SESSION['onset_room'] != NULL ? $_SESSION['onset_room'] : FALSE;

try {
    if (!$text || !$nick || !$room || !$sys) throw new Exception('不正なアクセス:invalid_access');

    require_once('config.php');

    $_dir = $config['roomSavepath'].$room;

    if ($config['maxNick'] <= $nick) throw new Exception('名前が長すぎます ('. mb_strlen($nick) .')');
    if ($config['maxText'] <= $text) throw new Exception('テキストが長すぎます ('. mb_strlen($text) .')');

    //ダイス処理
    $diceRes = Onset::diceroll($text, $sys);

    // TODO: htmlspecialcharsのラッパ
    $nick = htmlspecialchars($nick, ENT_QUOTES);
    $text = htmlspecialchars($text, ENT_QUOTES);
    $diceRes = htmlspecialchars($diceRes, ENT_QUOTES);

    $text = nl2br($text);

    $line = "<div class=\"chat\"><b>{$nick}</b>({$_SESSION['onset_id']})<br>\n{$text}<br>\n<i>{$diceRes}</i></div>\n";

    $line = $line.file_get_contents("{$dir}/xxlogxx.txt");
    file_put_contents("{$dir}/xxlogxx.txt", $line, LOCK_EX);
    $_SESSION['onset_nick'] = $nick;
} catch (Exception $e) {
    echo Onset::jsonStatus($e->getMessage(), -1);
}

echo Onset::jsonStatus('ok');
