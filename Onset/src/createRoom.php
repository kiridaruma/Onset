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

$hash = password_hash($pass, PASSWORD_DEFAULT);
unset($pass);			//念の為、平文のパスワードを削除

try{

	$uuid = uniqid("", true);
	mkdir($dir.$uuid) 									? ""	: function(){throw new Exception('Failed to make directory.');};
	touch("{$dir}{$uuid}/pass.hash")		? "" 	: function(){throw new Exception('Failed to make password hash.');};
	touch("{$dir}{$uuid}/xxlogxx.txt")	? ""	: function(){throw new Exception('Failed to make xxlogxx.txt.');};
	mkdir("{$dir}{$uuid}/connect") 			? "" 	: function(){throw new Exception('Failed to make directory "connect".');};

	chmod($dir.$uuid, 									0777) ? ""	: function(){throw new Exception('Failed to change permission directory.');};
	chmod("{$dir}{$uuid}/pass.hash", 		0666) ? ""	: function(){throw new Exception('Failed to change permission "pass.hash".');};
	chmod("{$dir}{$uuid}/xxlogxx.txt", 	0666) ? ""	: function(){throw new Exception('Failed to change permission "xxlogxx.txt"/');};
	chmod("{$dir}{$uuid}/connect/", 		0777) ? ""	: function(){throw new Exception('Failed to change permission "/connect".');};

	file_put_contents("{$dir}{$uuid}/pass.hash", $hash) ? "" : function(){throw new Exception('Failed to put contents to "pass.hash".');};

	$roomlist[$name]["path"] = $uuid;

	file_put_contents($dir."roomlist", serialize($roomlist)) ? "" : function(){throw new Exception('Failed to put contents to "roomlist".');};

} catch(Exception $e) {
		echo "Exception: ".$e;
}

header("Location: ../index.php");
