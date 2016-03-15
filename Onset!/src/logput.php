<?php
session_start();
if($_SESSION['onset_room'] === NULL){
      echo "不正なアクセス:invalid access";
      die();
}

$logdir = "../room/{$_SESSION['onset_room']}/xxlogxx.txt";
$text = file_get_contents($logdir);

$text = strip_tags($text);
$text = htmlspecialchars_decode($text);

#$putdir = "../tmp/{$_SESSION['onset_room']}.txt";
#touch($putdir);
#chmod($putdir, 0666);
#file_put_contents($putdir, $text);
header("Content-type: text/plain");
echo $text;
