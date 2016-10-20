<?php
require_once('core.php');

session_start();

$nick = Onset::varidate($_POST['nick']);
$text = Onset::varidate($_POST['text']);
$sys  = Onset::varidate($_POST['sys']);
$room = Onset::varidate($_SESSION['onset_room']);

try {
    if ($text === false || $nick === false || $room === false || $sys === false)
        throw new Exception('不正なアクセス:invalid_access');

    $_dir = RoomSavepath.$room;

    if ($config['maxNick'] <= mb_strlen($nick)) throw new Exception('名前が長すぎます ('. mb_strlen($nick) .')');
    if ($config['maxText'] <= mb_strlen($text)) throw new Exception('テキストが長すぎます ('. mb_strlen($text) .')');

    $diceRes = Onset::diceroll($text, $sys);

    $jsonData = (object)[
        'time' => time(),
        'nick' => $nick,
        'text' => $text,
        'dice' => $diceRes
    ];

    $chatLog = json_decode(file_get_contents($_dir.'/log.json'));
    $chatLog[] = $jsonData;
    file_put_contents($_dir.'/log.json', json_encode($chatLog), LOCK_EX);
    $_SESSION['onset_nick'] = $nick;

} catch (Exception $e) {
    echo Onset::jsonMessage($e->getMessage(), -1);
    die();
}

echo Onset::jsonStatus('ok');
