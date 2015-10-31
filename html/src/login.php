<?php

$name = isset($_POST['name']) || $_POST['name'] != 0 ? htmlspecialchars($_POST['name'], ENT_QUOTES) : FALSE;
$pass = isset($_POST['pass']) || $_POST['pass'] != 0 ? $_POST['pass'] : FALSE;
$room = isset($_POST['room']) || $_POST['room'] != 0 ? $_POST['room'] : FALSE;

if(!$name || !$pass || !$room){
      header("Location: ../index.php");
      die();
}

$room = str_replace("/", "／", $room);

if(!file_exists("../../room/{$room}")){
      header("Location: ../index.php");
      die();
}

$hash = file_get_contents("../../room/{$room}/pass.hash");

if(!password_verify($pass, $hash)){
      header("Location: ../index.php");
      die();
}

session_start();
$_SESSION['onset_name'] = $name;
$_SESSION['onset_key'] = file_get_contents("../../room/key.txt");
$_SESSION['onset_room'] = $room;
header("Location: ../Onset.php");
