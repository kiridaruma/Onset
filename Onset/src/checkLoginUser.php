<?php
require_once('config.php');

session_start();

$room = isset($_SESSION['onset_room']) 	&& $_SESSION['onset_room']	!= NULL ? $_SESSION['onset_room']	: FALSE;
$id   = isset($_SESSION['onset_id']) 		&& $_SESSION['onset_id']		!= NULL ? $_SESSION['onset_id'] 	: FALSE;

if(!$room || !$id){
  echo "Invalid Access: Room OR ID variables is NULL.";
  die();
}

$dir = $dir.$room."/connect/";
$arr = scandir($dir);

if($_POST['lock'] === 'unlock') {
  file_put_contents($dir.$id, time()."\n".$_SESSION['onset_name']);
  die();
}

file_put_contents($dir.$id, time()."\n".$_SESSION['onset_name']."\nlocked");

foreach($arr as $value) {
  if($value == "." || $value == "..") continue;

  list($time, $name, $isLock) = explode("\n", file_get_contents($dir.$value));

  if($time + 5 < time() && $isLock !== 'locked') {
    unlink($dir.$value);
    continue;
  }

  $ret .= $name.'#'.$value."\n";

  $num++;
}

echo $num."人がログイン中\n".$ret;
