<?php
session_start();

if(!isset($_SESSION['onset_room'])){
    header("Location: index.php");
    die();
}

require_once('src/config.php');
$url = $config['bcdiceURL'];
$s = '';
if($config['enableSSL']){$s = 's';}
$sysList = split("\n", file_get_contents("http{$s}://{$url}?list=1"));
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Onset!</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <script type="text/javascript" src="js/jquery.js"></script>
    <link rel="stylesheet" href="css.css">
    <script type="text/javascript" src="js/chat_rw.js"></script>
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Cache-Control" content="no-cache">
</head>
<body>

    <div class="top">
        <a href="src/logput.php" class="top-item">ログ出力</a>
        <a href="src/logout.php" class="top-item">ログアウト</a>
        <a class="top-item" onclick="checkLoginUser()">ログイン一覧</a>
    </div>

    <div class="form">
        <input type="text" id="name" value=<?= $_SESSION['onset_name'] ?>>(<?= $_SESSION['onset_id'] ?>)
        <select id="sys">
            <option value="None" selected>指定なし</option>
            <?php
            foreach($sysList as $value) {
                echo "<option value=\"{$value}\">{$value}</option>";
            }
            ?>
        </select>
        <br>
        <textarea id="text" rows="4"></textarea><br>
        <input type="button" id="button" value="送信" onclick="send_chat()">
    </div>

    <div class="notice"></div>

    <script>$(document).ready(function(){get_log();});</script>
    <div class="chats"></div>

</body>
</html>
