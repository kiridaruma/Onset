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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <script type="text/javascript" src="https://code.jquery.com/jquery-1.11.3.js"></script>
    <link rel="stylesheet" href="css.css">
    <script type="text/javascript" src="chat_rw.js"></script>
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
        <input type="text" id="name" value=<?= $_SESSION['onset_name'] ?>>(<?= $_SESSION['onset_id'] ?>)<br>
        <textarea id="text" rows="4"></textarea><br>
        <input type="button" id="button" value="送信" onclick="send_chat()">
    </div>

    <div class="notice"></div>

    <script>$(document).ready(function(){get_log();});</script>
    <div class="chats"></div>

</body>
</html>
