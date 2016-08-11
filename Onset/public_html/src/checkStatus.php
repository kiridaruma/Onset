<?php
<<<<<<< HEAD
require_once 'core.php';
=======
require_once('core.php');
>>>>>>> 737c102658c3bcaad9d92a4e018bf67910c14d6c
header('Content-Type: text/plain');

$coreStatusArr = array();
foreach (scandir('./') as $val) {
    if($val == '.' || $val == '..') continue;
    $coreStatusArr[$val] = is_readable($val);
}

foreach ($coreStatusArr as $key => $val) {
    echo $val ? "" : $key."にアクセスできません\n";
}

<<<<<<< HEAD
$BCDiceURL = Config::bcdiceURL;
$SSL       = Config::enableSSL ? 's' : '';

file_get_contents("http{$SSL}://{$BCDiceURL}?list=1");
echo strpos($http_response_header[0], '200') !== FALSE ? "ダイスボットの設定は正常です\n" : "ダイスボットにアクセスできません\n";

$dir = Config::roomSavepath;
$dirStatus = is_writable($dir) && is_readable($dir);
$roomListStatus = is_writable($dir) && is_readable($dir);
echo $dirStatus && $roomListStatus ? "部屋データの設定は正常です\n" : "部屋データにアクセスできません\n";
=======
$url = config::bcdiceURL;
$s = config::enableSSL ? 's' : '';
file_get_contents("http{$s}://{$url}?list=1");
echo strpos($http_response_header[0], '200') !== FALSE ? "ダイスボットの設定は正常です\n" : "ダイスボットにアクセスできません\n";

$roomPath = config::roomSavepath;
$roomDirStatus = is_writable($roomPath) && is_readable($roomPath);
$roomlistStatus = is_writable($roomPath) && is_readable($roomPath);
echo $roomDirStatus && $roomlistStatus ? "部屋データの設定は正常です\n" : "部屋データにアクセスできません\n";
>>>>>>> 737c102658c3bcaad9d92a4e018bf67910c14d6c
