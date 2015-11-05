<?php

session_start();

$time = isset($_POST['time']) && $_POST['time'] != NULL ? $_POST['time'] : FALSE;
$room = isset($_SESSION['onset_room']) && $_SESSION['onset_room'] != NULL ? $_SESSION['onset_room'] : FALSE;

if(!$time || !$room){
      echo "不正なアクセス：invalid_access";
      die();
}

$dir = "../room/{$room}/";

if($time < filemtime("{$dir}xxlogxx.txt") * 1000){
      echo file_get_contents("{$dir}xxlogxx.txt");
}else{
      echo "none";
}

clearstatcache();
