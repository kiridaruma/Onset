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

$name = htmlspecialchars($name, ENT_QUOTES);

$roomlist = unserialize(file_get_contents($dir."roomlist"));

if(isset($roomlist[$name])){
    echo "同名の部屋がすでに存在しています(ブラウザバックをおねがいします)";
    die();
}

$uuid = uniqid("", true);
$roomlist[$name]["path"] = $uuid;
file_put_contents($dir."roomlist", serialize($roomlist));

$hash = password_hash($pass, PASSWORD_DEFAULT);
unset($pass);     //念の為、平文のパスワードを削除

mkdir($dir.$uuid);
touch("{$dir}{$uuid}/pass.hash");
touch("{$dir}{$uuid}/xxlogxx.txt");
mkdir("{$dir}{$uuid}/connect");

chmod($dir.$uuid, 0777);
chmod("{$dir}{$uuid}/pass.hash", 0666);
chmod("{$dir}{$uuid}/xxlogxx.txt", 0666);
chmod("{$dir}{$uuid}/connect/", 0777);
file_put_contents("{$dir}{$uuid}/pass.hash", $hash);

header("Location: ../index.php");
