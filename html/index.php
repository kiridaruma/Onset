<?php

$roomlist = scandir('../room');
      //カレントディレクトリと一つ上のディレクトリを消去
foreach($roomlist as $key => $value){
	if($value == "." || $value == ".."){
		unset($roomlist[$key]);
	}
}

?>

<!DOCTYPE html>
<html>
	<head>
		<title>Onset!</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<script type="text/javascript" src="https://code.jquery.com/jquery-1.11.3.js"></script>
	</head>
	<body><font size="2">
		<form action="src/login.php" method="post">
	       <p>名前:<input type="text" name="name" value="名無し"><br>
	       パス:<input type="password" name="pass"><br>
             <input type="submit" value="入室"></p>
	       <p>部屋一覧<br>
                   <?php
                   if($roomlist[2] == NULL){
                        echo "部屋がありません";
                  }else{
	 		      foreach($roomlist as $value){
	       		       echo "<input type=\"radio\" name=\"room\" value=\"{$value}\">{$value}";
                               echo "<br>";
	 		      }
                  }
	 		?></p></form>
                  <a href="edit.php">部屋の作成/削除</a>
  </body></html>
