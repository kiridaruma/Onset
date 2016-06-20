<?php
require_once('config.php');
require_once('core.php');

session_start();

if(!isset($_SESSION['onset_room'])){
    echo "不正なアクセスです";
    die();
}
$dir = $config['roomSavepath'];
$logdir = $dir.$_SESSION['onset_room']."/xxlogxx.txt";
$text = htmlspecialchars_decode(strip_tags(file_get_contents($logdir)));

header("Content-type: text/plain");
echo $text;
