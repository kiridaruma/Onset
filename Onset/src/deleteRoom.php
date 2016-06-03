<?php

require_once('config.php');
require_once('core.php');

session_start();

if(!Onset::isValidAccess($_POST['rand'])) {
    echo Onset::errorJson('不正なアクセス');
    die();
}

$room = isset($_POST['room']) && $_POST['room'] != "" ? $_POST['room'] : FALSE;
$pass = isset($_POST['pass']) && $_POST['pass'] != "" ? $_POST['pass'] : FALSE;

if(!$room || !pass){
    echo Onset::errorJson("ルーム名かパスワードがセットされていません");
    die();
}

$roomlist = Onset::getRoomlist();
$roompath = $roomlist[$room]['path'];

if(!isset($roomlist[$room])) {
    echo Onset::errorJson("部屋が存在しません");
    die();
}

$dir = $config['roomSavepath'];

$hash = file_get_contents("{$dir}{$roompath}/pass.hash");
if(!password_verify($pass, $hash) && $config['pass'] != $pass){
    echo Onset::errorJson("パスワードを間違えています");
    die();
}

try{
    foreach(scandir("{$dir}{$roompath}/connect/") as $value){
        if($value != "." || $value != ".."){unlink("{$dir}{$roompath}/connect/{$value}") ? "" : function(){throw new Exception();};}
    }
    rmdir("{$dir}{$roompath}/connect/") ? "" : function(){throw new Exception();};

    foreach(scandir($dir.$roompath) as $value){
        if($value != "." || $value != ".."){unlink("{$dir}{$roompath}/{$value}") ? "" : function(){throw new Exception();};}
    }
    rmdir($dir.$roompath) ? "" : function(){throw new Exception();};

    unset($roomlist[$room]);
    file_put_contents($dir."roomlist", serialize($roomlist)) ? "" : function(){throw new Exception();};
} catch(Exception $e) {
    echo Onset::errorJson("部屋を消せませんでした");
}

header("Location: ../index.php");
