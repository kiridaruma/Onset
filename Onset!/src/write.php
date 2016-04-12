<?php
session_start();

$name = isset($_POST['name']) && $_POST['name'] != NULL ? trim(htmlspecialchars($_POST['name'] , ENT_QUOTES)) : FALSE;
$text = isset($_POST['text']) && $_POST['text'] != NULL ? trim(htmlspecialchars($_POST['text'] , ENT_QUOTES)) : FALSE;
$room = isset($_SESSION['onset_room']) && $_SESSION['onset_room'] != NULL ? $_SESSION['onset_room'] : FALSE;

if(!$text || !$name || !$room){
    echo "不正なアクセス:invalid_access";
    die();
}

$dir = "../room/{$room}/";

if(mb_strlen($text) > 300 || mb_strlen($name) > 20){	//チャット本文は300字､名前は20字で制限
    echo "文字数が多すぎます";
    die();
}

//var_dump($_POST);
//var_dump($name);
//var_dump($text);

$text = nl2br($text);

//ダイス処理
$diceRes = "";
foreach(scandir("dice") as $value){
    if($value == '.' || $value == '..'){continue;}
    require_once("dice/".$value);
    $funcname = str_replace(".php", "", $value);
    $res = $funcname($text);
    if($res === false){continue;}
    $diceRes = $res;
}


//var_dump($name);
//var_dump($text);

$line = "<div class=\"chat\"><b>{$name}</b>({$_SESSION['onset_id']})<br>\n{$text}<br>\n<i>{$diceRes}</i></div>\n";

//var_dump($line);


$line = $line.file_get_contents("{$dir}xxlogxx.txt");
file_put_contents("{$dir}xxlogxx.txt", $line, LOCK_EX);
$_SESSION['onset_name'] = $name;

?>
