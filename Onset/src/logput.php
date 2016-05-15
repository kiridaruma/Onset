<?php
require_once('config.php');
require_once('core.php');

session_start();

isNULLRoom($_SESSION['onset_room']);

$logdir = $dir.$_SESSION['onset_room']."/xxlogxx.txt";
$text = htmlspecialchars_decode(strip_tags(file_get_contents($logdir)));

header("Content-type: text/plain");
echo $text;
