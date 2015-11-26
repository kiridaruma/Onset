<?php
session_start();
if($_SESSION['onset_room'] === NULL){
      echo "不正なアクセス:invalid access";
      die();
}

$logdir = "../room/{$_SESSION['onset_room']}/xxlogxx.txt";
$text = file_get_contents($logdir);

$replace = ['<br>', '<i>', '</i>', '<b>', '</b>'];
foreach ($replace as $value) {
      $text = str_replace($value, "", $text);
}
$text = str_replace("<hr>", "\n", $text);
$text = htmlspecialchars_decode($text);

#$putdir = "../tmp/{$_SESSION['onset_room']}.txt";
#touch($putdir);
#chmod($putdir, 0666);
#file_put_contents($putdir, $text);
header("Content-type: text/plain");
echo $text;
