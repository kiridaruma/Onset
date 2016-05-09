<?php

require_once('config.php');
session_start();

if($_POST['rand'] != $_SESSION['onset_rand']){
    echo "無効なアクセス:invalid_access";
    die();
}

$name = isset($_POST['name']) && $_POST['name'] != "" ? $_POST['name'] : FALSE;
$pass = isset($_POST['pass']) && $_POST['pass'] != "" ? $_POST['pass'] : FALSE;
$mode = $_POST['mode'];

if(!$name || !$pass){
    echo "部屋名とパスワードを入力してください";
    die();
}

if(mb_strlen($name) > 30){
    echo "部屋名が長過ぎます";
    die();
}

$dir = $config['roomSavepath'];

$roomlist = unserialize(file_get_contents($dir."roomlist"));

$isExist = isset($roomlist[$name]);
$roompath = $roomlist[$name]['path'];

if(!$isExist){
    echo "部屋が存在しません(ブラウザバックをおねがいします)";
    die();
}

$hash = file_get_contents("{$dir}{$roompath}/pass.hash");
if(!password_verify($pass, $hash) && $config['pass'] != $pass){
    echo "パスワードを間違えています(ブラウザバックをおねがいします)";
    die();
}

unset($roomlist[$name]);
file_put_contents($dir."roomlist", serialize($roomlist));

foreach(scandir("{$dir}{$roompath}/connect/") as $value){
    if($value != "." || $value != ".."){unlink("{$dir}{$roompath}/connect/{$value}");}
}
rmdir("{$dir}{$roompath}/connect/");

foreach(scandir($dir.$roompath) as $value){
    if($value != "." || $value != ".."){unlink("{$dir}{$roompath}/{$value}");}
}
rmdir($dir.$roompath);

header("Location: ../index.php");
