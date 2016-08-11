<?php
require_once 'core.php';

session_start();

$playerName  = isset($_POST['playerName'])      && $_POST['playerName']      !== '' ? trim($_POST['playerName'])  : FALSE;
$chatContent = isset($_POST['chatContent'])     && $_POST['chatContent']     !== '' ? trim($_POST['chatContent']) : FALSE;
$diceSystem  = isset($_POST['diceSystem'])      && $_POST['diceSystem']      !== '' ? trim($_POST['diceSystem'])  : FALSE;
$roomId      = isset($_SESSION['onset_roomid']) && $_SESSION['onset_roomid'] !== '' ? $_SESSION['onset_roomid']   : FALSE;

try {
    $_SESSION['onset_playername'] = $playerName;

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
    $chatContent = htmlspecialchars($chatContent, ENT_QUOTES);

    // timezone
    date_default_timezone_set(Config::Timezone);

    // json
    $rawJson = [
        "time"        => date('U'),
        "playerId"    => $_SESSION['onset_playerid'],
        "playerName"  => $playerName,
        "chatContent" => $chatContent,
        "diceRes"     => $diceRes,
        "diceSystem"  => $diceSystem
    ];

    // add new lines.
    $json   = Onset::getChatLogs($roomId);
    $json[] = $rawJson;

    $json   = json_encode($json, JSON_UNESCAPED_UNICODE);

    // put.
    file_put_contents($roomDir.'/chatLogs.json', $json, LOCK_EX);

    // below is legacy.
    $chatContent = nl2br($chatContent);

    // さらば、友よ...
    $line = "<div class=\"chat\"><b>{$playerName}</b>({$_SESSION['onset_playerid']})<br>\n{$chatContent}<br>\n<i>{$diceRes}</i></div>\n";

    $line = $line . file_get_contents($roomDir.'/xxlogxx.txt');

    file_put_contents($roomDir.'/xxlogxx.txt', $line, LOCK_EX);

} catch (Exception $e) {
    echo Onset::jsonStatus($e->getMessage(), -1);
    die();
}

echo Onset::jsonStatus('ok');
