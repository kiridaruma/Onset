<?php
/*
Onset!の設定ファイルです
マスターパスワードや管理設定はここから行えます
*/

/*
Onsetの管理パスワードです
簡単なものに設定しないでください
*/
$config['pass'] = "";

/*
部屋データを置くディレクトリへのパスです
カスタマイズする場合は良しなに...
*/
$config['roomSavepath'] = __DIR__."/../../room/";

/*
bcdiceへのURL
ダイスボットへのパスを書いてください
デフォルトではindex.phpと同じ階層にあります
*/
$config['bcdiceURL'] = "localhost/Onset!/bcdice/roll.rb";

/*
sslを有効にするか
URLの先頭についてる、httpsってやつです
わからない人はいじらないほうがいいと思います
*/
$config['enableSSL'] = false;

/*
最大部屋数
1部屋当たりはそこまで容量食いません
サーバーのスペックに合わせて適当に設定してください
*/

$config["roomLimit"] = 100;

/*
部屋名の長さ制限
*/
$config['maxRoomName'] = 30;

/*
チャットの最大文字数と、ニックネームの最大文字数
*/
$config["maxChatText"] = 300;
$config["maxChatNick"] = 20;