<?php

$roomlist = scandir('../room');

for($i = 0; $i < count($roomlist); $i++){
      if($roomlist[$i] == "." || $roomlist[$i] == ".."){
            unset($roomlist[$i]);
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
	       <p>名前:<input type="text" name="name"><br>
	       パス:<input type="password" name="pass"></p>

	       <p><?php

	 		echo "部屋一覧<br>";
			$count = 1;
	 		foreach($roomlist as $value){
	       		echo "<input type=\"radio\" name=\"room\" value=\"{$value}\">{$value}";
				if($count % 3 == 0){
					echo "<br>";
				}
	 		}

	 		?>
	  <br><input type="submit" value="入室"></form></p>
	  <br><br><br>
	  <p>部屋の作成/削除<br>
	  <form action="src/roomedit.php" method="post">
		  <input type="radio" name="mode" value="create">作成<br>
		  <input type="radio" name="mode" value="del">削除<br>
		  
		  <input>
	  </form></p></font>
  </body></html>
