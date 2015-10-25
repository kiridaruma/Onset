<?php
session_start();
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Onset!</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<script type="text/javascript" src="jquery-1.11.3.min.js"></script>
		<script type="text/javascript" src="read.js"></script>
		<meta http-equiv="Pragma" content="no-cache">
		<meta http-equiv="Cache-Control" content="no-cache">
	</head>
	<body>

		<form action="src/write.php" method="post">
			<input type="text" name="name" value="<?php echo $_SESSION['name']; ?>"><br>
			<textarea name="text" rows="5" cols="50"></textarea><br>
			<input type="submit" name="send" value="送信">
		</form>



		<br><hr>
		<script>$(document).ready(function(){get_log();});</script>
		<font size="2">
		<chat><?php echo file_get_contents("log/xxlogxx.txt"); ?></chat>
		</font>

	</body>
</html>
