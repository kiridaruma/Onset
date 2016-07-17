<?php
require_once('core.php');
require_once('config.php');

session_start();

$time = isset($_POST['time'])          && $_POST['time']          != NULL ? $_POST['time']          : false;
$room = isset($_SESSION['onset_room']) && $_SESSION['onset_room'] != NULL ? $_SESSION['onset_room'] : false;

if (!$time || !$room) {
    echo Onset::jsonStatus("不正なアクセス", -1);
    die();
}

$_dir = $config['roomSavepath'].$room;

if ($time < filemtime($_dir."/xxlogxx.txt") * 1000) {
    $fp = fopen($dir."/xxlogxx.txt", 'r');

    do {
        $line = fgets($fp);
        if($line !== false) echo $line;
    } while($line !== false);

    fclose($fp);
} else {
    echo "none";
}

$tmp = $_dir."/connect/".$_SESSION['onset_id'];
file_put_contents($tmp, time()."\n".$_SESSION['onset_nick'], LOCK_EX);

clearstatcache();
