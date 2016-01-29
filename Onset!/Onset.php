<?php
session_start();

if(!isset($_SESSION['onset_room'])){
	header("Location: index.php");
	die();
}

?>

<!DOCTYPE html>
<html>
<head>
	<title><?= $_SESSION['onset_room'] ?>/Onset!</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<script type="text/javascript" src="https://code.jquery.com/jquery-1.11.3.js"></script>
	<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.5.0/pure-min.css">
	<link rel="stylesheet" href="css.css">
	<script type="text/javascript" src="chat_rw.js"></script>
	<meta http-equiv="Pragma" content="no-cache">
	<meta http-equiv="Cache-Control" content="no-cache">
</head>
<body><font size="2">

	<div class="pure-g top">
		<div class="pure-u-2-24"></div>
		<div class="pure-u-20-24">
			<a href="src/logput.php">ログ出力</a>
			<a href="src/logout.php">ログアウト</a>
		</div>
		<div class="pure-u-2-24"></div>
	</div>

	<div class="pure-g">
		<div class="pure-u-1-24"></div>
		<div class="pure-u-22-24">

			<p><form action="src/write.php" method="post">
				<input type="text" id="name" value=<?= $_SESSION['onset_name'] ?>>(<?= $_SESSION['onset_id'] ?>)<br>
				<textarea id="text" rows="4" cols="45"></textarea><br>
				<input type="button" value="送信" onclick="send_chat()" class="pure-button">
			</form></p>

			<err></err>

			<br><hr>
			<script>$(document).ready(function(){get_log();});</script>
			<font size="2">
				<chat><?php echo file_get_contents("room/{$_SESSION['onset_room']}/xxlogxx.txt"); ?></chat>

			</font>

		</div><div class="pure-u-1-24"></div></div>

	</body>
	</html>
