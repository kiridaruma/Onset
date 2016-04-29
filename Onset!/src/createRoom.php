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

$name = str_replace("/", "／", $name);
$name = htmlspecialchars($name, ENT_QUOTES);

if(file_exists($dir.$name)){
    echo "同名の部屋がすでに存在しています(ブラウザバックをおねがいします)";
    die();
}

$hash = password_hash($pass, PASSWORD_DEFAULT);
unset($pass);     //念の為、平文のパスワードを削除
mkdir($dir.$name);
touch("{$dir}{$name}/pass.hash");
touch("{$dir}{$name}/xxlogxx.txt");
mkdir("{$dir}{$name}/connect");

chmod($dir.$name, 0777);
chmod("{$dir}{$name}/pass.hash", 0666);
chmod("{$dir}{$name}/xxlogxx.txt", 0666);
chmod("{$dir}{$name}/connect/", 0777);
file_put_contents("{$dir}{$name}/pass.hash", $hash);

header("Location: ../index.php");
