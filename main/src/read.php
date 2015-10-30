<?php

session_start();
$dir = "../log/xxlogxx.txt";
$time = $_POST['time'];

if($time < filemtime($dir)){
      echo file_get_contents($dir);
}else{
      echo "none";
}
