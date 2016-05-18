<?php
session_start();

if(!isset($_SESSION['onset_room'])){
	header("Location: index.php");
	die();
}

require_once('src/config.php');
$url = $config['bcdiceURL'];
$s = '';
if($config['enableSSL']) $s = 's';
$sysList = split("\n", file_get_contents("http{$s}://{$url}?list=1"));
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
	<title>Onset!</title>
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="http://bootswatch.com/superhero/bootstrap.min.css">
	<link rel="stylesheet" href="css/local.css">
	<meta http-equiv="Pragma" content="no-cache">
	<meta http-equiv="Cache-Control" content="no-cache">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
</head>
<body>
	<header>
		<nav class="navbar navbar-default">
			<div class="container-fluid">
			<!-- Brand and toggle get grouped for better mobile display -->
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="#">Onset</a>
				</div>

			<!-- Collect the nav links, forms, and other content for toggling -->
				<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					<ul class="nav navbar-nav">
						<li><a href="src/logput.php">ログ出力</a></li>
						<li><a href="" onclick="checkLoginUser()">ログイン一覧</a></li>
					</ul>
					<ul class="nav navbar-nav navbar-right">
						<li><a href="">ID <?=$_SESSION['onset_id']?></a></li>
						<li><a href="src/logout.php">ログアウト</a></li>
					</ul>
				</div><!-- /.navbar-collapse -->
			</div><!-- /.container-fluid -->
		</nav>
	</header>

	<div class="container">
		<div class="form-inline">
			<input class="form-control" type="text" id="name" value=<?=$_SESSION['onset_name']?>>
			<select class="form-control" id="sys">
				<option value="None" selected>指定なし</option>
<?php foreach($sysList as $value) : ?>
				<option value="<?=$value?>"><?=$value?></option>
<?php endforeach; ?>
			</select>
			<textarea class="form-control" id="text" rows="1" placeholder="テキスト"></textarea>
			<button class="btn btn-info" type="button" id="button" value="送信" onclick="send_chat()">送信</button>
		</div>

		<div class="notice"></div>
			<script>$(document).ready(function(){get_log();});</script>
		<div class="chats"></div>
	</div>

	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
	<script type="text/javascript" src="js/chat_rw.js"></script>
</body>
</html>
