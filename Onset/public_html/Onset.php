<?php
session_start();

if(!isset($_SESSION['onset_room'])){
    header("Location: index.php");
    die();
}

require_once('src/config.php');
$url = $config['bcdiceURL'];
$s = $config['enableSSL'] ? 's' : '';
$sysList = explode("\n", file_get_contents("http{$s}://{$url}?list=1"));
?>

<!DOCTYPE html>
<html>
<head>
        <meta charset="UTF-8">
        <title>Onset!</title>
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Cache-Control" content="no-cache">

        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/onset.css">

        <script src="js/bootstrap.min.js"></script>
        <script src="js/jquery.js"></script>
        <script src="js/chat_rw.js"></script>
</head>
<body class="container">
    <div class="menu">
        <a href="src/logput.php">ログ出力</a>
        <a href="src/logout.php">ログアウト</a>
        <a onclick="checkLoginUser()">ログイン一覧</a>
    </div>

    <div class="form-group">
        ID:(<?= $_SESSION['onset_id'] ?>)
        <input type="text" class="form-control" id="nick" value=<?= $_SESSION['onset_nick'] ?>>

        <textarea id="text" class="form-control" rows="4" cols="35"></textarea>

        <select id="sys" class="form-control">
        <option value="None" selected>指定なし</option>
            <?php foreach($sysList as $value): ?>
                <option value="<?=$value?>"><?=$value?></option>
            <?php endforeach; ?>
        </select>

        <button type="button" class="form-control send" id="button" value="送信" onclick="send_chat()">送信</button>

   </div>

    <div id="notice"></div>
        <script>$(document).ready(function(){get_log();});</script>
    <div id="chatLog"></div>
</body>
</html>
