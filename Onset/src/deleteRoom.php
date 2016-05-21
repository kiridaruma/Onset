<?php

require_once('config.php');
require_once('core.php');

session_start();

if(isIllegalAccess($_POST['rand'], $_SESSION['onset_rand']) === false) {
	echo 'Exception: invalid_access.';
	die();
}

$room = isset($_POST['room']) && $_POST['room'] != "" ? $_POST['room'] : FALSE;
$pass = isset($_POST['pass']) && $_POST['pass'] != "" ? $_POST['pass'] : FALSE;
$mode = $_POST['mode'];

isSetNameAndPass($room, $pass);
isLongRoomName($room);

$roompath = $roomlist[$room]['path'];

if(isExistRoom($roomlist, $room) === false) {
	echo "部屋が存在しません(ブラウザバックをおねがいします)";
	die();
}

$json = file_get_contents($dir.$roompath.'/roomInfo.json');
$json = json_decode($json, true);

$hash = $json['roomPassword'];

isCorrectPassword($pass, $hash);

try{
	foreach(scandir("{$dir}{$roompath}/connect/") as $value) {
		if($value != "." || $value != "..") {
			unlink("{$dir}{$roompath}/connect/{$value}") ? "" : function() { throw new Exception('Failed to unlink "/connect".'); };
		}
	}
	rmdir("{$dir}{$roompath}/connect/") ? "" : function(){throw new Exception('Failed to delete "/connect".');};

	foreach(scandir($dir.$roompath) as $value) {
		if($value != "." || $value != "..") {
			unlink("{$dir}{$roompath}/{$value}") ? "" : function(){throw new Exception('Failed to unlink "." or "..".');};
		}
	}
	rmdir($dir.$roompath) ? "" : function(){throw new Exception();};

	unset($roomlist[$room]);
	file_put_contents($dir."roomlist", serialize($roomlist)) ? "" : function(){throw new Exception('Failed to put contents to "roomlist".');};

} catch(Exception $e) {
	echo "Exception: ".$e;
}

header("Location: ../index.php");
