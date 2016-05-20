<?php
require_once('config.php');
require_once('core.php');

session_start();

isNULLRoom($_SESSION['onset_room']);

$json = $dir.$_SESSION['onset_room']."/chatLogs.json";
$text = file_get_contents($json, true);

header("Content-type: application/json");
echo $text;
