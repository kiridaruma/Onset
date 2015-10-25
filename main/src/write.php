<?
session_start();
require 'func.php';

	//issetで$_POSTからtrimしてhtmlspecialcharsを通して値を取り出す
$name = $_POST['name'] != NULL ? trim(htmlspecialchars($_POST['name'] , ENT_QUOTES)) : NULL;
$text = $_POST['text'] != NULL ? trim(htmlspecialchars($_POST['text'] , ENT_QUOTES)) : NULL;

if($text === NULL || $name === NULL){	//テキストが空白ならエラーを返す
	$_SESSION['name'] = "名前を入れて！";
	header("Location: ../index.php");
	die();
}

//var_dump($_POST);
//var_dump($name);
//var_dump($text);

	//改行を<br>に
$name = nl2br($name);
$text = nl2br($text);

$dice = dice($text);
if($dice === FALSE){
	$dice_roll = "";
}else{
	$dice_roll = "<b>".$dice["text"]."\n→".$dice["num"]."</b>";
}


//var_dump($name);
//var_dump($text);

	//$lineへ一時格納
$line = date("Y/m/d G:i:s")."&#009;".$name."<br>".$text."<br>".$dice_roll."<hr><br>\n";

//var_dump($line);


$line = $line.file_get_contents("../log/xxlogxx.txt");
file_put_contents("../log/xxlogxx.txt", $line);
$_SESSION['name'] = $name;
header("Location: ../index.php");



?>
