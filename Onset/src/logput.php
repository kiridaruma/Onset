<?php
require_once('config.php');
require_once('core.php');

session_start();

$roomID  = isset($_SESSION['onset_room'])   && $_SESSION['onset_room'] != NULL ? $_SESSION['onset_room']  : FALSE;

isNULLRoom($roomID);

$text = file_get_contents($dir.$roomID.'/chatLogs.json', true);

header("Content-type: application/json");
echo $text;
