<?php

require_once('config.php');

session_start();

$time = isset($_POST['time']) 					&& $_POST['time'] 					!= NULL ? $_POST['time'] 					: false;
$room = isset($_SESSION['onset_room']) 	&& $_SESSION['onset_room'] 	!= NULL ? $_SESSION['onset_room']	: false;

if(!$time || !$room){
	echo "Invalid Access: Time OR Room variables is null.";
	die();
}

$dir = $dir.$room;

if($time < filemtime($dir."/xxlogxx.txt") * 1000) {
	$fp = fopen($dir."/xxlogxx.txt", 'r');
	$eof = false;

	while(!$eof){
		$line = fgets($fp);
		if($line !== false) {
			echo $line;
		} else {
			$eof = true;
		}
	}

	fclose($fp);
} else {
	echo "none";
}

$tmp = $dir."/connect/".$_SESSION['onset_id'];
file_put_contents($tmp, time()."\n".$_SESSION['onset_name']);

clearstatcache();
