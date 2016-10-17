<?php
require_once('src/config.php');
require_once('src/core.php');

$dir = $config['roomSavepath'];
$roomlist = [];
foreach(Onset::getRoomlist() as $room => $data){
    if(time() - filemtime($dir.$data['path']) > $config['roomDelTime']) continue;
    $roomlist[$room] = $data;
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Cache-Control" content="no-cache">
    
    <script src="js/jquery.js"></script>
    <script src="js/script.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>rand = <?= $rand ?>;</script>

    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/onset.css">
</head>
<body class="container text-center">
    <div class="header">
        <h1>Onset!</h1>
        <article><?=$welcomeMessage?></article>
    </div>

    <div class="login">
        <a id="toggle" onclick="toggle()">部屋の作成/削除</a>
        <form id="enter">
            <div id="input" class="form-group">
                <input type="text" class="form-control" id="nick" placeholder="名前">
                <input type="password" class="form-control" id="pass" placeholder="パスワード">
                <input type="button" class="form-control send" value="入室" onclick="enterRoom()">
                <div id="enterNotice" class="notice"></div>
            </div>

            <div class="form-group">
                <p>部屋一覧</p>
                <?php foreach($roomlist as $key => $value) : ?>
                    <div class="form-check">
                    <label class="form-check-label">
                        <input type="radio" class="form-check-input" name="room" value="<?=$key?>"><?=$key?>
                    </label>
                    </div>
                <?php endforeach; ?>
            </div>
        </form>
    </div>

    <div class="edit">
        
        <a onclick="toggle()" id="toggle">閉じる</a>

        <h2>作成</h2>

        <form id="create" class="form-group">
            <input type="text" class="form-control" id="room" placeholder="部屋名">
            <input type="password" class="form-control" id="pass" placeholder="パスワード">
            <input type="hidden" class="form-control" name="rand" value="<?=$rand?>">
            <input type="button" class="form-control send" value="作成" onclick="createRoom()">
            <span id="createNotice" class="notice"></span>
        </form>

        <h2>削除</h2>

        <form id="remove" class="form-group">
            <input type="password" class="form-control" id="pass" placeholder="パスワード">
            <input type="hidden" class="form-control" name="rand" value="<?=$rand?>">
            <input type="button" class="form-control del" value="削除" onclick="removeRoom()">
            <span id="removeNotice" class="notice"></span>
        <div>
                <p>部屋一覧</p>
                <?php foreach($roomlist as $key => $value) : ?>
                    <div class="form-check">
                    <label class="form-check-label">
                        <input type="radio" class="form-check-input" name="room" value="<?=$key?>"><?=$key?>
                    </label>
                    </div>
                <?php endforeach; ?>
            </div>
        </form>
    </div>
</body>
</html>
