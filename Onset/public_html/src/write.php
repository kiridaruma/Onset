<?php
require_once 'core.php';

session_start();

$playerName  = isset($_POST['playerName'])      && $_POST['playerName']            !== '' ? trim($_POST['playerName'])  : FALSE;
$chatContent = isset($_POST['chatContent'])     && $_POST['chatContent']     !== '' ? trim($_POST['chatContent']) : FALSE;
$diceSystem  = isset($_POST['diceSystem'])      && $_POST['diceSystem']      !== '' ? trim($_POST['diceSystem'])  : FALSE;
$roomId      = isset($_SESSION['onset_roomid']) && $_SESSION['onset_roomid'] !== '' ? $_SESSION['onset_roomid']   : FALSE;

try {
    if ($playerName  === false
    ||  $chatContent === false
    ||  $diceSystem  === false
    ||  $roomId      === false
    ) throw new Exception('不正なアクセス:invalid_access');

    require_once('config.php');

    if (Config::maxNick <= mb_strlen($playerName)) throw new Exception('名前が長すぎます ('. mb_strlen($playerName) .')');
    if (Config::maxText <= mb_strlen($chatContent)) throw new Exception('テキストが長すぎます ('. mb_strlen($chatContent) .')');

    $roomDir = Config::roomSavepath.$roomId;

    $diceRes = Onset::diceRoll($chatContent, $diceSystem);

    $diceRes     = htmlspecialchars($diceRes,     ENT_QUOTES);
    $playerName  = htmlspecialchars($playerName,  ENT_QUOTES);
    $chatContent = nl2br(htmlspecialchars($chatContent, ENT_QUOTES));

    $line = "<div class=\"chat\"><b>{$playerName}</b>({$_SESSION['onset_playerid']})<br>\n{$chatContent}<br>\n<i>{$diceRes}</i></div>\n";

    $line = $line . file_get_contents($roomDir.'/xxlogxx.txt');
    file_put_contents($roomDir.'/xxlogxx.txt', $line, LOCK_EX);
    $_SESSION['onset_nick'] = $playerName;

} catch (Exception $e) {
    echo Onset::jsonStatus($e->getMessage(), -1);
    die();
}

echo Onset::jsonStatus('ok');
