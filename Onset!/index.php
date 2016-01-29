<?php

$roomlist = scandir('room');
//カレントディレクトリと一つ上のディレクトリとhtaccessを消去
foreach($roomlist as $key => $value){
	if($value == "." || $value == ".." || $value == ".htaccess"){
		unset($roomlist[$key]);
	}
}

session_start();
$_SESSION['onset_rand'] = $rand = mt_rand();

?>

<!DOCTYPE html>
<html>

<head>
	<title>Onset!</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<script type="text/javascript" src="https://code.jquery.com/jquery-1.11.3.js"></script>
	<script type="text/javascript" src="script.js"></script>
	<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.5.0/pure-min.css">
	<link rel="stylesheet" href="css.css">
</head>

<body>

	<div class="pure-g">
		<div class="pure-u-1-24"></div>
		<div class="pure-u-22-24">

			<div id="header"><h1>Onset!</h1>
			PC/携帯問わず､気軽にできる軽量TRPG向けチャット</div><br>

			<div id="main">
			<form action="src/login.php" method="post" class="pure-form-stacked">

				<p>名前<input type="text" name="name" placeholder="名前"><br>
					パスワード<input type="password" name="pass" placeholder="パスワード"><br>
					<input type="submit" value="入室" class="pure-button"></p>

					<p><table class="pure-tabels">
						<th>部屋一覧</th>
						<?php
						foreach($roomlist as $value){
							echo "<tr><td>";
							echo "<input type=\"radio\" name=\"room\" value=\"{$value}\">{$value}";
							echo "</td></tr>";
						}
						?>
					</table></p>
			</form>
			</div>

			<a onclick="toggle()">部屋の作成/削除</a>

				<div id="edit"><h3>作成</h3><br>

					<form action="src/roomedit.php" method="post" class="pure-form-stacked">
						ルーム名<input type="text" name="name"><br>
						パスワード<input type="password" name="pass"><br>

						<input type="hidden" name="rand" value="<?= $rand ?>">
						<input type="hidden" name="mode" value="create">

						<input type="submit" value="作成" class="pure-button">
					</form>
					<br>

					<h3>削除</h3>

						<form action="src/roomedit.php" method="post" class="pure-form-stacked">
							<p><table class="pure-tabels">
								<th>部屋一覧</th>

								<?php
								foreach($roomlist as $value){
									echo "<tr><td>";
									echo "<input type=\"radio\" name=\"name\" value=\"{$value}\">{$value}";
									echo "</td></tr>";
								}
								?></table>
							</p>

							部屋のパスワード<input type="password" name="pass"><br>

							<input type="hidden" name="rand" value="<?= $rand ?>">
							<input type="hidden" name="mode" value="del">

							<input type="submit" value="削除" class="pure-button">

						</form></div>

						<p><a href="help.php">Onset!ってなに?(ヘルプ)</a></p>

					</div><div class="pure-u-1-24"></div></div>

				</body></html>
