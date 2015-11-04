<?php
session_start();
require 'roll_class.php';


$name = isset($_POST['name']) && $_POST['name'] != NULL ? trim(htmlspecialchars($_POST['name'] , ENT_QUOTES)) : NULL;
$text = isset($_POST['text']) && $_POST['text'] != NULL ? trim(htmlspecialchars($_POST['text'] , ENT_QUOTES)) : NULL;
$room = $_POST['room'];
$key = $_POST['key'];

if($text === NULL || $name === NULL){
	die();
}

$dir = "../room/{$room}/";
if($key != file_get_contents("{$dir}/key.txt")){
	die();
}

if(mb_strlen($text) > 300 || mb_strlen($name) > 20){	//チャット本文は300字､名前は20字で制限
	die();
}

//var_dump($_POST);
//var_dump($name);
//var_dump($text);

$text = nl2br($text);

	//ダイス処理
$roll = new Roll($text);

//var_dump($name);
//var_dump($text);

$line = date("Y/m/d G:i:s")."&#009;<b>{$name}</b><br>\n{$roll->text()}<br><b>{$roll->result()}</b><hr>\n";

//var_dump($line);


$line = $line.file_get_contents("{$dir}xxlogxx.txt");
file_put_contents("{$dir}xxlogxx.txt", $line);

?>
