<?php

require_once('config.php');
require_once('core.php');

session_start();

if(isIllegalAccess($_POST['rand'], $_SESSION['onset_rand']) === false) {
	echo 'Illegal Access: invalid_access.';
	die();
}

$name = isset($_POST['name']) && $_POST['name'] != "" ? $_POST['name'] : FALSE;
$pass = isset($_POST['pass']) && $_POST['pass'] != "" ? $_POST['pass'] : FALSE;
$mode = $_POST['mode'];

var_dump($name);
var_dump($pass);

if(!$name || !$pass){
	echo "部屋名とパスワードを入力してください";
	die();
}

if(mb_strlen($name) > 30){
	echo "部屋名が長過ぎます";
	die();
}

$roomlist = unserial($dir);

$roompath = $roomlist[$name]['path'];

if(isExistRoom($roomlist, $name)){
	echo "部屋が存在しません(ブラウザバックをおねがいします)";
	die();
}

$hash = file_get_contents("{$dir}{$roompath}/pass.hash");
if(!password_verify($pass, $hash) && $config['pass'] != $pass){
	echo "パスワードを間違えています(ブラウザバックをおねがいします)";
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

	unset($roomlist[$name]);
	file_put_contents($dir."roomlist", serialize($roomlist)) ? "" : function(){throw new Exception();};
} catch(Exception $e) {
	echo "部屋を消せませんでした";
}

header("Location: ../index.php");
