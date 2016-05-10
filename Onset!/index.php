<?php

require_once('src/config.php');

$dir = $config['roomSavepath'];
$roomlist = unserialize(file_get_contents($dir."roomlist"));
//カレントディレクトリと一つ上のディレクトリとhtaccessを消去

session_start();
$_SESSION['onset_rand'] = $rand = mt_rand();

$welcomeMesseage = file_get_contents('welcomeMesseage.txt');

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Onset!</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script type="text/javascript" src="js/jquery.js"></script>
    <script type="text/javascript" src="js/script.js"></script>
    <link rel="stylesheet" href="top.css">
</head>

<body>

    <div id="header">
        <h1>Onset!</h1>
        <?= $welcomeMesseage ?>
    </div>

    <p><a href="help.html">Onset!ヘルプページ</a></p>

    <form action="src/login.php" method="post" class="form">
        <div id="input">
            <input type="text" class="text" name="name" placeholder="名前"><br>
            <input type="password" class="text" name="pass" placeholder="パスワード"><br>
            <input type="submit" class="button" value="入室" class="pure-button">
        </div>

        <a onclick="toggle()">部屋の作成/削除</a>

        <div class="list">
            部屋一覧<br>
            <?php
            foreach($roomlist as $key => $value){
                echo "<label class=\"room\">";
                echo "<input type=\"radio\" name=\"room\" value=\"{$key}\">{$key}";
                echo "</label>";
            }
            ?>
        </div>
    </form>

    <div id="edit">
        
        <a onclick="toggle()">入室画面に戻る</a>
        
        <h2>作成</h2>

        <form action="src/createRoom.php" method="post">
            <input type="text" class="text" name="name" placeholder="部屋名"><br>
            <input type="password" class="text" name="pass" placeholder="パスワード"><br>

            <input type="hidden" name="rand" value="<?= $rand ?>">
            <input type="hidden" name="mode" value="create">

            <input type="submit" class="button" value="作成" class="pure-button">
        </form>
        
        <hr style="margin: 2em;">

        <h2>削除</h2>

        <form action="src/deleteRoom.php" method="post">

            <input type="password" class="text" name="pass" placeholder="パスワード"><br>
            <input type="hidden" name="rand" value="<?= $rand ?>">
            <input type="hidden" name="mode" value="del">
            <input type="submit" class="button" value="削除">

            <div class="list">
                部屋一覧<br>
                <?php
                foreach($roomlist as $key => $value){
                    echo "<label class=\"room\">";
                    echo "<input type=\"radio\" name=\"name\" value=\"{$key}\">{$key}";
                    echo "</label>";
                }
                ?>
            </div>
        </form>
    </div>

</body></html>
