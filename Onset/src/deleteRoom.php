<?php

require_once('config.php');
require_once('core.php');

session_start();

if(isIllegalAccess($_POST['rand'], $_SESSION['onset_rand']) === false) {
	echo 'Illegal Access: invalid_access.';
	die();
}

$room = isset($_POST['room']) && $_POST['room'] != "" ? $_POST['room'] : FALSE;
$pass = isset($_POST['pass']) && $_POST['pass'] != "" ? $_POST['pass'] : FALSE;
$mode = $_POST['mode'];

isSetNameAndPass($room, $pass);

$roompath = $roomlist[$room]['path'];

if(isExistRoom($roomlist, $room) === false) {
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

	unset($roomlist[$room]);
	file_put_contents($dir."roomlist", serialize($roomlist)) ? "" : function(){throw new Exception();};
} catch(Exception $e) {
	echo "部屋を消せませんでした";
}

header("Location: ../index.php");
