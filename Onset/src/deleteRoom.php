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

if(isExistRoom($roomlist, $room) === false) {
	echo "部屋が存在しません(ブラウザバックをおねがいします)";
	die();
}

// $roomID   : 部屋のUUID
// $roomName : 部屋の名前
foreach($roomlist as $k) {
	if($k['roomName'] === $room) {
		$roomID   = $k['roomID'];
		$roomName = $k['roomName'];
	}
}

$json = json_decode(file_get_contents($dir.$roomID.'/roomInfo.json'), true);
$hash = $json['roomPassword'];

// PW一致の確認。
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

	unset($roomlist[$roomID]);
	$roomlist = json_encode($roomlist);

	file_put_contents($dir."/roomLists.json", $roomlist) ? "" : function(){throw new Exception('Failed to put contents to "roomlist".');};

	header("Location: ../index.php");

} catch(Exception $e) {
	echo "Exception: ".$e;
}

