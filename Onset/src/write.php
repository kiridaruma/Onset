<?php
require_once('core.php');

session_start();

$nick = isset($_POST['nick']) && $_POST['nick'] != NULL ? trim($_POST['nick']) : false;
$text = isset($_POST['text']) && $_POST['text'] != NULL ? trim($_POST['text']) : false;
$sys  = isset($_POST['sys'])  && $_POST['sys']  != NULL ? trim($_POST['sys'])  : false;
$room = isset($_SESSION['onset_room']) && $_SESSION['onset_room'] != NULL ? $_SESSION['onset_room'] : false;

try {
    if (!$text || !$nick || !$room || !$sys) throw new Exception('不正なアクセス:invalid_access');

    require_once('config.php');

    $_dir = $config['roomSavepath'].$room;

    if ($config['maxNick'] <= mb_strlen($nick)) throw new Exception('名前が長すぎます ('. mb_strlen($nick) .')');
    if ($config['maxText'] <= mb_strlen($text)) throw new Exception('テキストが長すぎます ('. mb_strlen($text) .')');

    //ダイス処理
    $diceRes = Onset::diceroll($text, $sys);

    // エスケープ処理
    $nick    = Onset::h($nick);
    $text    = Onset::h($text);
    $diceRes = Onset::h($diceRes);

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
