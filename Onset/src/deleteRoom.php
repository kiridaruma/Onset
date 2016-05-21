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

foreach($roomlist as $k) {
	if($k['roomName'] === $room) {
		$roomID = $k['roomID'];
	}
}

if(isExistRoom($roomlist, $room) === false) {
	echo "部屋が存在しません(ブラウザバックをおねがいします)";
	die();
}

$json = file_get_contents($dir.$roomID.'/roomInfo.json');
$json = json_decode($json, true);

$hash = $json['roomPassword'];

isCorrectPassword($pass, $hash);

try{
	foreach(scandir("{$dir}{$roomID}/connect/") as $value) {
		if($value != "." || $value != "..") {
			unlink("{$dir}{$roomID}/connect/{$value}") ? "" : function() { throw new Exception('Failed to unlink "/connect".'); };
		}
	}
	rmdir("{$dir}{$roomID}/connect/") ? "" : function(){throw new Exception('Failed to delete "/connect".');};

	foreach(scandir($dir.$roomID) as $value) {
		if($value != "." || $value != "..") {
			unlink("{$dir}{$roomID}/{$value}") ? "" : function(){throw new Exception('Failed to unlink "." or "..".');};
		}
	}
	rmdir($dir.$roomID) ? "" : function(){throw new Exception();};

	foreach($roomlist as $k) {
		if($k['roomID'] = $roomID) unset($k);
	}

	file_put_contents($dir."/roomLists.json", $roomlist) ? "" : function(){throw new Exception('Failed to put contents to "roomlist".');};

	header("Location: ../index.php");
} catch(Exception $e) {
	echo "Exception: ".$e;
}

