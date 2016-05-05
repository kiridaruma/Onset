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