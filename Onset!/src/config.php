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
'roomSavepathFromI'はトップのindex.phpから見た部屋データへのパスです
基本は'roomSavepath'だけをいじってください
カスタマイズする場合は良しなに...
*/
$config['roomSavepath'] = "../../room/";
$config['roomSavepathFromI'] = "src/".$config['roomSavepath'];