<?php

$time = $_POST['time'];
$room = $_POST['room'];
$key = $_POST['key'];

$dir = "../../room/{$room}/";

if($key != file_get_contents("{$dir}key.txt")){
      echo "不正なアクセス：invalid_access";
      die();
}

if($time < filemtime("{$dir}xxlogxx.txt") * 1000){
      echo file_get_contents("{$dir}xxlogxx.txt");
}else{
      echo "none";
}

clearstatcache();
