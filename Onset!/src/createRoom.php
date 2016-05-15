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

if(mb_strlen($name) >= $config['maxRoomName']){
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

if(count($roomlist) >= $config["roomLimit"]){
	echo "これ以上部屋を立てられません、制限いっぱいです";
	die();
}

$hash = password_hash($pass, PASSWORD_DEFAULT);
unset($pass);			//念の為、平文のパスワードを削除
try{

	$uuid = uniqid("", true);
	mkdir($dir.$uuid) 									? ""	: function(){throw new Exception();};
	touch("{$dir}{$uuid}/pass.hash")		? "" 	: function(){throw new Exception();};
	touch("{$dir}{$uuid}/xxlogxx.txt")	? ""	: function(){throw new Exception();};
	mkdir("{$dir}{$uuid}/connect") 			? "" 	: function(){throw new Exception();};

	chmod($dir.$uuid, 									0777) ? ""	: function(){throw new Exception();};
	chmod("{$dir}{$uuid}/pass.hash", 		0666) ? ""	: function(){throw new Exception();};
	chmod("{$dir}{$uuid}/xxlogxx.txt", 	0666) ? ""	: function(){throw new Exception();};
	chmod("{$dir}{$uuid}/connect/", 		0777) ? ""	: function(){throw new Exception();};

	file_put_contents("{$dir}{$uuid}/pass.hash", $hash) ? "" : function(){throw new Exception();};

	$roomlist[$name]["path"] = $uuid;

	file_put_contents($dir."roomlist", serialize($roomlist)) ? "" : function(){throw new Exception();};

} catch(Exception $e) {
		echo "部屋を立てられませんでした。";
}

header("Location: ../index.php");
