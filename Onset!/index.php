<?php

$roomlist = scandir('room');
      //カレントディレクトリと一つ上のディレクトリとhtaccessを消去
foreach($roomlist as $key => $value){
	if($value == "." || $value == ".." || $value == ".htaccess"){
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
		<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.5.0/pure-min.css">
		<link rel="stylesheet" href="css.css">
	</head>

	<body>

		<div class="pure-g">
		<div class="pure-u-1-24"></div>
		<div class="pure-u-23-24">

		<p><h1>Onset!</h1><br></p>

		<form action="src/login.php" method="post" class="pure-form-stacked">

	       <p>名前<input type="text" name="name" value="名無し"><br>
	       パスワード<input type="password" name="pass"><br>
             <input type="submit" value="入室" class="pure-button"></p>

	      <p><ul class="pure-menu-list">
		<li class="pure-menu-heading">部屋一覧</li><br>

                   <?php
	 		foreach($roomlist as $value){
				echo "<li class=\"pure-menu-item\">";
	       	      echo "<input type=\"radio\" name=\"room\" value=\"{$value}\">{$value}";
                        echo "</li>";
	 		}
	 		?></ul>
			</p>
		</form>

		<p><a href="edit.php">部屋の作成/削除</a></p>
		<br><br>
		<p><a href= "https://github.com/kiridaruma/Onset">ソース</a></p>

	</div></div>

  </body></html>
