<?php

require_once('config.php');
session_start();

if($_POST['rand'] != $_SESSION['onset_rand']){
    echo "無効なアクセス:invalid_access";
    die();
}

$name = isset($_POST['name']) || $_POST['name'] != 0 ? $_POST['name'] : FALSE;
$pass = isset($_POST['pass']) || $_POST['pass'] != 0 ? $_POST['pass'] : FALSE;
$mode = $_POST['mode'];

if(!$name || !$pass){
    echo "部屋名とパスワードを入力してください";
    die();
}

if(mb_strlen($name) > 30){
    echo "部屋名が長過ぎます";
    die();
}

$dir = "../room/";

if(!file_exists($dir.$name)){
    echo "部屋が存在しません(ブラウザバックをおねがいします)";
    die();
}

$hash = file_get_contents("{$dir}{$name}/pass.hash");
if(!password_verify($pass, $hash) && $config['pass'] != $pass){
    echo "パスワードを間違えています(ブラウザバックをおねがいします)";
    die();
}

foreach(scandir("{$dir}{$name}/connect/") as $value){
    if($value != "." || $value != ".."){unlink("{$dir}{$name}/connect/{$value}");}
}
rmdir("{$dir}{$name}/connect/");

foreach(scandir($dir.$name) as $value){
    if($value != "." || $value != ".."){unlink("{$dir}{$name}/{$value}");}
}
rmdir($dir.$name);

header("Location: ../index.php");
