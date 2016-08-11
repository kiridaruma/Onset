<?php
require_once('src/core.php');

$dir = config::roomSavepath;
$roomList = [];
foreach(Onset::getRoomlist() as $room => $data){
    if(time() - filemtime($dir.$data['path']) > config::roomDelTime) continue;
    $roomList[$room] = $data;
}

session_start();
$_SESSION['onset_rand'] = $rand = mt_rand();

$welcomeMessage = file_get_contents('welcomeMessage.html');

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>Onset!</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script type="text/javascript" src="js/jquery.js"></script>
    <script type="text/javascript" src="js/script.js"></script>
    <script>rand = <?= $rand ?>;</script>
    <link rel="stylesheet" href="css/top.css">
</head>
<body>
<div class="contents">
    <div class="header">
        <h1>Onset!</h1>
        <article><?=$welcomeMessage?></article>
    </div>

    <hr />

    <div class="join">
        <a id="toggle" onclick="toggle()">部屋の作成/削除</a>
        <form class="form" id="enter">
            <div id="input">
                <input class="text" type="text" id="nick" placeholder="名前"><br />
                <input class="text" type="password" id="pass" placeholder="パスワード"><br />
                <input class="button" type="button" value="入室" onclick="enterRoom()"><br />
                <div id="enterNotice" class="notice"></div>
            </div>

            <div class="list">
                <p>部屋一覧</p>
                <?php foreach($roomList as $key => $value) : ?>
                    <label class="room">
                        <input type="radio" name="room" value="<?=$key?>"><?=$key?>
                    </label>
                <?php endforeach; ?>
            </div>
        </form>
    </div>

    <div class="edit">

        <a onclick="toggle()" id="toggle">閉じる</a>

        <h2>作成</h2>

        <form id="create">
            <input type="text" class="text" id="room" placeholder="部屋名"><br />
            <input type="password" class="text" id="pass" placeholder="パスワード"><br />
            <input type="hidden" name="rand" value="<?=$rand?>">
            <input type="button" class="button" value="作成" onclick="createRoom()"><br />
            <span id="createNotice" class="notice"></span>
        </form>

        <h2>削除</h2>

        <form id="remove">
            <input type="password" class="text" id="pass" placeholder="パスワード"><br />
            <input type="hidden" name="rand" value="<?=$rand?>">
            <input type="button" class="button" value="削除" onclick="removeRoom()"><br />
            <span id="removeNotice" class="notice"></span>
        <div class="list">
                <p>部屋一覧</p>
                <?php foreach($roomList as $key => $value) : ?>
                    <label class="room">
                        <input type="radio" name="room" value="<?=$key?>"><?=$key?>
                    </label>
                <?php endforeach; ?>
            </div>
        </form>
    </div>
</div>
</body>
</html>
