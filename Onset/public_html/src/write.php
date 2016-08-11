<?php
require_once('core.php');
header('content-type: application/json; charset=utf-8');

session_start();

$nick = isset($_POST['nick']) && $_POST['nick'] !== '' ? trim($_POST['nick']) : FALSE;
$text = isset($_POST['text']) && $_POST['text'] !== '' ? trim($_POST['text']) : FALSE;
$sys  = isset($_POST['sys'])  && $_POST['sys']  !== '' ? trim($_POST['sys'])  : FALSE;
$room = isset($_SESSION['onset_room']) && $_SESSION['onset_room'] !== '' ? $_SESSION['onset_room'] : FALSE;

try {
    if ($text === false || $nick === false || $room === false || $sys === false) throw new Exception('不正なアクセス:invalid_access');

    require_once('config.php');

    $_dir = config::roomSavepath.$room;

    if (config::maxNick <= mb_strlen($nick)) throw new Exception('名前が長すぎます ('. mb_strlen($nick) .')');
    if (config::maxText <= mb_strlen($text)) throw new Exception('テキストが長すぎます ('. mb_strlen($text) .')');

    $diceRes = Onset::diceroll($text, $sys);

    $nick    = htmlspecialchars($nick, ENT_QUOTES);
    $text    = htmlspecialchars($text, ENT_QUOTES);
    $diceRes = htmlspecialchars($diceRes, ENT_QUOTES);

    $text = nl2br($text);

    $line = "<div class=\"chat\"><b>{$nick}</b>({$_SESSION['onset_id']})<br>\n{$text}<br>\n<i>{$diceRes}</i></div>\n";

    $line = $line . file_get_contents($_dir.'/xxlogxx.txt');
    file_put_contents($_dir.'/xxlogxx.txt', $line, LOCK_EX);
    $_SESSION['onset_nick'] = $nick;

} catch (Exception $e) {
    echo Onset::jsonStatus($e->getMessage(), -1);
    die();
}

echo Onset::jsonStatus('ok');
