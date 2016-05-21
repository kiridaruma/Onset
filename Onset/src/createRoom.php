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

isSetNameAndPass($name, $pass);
isLongRoomName($name);

$name = htmlspecialchars($name, ENT_QUOTES);

if(isExistRoom($roomlist, $name) === true) {
	echo "同名の部屋がすでに存在しています(ブラウザバックをおねがいします)";
	die();
}

if(count($roomlist) >= $config["roomLimit"]){
	echo "これ以上部屋を立てられません、制限いっぱいです。";
	die();
}

$hash =	[
	"roomName" => $_POST['name'],
	"roomPassword" => password_hash($pass, PASSWORD_DEFAULT)
];

$roomJSON = json_encode($hash);

$roomJSONpw = password_hash($pass, PASSWORD_DEFAULT);
unset($pass);			//念の為、平文のパスワードを削除

try{

	$uuid = uniqid("", true);
	mkdir($dir.$uuid);

	// pass.hash
	// レガシなroomInfo.json
	touch($dir.$uuid.'/pass.hash');

	// roomInfo.json
	// 部屋データの管理
	touch($dir.$uuid.'/roomInfo.json');

	// chatLogs.json
	// チャットデータの管理
	touch($dir.$uuid.'/chatLogs.json');
	mkdir($dir.$uuid.'/connect');

	// 'chmod b111000000\n'
	// - Ar tonelico
	chmod($dir.$uuid, 									0777);
	chmod($dir.$uuid.'/pass.hash', 			0666);
	chmod($dir.$uuid.'/chatLogs.json',	0666);
	chmod($dir.$uuid.'/roomInfo.json', 	0666);
	chmod($dir.$uuid.'/connect/',		 		0777);

	file_put_contents($dir.$uuid.'/roomInfo.json', $roomJSON);
	file_put_contents($dir.$uuid.'/pass.hash',		 $roomJSONpw);

	$roomlist[$name]["path"] = $uuid;

	file_put_contents($dir."roomlist", serialize($roomlist)) ? "" : function(){throw new Exception('Failed to put contents to "roomlist".');};

} catch(Exception $e) {
		echo "Exception: ".$e;
}

header("Location: ../index.php");
