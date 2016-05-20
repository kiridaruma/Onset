<?php
require_once('src/config.php');
require_once('src/core.php');

session_start();
$_SESSION['onset_rand'] = $rand = mt_rand();

$welcomeMessage = file_get_contents('welcomeMessage.html');
?>

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/local.css">
	<script type="text/javascript" src="js/jquery.js"></script>
	<script type="text/javascript" src="js/script.js"></script>
	<title>Onset.</title>
</head>
<body>
	<div class="container col-md-6 col-md-offset-3 text-center">
		<header class="header"><br />
			<div class="jumbotron">
				<h1>Onset!</h1>
				<p>Onset is one of TRPG environment.</p>
			</div>
		</header>
		<article class="join">
			<div class="panel panel-info join">
			<div class="panel-heading" onclick="toggleJoin()">入室する</div>
			<div class="panel-body" id="join">
				<form class="form" action="src/login.php" method="post">
					<div class="form-inline form-group">
						<input class="form-control" type="text" name="name" placeholder="名前">
						<input class="form-control" type="password" name="pass" placeholder="パスワード">
						<select id="room" class="form-control" name="room">
<?php foreach($roomlist as $key => $value) : ?>
							<option value="<?=$key?>"><?=$key?></option>
<?php endforeach; ?>
						</select>
					</div>
					<div class="form-group">
						<button class="btn btn-info" type="submit">部屋に入る!</button>
					</div>
				</form>
			</div>
			</div>
		</article>
		<hr />
		<article class="edit">
			<div class="panel panel-success create">
				<div class="panel-heading" onclick="toggleCreate()">部屋の作成</div>
				<div class="panel-body" id="create">
					<form class="form" action="src/createRoom.php" method="post">
						<div class="form-inline form-group">
							<input class="form-control" type="text" name="name" placeholder="部屋名">
							<input class="form-control" type="password" name="pass" placeholder="パスワード">
							<input class="form-control" type="hidden" name="rand" value="<?=$rand?>">
							<input class="form-control" type="hidden" name="mode" value="create">
						</div>
						<div class="form-group">
							<button class="btn btn-success" type="submit">部屋を作る!</button>
						</div>
					</form>
				</div>
			</div>
		</article>
		<hr />
		<article class="delete">
			<div class="panel panel-danger delete">
				<div class="panel-heading" onclick="toggleDelete()">部屋の削除</div>
				<div class="panel-body" id="delete">
					<form class="form" action="src/deleteRoom.php" method="post">
						<div class="form-group form-inline">
							<select id="room" class="form-control" name="room">
<?php foreach($roomlist as $key => $value) : ?>
							<option value="<?=$key?>"><?=$key?></option>
<?php endforeach; ?>
							</select>
							<input class="form-control" type="password" name="pass" placeholder="パスワード">
							<input class="form-control" type="hidden" name="rand" value="<?=$rand?>">
							<input class="form-control" type="hidden" name="mode" value="del">
					</div>
					<div class="form-group">
						<button class="btn btn-danger" type="submit">部屋を削除する</button>
					</div>
					</form>
				</div>
			</div>
		</article>

	</div>
	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
</body>
</html>
