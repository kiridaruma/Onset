<?php
require_once 'core.php';

session_start();

if(!isset($_SESSION['onset_roomid'])){
    echo "不正なアクセスです";
    die();
}
$dir     = $config['roomSavepath'];
$roomDir = $dir.$_SESSION['onset_roomid']."/xxlogxx.txt";
$text    = htmlspecialchars_decode(strip_tags(file_get_contents($roomDir)));

header("Content-type: text/plain");
echo $text;
