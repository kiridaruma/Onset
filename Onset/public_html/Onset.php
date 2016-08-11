<?php
session_start();

if(!isset($_SESSION['onset_roomid'])){
    header("Location: index.php");
    die();
}

require_once 'src/config.php';
$url = Config::bcdiceURL;
$s = Config::enableSSL ? 's' : '';
$sysList = explode("\n", file_get_contents("http{$s}://{$url}?list=1"));
?>

<!DOCTYPE html>
<html>
<head>
        <meta charset="UTF-8">
        <title>Onset!</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
        <link rel="stylesheet" href="css/style.css">
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Cache-Control" content="no-cache">
        <script type="text/javascript" src="js/jquery.js"></script>
        <script type="text/javascript" src="js/chat_rw.js"></script>
</head>
<body>
    <header>
        <a href="src/logput.php" class="top-item">ログ出力</a>
        <a href="src/logout.php" class="top-item">ログアウト</a>
        <a class="top-item" onclick="checkLoginUser()">ログイン一覧</a>
    </header>
    <div class="contents">
    <div class="form">
        <input type="text" id="nick" value=<?= $_SESSION['onset_playername'] ?>>(<?= $_SESSION['onset_playerid'] ?>)
        <select id="sys">
        <option value="None" selected>指定なし</option>
<?php foreach($sysList as $value): ?>
    <option value="<?=$value?>"><?=$value?></option>
<?php endforeach; ?>
        </select><br />
        <textarea id="text" rows="3" cols="40"></textarea><br />
        <button type="button" id="button" value="送信" onclick="send_chat()">送信</button>
    </div>

    <div id="onsetNotice" class="notice"></div>
        <script>$(document).ready(function(){get_log();});</script>
    <div class="chats"></div>
    </div>
</body>
</html>
