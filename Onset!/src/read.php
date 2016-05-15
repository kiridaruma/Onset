<?php

session_start();

$time = isset($_POST['time']) && $_POST['time'] != NULL ? $_POST['time'] : FALSE;
$room = isset($_SESSION['onset_room']) && $_SESSION['onset_room'] != NULL ? $_SESSION['onset_room'] : FALSE;

if(!$time || !$room){
	echo "不正なアクセス：invalid_access";
	die();
}

require_once('config.php');

$dir = "{$config['roomSavepath']}{$room}";

if($time < filemtime("{$dir}/xxlogxx.txt") * 1000){
	$fp = fopen("{$dir}/xxlogxx.txt", 'r');
	$eof = FALSE;
	while(!$eof){
		$line = fgets($fp);
		if($line !== FALSE){
			echo $line;
		}else{
			$eof = TRUE;
		}
	}
	fclose($fp);
}else{
	echo "none";
}

$tmp = $dir."/connect/".$_SESSION['onset_id'];
file_put_contents($tmp, time()."\n".$_SESSION['onset_name']);

clearstatcache();
